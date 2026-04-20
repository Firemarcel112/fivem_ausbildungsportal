<?php

namespace App\Events;

use App\DTO\TrainingParticipantViewData;
use App\Interfaces\DiscordNotificationInterface;
use App\Models\Fraction;
use App\Models\Training;
use App\Traits\DiscordTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class TrainingCompleted implements DiscordNotificationInterface
{
    use DiscordTrait;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Collection $factions;

    /**
     * @param Training                                 $model
     * @param ?Collection<TrainingParticipantViewData> $participants,
     * @param array<int, array{
     * present: bool,
     * passed: bool,
     * signed_out: bool,
     * notices: string|null
     * }> $participants_fields
     */
    public function __construct(
        public Training $model,
        public ?Collection $participants = null,
        public array $participants_fields = [],
    ) {}

    public function getWebhookUrls(): array
    {
        return Fraction::query()
            ->pluck('discord_webhook_completed')
            ->filter()
            ->toArray();
    }

    public function getEmbed(?string $fraction_short_name = null): array
    {
        $default_fields = [
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
        ];

        $participant_fields = $this->getModifiedParticipants($fraction_short_name);

        $fields = array_merge($default_fields, $participant_fields);

        return $this->buildEmbed(
            title: __('general.ausbildung_abgeschlossen'),
            description: $this->model->name,
            url: route('ausbildung.show', $this->model->getKey()),
            fields: $fields
        );
    }

    public function getModifiedParticipants(?string $fraction_short_name = null)
    {
        $grouped_participants = $this->getGroupedParticipants();

        $map_labels = [
            'present' => __('general.anwesend'),
            'passed' => __('general.bestanden'),
            'signed_out' => __('general.abgemeldet'),
            'absence' => __('general.abwesend'),
            'not_passed' => __('general.nicht_bestanden'),
            'has_notices' => __('general.notizen'),
        ];

        $exclude_keys = [
            'participants_collection',
        ];

        $fields = [];

        foreach ($grouped_participants as $key => $data) {
            if (in_array($key, $exclude_keys)) {
                continue;
            }
            foreach ($data as $fraction_name => $participants) {
                $tmp = [];
                if (!empty($fraction_short_name) && $fraction_name != $fraction_short_name) {
                    continue;
                }
                $key_name = $map_labels[$key];
                switch ($key) {
                    case 'has_notices':
                        $tmp[$key_name] = $participants->map(function (TrainingParticipantViewData $participant) use ($fraction_name, $grouped_participants) {
                            return str('* ')
                                ->append($participant->full_name)
                                ->append(' (')
                                ->append($fraction_name)
                                ->append(')')
                                ->append("\n")
                                ->append('-> ')
                                ->append($grouped_participants['participants_collection']->get($participant->id)['notices'])
                                ->value();
                        })
                            ->implode("\n");
                        break;

                    default:
                        $tmp[$key_name] = $participants->map(function (TrainingParticipantViewData $participant) use ($fraction_name) {
                            return str('* ')
                                ->append($participant->full_name)
                                ->append(' (')
                                ->append($fraction_name)
                                ->append(')')
                                ->value();
                        })
                            ->implode("\n");
                }

                if (!empty($tmp[$key_name])) {
                    $fields[] = [
                        'name' => $key_name,
                        'value' => $tmp[$key_name],
                    ];
                }
            }
        }

        return $fields;
    }

    /**
     * Gibt die Teilnehmer gruppiert zurück
     *
     * @return array{
     * participants_collection: Collection<string, Collection<int, TrainingParticipantViewData>>,
     * present: Collection<string, Collection<int, TrainingParticipantViewData>>,
     * passed: Collection<string, Collection<int, TrainingParticipantViewData>>,
     * signed_out: Collection<string, Collection<int, TrainingParticipantViewData>>,
     * not_passed: Collection<string, Collection<int, TrainingParticipantViewData>>,
     * absence: Collection<string, Collection<int, TrainingParticipantViewData>>,
     * has_notices: Collection<string, Collection<int, TrainingParticipantViewData>>
     * }
     */
    public function getGroupedParticipants(): array
    {

        /**
         * @var Collection<int, array<mixed>> $participant_fields_data_collection
         */
        $participant_fields_data_collection = collect($this->participants_fields);

        $present = $participant_fields_data_collection->filter(function (array $item) {
            return $item['passed'] == 1 || $item['present'] == 1;
        })
            ->toArray();

        $passed = $participant_fields_data_collection->filter(function (array $item) {
            return $item['passed'] == 1;
        })
            ->toArray();

        $passed_participants = $this->participants?->filter(function (TrainingParticipantViewData $participant) use ($passed) {
            return Arr::exists($passed, $participant->id);
        })
            ->groupBy('default_fraction_short_name');

        $present_participants = $this->participants?->filter(function (TrainingParticipantViewData $participant) use ($present) {
            return Arr::exists($present, $participant->id);
        })
            ->groupBy('default_fraction_short_name');

        $signed_out = $participant_fields_data_collection->filter(function (array $item) {
            return $item['signed_out'] == 1;
        })
            ->toArray();

        $signed_out_participants = $this->participants?->filter(function (TrainingParticipantViewData $participant) use ($signed_out) {
            return Arr::exists($signed_out, $participant->id);
        })
            ->groupBy('default_fraction_short_name');

        $not_passed = $participant_fields_data_collection->filter(function (array $item) {
            return $item['present'] == 1 &&
                $item['passed'] == 0 &&
                $item['signed_out'] == 0;
        })
            ->toArray();

        $not_passed_participants = $this->participants?->filter(function (TrainingParticipantViewData $participant) use ($not_passed) {
            return Arr::exists($not_passed, $participant->id);
        })
            ->groupBy('default_fraction_short_name');

        $absence = $participant_fields_data_collection->filter(function (array $item) {
            return $item['present'] == 0 &&
                $item['passed'] == 0 &&
                $item['signed_out'] == 0;
        })
            ->toArray();

        $absence_participants = $this->participants?->filter(function (TrainingParticipantViewData $participant) use ($absence) {
            return Arr::exists($absence, $participant->id);
        })
            ->groupBy('default_fraction_short_name');

        $has_notices = $participant_fields_data_collection->filter(function (array $item) {
            return !is_null($item['notices']);
        })
            ->toArray();

        $has_notices_participants = $this->participants?->filter(function (TrainingParticipantViewData $participant) use ($has_notices) {
            return Arr::exists($has_notices, $participant->id);
        })
            ->groupBy('default_fraction_short_name');

        return [
            'participants_collection' => $participant_fields_data_collection,
            'present' => $present_participants,
            'passed' => $passed_participants,
            'signed_out' => $signed_out_participants,
            'not_passed' => $not_passed_participants,
            'absence' => $absence_participants,
            'has_notices' => $has_notices_participants,
        ];
    }
}
