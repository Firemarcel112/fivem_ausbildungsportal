<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\GeneralPolicy;
use App\Services\AlertService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('alert', function ($app) {
            return new AlertService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(config('app.prevent_lazy_loading', false));

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('discord', \SocialiteProviders\Discord\Provider::class);
        });

        Gate::define('is_trainer', [GeneralPolicy::class, 'isTrainer']);
        if (app()->environment('production')) {
            Gate::define('viewPulse', function (User $user) {
                return $user->isSuperadmin() || (config('app.env') == 'local');
            });
        }
    }
}
