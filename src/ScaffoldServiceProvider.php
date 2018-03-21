<?php

namespace Yish\Scaffold;

use Illuminate\Support\ServiceProvider;

class ScaffoldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/scaffold.php' => config_path('scaffold.php'),
        ], 'config');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.scaffold.make', function ($app) {
            $config = $app['config']->get('scaffold');
            return new ScaffoldMakeCommand($config);
        });

        $this->commands([
           'command.scaffold.make'
        ]);
    }
}
