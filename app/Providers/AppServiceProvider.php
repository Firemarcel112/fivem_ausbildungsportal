<?php

namespace App\Providers;

use App\Events\QualificationAction;
use App\Events\TrainingCompleted;
use App\Interfaces\DiscordNotificationInterface;
use App\Listeners\Discord\SendDiscordEmbed;
use App\Listeners\GenerateCertificate;
use App\Listeners\ModifyTrainingParticipantData;
use App\Listeners\Qualification\ClearCache;
use App\Models\Setting;
use App\Models\User;
use App\Services\AlertService;
use App\Services\Export\UserWithQualificationsService;
use App\Traits\ClockworkTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    use ClockworkTrait;

    public function registerEvents(): void
    {
        Event::listen(
            listener: SendDiscordEmbed::class,
            events: DiscordNotificationInterface::class,
        );

        Event::listen(
            events: TrainingCompleted::class,
            listener: ModifyTrainingParticipantData::class
        );

        Event::listen(
            events: QualificationAction::class,
            listener: ClearCache::class,
        );

        Event::listen(
            events: TrainingCompleted::class,
            listener: GenerateCertificate::class
        );
    }

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
        $this->registerEvents();

        Gate::before(function (User $user, string $ability, $args) {

            if (in_array('ignore-superadmin', $args)) {
                return null;
            }

            if ($user->isSuperadmin()) {
                return true;
            }

            return null;
        });

        $this->defineGates();

        Model::preventLazyLoading(config('app.prevent_lazy_loading', false));

        if (!empty((Auth::user()?->getKey() ?? 0) == 1) || config('app.superadmin_ip') == request()->ip()) {
            config(['app.debug' => true]);
        }

        $this->app->singleton(UserWithQualificationsService::class, function () {
            $qualifications = \App\Models\Qualification::getAllQualifications(true)
                ->pluck('name')
                ->toArray();

            return new UserWithQualificationsService($qualifications);
        });

        $this->loadSettings();
    }

    public function defineGates(): void
    {
        Gate::define('viewPulse', function (User $user) {
            return $user->isSuperadmin() && app()->isProduction();
        });
    }

    /**
     * Lädt die Settings in Config dateien
     *
     * @return void
     */
    public function loadSettings()
    {
        if (!DB::table('settings')->exists()) {
            return;
        }

        $this->beginClockwork('Load Settings');
        $settings = Setting::all();
        foreach ($settings as $setting) {
            config(['settings.' . $setting->key => $setting->value]);
        }
        $this->endClockwork('Load Settings');
    }
}
