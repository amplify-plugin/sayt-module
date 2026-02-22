<?php

namespace Amplify\System\Sayt;

use Amplify\System\Backend\Models\Category;
use Amplify\System\Sayt\Classes\CategoriesInfo;
use Amplify\System\Sayt\Classes\RemoteEasyAsk;
use Amplify\System\Sayt\Classes\RemoteFactory;
use Amplify\System\Sayt\Classes\RemoteResults;
use Illuminate\Support\Facades\Cache;

/**
 * @class EasyAskPageService
 */
class EasyAskStudio
{
    private RemoteEasyAsk $easyAsk;

    /**
     * EasyAsk Studio Constructor
     *
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        $host = config('amplify.sayt.dictionary.host');
        $port = config('amplify.sayt.dictionary.port', 80);
        $dictionary = config('amplify.sayt.dictionary.dictionary');
        $protocol = config('amplify.sayt.dictionary.protocol');

        if ($host == '' || $dictionary == '') {
            throw new \InvalidArgumentException('To use EasyAsk search engine you need to specify the host name and dictionary in system configuration');
        }

        $this->easyAsk = RemoteFactory::create($host, $dictionary, $port, $protocol);
    }

    /**
     * @throws \Exception
     */
    public function storeProducts(?string $seoPath = null, array $options = []): RemoteResults
    {
        $resultPerPage = results_per_page($options);
        $currentPage = $options['page'] ?? null;
        $returnSku = $options['return_skus'] ?? false;
        $groupBy = $options['group_by'] ?? null;
        $sortBy = $options['sort_by'] ?? null;
        $attribute = $options['attribute'] ?? null;
        $search = $options['q'] ?? null;
        $inStock = $options['stock'] ?? null;

        // Get the Options object and set the appropriate options
        $eaOptions = $this->easyAsk->getOptions()
            ->setResultsPerPage($resultPerPage)
            ->setReturnSKUs($returnSku)
            ->setCurrentPage($currentPage)
            ->setGroupId($groupBy)
            ->setSortOrder($sortBy)
            ->setStockAvail($inStock);

        $this->easyAsk->setOptions($eaOptions);

        match (true) {
            $currentPage != null => $this->easyAsk->userGoToPage($seoPath, $currentPage),
            $attribute != null => $this->easyAsk->userAttributeClick($seoPath, $attribute),
            $search != null => $this->easyAsk->userSearch($seoPath, $search),
            default => $this->easyAsk->userBreadCrumbClick($seoPath)
        };

        return $this->easyAsk->urlPost();
    }

    /**
     * @throws \Exception
     */
    public function storeProductDetail(mixed $identifier, ?string $seoPath = null, array $options = []): RemoteResults
    {
        $resultPerPage = 1;
        $returnSku = $options['return_skus'] ?? false;
        $sortBy = $options['sort_by'] ?? null;

        $productSearch = trim(config('amplify.sayt.product_search_by_id_prefix'));
        $seoPath = trim("{$seoPath}/-{$productSearch}={$identifier}", '/');

        // Get the Options object and set the appropriate options
        $eaOptions = $this->easyAsk->getOptions()
            ->setResultsPerPage($resultPerPage)
            ->setReturnSKUs($returnSku)
            ->setSortOrder($sortBy);

        $this->easyAsk->setOptions($eaOptions);

        $this->easyAsk->userBreadCrumbClick($seoPath);

        return $this->easyAsk->urlPost();
    }

    /**
     * Return all the featured products on a particular merchandise zone on EasyAsk
     *
     * @throws \Exception
     */
    public function marchProducts($zoneKey = null, array $options = []): RemoteResults
    {
        $resultPerPage = $options['per_page'] ?? getPaginationLengths()[0] ?? 12;
        $sortBy = $options['sort_by'] ?? null;

        $seoPath = "$\$Merchandising:{$zoneKey}";

        $eaOptions = $this->easyAsk->getOptions()
            ->setResultsPerPage($resultPerPage)
            ->setSortOrder($sortBy);

        $this->easyAsk->setOptions($eaOptions);

        $this->easyAsk->userBreadCrumbClick($seoPath);

        return $this->easyAsk->urlPost();
    }

    /**
     * @throws \Exception
     */
    public function storeCategories(?string $seoPath = null, array $options = []): CategoriesInfo
    {
        Cache::remember('site-db-categories', DAY, function () {
            return Category::all()->toArray();
        });

        $sortBy = $options['sort_by'] ?? null;

        $withSubCategory = $options['with_sub_category'] ?? false;
        $subCategoryDepth = $options['sub_category_depth'] ?? 1;
        $productCount = $options['product_count'] ?? false;

        $eaOptions = $this->easyAsk->getOptions()
            ->setResultsPerPage(1)
            ->setSortOrder($sortBy)
            ->setSubCategories($withSubCategory)
            ->setSubCategoryDepth($subCategoryDepth)
            ->setIncludeProductCount($productCount);

        $this->easyAsk->setOptions($eaOptions);

        $this->easyAsk->userBreadCrumbClick($seoPath);

        return $this->easyAsk->urlPost()->getCategories();
    }

    public function getDefaultCatPath(): string
    {
        $catalog = null;

        $productRestriction = null;

        /**
         * @var string $catalog
         */

        $catalog = Cache::rememberForever('site-default-catalog', function () {
            $catalog = \Amplify\System\Backend\Models\Category::find(\config('amplify.sayt.default_catalog'));
            return $catalog->category_name;
        });

        if ($catalog == null) {
            throw new \InvalidArgumentException('Default catalog is not configured.');
        }

        //handle by attribute
        if (config('amplify.sayt.use_product_restriction')) {

//            $productRestriction = "(InCompany 1 ea_or GLOBAL_flag = 'true') (((InWarehouse = " . $this->getOptions()->getCurrentWarehouse() . ' ea_or ' . implode(' ea_or ', explode(',', $this->options()->getAlternativeWarehouseIds())) . '))  ea_or NonStock <> 0 )';
//
//            if ($this->m_options->getStockAvail() === 1) {
//                $productRestriction .= ' (Avail = 1)';
//            }
//
//            $productRestriction .='(Amplify Id > 0)';

            $productRestriction .= '-amplify-id->-0';
        }

        $catPathPrefix = "{$catalog}/" . (!empty($productRestriction) ? $productRestriction : $catalog);

        return str_replace([' '], ['-'], $catPathPrefix);
    }

    public function getBaseUrl()
    {
        return $this->easyAsk->formBaseURL();
    }

    public function getSaytUrl()
    {
        $options = $this->easyAsk
            ->getOptions()
            ->setGrouping('////NONE////');

        $this->easyAsk->setOptions($options);

        $this->easyAsk->userSearch($this->getDefaultCatPath(), '');

        return $this->easyAsk->getUrl();
    }

}
