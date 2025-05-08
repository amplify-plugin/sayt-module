<?php

namespace Amplify\System\Sayt;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Amplify\System\Sayt\Facade\Sayt;

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

        $this->mergeConfigFrom(__DIR__.'/../config/sayt.php', 'sayt');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        AliasLoader::getInstance()->alias('Sayt', Sayt::class);

        //        $this->loadViewsFrom(__DIR__.'/easyask', 'easyask');
    }
}
