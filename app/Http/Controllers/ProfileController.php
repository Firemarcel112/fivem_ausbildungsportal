<?php

namespace App\Http\Controllers;

use App\Facades\Alert;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Anzeige eines Profils
     *
     * @param User $user
     * @return mixed
     */
    public function show(User $user)
    {
        if (!Auth::user()->hasPermissionTo('usermanagement.index') && Auth::user()->id !== $user->id) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->back();
        }
        return view('user.profile', compact('user'));
    }
}
