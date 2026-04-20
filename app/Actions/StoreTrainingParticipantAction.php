<?php

namespace App\Actions;

use App\DTO\ActionResult;
use App\Models\Trainings\Participant;
use Illuminate\Support\Facades\DB;

class StoreTrainingParticipantAction
{
    /**
     * @return ActionResult
     */
    public function execute(array $data): ActionResult
    {
        return DB::transaction(function () use ($data) {

            $training_id = $data['training_id'];
            $account_id = $data['account_id'];

            Participant::firstOrCreate(
                [
                    'training_id' => $training_id,
                    'user_id' => $account_id,
                ],
                [
                    'training_id' => $training_id,
                    'user_id' => $account_id,
                ]
            );

            return new ActionResult(
                true,
                __('general.erfolgreich_gespeichert')
            );
        });
    }
}
