<?php

use Illuminate\Support\Facades\Route;

Route::resource('', \App\Http\Controllers\HomeController::class)
    ->only(['index'])
    ->names(['index' => 'home']);

Route::match(['POST', 'GET'], 'discord/callback', [\App\Http\Controllers\DiscordController::class, 'callback'])
    ->name('discord.callback');

Route::get('discord/auth', [\App\Http\Controllers\Auth\LoginController::class, 'loginDiscord'])
    ->name('discord.auth');

Route::prefix('login')
    ->name('login')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Auth\LoginController::class, 'index'])
            ->name('.index');
        Route::post('/', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
    });

Route::prefix('register')
    ->name('register.')
    ->controller(\App\Http\Controllers\Auth\RegisterController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');
        Route::post('/', 'store')
            ->name('store');
    });

Route::post('announcement/store', [\App\Http\Controllers\AnnouncementController::class, 'store'])
    ->name('announcement.store');

Route::prefix('ausbilder')
    ->name('ausbilder.')
    ->controller(\App\Http\Controllers\Ausbilder\OverviewController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');
    });

Route::prefix('ausbildung')
    ->name('ausbildung.')
    ->controller(\App\Http\Controllers\TrainingController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('{training}', 'show')
            ->name('show');

        Route::post('store', 'store')
            ->name('store');

        Route::post('abschliessen/{training}', 'complete')
            ->name('abschliessen');

        Route::post('update/{training}', 'update')
            ->name('update');

        Route::post('teilnehmer/entfernen/{participant}', 'removeParticipant')
            ->name('teilnehmer.entfernen');

        Route::post('teilnehmer/hinzufuegen/{training}', 'addParticipants')
            ->name('teilnehmer.hinzufuegen');

        Route::post('delete/{training}', 'destroy')
            ->name('delete');
    });

Route::prefix('voraussetzungen')
    ->name('requirements.')
    ->controller(\App\Http\Controllers\RequirementsController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');
    });
