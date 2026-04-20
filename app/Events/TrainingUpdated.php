<?php

namespace App\Events;

use App\Interfaces\DiscordNotificationInterface;
use App\Models\Fraction;
use App\Models\Training;
use App\Models\User;
use App\Traits\DiscordTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class TrainingUpdated implements DiscordNotificationInterface
{
    use DiscordTrait;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param Training                      $model
     * @param array<int, string|int|Carbon> $original_values
     * @param array<int, string|int>        $notification_fractions_ids
     */
    public function __construct(
        public Training $model,
        public array $original_values,
        public array $notification_fractions_ids
    ) {}

    public function getWebhookUrls(): array
    {
        if (in_array('alle', $this->notification_fractions_ids)) {
            return Fraction::query()
                ->pluck('discord_webhook')
                ->filter()
                ->toArray();
        }

        return Fraction::query()
            ->isInFractionId($this->notification_fractions_ids)
            ->pluck('discord_webhook')
            ->filter()
            ->toArray();
    }

    public function getEmbed(): array
    {
        $fields = $this->getFieldDefinitions();
        $changes = false;

        foreach ($this->original_values as $key => $old_value) {
            $new_value = $this->model->{$key};
            $value = $old_value ?? '';
            if (empty($value)) {
                continue;
            }
            switch ($key) {
                case 'trainer_id':
                    $value = User::find($old_value)?->full_name;
                    $new_value = $this->model->trainer_name;
                    break;
                case 'date':
                    $value = $old_value->format('d.m.Y');
                    $new_value = $this->model->date->format('d.m.Y');
                    break;
                case 'time':
                    $value = $old_value->format('H:i');
                    $new_value = $this->model->time->format('H:i');
                    break;
                case 'additional_information':
                    $fields['additional_information'] = [
                        'name' => __('general.zusatzinformationen'),
                        'value' => '',
                        'inline' => false,
                    ];
                    break;
            }

            if ($value != $new_value) {
                $changes = true;
                if (empty($old_value)) {
                    $value = $new_value;
                } else {
                    $value .= ' => ' . $new_value;
                }
            }

            $fields[$key]['value'] = $value;
        }

        $fields = array_values($fields);

        return $changes ?
            $this->buildEmbed(
                title: 'Ausbildungsänderung',
                fields: $fields,
                description: $this->model->name . ' Lehrgangs-ID: ' . $this->model->getKey(),
                url: config('app.url') . '#ausbildung-' . $this->model->getKey(),
            ) : [];
    }

    private function getFieldDefinitions(): array
    {
        return [
            'trainer_id' => [
                'name' => __('general.ausbilder'),
                'value' => '',
                'inline' => false,
            ],
            'date' => [
                'name' => __('general.datum'),
                'value' => '',
                'inline' => true,
            ],
            'space' => [
                'name' => '',
                'value' => '',
                'inline' => true,
            ],
            'time' => [
                'name' => __('general.uhrzeit'),
                'value' => '',
                'inline' => true,
            ],
            'meeting_point' => [
                'name' => __('general.treffpunkt'),
                'value' => '',
            ],
            'max_participants' => [
                'name' => __('general.maximale_teilnehmer'),
                'value' => '',
                'inline' => true,
            ],
            'min_participants' => [
                'name' => __('general.minimale_teilnehmer'),
                'value' => '',
                'inline' => true,
            ],
            'sign_in' => [
                'name' => __('general.anmeldung'),
                'value' => config('app.url') . '#ausbildung-' . $this->model->getKey(),
            ],
        ];
    }
}
