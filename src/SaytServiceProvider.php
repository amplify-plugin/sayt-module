<?php

namespace Amplify\System\Sayt;

use Amplify\System\Sayt\Commands\ReconfigureSaytSearchCommand;
use Amplify\System\Sayt\Controllers\SearchProductController;
use Illuminate\Routing\Router;
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
            __DIR__ . '/../public' => public_path('vendor/easyask-sayt'),
        ], 'sayt-asset');

        $this->publishes([
            __DIR__ . '/../public/js/templates' => public_path('assets/sayt-templates'),
        ], 'sayt-templates');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sayt');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/amplify/sayt'),
        ], 'sayt-view');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ReconfigureSaytSearchCommand::class,
            ]);
        }

        /* @var Router $router */
        $router = $this->app['router'];
        $router->get('sayt/easyask/search/{query?}', SearchProductController::class)
            ->middleware('web')
        ->name('sayt.search');
    }
}
