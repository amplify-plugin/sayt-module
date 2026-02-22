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
define('PRODUCT_IN_STOCK_CHEKCED', 'InStock:In-Stock');

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
        $data['ProductDetailSearch']['Fieldname'] = config('amplify.sayt.product_search_by_id_prefix');
        $data['connection']['host'] = config('amplify.sayt.dictionary.host');
        $data['connection']['port'] = config('amplify.sayt.dictionary.port');
        $data['connection']['dxp'] = config('amplify.sayt.dictionary.dictionary');

        return json_decode(json_encode($data));
    }
}


if (!function_exists('eaResultSortBy')) {
    function eaResultSortBy(): array
    {
        return [
            'Relevance' => 'Relevance',
            'Product Code - ASC' => 'Product code A-Z',
            'Product Code - DESC' => 'Product code Z-A',
            'Manufacturer - ASC' => 'Brand A-Z',
            'Manufacturer - DESC' => 'Brand Z-A',
        ];
    }
}

if (!function_exists('results_per_page')) {
    function results_per_page(array $options = []): int
    {
        if (! empty($options['per_page'])) {
            return $options['per_page'];
        }

        return request()->filled('per_page')
            ? request('per_page', getPaginationLengths()[0])
            : request()->cookie('resultsPerPage', getPaginationLengths()[0]);
    }
}
