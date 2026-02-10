<?php

namespace Amplify\System\Sayt\Facade;

use Amplify\System\Sayt\Classes\CategoriesInfo;
use Amplify\System\Sayt\Classes\RemoteResults;
use Illuminate\Support\Facades\Cache;

/**
 * @method static clearCache()
 * @method static storeSearch($site_search = null)
 * @method static RemoteResults storeProducts(?string $seoPath = null, $paginate_per_page = 10, $CA_BreadcrumbClick = false, $pageType = null)
 * @method static storeProductDetail(mixed $identifier, ?string $seoPath = null, array $options = [])
 * @method static CategoriesInfo storeCategories(?string $seoPath = null, array $options = [])
 * @method static getSubCategoriesByCategory($category_name)
 * @method static string getDefaultCatPath()
 * @method static RemoteResults marchProducts($site_search, $paginatePerPage = 10)
 */
class Sayt extends \Illuminate\Support\Facades\Facade
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

        if ($seoPath == 'search') {
            $seoPath = null;
        }

        $options = \request()->all();


        return \Sayt::storeProducts($seoPath, $options);
    }

    public static function getCategory(): CategoriesInfo
    {
        $seoPath = \request()->route('query', Sayt::getDefaultCatPath());

        if ($query = request()->query('q')) {
            $seoPath = "{$seoPath}/-{$query}";
        }

        $options = \request()->all();

        $options['with_sub_category'] = true;

        return Cache::remember("categories-{$seoPath}", DAY, function () use ($seoPath, $options) {
            return \Sayt::storeCategories($seoPath, $options);
        });
    }
}
