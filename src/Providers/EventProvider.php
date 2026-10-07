<?php

namespace Amplify\System\Sayt\Providers;

use Amplify\System\Sayt\Listeners\ClearSaytCatalogListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \Amplify\Frontend\Events\ContactLoggedIn::class => [
            ClearSaytCatalogListener::class,
        ],
        \Amplify\Frontend\Events\ContactLoggedOut::class => [
            ClearSaytCatalogListener::class,
        ],
    ];
}
