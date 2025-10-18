<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    /**
     * Zeigt den Benutzer an
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(User $user)
    {
        if (Auth::user()->getId() != $user->getId()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->back();
        }
        return view('auth.settings', compact('user'));
    }

    public function store(Request $request, User $user)
    {
        if (Auth::user()->getId() != $user->getId()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->back();
        }
        if (!empty($request->input('password'))) {
            $user->setPassword($request->input('password'));
        }
        if (!empty($request->input('username'))) {
            $user->setName($request->input('username'));
        }

        if ($user->isDirty()) {
            if ($user->save()) {
                Alert::addAlert(__('general.erfolgreich_gespeichert'), 'success');
            }
        } else {
            Alert::addAlert(__('general.keine_aenderungen'));
        }

        return redirect()->back();
    }

    /**
     * Verlinkung von Discord Account
     *
     * @return void
     */
    public function linkDiscordAccount()
    {
        session(['discord_type' => 'LINK']);
        return Socialite::driver('discord')
            ->scopes(explode(',', config('services.discord.scopes')))
            ->redirect();
    }
}
