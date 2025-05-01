<?php

// INavigateNode types
use Amplify\System\Sayt\Classes\BreadCrumbTrail;

define('NODE_TYPE_CATEGORY', 1);
define('NODE_TYPE_ATTRIBUTE', 2);
define('NODE_TYPE_USER_SEARCH', 3);

// INavigateResults parameters
define('CATEGORY_DISPLAY_MODE_FULL', 0);
define('CATEGORY_DISPLAY_MODE_INITIAL', 1);

define('ATTR_DISPLAY_MODE_FULL', 0);
define('ATTR_DISPLAY_MODE_INITIAL', 1);
define('ATTR_FILTER_NORMAL', 1);
define('ATTR_FILTER_MERCHANDISER', 2);
define('ATTR_FILTER_ALL', (1 | 2));

// INavigateAttribute node types
define('ATTR_TYPE_NORMAL', 11);
define('ATTR_TYPE_MERCHANDISER', 21);

if (! function_exists('eaBreadcrumbs')) {
    function eaBreadcrumbs($variables = []): array
    {
        try {
            if (isset($variables['breadcrumbTrail'])) {
                $breadcrumbTrails = $variables['breadcrumbTrail'];
            } elseif (isset($variables['easyAskData']['breadcrumbTrail'])) {
                $breadcrumbTrails = $variables['easyAskData']['breadcrumbTrail'];
            } else {
                $breadcrumbTrails = new BreadCrumbTrail(null);
            }

            return $breadcrumbTrails->getSearchPath();
        } catch (\Exception $exception) {
            return [];
        }
    }
}

if (! function_exists('easyask')) {
    function easyask() {}
}

if (! function_exists('eaShopConfig')) {
    function eaShopConfig()
    {
        $data['topnavCategories'] = [];
        $data['homePageBrands'] = [];
        $data['HPTopCategories'] = [];
        $data['options']['listSeparatorChar'] = '|';
        $data['ProductDetailSearch']['Fieldname'] = config('amplify.search.product_search_by_id_prefix');
        $data['connection']['host'] = config('amplify.search.easyask_host');
        $data['connection']['port'] = config('amplify.search.easyask_port');
        $data['connection']['dxp'] = config('amplify.search.easyask_dictionary');

        return json_decode(json_encode($data));
    }
}

if (! function_exists('eaDefaultCategories')) {
    function eaDefaultCategories($requestFromModule): array
    {
        $categories = \App\Models\Category::query()
            ->whereNull('parent_id')
            ->withCount('products')
            ->get();

        return $categories->map(function ($category) {
            return [
                'ids' => $category->id,
                'name' => $category->category_name,
                'nodeString' => $category->category_code,
                'productCount' => $category->products_count,
            ];
        })->toArray();
    }
}
