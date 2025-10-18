<?php

namespace App\Listeners\Discord;

use App\Enums\Training\Type;
use App\Events\DiscordNotify;
use App\Models\Trainings\Participant;
use App\Models\Trainings\Training;
use App\Models\User\Qualification as UserQualification;
use App\Traits\DiscordTrait;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrainingCompleted implements ShouldQueue
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
        if ($event->type != Type::TRAINING_COMPLETED) {
            return;
        }
        $model = $event->model;

        if (!($model instanceof Training)) {
            return;
        }
        $default_fields = [
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
        ];

        if ($event->context['cancelled'] ?? false) {
            $fields[] = [
                'name' => 'Info:',
                'value' => $event->context['cancelled_notice'] ?? __('general.lehrgang_entfaellt'),
            ];
            foreach ($event->notifications as $webhook_url) {
                $fields = array_merge($default_fields, $fields);
                $embed = $this->buildEmbed(
                    title: __('general.ausbildung_abgeschlossen'),
                    description: $model->getName(false, true),
                    fields: $fields,
                );
                $this->sendToDiscord($webhook_url, $embed);
            }
            return;
        }


        $participants_data = $event->context['participants_data'] ?? collect();
        $participants = $event->context['participants'] ?? collect();
        $fractions = $event->context['fractions'] ?? collect();
        $request = $event->context['request'] ?? null;

        $cases = [
            'Angemeldet',
            'Anwesend',
            'bestanden',
            'nicht_bestanden',
            'abgemeldet',
            'abwesend',
            'Notizen'
        ];
        $results['Angemeldet'] = collect();
        $results['Anwesend'] = collect();
        $results['bestanden'] = collect();
        $results['nicht_bestanden'] = collect();
        $results['abgemeldet'] = collect();
        $results['abwesend'] = collect();
        foreach ($cases as $case) {
            if (in_array($case, ['Angemeldet', 'abwesend', 'notizen', 'nicht_bestanden'])) {
                continue;
            }
            $results[$case] = $participants_data->filter(function ($participant, $id) use ($request, $case) {
                $case = strtolower($case);
                return in_array($id, array_keys($request[$case] ?? []));
            });
        }
        $results['Angemeldet'] = $participants_data->mapWithKeys(function ($participant) {
            return [
                $participant['id'] => [
                    'id' => $participant['id'],
                    'name' => $participant['name'],
                    'fraction' => $participant['fraction'],
                ],
            ];
        });

        // Teilnehmer die Bestanden haben sind automatisch als Anwesend makiert
        $results['Anwesend'] = $results['Anwesend']->merge($results['bestanden'])->unique();
        $results['nicht_bestanden'] = $results['Anwesend']->filter(function ($participant) use ($results) {
            return !$results['bestanden']->pluck('id')->contains($participant['id']);
        });
        $all_participants_ids = $participants->pluck('user_id');

        $present_participants_ids = $results['Anwesend']
            ->pluck('id')
            ->merge($results['abgemeldet']->pluck('id'))
            ->unique();

        $absent_participants_ids = $all_participants_ids->diff($present_participants_ids);

        $results['abwesend'] = $participants_data->filter(function ($participant, $id) use ($absent_participants_ids) {
            return $absent_participants_ids->contains($id);
        });

        foreach ($request->notices ?? [] as $key => $notice) {
            $results['Notizen'][$key] = $notice;
        }

        /** @var Participant $participant */
        foreach ($participants as $participant) {
            $participant->setPresent($results['Anwesend']->pluck('id')->contains($participant->getId()) ? 1 : 0);
            $participant->setPassed($results['bestanden']->pluck('id')->contains($participant->getId()) ? 1 : 0);
            $participant->setLoggedOut($results['abgemeldet']->pluck('id')->contains($participant->getId()) ? 1 : 0);
            if (!empty($request->notices[$participant->getId()])) {
                $participant->setNotices($request->notices[$participant->getId()]);
            }
            $participant->save();
            if ($participant->getPassed()) {
                $add_qualification = UserQualification::firstOrNew([
                    'user_id' => $participant->getUserId(),
                    'qualification_id' => $model->getQualificationId(),
                ]);
                $add_qualification->setTrainingId($model->getId());
                $add_qualification->save();
            }
        }
        foreach ($fractions as $fraction) {
            if (empty($fraction->getDiscordWebhookCompleted())) {
                continue;
            }
            $fields = [];
            $fields = $default_fields;
            $formated_data = [];
            switch ($fraction->getShortName()) {
                default:
                    foreach ($cases as $case) {
                        if ($case == 'Notizen') {
                            continue;
                        }
                        $formated_data[$case] = $results[$case]
                            ->filter(function ($data) use ($fraction) {
                                return empty($data['fraction']) ? false : ($data['fraction'] == $fraction->getShortName() || $fraction->getMaster());
                            })
                            ->map(function ($data) {
                                return '* ' . $data['name'] . " ({$data['fraction']})";
                            })
                            ->implode("\n");
                        if (!empty($formated_data[$case])) {
                            $fields[] = [
                                'name' => str_replace('_', ' ', ucwords($case)),
                                'value' => $formated_data[$case],
                            ];
                        }
                        if (empty($formated_data[$case])) {
                            continue;
                        }
                    }
            }
            if (!empty($fields[3])) {
                $embed = $this->buildEmbed(
                    title: __('general.ausbildung_abgeschlossen'),
                    description: $model->getName() . ' - Lehrgangs-ID:' . $model->getTrainingId(),
                    fields: $fields,
                );
                $this->sendToDiscord($fraction->getDiscordWebhookCompleted(), $embed);
            }
        }
    }
}
