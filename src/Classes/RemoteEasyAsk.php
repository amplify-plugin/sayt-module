<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\IRemoteEasyAsk;
use Spatie\Url\Url;

// The Easy Ask Session
class RemoteEasyAsk implements IRemoteEasyAsk
{
    // connection info
    private $m_sHostName = '';

    private $m_nPort = -1;

    private $m_sProtocol = 'http';

    private $m_sRootUri = 'EasyAsk/apps/Advisor.jsp';

    private $m_options = null;

    private ?Url $url = null;

    // Creates the EasyAsk instance.
    public function __construct($host, $port, $dictionary, $protocol)
    {
        $this->m_sHostName = $host;
        $this->m_sProtocol = $protocol;
        $this->m_nPort = $port;
        $this->m_options = new Options($dictionary);
    }

    public function options()
    {
        return $this->m_options;
    }

    // Creates the generic URL for the website.
    private function formBaseURL()
    {
        $this->url = Url::fromString("{$this->m_sHostName}/{$this->m_sRootUri}")
            ->withAllowedSchemes(['http', 'https'])
            ->withScheme($this->m_sProtocol)
            ->withQueryParameters([
                'disp' => 'json',
                'oneshot' => 1,
                'dct' => $this->m_options->getDictionary(),
                'indexed' => 1,
                'ResultsPerPage' => $this->m_options->getResultsPerPage(),
                'defsortcols' => $this->m_options->getSortOrder(),
                'subcategories' => $this->m_options->getSubCategories(),
                'rootprods' => $this->m_options->getToplevelProducts(),
                'navigatehierarchy' => $this->m_options->getNavigateHierarchy(),
                'returnskus' => $this->m_options->getReturnSKUs(),
                'defarrangeby' => $this->m_options->getGrouping(),
                'eap_GroupID' => $this->m_options->getGroupId(),
                'eap_CustomerID' => $this->m_options->getCustomerId(),
                'eap_custNum' => $this->m_options->getCustomerId(),
                'eap_custShipTo' => $this->m_options->getCustomerShipTo(),
                'eap_curWhsId' => $this->m_options->getCurrentWarehouse(),
                'eap_altWhsIds' => $this->m_options->getAlternativeWarehouseIds(),
                'eap_loginId' => $this->m_options->getLoginId(),
                'avail' => $this->m_options->getStockAvail(),
                //                'customer' => $this->m_options->getCustomer(),
            ]);

        if ($this->m_nPort != 0) {
            $this->url = $this->url->withPort($this->m_nPort);
        }

        return $this->url;
    }

    // User performs a search. Creates a URL based off of the search and then creates a RemoteResults and
    // loads the URL into it.
    public function userSearch($path, $question): void
    {
        $this->url = $this->formBaseURL()->withQueryParameters([
            'RequestAction' => 'advisor',
            'CatPath' => $path,
            'RequestData' => 'CA_Search',
            'q' => $question,
        ]);

    }

    // User clicks on a category. Creates a URL based off of the action and then creates a RemoteResults and
    // loads the URL into it.
    public function userCategoryClick($path, $cat)
    {
        $pathToCat = ($path != null && strlen($path) > 0
                ? ($path.'/')
                : '').$cat;
        $url = $this->formBaseURL().'&RequestAction=advisor&CatPath='.urlencode($pathToCat)
            .'&RequestData=CA_CategoryExpand';
        echo $url;

        return $this->urlPost($url);
    }

    // User clicks on a breadcrumb. Creates a URL based off of the action and then creates a RemoteResults and
    // loads the URL into it.
    public function userBreadCrumbClick($path): void
    {
        $this->url = $this->formBaseURL()->withQueryParameters([
            'RequestAction' => 'advisor',
            'CatPath' => $path,
            'RequestData' => 'CA_BreadcrumbSelect',
        ]);
    }

    // @deprecated unknown code
    //    public function _getRequestData($search, $currentPage)
    //    {
    //        $requestData = '';
    //        if ($currentPage > 1) {
    //            $requestData = 'page' . $currentPage;
    //        } elseif ($search) {
    //            $requestData = 'CA_Search';
    //        } else {
    //            $requestData = 'CA_BreadcrumbSelect';
    //        }
    //
    //        return $requestData;
    //    }

    // @deprecated unknown code
    //    public function CA_BreadcrumbClick($path, $pageType = null)
    //    {
    //        $search = request('search') === 'true' || request('search') == true;
    //        $searchQuery = request('q');
    //        $currentPage = (int) request('currentPage') ?? 1;
    //        $catPath = $search && $currentPage > 1
    //            ? '-'.$searchQuery
    //            : $path;
    //        $requestAction = $currentPage > 1
    //            ? 'navbar'
    //            : 'advisor';
    //
    //        if (! empty($pageType) && in_array($pageType, ['shop', 'shop_category'])) {
    //            $opts = $this->getOptions();
    // //            $opts->setToplevelProducts(true);
    //            $this->setOptions($opts);
    //        }
    //
    //        $requestData = $this->_getRequestData($search, $currentPage);
    //        $url = $this->formBaseURL()
    //                         ."&ie=UTF-8&RequestAction={$requestAction}&RequestData={$requestData}"
    //                         .(! empty($catPath) ? "&CatPath={$catPath}" : '').(! empty($searchQuery) ? "&q={$searchQuery}" : '');
    //
    //        return $this->urlPost($url);
    //    }

    // User clicks on a attribute. Creates a URL based off of the action and then creates a RemoteResults and
    // loads the URL into it.
    public function userAttributeClick($path, $attr): void
    {
        $this->url = $this->formBaseURL()->withQueryParameters([
            'RequestAction' => 'advisor',
            'CatPath' => $path,
            'RequestData' => 'CA_AttributeSelected',
            'AttribSel' => $attr,
        ]);
    }

    // User performs a page operation. Creates a URL based off of the action and then creates a RemoteRsults
    // instance and loads the URL into it.
    public function userPageOp($path, $curPage, $pageOp)
    {
        $url = $this->formBaseURL().'&RequestAction=navbar&CatPath='.urlencode($path).'&RequestData='
            .urlencode($pageOp);
        if ($curPage != null && strlen($curPage) > 0) {
            $url .= '&currentpage='.$curPage;
        }

        return $this->urlPost($url);
    }

    // User requests to go to a specific page. Creates a URL based off of the action and then creates a RemoteResults
    // instance and loads the URL into it.
    public function userGoToPage($path, $pageNumber): void
    {
        $this->url = $this->formBaseURL()->withQueryParameters([
            'RequestAction' => 'navbar',
            'CatPath' => $path,
            'RequestData' => "page{$pageNumber}",
        ]);
    }

    // Sets the protocol.  By default it is http.
    public function setProtocol($protocol)
    {
        $this->m_sProtocol = $protocol;
    }

    // Sets the EasyAsk options to an Options instance
    public function setOptions($val)
    {
        $this->m_options = $val;
    }

    // Gets the current EasyAsk Options
    public function getOptions(): Options
    {
        return $this->m_options;
    }

    // User Post does a http POST. Creates a RemoteResults instance and
    //  Posts the URL to get results from the EasyAsk server.
    /**
     * @throws \Exception
     */
    public function urlPost($url = null): RemoteResults
    {
        $this->url = $url ? Url::fromString($url) : $this->url;

        $queryParams = $this->url->getAllQueryParameters();

        $queryParams = $this->injectDefaultScopes($queryParams);

        $filteredQuery = collect($queryParams)->filter(fn ($value) => ! empty($value))->toArray();

        $url = $this->url->withoutQueryParameters()->withQueryParameters($filteredQuery);

        $res = new RemoteResults;

        $res->load($url);

        return $res;
    }

    private function getSearchScope(): string
    {
        $catalog = null;

        $productRestriction = null;

        if (\config('amplify.search.default_catalog')) {
            $catalog = \App\Models\Category::find(\config('amplify.search.default_catalog'));
        }

        if ($catalog == null) {
            throw new \InvalidArgumentException('Default catalog is not configured.');
        }

        if (config('amplify.search.use_product_restriction')) {

            $productRestriction = "(InCompany 1 ea_or GLOBAL_flag = 'true') (((InWarehouse = ".$this->getOptions()->getCurrentWarehouse().' ea_or '.implode(' ea_or ', explode(',', $this->options()->getAlternativeWarehouseIds())).'))  ea_or NonStock <> 0 )';

//            if ($this->m_options->getStockAvail() === 1) {
//                $productRestriction .= ' (Avail = 1)';
//            }

            $productRestriction .='(Amplify Id > 0)';

            $productRestriction .= ' )';
        }

        $catPathPrefix = "{$catalog->category_name}/".(! empty($productRestriction) ? $productRestriction : $catalog->category_name);

        return str_replace([' '], ['-'], $catPathPrefix);
    }

    private function injectDefaultScopes(array $queryParams = []): array
    {
        $catPath = $queryParams['CatPath'] ?? '';

        $catPathPrefix = $this->getSearchScope();

        $queryParams['CatPath'] = trim(((str_contains($catPath, $catPathPrefix)) ? $catPath : $catPathPrefix), '/');

        return $queryParams;
    }
}
