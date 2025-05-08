<?php

namespace Amplify\System\Sayt;

use Amplify\System\Sayt\Classes\PromoRemoteEasyAsk;
use Amplify\System\Sayt\Classes\PromoRemoteFactory;
use Amplify\System\Sayt\Classes\RemoteEasyAsk;
use Amplify\System\Sayt\Classes\RemoteFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @class EasyAskPageService
 *
 * @property-read Request $request
 * @property-read Application $app
 */
class EasyAskStudio
{
    public function __construct(public readonly Request $request, public readonly Application $app) {}

    /**
     * @return Application|SessionManager|Store|mixed
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function EAGetConfig()
    {
        if (! empty(session()->all())) {
            $config = session()->get('ezshop-config');
        } else {
            $config = collect([]);
        }

        if (collect($config)->isEmpty()) {
            $config = eaShopConfig();
            if (! $config) {
                dd("Invalid config data found in file 'ezshop-config.json'");
            }
            session(['ezshop-config' => $config]);
        } else {
            $config = session('ezshop-config');
        }

        return $config;
    }

    /**
     * @return RedirectResponse
     */
    public function clearCache()
    {
        session()->forget([
            'ezshop-config',
            'ezshop-featured-products',
            'ezshop-top-sellers',
            'ezshop-new-arrivals',
            'ezshop-best-rated',
            'ezshop-hero-products',
            'ezshop-top-categories',
            'ezshop-categories',
        ]);

        session()->flush();

        return Redirect::away('/store/home');
    }

    /**
     * @return PromoRemoteEasyAsk
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function EARelProdSetup()
    {
        // The call to create a RemoteEasyAsk object that allows you to access the remote server
        // to get the resultset of the search. We need to to provide the server and port information
        // $uri = $request->path();
        // dd($seopath);
        $config = $this->EAGetConfig();

        $eahostname = $config->connection->host; // this should be a configurable value
        $eaport = $config->connection->port; // leave blank for default value
        $eadictionary = $config->connection->dxp;  // this should be a configurable value
        $eaprotocol = config('amplify.search.protocol', 'https');                   // this should be a configurable value from a dropdown
        $rpp = 20;
        $groupId = '';

        if ($eahostname == '' || $eadictionary == '') {
            dd('To use easyask search engine you need to specify the host name and dictionary in EZ Shop confugration file');
        }

        // Get the RemoteEasyAsk object
        $eaRelProd = PromoRemoteFactory::create($eahostname, $eaport, $eadictionary, $eaprotocol);

        // Get the Options object and set the appropriate options

        $opts = $eaRelProd->getOptions();
        $opts->setResultsPerPage($rpp);
        // $opts->setGrouping("");
        // $opts->setNavigateHierarchy(false);
        //       $opts->setSubCategories(false);
        // $opts->setGroupId($groupId);
        $opts->setCustomerId('');
        $eaRelProd->setOptions($opts);

        return $eaRelProd;
    }

    /**
     * @return RemoteEasyAsk
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function EAConvSetup()
    {
        // The call to create a RemoteEasyAsk object that allows you to access the remote server
        // to get the resultset of the search. We need to to provide the server and port information
        // $uri = $request->path();
        // dd($seopath);
        $config = $this->EAGetConfig();

        $eahostname = $config->connection->host;          // this should be a configurable value
        $eaport = $config->connection->port;          // leave blank for default value
        $eadictionary = $config->connection->dxp.'_conv'; // this should be a configurable value
        $eaprotocol = 'https';                            // this should be a configurable value from a dropdown
        $rpp = 10;
        $groupId = '';

        if ($eahostname == '' || $eadictionary == '') {
            dd('To use easyask search engine you need to specify the host name and dictionary in EZ Shop configuration file');
        }

        // Get the RemoteEasyAsk object
        $ea = RemoteFactory::create($eahostname, $eaport, $eadictionary, $eaprotocol);

        // Get the Options object and set the appropriate options

        $opts = $ea->getOptions();
        $opts->setResultsPerPage($rpp);
        // $opts->setGrouping("");
        $opts->setNavigateHierarchy(false);
        $opts->setSubCategories(false);
        // $opts->setGroupId($groupId);
        $opts->setCustomerId('');

        return $ea;
    }

    /**
     * @return RemoteEasyAsk
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getEASetup()
    {
        return $this->EASetup();
    }

    /**
     * @return RemoteEasyAsk
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function EASetup($paginate_per_page = 10)
    {
        // The call to create a RemoteEasyAsk object that allows you to access the remote server
        // to get the resultset of the search. We need to provide the server and port information
        // $uri = $request->path();
        // dd($seopath);
        $config = $this->EAGetConfig();

        $eahostname = $config->connection->host; // this should be a configurable value
        $eaport = $config->connection->port; // leave blank for default value
        $eadictionary = $config->connection->dxp;  // this should be a configurable value
        $eaprotocol = $config->connection->protocol ??
            'https';                   // this should be a configurable value from a dropdown
        $rpp = request('resultsPerPage', $paginate_per_page);
        $rs = request('returnSKUS', 0);
        $currentPage = request('currentPage', 1);
        $groupId = '';

        if ($eahostname == '' || $eadictionary == '') {
            dd('To use easyask search engine you need to specify the host name and dictionary in EZ Shop confugration file');
        }

        // Get the RemoteEasyAsk object
        $ea = RemoteFactory::create($eahostname, $eaport, $eadictionary, $eaprotocol);

        // Get the Options object and set the appropriate options

        $opts = $ea->getOptions();
        $opts->setResultsPerPage($rpp);
        // Setting returnSKUS
        $opts->setReturnSKUs($rs);
        $opts->setCurrentPage($currentPage);
        // $opts->setGrouping("");
        $opts->setNavigateHierarchy(false);
        $opts->setSubCategories(false);
        // $opts->setGroupId($groupId);
        $opts->setCustomerId('');

        return $ea;
    }

    /**
     * @return Application|SessionManager|Store|mixed
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getTop2LevelCategories()
    {
        if (! request()->session()->has('ezshop-categories')) {
            // Categories not set in current session so need to get them from EA...
            $EACatConnection = $this->EASetup();
            $opts = $EACatConnection->getOptions();
            $opts->setResultsPerPage(1);
            $opts->setToplevelProducts(true);
            $opts->setGrouping('///NONE///');
            $opts->setNavigateHierarchy(false);
            $opts->setSubCategories(1);
            $EACatConnection->setOptions($opts);
            $EACatresults = $EACatConnection->userBreadcrumbClick('');
            // dd($EACatresults);
            $top2LevelCategories = $EACatresults->getDetailedCategoriesFull();

            session(['ezshop-categories' => $top2LevelCategories]);
        } else {
            $top2LevelCategories = session('ezshop-categories');
        }

        // dd($top2LevelCategories);
        return $top2LevelCategories;
    }

    /**
     * @return Application|Factory|View
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function storeHome()
    {
        $conversationMode = false;
        $quietMode = true;
        $config = $this->EAGetConfig();
        // $detect = new Mobile_Detect;
        // $searchPlaceholder = $detect->isMobile()? $config->shortSearchPlaceholder : $config->longSearchPlaceholder;
        $top2LevelCategories = $this->getTop2LevelCategories();
        $categories = $config->topnavCategories;
        $HPbrands = $config->homePageBrands;

        // Get Featured Products

        if (! request()->session()->has('ezshop-featured-products')) {
            $EAFPConnection = $this->EASetup();
            $opts = $EAFPConnection->getOptions();
            $opts->setGrouping('///NONE///');
            $opts->setResultsPerPage(20);
            $opts->setNavigateHierarchy(false);
            $opts->setSubCategories(false);
            $opts->setCustomerId('');
            $EAFPConnection->setOptions($opts);
            $FPSEOPath = 'Home-Page-Merchandising:HP-Featured-Products';
            $numFeaturedProducts = 0;
            if ($EAFPresults = $EAFPConnection->userBreadCrumbClick($FPSEOPath)) {
                $numFeaturedProducts = $EAFPresults->getTotalItems();
                $numFeaturedProducts = ($numFeaturedProducts > 20)
                    ? 20
                    : $numFeaturedProducts;
                session(['ezshop-featured-products' => $EAFPresults]);
            }
        } else {
            $EAFPresults = session('ezshop-featured-products');
            $numFeaturedProducts = $EAFPresults->getTotalItems();
            $numFeaturedProducts = ($numFeaturedProducts > 20)
                ? 20
                : $numFeaturedProducts;
        }
        // dd($EAFPresults);
        // Get Top-sellers, New Arrivals & Best Rated product lists
        if (! request()->session()->has('ezshop-top-sellers')) {
            $EAHPConnection = $this->EASetup();
            $opts = $EAHPConnection->getOptions();
            $opts->setResultsPerPage(3);
            $opts->setGrouping('///NONE///');
            $opts->setNavigateHierarchy(false);
            $opts->setSubCategories(false);
            $opts->setCustomerId('');
            $EAHPConnection->setOptions($opts);
            $HPSEOPath = 'Home-Page-Merchandising:HP-Top-Sellers';
            $numTopSellers = 0;
            if ($EATSresults = $EAHPConnection->userBreadCrumbClick($HPSEOPath)) {
                $numTopSellers = $EATSresults->getTotalItems();
                if ($numTopSellers > 3) {
                    $numTopSellers = 3;
                }
                session(['ezshop-top-sellers' => $EATSresults]);
            }
        } else {
            $EATSresults = session('ezshop-top-sellers');
            $numTopSellers = $EATSresults->getTotalItems();
            if ($numTopSellers > 3) {
                $numTopSellers = 3;
            }
        }
        if (! request()->session()->has('ezshop-new-arrivals')) {
            $EAHPConnection = $this->EASetup();
            $opts = $EAHPConnection->getOptions();
            $opts->setResultsPerPage(3);
            $opts->setGrouping('///NONE///');
            $opts->setNavigateHierarchy(false);
            $opts->setSubCategories(false);
            $opts->setCustomerId('');
            $EAHPConnection->setOptions($opts);
            $HPSEOPath = 'Home-Page-Merchandising:HP-New-Arrivals';
            $numNewArrivals = 0;
            if ($EANAresults = $EAHPConnection->userBreadCrumbClick($HPSEOPath)) {
                $numNewArrivals = $EANAresults->getTotalItems();
                if ($numNewArrivals > 3) {
                    $numNewArrivals = 3;
                }

                session(['ezshop-new-arrivals' => $EANAresults]);
            }

            // dd($numNewArrivals, $EANAresults);
        } else {
            $EANAresults = session('ezshop-new-arrivals');
            $numNewArrivals = $EANAresults->getTotalItems();
            if ($numNewArrivals > 3) {
                $numNewArrivals = 3;
            }
        }
        if (! request()->session()->has('ezshop-best-rated')) {
            $EAHPConnection = $this->EASetup();
            $opts = $EAHPConnection->getOptions();
            $opts->setResultsPerPage(3);
            $opts->setGrouping('///NONE///');
            $opts->setNavigateHierarchy(false);
            $opts->setSubCategories(false);
            $opts->setCustomerId('');
            $EAHPConnection->setOptions($opts);
            $HPSEOPath = 'Home-Page-Merchandising:HP-Best-Rated';
            $numBestRated = 0;
            if ($EABRresults = $EAHPConnection->userBreadCrumbClick($HPSEOPath)) {
                $numBestRated = $EABRresults->getTotalItems();
                session(['ezshop-best-rated' => $EABRresults]);
                if ($numBestRated > 3) {
                    $numBestRated = 3;
                }
            }
        } else {
            $EABRresults = session('ezshop-best-rated');
            $numBestRated = $EABRresults->getTotalItems();
            if ($numBestRated > 3) {
                $numBestRated = 3;
            }
        }
        // Get hero products...

        if (! request()->session()->has('ezshop-hero-products')) {
            $EAHPConnection = $this->EASetup();
            $opts = $EAHPConnection->getOptions();
            $opts->setGrouping('///NONE///');
            $opts->setNavigateHierarchy(false);
            $opts->setSubCategories(false);
            $opts->setResultsPerPage(10);
            $opts->setCustomerId('');
            $EAHPConnection->setOptions($opts);
            $HPSEOPath = 'Home-Page-Merchandising:HP-Hero-Products';
            $EAHPresults = $EAHPConnection->userBreadCrumbClick($HPSEOPath);
            $numHeroProducts = $EAHPresults->getTotalItems();
            $numHeroProducts = ($numHeroProducts > 10)
                ? 10
                : $numHeroProducts;
            session(['ezshop-hero-products' => $EAHPresults]);
        } else {
            $EAHPresults = session('ezshop-hero-products');
            $numHeroProducts = $EAHPresults->getTotalItems();
            $numHeroProducts = ($numHeroProducts > 10)
                ? 10
                : $numHeroProducts;
        }
        $banners = $EAHPresults->getDisplayBanners();
        $hasBanners = ! empty($banners)
            ? count($banners)
            : 0;

        //      Get top categories, product images & lowest price...
        //
        if (! request()->session()->has('ezshop-top-categories')) {
            $EAHPConnection = $this->EASetup();
            $topCatList = $config->HPTopCategories;
            $topCatInfo = [];
            $i = 0;

            foreach ($topCatList as $topCat) {
                $catSEOPath = $topCat->SEOPath;
                $catName = $topCat->name;
                $topCatInfo[$i] = new \stdClass;
                $topCatInfo[$i]->name = $catName;
                $topCatInfo[$i]->SEOPath = $catSEOPath;
                $topCatInfo[$i]->images = [];
                $topCatInfo[$i]->productIds = [];
                $topCatInfo[$i]->productNames = [];
                $topCatInfo[$i]->productPrice = [];
                $opts = $EAHPConnection->getOptions();
                $opts->setGrouping('///NONE///');
                $opts->setNavigateHierarchy(false);
                $opts->setSubCategories(false);
                $opts->setSortOrder('');
                $opts->setCustomerId('');
                $opts->setResultsPerPage(3);
                $EAHPConnection->setOptions($opts);
                $EATCresults = $EAHPConnection->userBreadCrumbClick('HP-Top-Categories:TC-'.$catSEOPath);

                $np = $EATCresults->getTotalItems();
                if ($np > 3) {
                    $np = 3;
                }
                for ($j = 0; $j < $np; $j++) {
                    // Get product image & id...
                    $topCatInfo[$i]->images[$j] =
                        $EATCresults->getCellData($j, $EATCresults->getColumnIndex($config->fieldNames->mainImageURL));
                    $topCatInfo[$i]->productIds[$j] =
                        $EATCresults->getCellData($j, $EATCresults->getColumnIndex($config->fieldNames->productID));
                    $topCatInfo[$i]->productNames[$j] =
                        $EATCresults->getCellData($j, $EATCresults->getColumnIndex($config->fieldNames->productName));
                    $topCatInfo[$i]->productPrice[$j] =
                        $EATCresults->getCellData($j, $EATCresults->getColumnIndex($config->fieldNames->price));
                }
                $opts = $EAHPConnection->getOptions();
                $opts->setGrouping('///NONE///');
                $opts->setNavigateHierarchy(false);
                $opts->setSubCategories(true);
                $opts->setCustomerId('');
                $opts->setSortOrder($config->fieldNames->price);
                $opts->setResultsPerPage(1);
                $EAHPConnection->setOptions($opts);
                $EATCresults = $EAHPConnection->userBreadCrumbClick($catSEOPath);
                $topCatInfo[$i]->lowestPrice =
                    $EATCresults->getCellData(0, $EATCresults->getColumnIndex($config->fieldNames->price));

                $i++;
            }
            // dd($topCatInfo);

            session(['ezshop-top-categories' => $topCatInfo]);
        } else {
            $topCatInfo = session('ezshop-top-categories');
        }
        // dd($topCatInfo);
        $currentSEOPath = null;

        return view('easyask::store.storeHome', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'topCatInfo', 'categories', 'numBestRated', 'EABRresults', 'numNewArrivals', 'EANAresults', 'numTopSellers', 'EATSresults', 'HPbrands', 'numFeaturedProducts', 'EAFPresults', 'numHeroProducts', 'EAHPresults', 'config', 'banners', 'hasBanners', 'conversationMode'));
    }

    /**
     * @return Application|Factory|View
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function storeLandingPage($lpname)
    {
        //  Processes request for landing page - url = store/landing/xxx where xxx is landing page name
        $conversationMode = false;
        $quietMode = true;
        $EAConnection = $this->EASetup();
        $config = $this->EAGetConfig();
        // $detect = new Mobile_Detect;
        // $searchPlaceholder = $detect->isMobile()? $config->shortSearchPlaceholder : $config->longSearchPlaceholder;
        $categories = $config->topnavCategories;
        $top2LevelCategories = $this->getTop2LevelCategories();

        $opts = $EAConnection->getOptions();
        $opts->setGrouping('///NONE///');
        $opts->setNavigateHierarchy(false);
        $opts->setSubCategories(false);
        $opts->setCustomerId('');
        $EAConnection->setOptions($opts);
        $SEOPath = 'Landing-Page:LP-'.$lpname;
        $EAresults = $EAConnection->userBreadCrumbClick($SEOPath);
        $returnCode = $EAresults->getReturnCode();
        $numResults = $EAresults->getTotalItems();
        $banners = $EAresults->getDisplayBanners();
        $hasBanners = ! empty($banners)
            ? count($banners)
            : 0;
        $currentSEOPath = null;

        if ($numResults == -1) {
            // No results...
            $noResultsMessage = 'Sorry - that landing page does not exist';

            $noResultsSearches = $EAresults->getNoResultsPage()->getSearches();

            // dd($noResultsSearches);
            return view('easyask::store.noResults', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'categories', 'config', 'noResultsMessage', 'noResultsSearches', 'conversationMode'));
        }
        $pageTitle = '';
        $seopath = $EAresults->getCurrentSeoPath();
        $message = null;

        return view('easyask::store.storeProducts', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'categories', 'EAresults', 'pageTitle', 'seopath', 'config', 'banners', 'hasBanners', 'message', 'conversationMode'));
    }

    /**
     * @return Application|Factory|View
     */
    public function storeSpeech()
    {
        return view('easyask::store.speech');
    }

    /**
     * @return array|RedirectResponse
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function storeSearch($site_search = null)
    {
        // dd(request()->all());

        // The call to create a RemoteEasyAsk object that allows you to access the remote server
        // to get the resultset of the search. We need to to provide the server and port information
        // dd($seopath);
        $conversationMode = false;
        $quietMode = true;
        $referrer = $_SERVER['HTTP_REFERER'];

        $EAConnection = $this->EASetup();
        $currentSEOPath = null;
        $config = $this->EAGetConfig();
        // $detect = new Mobile_Detect;
        //       $searchPlaceholder = $detect->isMobile()? $config->shortSearchPlaceholder : $config->longSearchPlaceholder;
        $categories = $config->topnavCategories;
        $top2LevelCategories = $this->getTop2LevelCategories();

        // $queryText is the search term you are searching for
        // Get the RemoteResults object for the search
        $searchText = $site_search ?? request('site_search');
        if ($searchText == null) {
            return [
                'top2LevelCategories' => $top2LevelCategories,
                'categories' => $categories,
            ];
        }

        $EAresults = $EAConnection->userSearch('All Products', $searchText);
        $numResults = $EAresults->getTotalItems();

        if ($numResults == -1) {
            // No results...
            $noResultsMessage = $EAresults->getNoResultsPage()->getMessage();
            $noResultsSearches = $EAresults->getNoResultsPage()->getSearches();

            // dd($noResultsSearches);
            return [
                'quietMode' => $quietMode,
                'currentSEOPath' => $currentSEOPath,
                'top2LevelCategories' => $top2LevelCategories,
                'categories' => $categories,
                'config' => $config,
                'noResultsMessage' => $noResultsMessage,
                'noResultsSearches' => $noResultsSearches,
                'conversationMode' => $conversationMode,
            ];
            // return view('easyask::store.noResults', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'categories', 'config', 'noResultsMessage', 'noResultsSearches', 'conversationMode'));
        }

        $products = $EAresults->getProducts();
        if ($numResults == 1) {
            //  Single product found, so go straight to detail page...

            $productID = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->productID));
            $pageTitle = 'Product Details';
            $productName = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->productName));
            $imgURL = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->mainImageURL));
            $images = false;
            $imageList = '';
            $colors = false;
            $colorList = '';
            $sizes = false;
            $sizeList = '';
            if ($config->fieldNames->otherImagesList) {
                $otherImagesList =
                    $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->otherImagesList));
                if ($otherImagesList) {
                    $imageList = explode($config->options->listSeparatorChar, $otherImagesList);

                    $images = true;
                }
            }
            if ($config->fieldNames->sizeList) {
                $sizeListStr = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->sizeList));
                if ($sizeListStr) {
                    $sizeList = explode($config->options->listSeparatorChar, $sizeListStr);
                    $sizes = true;
                }
            }
            if ($config->fieldNames->colorList) {
                $colorListStr = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->colorList));
                if ($colorListStr) {
                    $colorList = explode($config->options->listSeparatorChar, $colorListStr);
                    $colors = true;
                }
            }
            $price = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->price));
            $averageRating = ($config->fieldNames->averageRating)
                ? $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->averageRating))
                : 0;
            $prodCat = ($config->fieldNames->category)
                ? $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->category))
                : '';
            $skuId = ($config->fieldNames->skuID)
                ? $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->skuID))
                : '';
            $listPrice = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->listPrice));
            $productID = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->productID));
            $productURL = '/store/product/'
                .$EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->productURL))
                .'/'.$productID;
            $description = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->productDesc));
            // dd($description);
            $EARelProdConnection = $this->EARelProdSetup();
            $EARelProdResults = $EARelProdConnection->getPromotions($productID, 'cross-sell');
            //       dd($EARelProdResults);
            $numRelResults = $EARelProdResults->getTotalItems();

            if ($numRelResults > 20) {
                $numRelResults = 20;
            }

            return [
                'quietMode' => $quietMode,
                'currentSEOPath' => $currentSEOPath,
                'top2LevelCategories' => $top2LevelCategories,
                // 'categories'          => $categories,
                'categories' => $EAresults->getCategories(),
                'numRelResults' => $numRelResults,
                'EARelProdResults' => $EARelProdResults,
                'prodCat' => $prodCat,
                'pageTitle' => $pageTitle,
                'skuId' => $skuId,
                'productName' => $productName,
                'imageList' => $imageList,
                'imgURL' => $imgURL,
                'price' => $price,
                'listPrice' => $listPrice,
                'productID' => $productID,
                'productURL' => $productURL,
                'description' => $description,
                'images' => $images,
                'colors' => $colors,
                'colorList' => $colorList,
                'sizes' => $sizes,
                'averageRating' => $averageRating,
                'sizeList' => $sizeList,
                'config' => $config,
                'conversationMode' => $conversationMode,
                'products' => $products,
                'attributes' => $EAresults->getAttributes(),
            ];

            // return view('easyask::store.storeProductDetail', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'categories', 'numRelResults', 'EARelProdResults', 'prodCat', 'pageTitle', 'skuId', 'productName', 'imageList', 'imgURL', 'price', 'listPrice', 'productID', 'productURL', 'description', 'images', 'colors', 'colorList', 'sizes', 'averageRating', 'imageList', 'sizeList', 'config', 'conversationMode'));
        }

        if ($EAresults->isRedirect()) {
            $url = $EAresults->getRedirect();

            return Redirect::away($url);
        }

        $pageTitle = 'Results for "'.$EAresults->getQuestion().'"';

        //  Unpack & display results...
        $seopath = $EAresults->getCurrentSeoPath();
        $categories = $config->topnavCategories;
        $top2LevelCategories = $this->getTop2LevelCategories();

        $banners = $EAresults->getDisplayBanners();
        $hasBanners = ! empty($banners)
            ? count($banners)
            : 0;
        $message = $EAresults->getMessage();

        return [
            'quietMode' => $quietMode,
            'currentSEOPath' => $currentSEOPath,
            'top2LevelCategories' => $top2LevelCategories,
            // 'categories'          => $categories,
            'EAresults' => $EAresults,
            'pageTitle' => $pageTitle,
            'seopath' => $seopath,
            'config' => $config,
            'banners' => $banners,
            'hasBanners' => $hasBanners,
            'message' => $message,
            'conversationMode' => $conversationMode,
            'products' => $products,
            'categories' => $EAresults->getCategories(),
            'attributes' => $EAresults->getAttributes(),
        ];
        // return view('easyask::store.storeProducts', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'categories', 'EAresults', 'pageTitle', 'seopath', 'config', 'banners', 'hasBanners', 'message', 'conversationMode'));
    }

    public function storeConversation($seopath)
    {
        $conversationMode = true;
        $quietMode = true;
        $config = $this->EAGetConfig();
        $categories = $config->topnavCategories;
        $currentSEOPath = '';

        $top2LevelCategories = $this->getTop2LevelCategories();
        $convList = session('convList');
        $convSEOList = session('convSEOList');
        $seolistct = count($convSEOList);
        $countList = session('countList');
        $priceUsed = session('priceUsed');
        $initialPriceRanges = session('initialPriceRanges');
        //
        //  Check whether current input is a command...
        $showCommands = $config->conversational->commandShow;
        foreach ($showCommands as $showCommand) {
            //  "Show results" command

            if ($seopath == $showCommand) {
                if (count($convSEOList) > 0) {
                    $url = '/store/products/'.array_pop($convSEOList);

                    return Redirect::away($url);
                } else {
                    return Redirect::away('/store/newconversation');
                }
            }
        }

        //  "Previous" command

        // $path should be set to last SEO path in array...
        $seolistct = count($convSEOList);
        $path = ($seolistct == 0)
            ? ''
            : $convSEOList[$seolistct - 1];
        $firstSearch = (count($convList) == 0)
            ? true
            : false;

        //  Do EasyAsk search...

        $EAConnection = $this->EAConvSetup();

        // Get the RemoteResults object for the search

        $EAresults = $EAConnection->userSearch($path, $seopath);
        $numResults = $EAresults->getTotalItems();
        if ($numResults == -1) {
            // No results...
            // dd($EAresults);
            $message = $config->conversational->noResultsMessage;

            return view('easyask::store.storeConversation', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'categories', 'EAresults', 'seopath', 'config', 'message', 'conversationMode', 'convList', 'convSEOList', 'countList'));
        }
        if ($numResults == 1) {
            //  Single product found, so go straight to detail page...

            $productID = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->productID));
            $pageTitle = 'Product Details';
            $productName = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->productName));
            $imgURL = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->mainImageURL));
            $images = false;
            $imageList = '';
            $colors = false;
            $colorList = '';
            $sizes = false;
            $sizeList = '';
            if ($config->fieldNames->otherImagesList) {
                $otherImagesList =
                    $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->otherImagesList));
                if ($otherImagesList) {
                    $imageList = explode($config->options->listSeparatorChar, $otherImagesList);

                    $images = true;
                }
            }
            if ($config->fieldNames->sizeList) {
                $sizeListStr = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->sizeList));
                if ($sizeListStr) {
                    $sizeList = explode($config->options->listSeparatorChar, $sizeListStr);
                    $sizes = true;
                }
            }
            if ($config->fieldNames->colorList) {
                $colorListStr = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->colorList));
                if ($colorListStr) {
                    $colorList = explode($config->options->listSeparatorChar, $colorListStr);
                    $colors = true;
                }
            }
            $price = $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->price));
            $averageRating = ($config->fieldNames->averageRating)
                ? $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->averageRating))
                : 0;
            $prodCat = ($config->fieldNames->category)
                ? $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->category))
                : '';
            $skuId = ($config->fieldNames->skuID)
                ? $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->skuID))
                : '';
            $listPrice =
                $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->listPrice));
            $productID =
                $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->productID));
            $productURL = '/store/product/'
                .$EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->productURL))
                .'/'.$productID;
            $description =
                $EAresults->getCellData(0, $EAresults->getColumnIndex($config->fieldNames->productDesc));
            $EARelProdConnection = $this->EARelProdSetup();
            $EARelProdResults = $EARelProdConnection->getPromotions($productID, 'cross-sell');
            $numRelResults = $EARelProdResults->getTotalItems();

            if ($numRelResults > 20) {
                $numRelResults = 20;
            }

            return view('easyask::store.storeProductDetail', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'categories', 'numRelResults', 'EARelProdResults', 'prodCat', 'pageTitle', 'skuId', 'productName', 'imageList', 'imgURL', 'price', 'listPrice', 'productID', 'productURL', 'description', 'images', 'colors', 'colorList', 'sizes', 'averageRating', 'imageList', 'sizeList', 'config', 'conversationMode'));
        }
        if ($numResults < $config->conversational->showThreshold) {
            $url = '/store/products/'.array_pop($convSEOList).'/-'.$seopath;

            return Redirect::away($url);
        }
        if ($EAresults->isRedirect()) {
            $url = $EAresults->getRedirect();

            return Redirect::away($url);
        }

        $attribs = $EAresults->getAttributeNamesFull();
        //
        //  If there are less than nnn products, we won't suggest refinements...

        //  Check if price has been used...

        // Check commentary to see if price field used...
        $commentaryList = explode(';', $EAresults->getCommentary());
        foreach ($commentaryList as $commentaryItem) {
            $item = (explode(' ', $commentaryItem))[0];
            if ($item == $config->conversational->priceAttribute) {
                $priceUsed = true;
            }
        }

        if ($numResults > $config->conversational->refineThreshold) {
            //    Choose max 2 attributes to suggest as refinements...

            //   Randomise the refine phrase...

            $ct = count($config->conversational->refinePhrase1);
            $refinePhrase1 = $config->conversational->refinePhrase1[rand(0, $ct - 1)];
            $ct = count($config->conversational->refinePhrase2);
            $refinePhrase2 = $config->conversational->refinePhrase2[rand(0, $ct - 1)];

            $attrib = [];
            $numAttribs = 0;
            foreach ($attribs as $attribute) {
                if (($attribute == $config->conversational->priceAttribute) && $priceUsed) {
                    continue;
                }
                $attrib[] = $attribute;
                $numAttribs++;
                if ($numAttribs == 2) {
                    break;
                }
            }

            $message = str_replace('{{numresults}}', $numResults, $config->conversational->resultsMessage);
            switch ($numAttribs) {
                case 0:
                    break;
                case 1:
                    $message .= ' '.str_replace('{{attrib1}}', $attrib[0], $refinePhrase1);
                    break;
                case 2:
                    $message .= ' '.str_replace('{{attrib1}}', $attrib[0], $refinePhrase2);
                    $message = str_replace('{{attrib2}}', $attrib[1], $message);
                    break;
            }
        } else {
            $message = str_replace('{{numresults}}', $numResults, $config->conversational->resultsMessage);
        }

        $categories = $config->topnavCategories;
        $top2LevelCategories = $this->getTop2LevelCategories();
        $currentSEOPath = $EAresults->getCurrentSeoPath();
        $convList[] = $seopath;
        $convSEOList[] = $currentSEOPath;

        $countList[] = $numResults;
        session(['convList' => $convList]);
        session(['convSEOList' => $convSEOList]);
        session(['countList' => $countList]);
        session(['priceUsed' => $priceUsed]);

        return view('easyask::store.storeConversation', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'categories', 'EAresults', 'seopath', 'config', 'message', 'conversationMode', 'convList', 'convSEOList', 'countList'));
    }

    public function storeNewConversation()
    {
        $quietMode = true;
        $convList = [];
        $convSEOList = [];

        $countList = [];
        $priceUsed = false;
        session(['convList' => $convList]);
        session(['convSEOList' => $convSEOList]);
        session(['countList' => $countList]);
        session(['priceUsed' => $priceUsed]);

        $conversationMode = true;
        $config = $this->EAGetConfig();
        // $detect = new Mobile_Detect;
        //       $searchPlaceholder = $detect->isMobile()? $config->shortSearchPlaceholder : $config->longSearchPlaceholder;
        $categories = $config->topnavCategories;
        $top2LevelCategories = $this->getTop2LevelCategories();
        $currentSEOPath = '';
        $message = $config->conversational->newSearchMessage;

        return view('easyask::store.storeConversation', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'categories', 'config', 'message', 'conversationMode', 'convList', 'convSEOList', 'countList'));
    }

    public static function getEaProductsData()
    {
        $seopath = \request()->route('query', null);
        if ($seopath == 'search') {
            $seopath = '-'.request('q', request('ea_server_products', ''));
        }

        return (new self)->storeProducts($seopath, 12, true, 'shop');
    }

    public static function getEaProductDetail()
    {
        return (new self)->storeProductDetail(request('seopath', ''));
    }

    public function storeProducts($seopath, $paginate_per_page = 10, $CA_BreadcrumbClick = false, $pageType = null)
    {
        $conversationMode = false;
        $quietMode = true;
        $EAConnection = $this->EASetup($paginate_per_page);
        $currentSEOPath = $seopath;

        $config = $this->EAGetConfig();

        $page = request('currentPage', 0);
        $sortOption = $this->getSortOption();
        $groupOption = request('groupby', '');
        $attribsel = request('attribsel', '');

        // Get the RemoteResults object for the search
        if ($page) {
            if ($sortOption) {
                $opts = $EAConnection->getOptions();
                $opts->setSortOrder($sortOption);
                $EAConnection->setOptions($opts);
            }
            $EAresults = $CA_BreadcrumbClick
                ? $EAConnection->CA_BreadcrumbClick($seopath, $pageType)
                : $EAConnection->userGoToPage($seopath, $page);
        } elseif ($sortOption) {
            // Sort request
            $opts = $EAConnection->getOptions();
            $opts->setSortOrder($sortOption);
            $EAConnection->setOptions($opts);
            $EAresults = $CA_BreadcrumbClick
                ? $EAConnection->CA_BreadcrumbClick($seopath, $pageType)
                : $EAConnection->userBreadCrumbClick($seopath);
        } elseif ($groupOption) {
            // Grouping request
            $opts = $EAConnection->getOptions();
            $opts->setGrouping($groupOption);
            $EAConnection->setOptions($opts);
            $EAresults = $EAConnection->userBreadCrumbClick($seopath);
        } elseif ($attribsel) {
            //  Range attribute click
            $EAresults = $EAConnection->userAttributeClick($seopath, $attribsel);
        } else {
            $EAresults = $CA_BreadcrumbClick
                ? $EAConnection->CA_BreadcrumbClick($seopath, $pageType)
                : $EAConnection->userBreadCrumbClick($seopath);
        }

        //  Unpack & display results...
        $pageTitle = '';
        $categories = $config->topnavCategories;
        $returnCode = $EAresults->getReturnCode();
        $numResults = $EAresults->getTotalItems();

        if ($EAresults->isRedirect()) {
            $url = $EAresults->getRedirect();

            return Redirect::away($url);
        }
        if ($numResults == -1 && (bool) $seopath) {
            // No results...
            $noResultsMessage = 'No results found';
            $noResultsSearches = 'No results searches found';

            if ($EAresults->getNoResultsPage() !== null) {
                $noResultsMessage = $EAresults->getNoResultsPage()->getMessage();
                $noResultsSearches = $EAresults->getNoResultsPage()->getSearches();
            }

            return compact('quietMode', 'EAresults', 'currentSEOPath', 'categories', 'config', 'noResultsMessage', 'noResultsSearches', 'conversationMode');
        }

        $navPath = $EAresults->getNavPath();
        $breadcrumbTrail = $EAresults->getBreadCrumbTrail();
        $banners = $EAresults->getDisplayBanners();
        $hasBanners = ! empty($banners)
            ? count($banners)
            : 0;
        $seopath = $EAresults->getCurrentSeoPath();
        $message = $EAresults->getMessage();

        $categories = $EAresults->getCategories();
        $attributes = $EAresults->getAttributes();
        $products = $EAresults->getProducts();
        $question = $EAresults->getQuestion();
        $commentary = $EAresults->getCommentary();
        $normalizedQuestion = $EAresults->getNormalizedQuestion();

        return compact('quietMode',
            'currentSEOPath',
            'categories',
            'attributes',
            'products',
            'EAresults',
            'pageTitle',
            'seopath',
            'config',
            'breadcrumbTrail',
            'banners',
            'hasBanners',
            'message',
            'navPath',
            'question',
            'normalizedQuestion',
            'commentary',
            'conversationMode');
    }

    public function storeProductDetail($seopath)
    {
        $conversationMode = false;
        $quietMode = true;
        $top2LevelCategories = $this->getTop2LevelCategories();
        $currentSEOPath = null;
        $config = $this->EAGetConfig();
        $categories = $config->topnavCategories;

        if ($seopath == '') {
            $noResultsMessage = 'No product id specified!';

            $noResultsSearches = null;

            // dd($noResultsSearches);
            return view('easyask::store.noResults', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'categories', 'config', 'noResultsMessage', 'noResultsSearches', 'conversationMode'));
        }

        $productID = (strpos($seopath, '/'))
            ? substr($seopath, strpos($seopath, '/') + 1)
            : $seopath;

        $EASPConnection = $this->EASetup();

        $config = session('ezshop-config');
        //       $detect = new Mobile_Detect;
        //       $searchPlaceholder = $detect->isMobile()? $config->shortSearchPlaceholder : $config->longSearchPlaceholder;
        $categories = $config->topnavCategories;
        // Setup options for no grouping...

        $opts = $EASPConnection->getOptions();
        $opts->setGrouping('///NONE///');
        $opts->setNavigateHierarchy(false);
        $opts->setSubCategories(false);
        $opts->setCustomerId('');
        $searchString = $config->ProductDetailSearch->Fieldname.' = ';
        $idVal = (isset($config->ProductDetailSearch->Type) && $config->ProductDetailSearch->Type == 'Text')
            ? "'".$productID."'"
            : $productID;
        $searchString = $searchString.$idVal;

        $EASPresults = $EASPConnection->userSearch('', $searchString);
        $numProducts = $EASPresults->getTotalItems();
        if ($numProducts != 1) {
            $noResultsMessage = 'Bad product id specified!';
            $noResultsSearches = null;

            // dd($noResultsSearches);
            return view('easyask::store.noResults', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'categories', 'config', 'noResultsMessage', 'noResultsSearches', 'conversationMode'));
        }
        //  Unpack & display results...
        $pageTitle = 'Product Details';
        $productName = $EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->productName));
        $imgURL = $EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->mainImageURL));
        $images = false;
        $imageList = '';
        $colors = false;
        $colorList = '';
        $sizes = false;
        $sizeList = '';
        if ($config->fieldNames->otherImagesList) {
            $otherImagesList =
                $EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->otherImagesList));
            if ($otherImagesList) {
                $imageList = explode($config->options->listSeparatorChar, $otherImagesList);

                $images = true;
            }
        }
        if ($config->fieldNames->sizeList) {
            $sizeListStr = $EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->sizeList));
            if ($sizeListStr) {
                $sizeList = explode($config->options->listSeparatorChar, $sizeListStr);
                $sizes = true;
            }
        }
        if ($config->fieldNames->colorList) {
            $colorListStr = $EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->colorList));
            if ($colorListStr) {
                $colorList = explode($config->options->listSeparatorChar, $colorListStr);
                $colors = true;
            }
        }
        $price = $EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->price));
        $averageRating = ($config->fieldNames->averageRating)
            ? $EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->averageRating))
            : 0;
        $prodCat = ($config->fieldNames->category)
            ? $EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->category))
            : '';
        $skuId = ($config->fieldNames->skuID)
            ? $EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->skuID))
            : '';
        $listPrice = $EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->listPrice));
        $productID = $EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->productID));
        $productURL = '/store/product/'
            .$EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->productURL))
            .'/'.$productID;
        $description = $EASPresults->getCellData(0, $EASPresults->getColumnIndex($config->fieldNames->productDesc));
        // dd($description);
        $EARelProdConnection = $this->EARelProdSetup();
        $EARelProdResults = $EARelProdConnection->getPromotions($productID, 'cross-sell');
        //       dd($EARelProdResults);
        $numRelResults = $EARelProdResults->getTotalItems();

        if ($numRelResults > 20) {
            $numRelResults = 20;
        }

        return view('easyask::store.storeProductDetail', compact('quietMode', 'currentSEOPath', 'top2LevelCategories', 'categories', 'numRelResults', 'EARelProdResults', 'prodCat', 'pageTitle', 'skuId', 'productName', 'imageList', 'imgURL', 'price', 'listPrice', 'productID', 'productURL', 'description', 'images', 'colors', 'colorList', 'sizes', 'averageRating', 'imageList', 'sizeList', 'config', 'conversationMode'));
    }

    public function getProductById($productID)
    {
        $EASPConnection = $this->EASetup();
        $config = eaShopConfig();
        $searchString = $config->ProductDetailSearch->Fieldname.' = '.$productID;
        $EASPresults = $EASPConnection->userSearch('', $searchString);

        if ($EASPresults->getTotalItems() > 0) {
            $product = $EASPresults->getProducts();
            $product->seoPath = $EASPresults->getCurrentSeoPath();

            return $product;
        }

        return null;
    }

    public function storeCategories()
    {
        $EACatConnection = $this->EASetup();
        $opts = $EACatConnection->getOptions();
        $opts->setResultsPerPage(1);
        $opts->setToplevelProducts(true);
        $opts->setGrouping('///NONE///');
        $opts->setNavigateHierarchy(false);
        $opts->setSubCategories(1);
        $EACatConnection->setOptions($opts);
        $EACatresults = $EACatConnection->userBreadcrumbClick('');

        // dd($EACatresults);
        //        dd($EACatresults->getCategories());
        return $top2LevelCategories = $EACatresults->getCategories()->categoryList ?? [];
    }

    public function getCategory()
    {
        $EACatConnection = $this->EASetup();
        $EACatresults = $EACatConnection->userBreadcrumbClick('');

        return $EACatresults->getCategories();
    }

    public function getSubCategoriesByCategory($category_name)
    {
        $EACatConnection = $this->EASetup();
        $opts = $EACatConnection->getOptions();
        $opts->setSubCategories(true);
        $EACatConnection->setOptions($opts);
        $EACatresults = $EACatConnection->userBreadcrumbClick($category_name);

        return $EACatresults->getCategories();
    }

    public function marchProducts($site_search, $paginatePerPage = 10)
    {
        // The call to create a RemoteEasyAsk object that allows you to access the remote server
        // to get the resultset of the search. We need to provide the server and port information
        $conversationMode = false;
        $quietMode = true;

        $EAConnection = $this->EASetup($paginatePerPage);
        $currentSEOPath = null;
        $config = $this->EAGetConfig();
        // $detect = new Mobile_Detect;
        //       $searchPlaceholder = $detect->isMobile()? $config->shortSearchPlaceholder : $config->longSearchPlaceholder;

        // $queryText is the search term you are searching for
        // Get the RemoteResults object for the search

        $EAresults = $EAConnection->userSearch('All Products', $site_search);
        $numResults = $EAresults->getTotalItems();
        if ($numResults == -1) {
            // No results...
            $noResultsMessage = $EAresults->getNoResultsPage() ? $EAresults->getNoResultsPage()->getMessage() : '';
            $noResultsSearches = $EAresults->getNoResultsPage() ? $EAresults->getNoResultsPage()->getSearches() : '';

            return [
                'quietMode' => $quietMode,
                'currentSEOPath' => $currentSEOPath,
                'config' => $config,
                'noResultsMessage' => $noResultsMessage,
                'noResultsSearches' => $noResultsSearches,
            ];
        }

        $navPath = $EAresults->getNavPath();
        $seopath = $EAresults->getCurrentSeoPath();
        $message = null;
        $products = $EAresults->getProducts();

        return compact('quietMode',
            'currentSEOPath',
            'products',
            'EAresults',
            'seopath',
            'config',
            'message',
            'navPath',
            'conversationMode');
    }

    private function getSortOption()
    {
        $sortOption = request('sort_by', '');
        if (! empty($sortOption) && stripos($sortOption, 'Relevance') === false) {
            $sortDirection = explode(' - ', $sortOption);
            if (! empty($sortDirection[1])) {
                $sortOption = $sortDirection[0].','.($sortDirection[1] == 'ASC' ? 't' : 'f');
            }
        }

        return $sortOption;
    }
}
