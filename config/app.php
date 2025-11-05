<?php

return [

    /*
	|--------------------------------------------------------------------------
	| Application Name
	|--------------------------------------------------------------------------
	|
	| This value is the name of your application, which will be used when the
	| framework needs to place the application's name in a notification or
	| other UI elements where an application name needs to be displayed.
	|
	*/

    'name' => env('APP_NAME', 'Laravel'),

    /*
	|--------------------------------------------------------------------------
	| Application Environment
	|--------------------------------------------------------------------------
	|
	| This value determines the "environment" your application is currently
	| running in. This may determine how you prefer to configure various
	| services the application utilizes. Set this in your ".env" file.
	|
	*/

    'env' => env('APP_ENV', 'production'),

    /*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

    'debug' => (bool) env('APP_DEBUG', false),

    /*
	|--------------------------------------------------------------------------
	| Application URL
	|--------------------------------------------------------------------------
	|
	| This URL is used by the console to properly generate URLs when using
	| the Artisan command line tool. You should set this to the root of
	| the application so that it's available within Artisan commands.
	|
	*/

    'url' => env('APP_URL', 'http://localhost'),

    /*
	|--------------------------------------------------------------------------
	| Application Timezone
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default timezone for your application, which
	| will be used by the PHP date and date-time functions. The timezone
	| is set to "UTC" by default as it is suitable for most use cases.
	|
	*/

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
	|--------------------------------------------------------------------------
	| Application Locale Configuration
	|--------------------------------------------------------------------------
	|
	| The application locale determines the default locale that will be used
	| by Laravel's translation / localization methods. This option can be
	| set to any locale for which you plan to have translation strings.
	|
	*/

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
	|--------------------------------------------------------------------------
	| Encryption Key
	|--------------------------------------------------------------------------
	|
	| This key is utilized by Laravel's encryption services and should be set
	| to a random, 32 character string to ensure that all encrypted values
	| are secure. You should do this prior to deploying the application.
	|
	*/

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
	|--------------------------------------------------------------------------
	| Maintenance Mode Driver
	|--------------------------------------------------------------------------
	|
	| These configuration options determine the driver used to determine and
	| manage Laravel's "maintenance mode" status. The "cache" driver will
	| allow maintenance mode to be controlled across multiple machines.
	|
	| Supported drivers: "file", "cache"
	|
	*/

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    // Prüft Lazy Loading von Models
    'prevent_lazy_loading' => env('APP_PREVENT_LAZY_LOADING', false),
    // Setzt dass App theme
    'theme' => env('APP_THEME', 'dark'),
    // Setzt die CDN URL
    'cdn_url' => env('CDN_URL', ''),
    // Login erlauben
    'login_allowed' => env('LOGIN_ALLOWED', true),
    // Registrierung erlauben
    'registration_allowed' => env('REGISTRATION_ALLOWED', true),
    // Superadmin Benutzer
    'superadmins' => env('SUPERADMIN_USERS', ''),
    // Standard Treffpunkt für Ausbildungen
    'default_meeting_point' => env('DEFAULT_MEETING_POINT', null),
    // Name des Primary Discords
    'primary_guild_name' => env('PRIMARY_GUILD_NAME', null),
    // Zeit in Minuten bevor die Ausbildung beginnt: Ob sich noch angemeldet werden darf
    'enroll_deadline' => env('ENROLL_DEADLINE', 10),
    // Zeit in Minuten bevor die Ausbildung beginnt ob diese schon abgeschlossen werden darf
    'training_complete_minutes' => (int) env('TRAINING_COMPLETE_MINUTES', 10),

    'certificates' => [
        'organisation_name' => env('PDF_CERTIFICATE_ORGANISATION_NAME', env('APP_NAME')),
        'sub_name' => env('PDF_CERTIFICATE_SUB_NAME'),
    ],
    'pdf_sign' => [
        // Pfad zur Jsign java
        'jsign_path' => env('PDF_SIGN_JSIGNPDF_PATH'),
        // P12 Key Name im Ordner /storage/private/
        'p12_key_name' => env('P12_KEY_NAME'),
        // Password des P12 keys
        'p12_password' => env('P12_PASSWORD'),
    ]

];
