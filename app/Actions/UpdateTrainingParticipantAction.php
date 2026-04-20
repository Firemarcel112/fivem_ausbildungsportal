<?php

namespace App\Actions;

use App\DTO\ActionResult;
use App\Models\Trainings\Participant;
use Illuminate\Support\Facades\DB;

class UpdateTrainingParticipantAction
{
    /**
     * @return ActionResult
     */
    public function execute(Participant $model, array $data): ActionResult
    {
        return DB::transaction(function () use ($model, $data) {

            $model->fill($data);
            $model->save();

            return new ActionResult(
                true,
                __('general.erfolgreich_gespeichert'),
                $model,
            );
        });
    }
}
