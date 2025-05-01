<?php

namespace Amplify\System\Sayt;

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
 * @method static storeProducts($seopath, $paginate_per_page = 10, $CA_BreadcrumbClick = false, $pageType = null)
 * @method static storeProductDetail($seopath)
 * @method static getProductById($productID)
 * @method static storeCategories()
 * @method static getCategory()
 * @method static getSubCategoriesByCategory($category_name)
 * @method static marchProducts($site_search, $paginatePerPage = 10)
 */
class SaytFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Sayt::class;
    }
}
