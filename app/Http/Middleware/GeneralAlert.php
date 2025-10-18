<?php

namespace App\Http\Middleware;

use Closure;
use App\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GeneralAlert
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!empty(Auth::user())) {
            $this->discordAccountNotLinked();
        }
        return $next($request);
    }

    /**
     * Prüft ob der Discord Account verlinkt ist
     *
     * @return void
     */
    public function discordAccountNotLinked()
    {
        if (empty(Auth::user()->discord) && !empty(config('services.discord.client_id'))) {
            foreach (session('alerts', []) as $alert) {
                if (str_contains($alert['message'], 'Dein Discord-Account ist nicht verknüpft!')) {
                    return;
                }
            }
            Alert::addAlert(
                '<a class="text-warning" href="' . route('user.show', Auth::user()) . '">
                    Dein Discord-Account ist nicht verknüpft! Klicke hier um deinen Discord-Account zu verknüpfen!
                </a>',
                'warning',
                true,
            );
        }
    }
}
