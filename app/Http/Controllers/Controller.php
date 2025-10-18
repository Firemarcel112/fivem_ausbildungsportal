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
    public function checkPermission($permission)
    {
        if (Auth::check() && !Auth::user()->hasPermissionTo($permission)) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->back();
        }
        return true;
    }
}
