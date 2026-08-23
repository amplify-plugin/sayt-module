<?php

namespace Amplify\System\Sayt\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SaytSuggestionController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function __invoke(string $keyword): JsonResponse
    {
        /**
         * https://example.com/sayt/search?keyword=snap
        */
        return response()->json([
            'success' => true,
            'html' => \view('sayt::site-search.suggestion', [
                'keyword' => $keyword
            ])->render(),
        ]);
    }
}
