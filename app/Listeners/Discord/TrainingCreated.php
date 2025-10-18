<?php

namespace App\Listeners\Discord;

use App\Enums\Training\Type;
use App\Events\DiscordNotify;
use App\Models\Trainings\Training;
use App\Traits\DiscordTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class TrainingCreated implements ShouldQueue
{
    use DiscordTrait;

    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(DiscordNotify $event): void
    {
        if ($event->type != Type::TRAINING_CREATED) {
            return;
        }
        $model = $event->model;
        if (!($model instanceof Training)) {
            return;
        }

        $fields = [
            [
                'name' => __('general.ausbilder'),
                'value' => $model->getTrainerName(),
                'inline' => true,
            ],
            [
                'name' => '',
                'value' => '',
                'inline' => true,
            ],
            [
                'name' => __('general.datum_uhrzeit'),
                'value' => $model->getFormatedDate(),
                'inline' => true,
            ],
            [
                'name' => __('general.treffpunkt'),
                'value' => $model->getMeetingPoint(),
            ],
            [
                'name' => __('general.maximale_teilnehmer'),
                'value' => $model->getMaxParticipants(),
                'inline' => true,
            ],
            [
                'name' => __('general.minimale_teilnehmer'),
                'value' => $model->getMinParticipants(),
                'inline' => true,
            ],
            [
                'name' => __('general.anmeldung'),
                'value' => config('app.url') . '#ausbildung-' . $model->getQueueableId(),
            ],
        ];

        if (!empty($model->getAdditionalInformation())) {
            $fields[] = [
                'name' => __('general.zusatzinformationen'),
                'value' => $model->getAdditionalInformation(),
            ];
        }

        $embed = $this->buildEmbed(
            title: __('general.neue_ausbildung_verfuegbar'),
            description: $model->getName(false, true),
            url: config('app.url') . '#ausbildung-' . $model->getQueueableId(),
            fields: $fields,
        );

        foreach ($event->notifications as $url) {
            try {
                $this->sendToDiscord($url, $embed);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }
}
