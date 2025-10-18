<?php

$providers = [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    OwenIt\Auditing\AuditingServiceProvider::class,
    \SocialiteProviders\Manager\ServiceProvider::class,
];

if (config('app.env') == 'local') {
    $providers[] = BarryVdh\LaravelIdeHelper\IdeHelperServiceProvider::class;
}

return $providers;
