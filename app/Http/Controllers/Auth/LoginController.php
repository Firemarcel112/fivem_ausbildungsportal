<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthenticationService;
use Illuminate\Support\Facades\Auth;

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
     * @param  LoginRequest                            $request
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request, AuthenticationService $auth_service)
    {

        if (!$auth_service->attemptLogin($request->input('username'), $request->input('password'), $request->has('remember'))) {
            Alert::addAlert(__('general.benutzername_oder_passwort_falsch'), 'error');
        }

        return redirect()->intended($auth_service->getRedirectRouteForUser(Auth::user()));
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
