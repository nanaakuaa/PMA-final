<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\PasswordCreated;
use App\Events\PasswordUpdated;
use App\Events\PasswordDeleted;
use App\Listeners\NotifyPasswordCreated;
use App\Listeners\NotifyPasswordUpdated;
use App\Listeners\NotifyPasswordDeleted;

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
        // Register event listeners
        $this->app['events']->listen(
            PasswordCreated::class,
            NotifyPasswordCreated::class
        );

        $this->app['events']->listen(
            PasswordUpdated::class,
            NotifyPasswordUpdated::class
        );

        $this->app['events']->listen(
            PasswordDeleted::class,
            NotifyPasswordDeleted::class
        );
    }
}

