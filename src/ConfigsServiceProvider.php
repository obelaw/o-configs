<?php

namespace Obelaw\Configs;

use Illuminate\Support\ServiceProvider;
use Obelaw\Configs\Schema\Fields\Text;
use Obelaw\Configs\Schema\Section;

class ConfigsServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/o-configs.php', 'o-configs');

        // Scoped: each request / queued job reads fresh values, so a long-running
        // worker never keeps the values of the request (or tenant) before it.
        $this->app->scoped('obelaw.o.configs', fn ($app) => new ConfigsManager($app));
        $this->app->alias('obelaw.o.configs', ConfigsManager::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'obelaw-configs');

        $this->publishes([
            __DIR__ . '/../config/o-configs.php' => config_path('o-configs.php'),
        ], 'o-configs-config');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        Section::make(
            name: 'Select Status',
            description: 'Select the status of the application',
            schema: function () {
                return [
                    Text::make('name', 'configs.name', 'Name', 'Enter the name'),
                ];
            }
        );
    }
}
