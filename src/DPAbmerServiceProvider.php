<?php

namespace DoublePoint\DPAbmer;

use Illuminate\Support\ServiceProvider;

class DPAbmerServiceProvider extends ServiceProvider
{
    /**
     * The console commands.
     *
     * @var bool
     */
    protected $commands = [
        'DoublePoint\DPAbmer\Console\ABMCreateCommand'
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Cargamos las rutas
        /*if (! $this->app->routesAreCached()) {
            require __DIR__.'/Http/routes.php';
        }*/

        // Cargamos las vistas
        $this->loadViewsFrom(__DIR__.'/resources/views/layouts', 'layouts');
        $this->loadViewsFrom(__DIR__.'/resources/views/install', 'install');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
        //require __DIR__ . '/Http/routes.php';
    }
}