<?php

namespace Amplify\System\Sayt\Facade;

use Amplify\System\Sayt\Classes\RemoteResults;
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
 * @method static RemoteResults storeProducts($seopath, $paginate_per_page = 10, $CA_BreadcrumbClick = false, $pageType = null)
 * @method static storeProductDetail(mixed $identifier, ?string $seoPath = null, array $options = [])
 * @method static getProductById($productID)
 * @method static storeCategories()
 * @method static getCategory()
 * @method static getSubCategoriesByCategory($category_name)
 * @method static RemoteResults marchProducts($site_search, $paginatePerPage = 10)
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

    public static function search(): RemoteResults
    {
        $seoPath = \request()->route('query');

        $options = \request()->all();

        return \Sayt::storeProducts($seoPath, $options);
    }
}
