<?php

namespace Amplify\System\Sayt;

use Amplify\System\Sayt\Commands\ReconfigureSaytSearchCommand;
use Illuminate\Support\ServiceProvider;

class SaytServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('eastudio', function ($app) {
            return new EasyAskStudio($app['request'], $app);
        });

        $this->mergeConfigFrom(__DIR__.'/../config/sayt.php', 'amplify.sayt');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/sayt.php' => config_path('amplify/sayt.php'),
        ], 'sayt-config');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/easyask-sayt'),
        ], 'sayt-asset');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sayt');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ReconfigureSaytSearchCommand::class,
            ]);
        }
    }
}
