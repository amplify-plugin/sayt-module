<?php

namespace Amplify\System\Sayt\Controllers;

use Amplify\System\Sayt\Facade\Sayt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SearchProductController extends Controller
{
    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): JsonResponse
    {
        $inputs = $request->all();

        $inputs['search'] = $inputs['search'] ?? '1';

        $apiResponse = Sayt::storeProducts(null, $inputs);

        $page = $request->integer('page', 1);

        $nextPage = ($page != $apiResponse->getPageCount())
            ? $page + 1
            : null;

        $prevPage = ($page >= 2)
            ? $page - 1
            : null;

        $response = [
            'current_page' => $apiResponse->getCurrentPage(),
            'data' => $apiResponse->getProducts(),
            'first_page_url' => $request->fullUrlWithoutQuery('page'),
            'from' => $apiResponse->getCurrentPage(),
            'next_page_url' => $nextPage != null
                ? $request->fullUrlWithQuery(['page' => $nextPage])
                : null,
            'path' => $request->url(),
            'per_page' => $apiResponse->getResultsPerPage(),
            'prev_page_url' => $prevPage != null
                ? $request->fullUrlWithQuery(['page' => $prevPage])
                : null,
            'to' => $apiResponse->getLastItem(),
        ];

        return response()->json($response);
    }
}
