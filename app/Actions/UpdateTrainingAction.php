<?php

namespace App\Actions;

use App\DTO\ActionResult;
use App\Models\Training;
use Illuminate\Support\Facades\DB;

class UpdateTrainingAction
{
    public function execute(Training $model, array $data): ActionResult
    {
        return DB::transaction(function () use ($data, $model) {

            $model->update($data);

            return new ActionResult(
                true,
                __('general.ausbildung_erfolgreich_geaendert'),
                $model,
            );
        });
    }
}
