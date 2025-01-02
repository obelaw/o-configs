<?php

namespace Obelaw\Configs;

use Illuminate\Support\ServiceProvider;
use Obelaw\Configs\Services\ConfigurationService;
use Obelaw\Configs\Classes\ConfigsClass;
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
        $this->app->singleton('obelaw.o.configs', ConfigurationService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'obelaw-configs');

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
