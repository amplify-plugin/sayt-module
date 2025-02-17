<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\IRemoteEasyAsk;

// The Easy Ask Session
class RemoteEasyAsk implements IRemoteEasyAsk
{
    // connection info
    private $m_sHostName = '';

    private $m_nPort = -1;

    private $m_sProtocol = 'http';

    private $m_sRootUri = 'EasyAsk/apps/Advisor.jsp';

    private $m_options = null;

    // Creates the EasyAsk instance.
    public function __construct($sHostName, $nPort, $dictionary, $protocol)
    {
        $this->m_sHostName = $sHostName;
        $this->m_nPort = $nPort;
        $this->m_sProtocol = $protocol;
        $this->m_options = new Options($dictionary);
    }

    // Creates the generic URL for the website.
    private function formBaseURL()
    {
        $port = isset($this->m_nPrt)
            ? ':'.$this->m_nPort
            : '';
        // $port = ($this->m_nPort || sizeof($this->m_nPort) > 0) ? ":" . $this->m_nPort : "";

        return $this->m_sProtocol.'://'.$this->m_sHostName.$port.'/'.$this->m_sRootUri
               .'?disp=json&oneshot=1';
    }

    // Converts a string parameter to a format usable by the website URL
    private function addParam($name, $val)
    {
        return ($val != null && strlen($val) > 0)
            ? '&'.$name.'='.$val
            : '';
    }

    // Converts a boolean parameter to a format usable by the website URL
    private function addTrueParam($name, $val)
    {
        return $val
            ? '&'.$name.'='.$val
            : '';
    }

    // Coverts a value without a name to a format usable by the website URL
    private function addNonNullVal($val)
    {
        return $val != null
            ? $val
            : '';
    }

    // Creates a url for the current host settings and EasyAsk options
    private function formURL()
    {
        return $this->formBaseURL().'&dct='.$this->m_options->getDictionary().'&indexed=1'.
               '&ResultsPerPage='.$this->m_options->getResultsPerPage().
               $this->addParam('defsortcols', $this->m_options->getSortOrder()).
               $this->addTrueParam('subcategories', $this->m_options->getSubCategories()).
               $this->addTrueParam('rootprods', $this->m_options->getToplevelProducts()).
               $this->addTrueParam('navigatehierarchy', $this->m_options->getNavigateHierarchy()).
               $this->addTrueParam('returnskus', $this->m_options->getReturnSKUs()).
               $this->addParam('defarrangeby', $this->m_options->getGrouping()).
               $this->addParam('eap_GroupID', $this->m_options->getGroupId()).
               $this->addParam('eap_CustomerID', $this->m_options->getCustomerId()).
               $this->addParam('customer', $this->m_options->getCustomer()).
               $this->addNonNullVal($this->m_options->getCallOutParam());
    }

    // User performs a search. Creates a URL based off of the search and then creates a RemoteResults and
    // loads the URL into it.
    public function userSearch($path, $question)
    {
        $url = $this->formURL().'&RequestAction=advisor&CatPath='.urlencode($path).'&RequestData=CA_Search&q='
               .urlencode($question);

        return $this->urlPost($url);
    }

    // User clicks on a category. Creates a URL based off of the action and then creates a RemoteResults and
    // loads the URL into it.
    public function userCategoryClick($path, $cat)
    {
        $pathToCat = ($path != null && strlen($path) > 0
                ? ($path.'/')
                : '').$cat;
        $url = $this->formURL().'&RequestAction=advisor&CatPath='.urlencode($pathToCat)
                     .'&RequestData=CA_CategoryExpand';
        echo $url;

        return $this->urlPost($url);
    }

    // User clicks on a breadcrumb. Creates a URL based off of the action and then creates a RemoteResults and
    // loads the URL into it.
    public function userBreadCrumbClick($path)
    {
        $url = $this->formURL().'&RequestAction=advisor';
        if (! empty($path)) {
            $url = $url.'&CatPath='.urlencode($path).'&RequestData=CA_BreadcrumbSelect';
        }

        return $this->urlPost($url);
    }

    public function _getRequestData($search, $currentPage)
    {
        $requestData = '';
        if ($currentPage > 1) {
            $requestData = 'page'.$currentPage;
        } elseif ($search) {
            $requestData = 'CA_Search';
        } else {
            $requestData = 'CA_BreadcrumbSelect';
        }

        return $requestData;
    }

    public function CA_BreadcrumbClick($path, $pageType = null)
    {
        $search = request('search') === 'true' || request('search') == true;
        $searchQuery = request('q') ?? '';
        $currentPage = (int) request('currentPage') ?? 1;
        $catPath = $search && $currentPage > 1
            ? '-'.$searchQuery
            : $path;
        $requestAction = $currentPage > 1
            ? 'navbar'
            : 'advisor';

        if (! empty($pageType) && in_array($pageType, ['shop', 'shop_category'])) {
            $opts = $this->getOptions();
            $opts->setToplevelProducts(true);
            $this->setOptions($opts);
        }

        $requestData = $this->_getRequestData($search, $currentPage);
        $url = $this->formURL()
                         ."&ie=UTF-8&RequestAction={$requestAction}&RequestData={$requestData}"
                         .(! empty($catPath) ? "&CatPath={$catPath}" : '').(! empty($searchQuery) ? "&q={$searchQuery}" : '');

        return $this->urlPost($url);
    }

    public function getBestSellerEaProduct($path)
    {
        $searchQuery = $path;
        $catPath = 'All Products';
        $requestAction = 'advisor';
        $requestData = 'CA_Search';
        $url = $this->formURL()
                         ."&ie=UTF-8&defsortcols=&RequestAction={$requestAction}&RequestData={$requestData}&CatPath={$catPath}&dct=amplify-rbs&q={$searchQuery}";

        return $this->urlPost($url);
    }

    // User clicks on a attribute. Creates a URL based off of the action and then creates a RemoteResults and
    // loads the URL into it.
    public function userAttributeClick($path, $attr)
    {
        $url = $this->formURL().'&RequestAction=advisor&CatPath='.urlencode($path)
               .'&RequestData=CA_AttributeSelected&AttribSel='.urlencode($attr);

        //		echo $url;
        return $this->urlPost($url);
    }

    // User performs a page operation. Creates a URL based off of the action and then creates a RemoteRsults
    // instance and loads the URL into it.
    public function userPageOp($path, $curPage, $pageOp)
    {
        $url = $this->formURL().'&RequestAction=navbar&CatPath='.urlencode($path).'&RequestData='
               .urlencode($pageOp);
        if ($curPage != null && strlen($curPage) > 0) {
            $url += '&currentpage='.$curPage;
        }

        return $this->urlPost($url);
    }

    // User requests to go to a specific page. Creates a URL based off of the action and then creates a RemoteResults
    // instance and loads the URL into it.
    public function userGoToPage($path, $pageNumber)
    {
        $url =
            $this->formURL().'&RequestAction=navbar&CatPath='.urlencode($path).'&RequestData=page'.$pageNumber;

        return $this->urlPost($url);
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
    public function getOptions()
    {
        return $this->m_options;
    }

    // User Post does an http POST. Creates a RemoteResults instance and
    // and Posts the URL to get results from the EasyAsk server.
    public function urlPost($url)
    {
        $res = new RemoteResults;

        $res->load($url);

        return $res;
    }
}
