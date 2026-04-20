<?php

namespace App\Actions;

use App\DTO\ActionResult;
use App\Models\Training;
use Illuminate\Support\Facades\DB;

class StoreTrainingAction
{
    public function execute(array $data): ActionResult
    {
        return DB::transaction(function () use ($data) {

            $casted_data = [
                'trainer_id' => (int) $data['trainer_id'],
                'qualification_id' => (int) $data['qualification_id'],
                'meeting_point' => $data['meeting_point'],
                'date' => $data['date'],
                'time' => $data['time'],
                'min_participants' => (int) $data['min_participants'],
                'max_participants' => (int) $data['max_participants'],
                'additional_information' => $data['additional_information'],
            ];

            $model = Training::create($casted_data);

            return new ActionResult(
                true,
                __('general.erfolgreich_gespeichert'),
                $model,
            );
        });
    }
}
