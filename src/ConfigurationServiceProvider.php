<?php

namespace Obelaw\Configuration;

use Illuminate\Support\ServiceProvider;
use Obelaw\Configuration\Configurations;

class ConfigurationServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('obelaw.o.configuration', Configurations::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }
}
