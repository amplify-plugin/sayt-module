<?php

namespace Amplify\System\Sayt;

use Amplify\System\Sayt\Http\Middlewares\SaytInitialized;
use Amplify\System\Sayt\Providers\EventProvider;
use Amplify\System\Sayt\Providers\WidgetProvider;
use Illuminate\Support\Facades\Route;
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
        $this->mergeConfigFrom(__DIR__ . '/../config/sayt.php', 'amplify.sayt');

        $this->app->singleton('eastudio', fn() => new EasyAskStudio);

        $this->app->register(WidgetProvider::class);
        $this->app->register(EventProvider::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/sayt.php' => config_path('amplify/sayt.php'),
        ], 'sayt-config');

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/sayt'),
        ], 'sayt-asset');

        $this->publishes([
            __DIR__ . '/../public/js/templates' => public_path('assets/sayt-templates'),
        ], 'sayt-templates');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sayt');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/amplify/sayt'),
        ], 'sayt-view');

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->registerBackendMenu();

        Route::pushMiddlewareToGroup('frontend', SaytInitialized::class);

    }

    private function registerBackendMenu(): void
    {
        $sidebar = $this->app->make('sidebar');

        $sidebar->group('Settings')
            ->items(function ($catalog) {
                $catalog->item('SAYT')
                    ->can('sayt-setting.list')
                    ->icon('la la-search')
                    ->url(backpack_url('sayt-setting'));
            });
    }
}
