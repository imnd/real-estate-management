<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthenticatedSessionController,
    PremiseController,
    StatisticsController
};

Route::prefix('v1')
    ->name('api.')
    ->group(function () {
        Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login');

        Route::middleware('auth:sanctum')
            ->group(function () {
                Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

                Route::prefix('premises')
                    ->name('premises.')
                    ->controller(PremiseController::class)
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::post('/', 'store')->name('store');
                        Route::get('{premise}', 'show')->name('show');
                        Route::put('{premise}', 'update')->name('update');
                        Route::delete('{premise}', 'destroy')->name('destroy');
                    });

                Route::prefix('statistics')
                    ->name('statistics.')
                    ->controller(StatisticsController::class)
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('complexes/{complex}', 'complex')->name('complex');
                        Route::get('buildings/{building}', 'building')->name('building');
                        Route::get('sections/{section}', 'section')->name('section');
                        Route::get('floors/{floor}', 'floor')->name('floor');
                    });
            });
    });
