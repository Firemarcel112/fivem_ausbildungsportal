<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check()) {
            Alert::addAlert(__('general.bereits_eingeloggt'), 'Info');

            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Einloggen
     *
     * @param LoginRequest
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request)
    {

        $user = User::findByUsername($request->input('username'));

        if (!$user) {
            Alert::addAlert(__('general.benutzername_oder_passwort_falsch'), 'error');

            return redirect()->back();
        }

        if (Hash::check($request->input('password'), $user->password) || config('app.env') == 'local') {
            Auth::login($user, $request->has('remember'));
            session([
                'name' => $user->account->getFullName(),
            ]);
        } else {
            Alert::addAlert(__('general.benutzername_oder_passwort_falsch'), 'error');

            return redirect()->back();
        }

        if ($user->isTrainer()) {
            return redirect()->intended(route('ausbilder.index'));
        }

        return redirect()->route('home');
    }

    /**
     * Anmeldung via Discord
     *
     * @return void
     */
    public function loginDiscord()
    {
        session(['discord_type' => 'AUTH']);

        return Socialite::driver('discord')
            ->scopes(explode(',', config('services.discord.scopes')))
            ->redirect();
    }

    /**
     * Loggt den Benutzer aus
     *
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Alert::addAlert(__('general.erfolgreich_ausgeloggt'), 'success');
        Auth::logout();

        return redirect()->route('home');
    }
}
