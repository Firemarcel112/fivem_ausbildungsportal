<?php

namespace App\Events;

use App\Interfaces\DiscordNotificationInterface;
use App\Models\Fraction;
use App\Models\Training;
use App\Traits\DiscordTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrainingCreated implements DiscordNotificationInterface
{
    use DiscordTrait;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param Training               $model
     * @param array<int, string|int> $notification_fractions_ids
     */
    public function __construct(
        public Training $model,
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
        $fields = [
            [
                'name' => __('general.ausbilder'),
                'value' => $this->model->trainer_name,
                'inline' => true,
            ],
            [
                'name' => '',
                'value' => '',
                'inline' => true,
            ],
            [
                'name' => __('general.datum_uhrzeit'),
                'value' => $this->model->full_date_with_time,
                'inline' => true,
            ],
            [
                'name' => __('general.treffpunkt'),
                'value' => $this->model->meeting_point,
            ],
            [
                'name' => __('general.maximale_teilnehmer'),
                'value' => $this->model->max_participants,
                'inline' => true,
            ],
            [
                'name' => __('general.minimale_teilnehmer'),
                'value' => $this->model->min_participants,
                'inline' => true,
            ],
            [
                'name' => __('general.anmeldung'),
                'value' => config('app.url') . '#ausbildung-' . $this->model->getKey(),
            ],
        ];

        if (!empty($this->model->additional_information)) {
            $fields[] = [
                'name' => __('general.zusatzinformationen'),
                'value' => $this->model->additional_information,
            ];
        }

        return $this->buildEmbed(
            title: __('general.neue_ausbildung_verfuegbar'),
            description: $this->model->name,
            url: str(config('app.url'))
                ->append('#ausbildung-')
                ->append($this->model->getKey())
                ->value(),
            fields: $fields
        );
    }
}
