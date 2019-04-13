<?php

namespace DenisKisel\LaravelAdminReadySolution;

use DenisKisel\LaravelAdminReadySolution\Commands\ReadySolutionCommand;
use Illuminate\Support\ServiceProvider;

class LaravelAdminReadySolutionServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'deniskisel');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'deniskisel');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
//        $this->mergeConfigFrom(__DIR__.'/../config/laraveladminreadysolution.php', 'laraveladminreadysolution');

        // Register the service the package provides.
        $this->app->singleton('laraveladminreadysolution', function ($app) {
            return new LaravelAdminReadySolution;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laraveladminreadysolution'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/deniskisel'),
        ], 'laraveladminreadysolution.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/deniskisel'),
        ], 'laraveladminreadysolution.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/deniskisel'),
        ], 'laraveladminreadysolution.views');*/

        // Registering package commands.
         $this->commands([
             ReadySolutionCommand::class
         ]);
    }
}
