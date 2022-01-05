<?php

namespace SaeedVaziry\Monitoring;

use Illuminate\Support\ServiceProvider;
use SaeedVaziry\Monitoring\Commands\RecordCommand;

class MonitoringServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // facade
        $this->app->bind('monitoring', function () {
            return new Monitoring();
        });

        // merge config file
        $this->mergeConfigFrom(__DIR__ . '/../config/monitoring.php', 'monitoring');

        // register command
        $this->app->singleton('command.monitoring.record', function () {
            return new RecordCommand();
        });

        $this->publishes([
            __DIR__ . '/../config/monitoring.php' => config_path('monitoring.php')
        ], 'monitoring-config');

        $this->publishes([
            __DIR__ . '/../migrations/' => database_path('migrations')
        ], 'monitoring-migrations');

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/monitoring'),
        ], ['monitoring-assets', 'laravel-assets']);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // load migrations
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../migrations/');
        }

        // register command
        $this->commands(RecordCommand::class);

        // register routes
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');

        // register views
        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'monitoring');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.monitoring.record',
        ];
    }
}
