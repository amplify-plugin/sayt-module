<?php

namespace Amplify\System\Sayt;

use Amplify\System\Sayt\Commands\ReconfigureSaytSearchCommand;
use Amplify\System\Sayt\Controllers\SearchProductController;
use Amplify\System\Sayt\Widgets\ShopAttributeFilter;
use Amplify\System\Sayt\Widgets\ShopCategories;
use Amplify\System\Sayt\Widgets\ShopCurrentFilter;
use Amplify\System\Sayt\Widgets\ShopEmptyResult;
use Amplify\System\Sayt\Widgets\ShopInStockFilter;
use Amplify\System\Sayt\Widgets\SiteSearch;
use Amplify\System\Sayt\Widgets\ShopPagination;
use Amplify\System\Sayt\Widgets\ShopSearchInResult;
use Amplify\System\Sayt\Widgets\ShopSidebar;
use Amplify\System\Sayt\Widgets\ShopToolbar;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

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

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sayt');

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
