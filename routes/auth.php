<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'alerts'])
    ->group(function () {

        Route::get('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])
            ->name('logout');

        Route::prefix('discord')
            ->name('discord.')
            ->group(function () {
                Route::get('link', [\App\Http\Controllers\Auth\UserController::class, 'linkDiscordAccount'])
                    ->name('link');
            });

        Route::prefix('admin')
            ->name('admin.')
            ->group(function () {
                Route::prefix('rollen')
                    ->name('roles.')
                    ->controller(\App\Http\Controllers\Admin\RolesController::class)
                    ->group(function () {
                        Route::get('', 'index')
                            ->name('index');

                        Route::get('{role}/bearbeiten', 'edit')
                            ->name('edit');

                        Route::post('{role}/update', 'update')
                            ->name('update');

                        Route::post('store', 'store')
                            ->name('store');
                    });
            });

        Route::prefix('ausbildung')
            ->name('training.')
            ->controller(\App\Http\Controllers\TrainingController::class)
            ->group(function () {

                Route::post('{training}/register', 'register')
                    ->where('training', '[0-9]+')
                    ->name('register');

                Route::post('{training}/sign_out', 'signOut')
                    ->where('ausbildung', '[0-9]+')
                    ->name('sign_out');
            });

        Route::prefix('profil/{user}')
            ->name('profile.')
            ->controller(\App\Http\Controllers\ProfileController::class)
            ->group(function () {
                Route::get('', 'show')
                    ->name('show');
            });


        Route::prefix('benutzer')
            ->name('user.')
            ->controller(\App\Http\Controllers\Auth\UserController::class)
            ->group(function () {
                Route::get('einstellungen/{user}', 'show')
                    ->name('show')
                    ->where('user', '[0-9]+');

                Route::post('einstellungen/store/{user}', 'store')
                    ->name('store')
                    ->where('user', '[0-9]+');
            });

        Route::prefix('benutzerverwaltung')
            ->name('usermanagement.')
            ->middleware('can:usermanagement.index')
            ->controller(\App\Http\Controllers\Admin\UserManagementController::class)
            ->group(function () {
                Route::match(['GET', 'POST'], '', 'index')
                    ->name('index');
                Route::post('store', 'store')
                    ->name('store');
                Route::get('edit/{user}', 'edit')
                    ->name('edit')
                    ->where('user', '[0-9]+');
                Route::post('update/{user}', 'update')
                    ->name('update')
                    ->where('user', '[0-9]+');
                Route::post('destroy/{user}', 'destroy')
                    ->name('destroy')
                    ->where('user', '[0-9]+');
            });
        Route::prefix('ausbildungen')
            ->name('trainings.')
            ->group(function () {
                Route::controller(\App\Http\Controllers\TrainingController::class)
                    ->group(function () {
                        Route::post('ban/{user}', 'ban')
                            ->name('ban');
                        Route::post('request', 'request')
                            ->name('request');
                    });

                Route::controller(\App\Http\Controllers\TrainingRequestController::class)
                    ->name('request.')
                    ->group(function () {
                        Route::get('wuensche', 'index')
                            ->name('index');
                    });
            });
        Route::prefix('qualifikation')
            ->name('qualifications.')
            ->controller(\App\Http\Controllers\QualificationsController::class)
            ->group(function () {
                Route::post('zuweisen/{user}', 'assign')
                    ->name('user.assign');
            });

        Route::prefix('administration')
            ->name('administration.')
            ->group(function () {
                Route::prefix('fractions')
                    ->name('fractions.')
                    ->controller(\App\Http\Controllers\Administration\FractionsController::class)
                    ->group(function () {
                        Route::get('', 'index')
                            ->name('index');

                        Route::post('store', 'store')
                            ->name('store');

                        Route::get('{fraction}/bearbeiten', 'edit')
                            ->name('edit');

                        Route::post('{fraction}/update', 'update')
                            ->name('update');

                        Route::post('{fraction}/destroy', 'destroy')
                            ->name('destroy');
                    });
                Route::prefix('qualifications')
                    ->name('qualifications.')
                    ->controller(\App\Http\Controllers\Administration\QualificationsController::class)
                    ->group(function () {
                        Route::get('', 'index')
                            ->name('index');
                        Route::post('store', 'store')
                            ->name('store');
                        Route::get('{qualification}/bearbeiten', 'edit')
                            ->name('edit');
                        Route::post('{qualification}/update', 'update')
                            ->name('update');
                        Route::post('{qualification}/destroy', 'destroy')
                            ->name('destroy');
                        Route::post('{qualification}/toggle/hide', 'toggleHide')
                            ->name('toggle_hide');
                    });
                Route::prefix('requirements/{qualification}')
                    ->name('requirements.')
                    ->controller(\App\Http\Controllers\Administration\RequirementsController::class)
                    ->group(function () {
                        Route::get('', 'show')
                            ->name('show');
                        Route::post('store', 'store')
                            ->name('store');
                        Route::get('{requirement}/bearbeiten', 'edit')
                            ->name('edit');
                        Route::post('{requirement}/update', 'update')
                            ->name('update');
                        Route::post('{requirement}/destroy', 'destroy')
                            ->name('destroy');
                    });
            });

        Route::prefix('dokumente')
            ->name('documents.')
            ->controller(\App\Http\Controllers\DocumentsController::class)
            ->group(function () {
                Route::match(['GET', 'POST'], '', 'index')
                    ->name('index');
                Route::get('anzeigen/{document}', 'show')
                    ->name('show');

                Route::post('store', 'store')
                    ->name('store');

                Route::get('edit/{document}', 'edit')
                    ->name('edit');

                Route::post('update/{document}', 'update')
                    ->name('update');

                Route::post('destroy/{document}', 'destroy')
                    ->name('destroy');
            });
    });
