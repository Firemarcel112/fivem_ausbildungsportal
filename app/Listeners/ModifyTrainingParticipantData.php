<?php

namespace App\Listeners;

use App\Actions\UpdateTrainingParticipantAction;
use App\DTO\TrainingParticipantViewData;
use App\Events\TrainingCompleted;
use App\Models\Trainings\Participant;
use App\Models\User\Qualification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ModifyTrainingParticipantData implements ShouldQueue
{
    public function __construct(
        public UpdateTrainingParticipantAction $update_training_participant_action
    ) {}

    /**
     * Handle the event.
     */
    public function handle(TrainingCompleted $event): void
    {
        $participants = $this->prepareForUpdate($event->getGroupedParticipants());

        /**
         * @var \App\Models\Qualification $qualification
         */
        $qualification = $event->model->qualification;

        foreach ($participants as $participant) {
            $this->update_training_participant_action->execute($participant['model'], $participant['fields']);

            Qualification::firstOrCreate([
                'user_id' => $participant['model']->user_id,
                'qualification_id' => $qualification->getKey(),
            ], [
                'training_id' => $event->model->getKey(),
            ]);
        }
    }

    /**
     * @param  array<mixed>        $grouped_participants
     * @return array<array<mixed>>
     */
    protected function prepareForUpdate(array $grouped_participants): array
    {
        $return_data = [];
        foreach ($grouped_participants as $key => $grouped_data) {
            if ($key == 'participants_collection') {
                continue;
            }
            foreach ($grouped_data as $fraction_name => $participants) {
                $data = $participants->each(function (TrainingParticipantViewData $participant) use ($key, &$return_data, $grouped_participants) {
                    if (!isset($return_data[$participant->id])) {
                        $return_data[$participant->id] = [];
                        $return_data[$participant->id]['model'] = [];
                        $return_data[$participant->id]['fields'] = [];
                    }
                    $return = [];
                    if (empty($return_data[$participant->id]['model'])) {
                        $return_data[$participant->id]['model'] = Participant::find($participant->id);
                    }

                    switch ($key) {
                        case 'present':
                            $return = [
                                'present' => 1,
                            ];
                            break;
                        case 'passed':
                            $return = [
                                'passed' => 1,
                            ];
                            break;
                        case 'signed_out':
                            $return = [
                                'logged_out' => 1,
                            ];
                            break;
                        case 'not_passed':
                            $return = [
                                'present' => 1,
                                'passed' => 0,
                            ];
                        case 'absence':
                            break;
                        case 'has_notices':
                            $return = [
                                'notices' => $grouped_participants['participants_collection']->get($participant->id)['notices'],
                            ];
                    }
                    $return_data[$participant->id]['fields'] = array_merge($return_data[$participant->id]['fields'], $return);
                });
            }
        }

        return $return_data;
    }
}
