<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Premise;
use App\Observers\PremiseObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Premise::observe(PremiseObserver::class);
    }
}
