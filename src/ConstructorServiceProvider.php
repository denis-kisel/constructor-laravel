<?php

namespace DenisKisel\Constructor;

use DenisKisel\Constructor\Commands\AdminCommand;
use DenisKisel\Constructor\Commands\AdminPageCommand;
use DenisKisel\Constructor\Commands\AdminTranslationCommand;
use DenisKisel\Constructor\Commands\CategoryProductCommand;
use DenisKisel\Constructor\Commands\CustomCommand;
use DenisKisel\Constructor\Commands\CustomTranslationCommand;
use DenisKisel\Constructor\Commands\InstallAdminLocaleCommand;
use DenisKisel\Constructor\Commands\InstallImageCommand;
use DenisKisel\Constructor\Commands\InstallLocaleCommand;
use DenisKisel\Constructor\Commands\ModelCommand;
use DenisKisel\Constructor\Commands\ModelTranslationCommand;
use DenisKisel\Constructor\Commands\PageCommand;
use DenisKisel\Constructor\Commands\PageTranslationCommand;
use DenisKisel\Constructor\Commands\PaymentCommand;
use Illuminate\Support\ServiceProvider;

class ConstructorServiceProvider extends ServiceProvider
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
//        $this->mergeConfigFrom(__DIR__.'/../config/constructor.php', 'constructor');

        // Register the service the package provides.
        $this->app->singleton('constructor', function ($app) {
            return new Constructor;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['constructor'];
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
        ], 'constructor.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/deniskisel'),
        ], 'constructor.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/deniskisel'),
        ], 'constructor.views');*/

        $this->publishes([
            __DIR__ . '/../resources/example/' => __DIR__ . '/../../../../app/Admin/Controllers/Widgets/',
        ]);

        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/image.php' => config_path('image.php'),
        ], 'image.config');

        // Registering package commands.
         $this->commands([
             PageCommand::class,
             PageTranslationCommand::class,
             AdminPageCommand::class,
             CategoryProductCommand::class,
             ModelCommand::class,
             ModelTranslationCommand::class,
             AdminCommand::class,
             AdminTranslationCommand::class,
             InstallLocaleCommand::class,
             InstallAdminLocaleCommand::class,
<<<<<<< HEAD
             InstallImageCommand::class,
=======
             PaymentCommand::class,
>>>>>>> c79c413dd29e55d5eeedec22f269b696d05b5f8e
         ]);
    }
}
