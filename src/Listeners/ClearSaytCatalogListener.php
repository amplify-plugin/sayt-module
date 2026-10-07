<?php

namespace Amplify\System\Sayt\Listeners;

use Amplify\Frontend\Events\ContactLoggedIn;
use Amplify\Frontend\Events\ContactLoggedOut;

class ClearSaytCatalogListener
{
    /**
     * Handle the event.
     */
    public function handle(ContactLoggedIn|ContactLoggedOut $event): void
    {
            $key = config('amplify.sayt.catalog_cache_key', 'site_catalog');
            
            request()->session()->forget($key);
    }
}
