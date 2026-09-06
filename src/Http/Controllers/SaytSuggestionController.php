<?php

namespace Amplify\System\Sayt\Http\Controllers;

use Amplify\System\Sayt\Classes\CategoriesInfo;
use Amplify\System\Sayt\Classes\RemoteResults;
use Amplify\System\Sayt\Facade\Sayt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SaytSuggestionController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function __invoke(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'keyword' => ['required', 'string', 'min:3', 'max:32'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->first(),
            ], 500);
        }

        $keyword = $request->input('keyword');

        $suggestions = Sayt::storeSuggestion($keyword);

        $eaResult = new RemoteResults();

        $categories = [];

        if ($suggestions->hasSuggestion()) {
            $eaResult = $this->studioCall($keyword);
            $eaCategories = $eaResult->getCategories();
            $categories = $eaCategories->getCategories($eaCategories->initialCategoriesExists() ? 1 : 0);
        }

        return response()->json([
            'success' => true,
            'html' => \view('sayt::site-search.dropdown', [
                'query' => $keyword,
                'suggestions' => $suggestions,
                'categories' => array_slice($categories, 0, 3),
                'products' => $eaResult->getProducts(),
                'total' => $eaResult->getTotalItems(),
            ])->render(),
        ]);
    }

    private function studioCall(string $keyword)
    {

        return Sayt::storeProducts(options: [
            'q' => $keyword,
            'product_count' => false,
            'limit' => 3
        ]);

        //https://steven.prod.easyaskondemand.com/EasyAsk/apps/Advisor.jsp?callback=jQuery32107775214889399938_1787514987375&disp=json&oneshot=1&ie=UTF-8&dct=steven.dxp&indexed=1&ResultsPerPage=3&defsortcols=&subcategories=false&rootprods=false&navigatehierarchy=false&returnskus=false&defarrangeby=%2F%2F%2F%2FNONE%2F%2F%2F%2F&eap_GroupID=&eap_CustomerID=public&eap_custNum=1000&customer=easayt&eap_custShipTo=&eap_altWhsIds=8%3B16%3B17%3B19&eap_loginId=&avail=&subcategoryDepth=1&includeCategoryCounts=false&RequestAction=advisor&CatPath=Catalog%2F-amplify-id-%3E-0&RequestData=CA_Search&q=snap&_=1787514987380

    }

    /**
     * @throws \ErrorException
     */
    private function suggestionCall(string $keyword)
    {
        dd();
    }


}
