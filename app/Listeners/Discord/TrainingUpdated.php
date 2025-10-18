<?php

namespace App\Listeners\Discord;

use App\Enums\Training\Type;
use App\Events\DiscordNotify;
use App\Models\Trainings\Training;
use App\Models\User;
use App\Traits\DiscordTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class TrainingUpdated implements ShouldQueue
{
    use DiscordTrait;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DiscordNotify $event): void
    {
        if ($event->type != Type::TRAINING_UPDATED) {
            return;
        }
        $model = $event->model;
        if (!($model instanceof Training)) {
            return;
        }

        $changes = false;
        $fields = [];
        foreach ($event->context['old_values'] as $key => $old_value) {
            $new_value = $event->context['new_values'][$key];
            $value = $old_value ?? '';
            if ($old_value != $new_value) {
                $changes = true;
            }
            if ($old_value != $new_value) {
                if (empty($old_value)) {
                    $value = $new_value;
                } else {
                    $value .= ' => ' . $new_value;
                }
            }
            if (empty($value)) {
                continue;
            }
            switch ($key) {
                case 'trainer_id':
                    $old_ausbilder = User::find($old_value)?->getFullName() ?? __('general.unbekannt');
                    $new_ausbilder = User::find($new_value)?->getFullName() ?? __('general.unbekannt');
                    $value = $old_ausbilder;
                    if ($old_value != $new_value) {
                        $value .= ' => ' . $new_ausbilder;
                    }
                    break;
                case 'date':
                    $value = (new \DateTime($old_value))->format('d.m.Y');
                    $new_value_formatted = (new \DateTime($new_value))->format('d.m.Y');
                    if ($value != $new_value_formatted) {
                        $value .= ' => ' . $new_value_formatted;
                    }
                    break;
                case 'time':
                    $value = (new \DateTime($old_value))->format('H:i');
                    $new_value_formatted = (new \DateTime($new_value))->format('H:i');
                    if ($value != $new_value_formatted) {
                        $value .= ' => ' . $new_value_formatted;
                    }
                    break;
                case 'additional_informations':
                    $inline = false;
                    break;
            }
            $key = __('dynamicKeys.training.' . $key);
            $fields[] = [
                'name' => ucfirst(str_replace('_', ' ', $key)),
                'value' => $value,
                'inline' => $inline ?? true,
            ];
        }
        $fields[] = [
            'name' => 'Anmeldung',
            'value' => config('app.url') . '#ausbildung-' . $model->getId(),
        ];

        $embed = $this->buildEmbed(
            title: 'Ausbildungsänderung',
            fields: $fields,
            description: $model->getName() . ' Lehrgangs-ID: ' . $model->getId(),
            url: config('app.url') . '#ausbildung-' . $model->getId(),
        );

        if ($changes) {
            foreach ($event->notifications as $url) {
                try {
                    if (empty($url)) {
                        continue;
                    }
                    $this->sendToDiscord($url, $embed);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        }
    }
}
