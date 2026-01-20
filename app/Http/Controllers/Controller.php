<?php

namespace App\Http\Controllers;

use App\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{

    /**
     * Prüft die Permission und leitet den Benutzer zurück
     * @param mixed $permission
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public function checkPermission(string|array $permissions)
    {
        if (!Auth::check()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            abort(403);
        }
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if (Auth::user()->hasPermissionTo($permission)) {
                    return true;
                }
            }
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            abort(403);
        }
        if (!Auth::user()->hasPermissionTo($permissions)) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            abort(403);
        }
        return true;
    }
}
