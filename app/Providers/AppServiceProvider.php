<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
use App\Policies\GeneralPolicy;
use App\Services\AlertService;
use App\Traits\ClockworkTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    use ClockworkTrait;

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

        $this->loadSettings();
    }

    /**
     * Lädt die Settings in Config dateien
     * @return void
     */
    public function loadSettings()
    {
        $this->beginClockwork('Load Settings');
        $settings = Setting::all();
        foreach ($settings as $setting) {
            config(['settings.' . $setting->getKey() => $setting->getValue()]);
        }
        $this->endClockwork('Load Settings');
    }
}
