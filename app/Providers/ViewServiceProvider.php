<?php

namespace App\Providers;

use App\DTO\SimpleUserViewData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        view()->composer('*', function (View $view) {

            if (Auth::check()) {
                Auth::user()?->loadMissing(['roles.permissions', 'permissions']);
            }

            $user_dto = SimpleUserViewData::fromModel(Auth::user());

            $view->with('auth_user', $user_dto);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
