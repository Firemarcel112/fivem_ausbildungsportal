<?php

namespace App\Actions;

use App\DTO\ActionResult;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateUserAction
{
    public function execute(User $user, array $data): ActionResult
    {
        return DB::transaction(function () use ($user, $data) {
            $account = $user->account;

            $user_data = [
                'name' => $data['username'],
            ];

            if (!empty($data['password'])) {
                $user_data['password'] = $data['password'];
            }

            $user->update($user_data);
            $user_changed = $user->wasChanged();

            $account->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'date_of_birth' => $data['date_of_birth'],
                'birth_location' => $data['birth_location'],
                'gender' => $data['gender'],
            ]);
            $account_changed = $account->wasChanged();

            $sync_result = $account->fractions()->sync([
                $data['default_fraction'] => ['default' => 1],
            ]);

            $fractions_changed = count($sync_result['attached']) > 0 ||
                count($sync_result['detached']) > 0 ||
                count($sync_result['updated']) > 0;

            if ($user_changed || $account_changed || $fractions_changed) {
                return new ActionResult(
                    true,
                    __('general.erfolgreich_aktualisiert')
                );
            }

            return new ActionResult(
                true,
                __('general.keine_aenderungen')
            );
        });
    }
}
