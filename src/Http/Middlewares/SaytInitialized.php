<?php

namespace Amplify\System\Sayt\Http\Middlewares;

use Amplify\System\Sayt\Facade\Sayt;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SaytInitialized
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = config('amplify.sayt.catalog_cache_key', 'site_catalog');

        if (!$request->session()->has($key)) {
            $request->session()->put($key, Sayt::getCurrentCatalog());
        }

        return $next($request);
    }
}
