<?php

namespace Modules\Events\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class EventsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }
    
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/events-routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'events');
    }
}
