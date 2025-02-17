<?php

namespace Amplify\System\Sayt;

use Illuminate\Foundation\AliasLoader;
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
        $this->app->singleton(Sayt::class, fn () => new Sayt);

        $this->mergeConfigFrom(__DIR__.'/config/sayt.php', 'sayt');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        AliasLoader::getInstance()->alias('Sayt', SaytFacade::class);

        //        $this->loadViewsFrom(__DIR__.'/easyask', 'easyask');
    }
}
