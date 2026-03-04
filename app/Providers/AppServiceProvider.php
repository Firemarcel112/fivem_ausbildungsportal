<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
use App\Policies\GeneralPolicy;
use App\Services\AlertService;
use App\Services\Export\UserWithQualificationsService;
use App\Traits\ClockworkTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
            return new AlertService;
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

        if (!empty((Auth::user()?->getKey() ?? 0) == 1) || config('app.superadmin_ip') == request()->ip()) {
            config(['app.debug' => true]);
        }

        $this->app->singleton(UserWithQualificationsService::class, function () {
            $qualifications = \App\Models\Qualifications\Qualification::getAllQualifications()
                ->pluck('name')
                ->toArray();

            return new UserWithQualificationsService($qualifications);
        });

        $this->loadSettings();
    }

    /**
     * Lädt die Settings in Config dateien
     *
     * @return void
     */
    public function loadSettings()
    {
        $this->beginClockwork('Load Settings');
        $settings = Setting::all();
        foreach ($settings as $setting) {
            config(['settings.' . $setting->key => $setting->value]);
        }
        $this->endClockwork('Load Settings');
    }
}
