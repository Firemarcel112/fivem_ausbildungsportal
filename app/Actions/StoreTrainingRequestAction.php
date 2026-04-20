<?php

namespace App\Actions;

use App\DTO\ActionResult;
use App\Models\Trainings\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreTrainingRequestAction
{
    public function execute(array $data): ActionResult
    {
        return DB::transaction(function () use ($data) {
            /** @var User $auth_user */
            $auth_user = Auth::user();

            $model = new Request;

            $data['user_id'] = $auth_user->getKey();

            $create = $model->firstOrNew($data);

            if ($create->exists) {
                return new ActionResult(
                    true,
                    __('general.bereits_angefragt'),
                );
            }

            return new ActionResult(
                true,
                __('general.erfolgreich_gespeichert'),
            );
        });
    }
}
