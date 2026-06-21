<?php

namespace App\Providers;

use App\Models\Reserva;
use App\Observers\ReservaObserver;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

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
        Vite::prefetch(concurrency: 3);
        Schema::defaultStringLength(191);
        Reserva::observe(ReservaObserver::class);
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
