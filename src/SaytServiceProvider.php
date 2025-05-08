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
        $this->app->singleton(Sayt::class, function ($app) {
            return new Sayt($app['request'], $app);
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
