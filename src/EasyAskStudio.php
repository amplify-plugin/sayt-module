<?php

namespace Amplify\System\Sayt;

use Amplify\System\Sayt\Classes\RemoteEasyAsk;
use Amplify\System\Sayt\Classes\RemoteFactory;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Redirect;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
        $host = config('amplify.search.easyask_host');
        $port = intval(config('amplify.search.easyask_port', 80));
        $dictionary = config('amplify.search.easyask_dictionary');
        $protocol = config('amplify.search.protocol');

        if ($host == '' || $dictionary == '') {
            throw new \InvalidArgumentException('To use EasyAsk search engine you need to specify the host name and dictionary in system configuration');
        }

        $this->easyAsk = RemoteFactory::create($host, $dictionary, $port, $protocol);
    }

    /**
     * @return SessionManager|Store|mixed
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
        $rpp = request('per_page', $paginate_per_page);
        $rs = request('return_skus', 0);
        $currentPage = request('page', request('currentPage', 1));
        $groupId = '';

        if ($eahostname == '' || $eadictionary == '') {
            dd('To use easyask search engine you need to specify the host name and dictionary in EZ Shop confugration file');
        }

        dd(debug_backtrace());

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
     * @return SessionManager|Store|mixed
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
            //            $opts->setToplevelProducts(true);
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

    public static function getEaProductDetail()
    {
        return \Sayt::storeProductDetail(request('seopath', ''));
    }

    public function storeProducts($seoPath, array $options = [])
    {
        $resultPerPage = $options['per_page'] ?? getPaginationLengths()[0] ?? 12;
        $currentPage = $options['page'] ?? null;
        $returnSku = $options['return_skus'] ?? false;
        $groupId = $options['group'] ?? null;
        $groupBy = $options['group_by'] ?? null;
        $sortBy = $options['sort_by'] ?? null;
        $attribute = $options['attribute'] ?? null;
        $search = $options['q'] ?? null;

        // Get the Options object and set the appropriate options
        $eaOptions = $this->easyAsk->getOptions()
            ->setResultsPerPage($resultPerPage)
            ->setReturnSKUs($returnSku)
            ->setCurrentPage($currentPage)
            ->setCustomer(customer()?->toArray() ?? [])
            ->setCustomerId(customer()?->customer_erp_id ?? null)
            ->setGroupId($groupId)
            ->setNavigateHierarchy(false)
            ->setSubCategories(false)
            ->setSortOrder($sortBy);

        $this->easyAsk->setOptions($eaOptions);

        match (true) {
            $currentPage != null => $this->easyAsk->userGoToPage($seoPath, $currentPage),
            $attribute != null => $this->easyAsk->userAttributeClick($seoPath, $attribute),
            $search != null => $this->easyAsk->userSearch($seoPath, $search),
            default => $this->easyAsk->userBreadCrumbClick($seoPath)
        };

        $result = $this->easyAsk->urlPost();

        return $result;


//        //  Unpack & display results...
//        $pageTitle = '';
//        $categories = $config->topnavCategories;
//        $returnCode = $EAresults->getReturnCode();
//        $numResults = $EAresults->getTotalItems();
//
//        if ($EAresults->isRedirect()) {
//            $url = $EAresults->getRedirect();
//
//            return Redirect::away($url);
//        }
//
//        if ($numResults == -1 && (bool) $seoPath) {
//            // No results...
//            $noResultsMessage = 'No results found';
//            $noResultsSearches = 'No results searches found';
//
//            if ($EAresults->getNoResultsPage() !== null) {
//                $noResultsMessage = $EAresults->getNoResultsPage()->getMessage();
//                $noResultsSearches = $EAresults->getNoResultsPage()->getSearches();
//            }
//
//            return compact('quietMode', 'EAresults', 'currentSEOPath', 'categories', 'config', 'noResultsMessage', 'noResultsSearches', 'conversationMode');
//        }
//
//        $navPath = $EAresults->getNavPath();
//        $breadcrumbTrail = $EAresults->getBreadCrumbTrail();
//        $banners = $EAresults->getDisplayBanners();
//        $hasBanners = ! empty($banners)
//            ? count($banners)
//            : 0;
//        $seoPath = $EAresults->getCurrentSeoPath();
//        $message = $EAresults->getMessage();
//
//        $categories = $EAresults->getCategories();
//        $attributes = $EAresults->getAttributes();
//        $products = $EAresults->getProducts();
//        $question = $EAresults->getQuestion();
//        $commentary = $EAresults->getCommentary();
//        $normalizedQuestion = $EAresults->getNormalizedQuestion();
//
//        return compact('quietMode',
//            'currentSEOPath',
//            'categories',
//            'attributes',
//            'products',
//            'EAresults',
//            'pageTitle',
//            'seoPath',
//            'config',
//            'breadcrumbTrail',
//            'banners',
//            'hasBanners',
//            'message',
//            'navPath',
//            'question',
//            'normalizedQuestion',
//            'commentary',
//            'conversationMode');
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
        //        $opts->setToplevelProducts(true);
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

    private function getSortOption($sortOption)
    {


        return $sortOption;
    }
}
