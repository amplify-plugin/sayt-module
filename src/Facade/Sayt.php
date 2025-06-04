<?php

namespace Amplify\System\Sayt\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static clearCache()
 * @method static getEASetup()
 * @method static storeHome()
 * @method static storeLandingPage($lpname)
 * @method static storeSpeech()
 * @method static storeSearch($site_search = null)
 * @method static storeConversation($seopath)
 * @method static storeNewConversation()
 * @method static getEaProductsData()
 * @method static getEaProductDetail()
 * @method static array storeProducts($seopath, $paginate_per_page = 10, $CA_BreadcrumbClick = false, $pageType = null)
 * @method static storeProductDetail($seopath)
 * @method static getProductById($productID)
 * @method static storeCategories()
 * @method static getCategory()
 * @method static getSubCategoriesByCategory($category_name)
 * @method static marchProducts($site_search, $paginatePerPage = 10)
 */
class Sayt extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'eastudio';
    }

    public static function search()
    {
        $seoPath = \request()->route('query');

        if (empty($seoPath) || $seoPath == 'search') {

            if (\config('amplify.search.default_catalog')) {

                $catalog = \App\Models\Catalog::find(\config('amplify.search.default_catalog'));

                $productRestriction = $catalog->name;

                $seoPath = "{$catalog->name}/{$productRestriction}";
            }
        }

        $options = \request()->all();

        return \Sayt::storeProducts($seoPath, $options);
    }
}
