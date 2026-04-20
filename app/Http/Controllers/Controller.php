<?php

namespace App\Http\Controllers;

use App\Facades\Alert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    /**
     * Prüft die Permission und leitet den Benutzer zurück
     *
     * @param  string|array<int, string> $permissions
     * @return bool|RedirectResponse
     */
    public function checkPermission(string|array $permissions)
    {
        if (!Auth::check()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            abort(403);
        }

        /** @var \App\Models\User $user; */
        $user = Auth::user();

        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if ($user->can($permission)) {
                    return true;
                }
            }
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            abort(403);
        }
        if (!$user->can($permissions)) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            abort(403);
        }

        return true;
    }
}
