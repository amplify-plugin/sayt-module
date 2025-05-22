<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\INavigateResults;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Serves as the xml document that holds search results
class RemoteResults implements INavigateResults
{
    private $m_doc;

    private $m_attrsInfo = null;

    private $m_catPath = null;

    private $m_seoPath = null;

    private $m_itemDescriptions = null;

    private $m_items;

    private $m_bct = null;

    private $m_bHierarachyProcessed = false;

    private $m_navHier = null;

    private $m_commonAttributes = null;

    private $m_commentary = null;

    private $m_displayFormat = null;

    private $m_catInfo = null;

    private $m_groupSet = null;

    private $m_isGrouped = false;

    private $m_featuredProducts = null;

    private $m_carveOuts = null;

    private $m_arrangedByChoices = null;

    private $m_banners = null;

    private $m_displaybanners = null;

    private $m_stateInfo = null;

    private $m_noResultsInfo = null;

    // Creates a new instance.
    public function __construct()
    {
        $this->m_doc = new \DOMDocument;
    }

    // Loads a URL into the instance, then determines the appropriate results and layout.
    public function load($url)
    {
        $this->m_doc = $this->url_get_contents_json($url);

        $this->determineLayout();
    }

    /**
     * @throws \Exception
     */
    public function url_get_contents_json($Url): object
    {
        try {
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->accept('application/json')
                ->get($Url);

            return json_decode(($response->body() ?? '{}'), false, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $exception) {
            if (suppress_exception()) {
                Log::error('EA Remote Result url_get_contents_json Method  Exception : '.$exception->getMessage());
            } else {
                throw new \Exception($exception->getMessage(), 500, $exception);
            }

            return new \stdClass;
        }
    }

    public function valid_response_code($httpcode)
    {
        $resp = false;
        if (($httpcode == 200) || ($httpcode == 301) || ($httpcode == 302)) {
            $resp = true;
        }

        return $resp;
    }

    // If there is a return code json node in the doc, returns the contained code.
    public function getReturnCode()
    {
        $nodeRC = $this->m_doc->returnCode ?? null;
        if ($nodeRC >= 0) {
            return $nodeRC;
        }

        return -1;
    }

    // If an error message currently exists in the RemoteRestults, returns it.
    public function getErrorMsg()
    {
        $node = $this->m_doc->errorMsg;

        return $node != null
            ? $node
            : null;
    }

    // If a message currently exists in the RemoteRestults, returns it.
    public function getMessage()
    {
        $node = $this->m_doc?->source?->message ?? null;

        return $node != null
            ? $node
            : null;
    }

    /**
     * Returns the Search Engine Optimization path based off of the current bread crumb trail.
     *
     * @return string
     */
    public function getCatPath()
    {
        $purePath =
            $this->m_doc->source->navPath->navPathNodeList[count($this->m_doc->source->navPath->navPathNodeList)
            - 1]->purePath;

        return $this->m_catPath = ($purePath
            ? $purePath
            : 'All Products');
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->getCatPath();
    }

    // Returns the Search Engine Optimization path based off of the current bread crumb trail.
    public function getCurrentSeoPath()
    {
        $seoPath = ! empty($this->m_doc->source) ? $this->m_doc->source->navPath->navPathNodeList[count($this->m_doc->source->navPath->navPathNodeList)
        - 1]->seoPath : '';

        return $this->m_seoPath = ($seoPath
            ? $seoPath
            : '');
    }

    // Creates a new CategoriesInfo instance based off of the xml doc
    private function processCategories()
    {
        if ($this->m_catInfo == null) {
            $this->m_catInfo = new CategoriesInfo($this->m_doc->source);
        }
    }

    // Returns the current list of ResultCategory
    public function getDetailedCategories($nDisplayMode)
    {
        $this->processCategories();

        return $this->m_catInfo->getDetailedCategories($nDisplayMode);
    }

    // Returns the current list of ResultCategory
    public function getDetailedCategoriesFull()
    {
        return $this->getDetailedCategories(0);
    }

    // Returns the initial list size for categories
    public function getInitDisplayLimitForCategories()
    {
        $this->processCategories();

        return $this->m_catInfo->getInitDisplayLimitForCategories();
    }

    // Return a string for the suggested category name (common parent)
    public function getSuggestedCategoryTitle()
    {
        $this->processCategories();

        return $this->m_catInfo->getSuggestedCategoryTitle();
    }

    public function getSuggestedCategoryID()
    {
        $this->processCategories();

        return $this->m_catInfo->getSuggestedCategoryID();
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->m_doc->source->products ?? [];
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->m_doc->source->attributes ?? [];
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->m_doc->source->categories ?? [];
    }

    /**
     * Creates an ItemDescriptions instance for the xmlDoc
     */
    private function processItemDescriptions()
    {
        if ($this->m_itemDescriptions == null) {
            if (isset($this->m_doc->source->products)) {
                $node = $this->m_doc->source->products->itemDescription;
                if ($node) {
                    $this->m_itemDescriptions = new ItemDescriptions($node);
                } else {
                    $this->m_itemDescriptions = new ItemDescriptions(null);
                }
            } else {
                $this->m_itemDescriptions = new ItemDescriptions(null);
            }
        }
    }

    /**
     * Returns an ItemDescriptions instance for the xmlDoc
     */
    public function getItemDescriptions()
    {
        $this->processItemDescriptions();

        return $this->m_itemDescriptions;
    }

    /**
     * Returns to total number of pages needed to hold the results of the INavigateResults.
     *
     * @return mixed
     */
    public function getPageCount()
    {
        return $this->getItemDescriptions()->getPageCount();
    }

    // Gets the index of the current page of results that the INavigateResults is displaying.
    public function getCurrentPage()
    {
        return $this->getItemDescriptions()->getCurrentPage();
    }

    public function getIsDrillDown()
    {
        return $this->getItemDescriptions()->getIsDrillDown();
    }

    // Gets the total number of items currently contained within the INavigateResults.
    public function getTotalItems()
    {
        return $this->getItemDescriptions()->getTotalItems();
    }

    // Returns the current number of results per page.
    public function getResultsPerPage()
    {
        return $this->getItemDescriptions()->getResultsPerPage();
    }

    // Returns the index of the first result item.
    public function getFirstItem()
    {
        return $this->getItemDescriptions()->getFirstItem();
    }

    // Returns the index of the last result item.
    public function getLastItem()
    {
        return $this->getItemDescriptions()->getLastItem();
    }

    // Returns the current sort order, if any, for the results
    public function getSortOrder()
    {
        return $this->getItemDescriptions()->getSortOrder();
    }

    // Returns a list of data descriptions for the xmlDoc
    public function getDataDescriptions()
    {
        return $this->getItemDescriptions()->getDataDescriptions();
    }

    public function getResultCount()
    {
        return $this->getTotalItems();
    }

    // Creates a list of itemRows based off of the search.
    public function processItems()
    {
        if ($this->m_items == null) {
            $this->m_items = [];
            if (! $this->m_isGrouped) {
                $items = $this->m_doc->source->products->items;
                if ($items) {
                    foreach ($items as $item) {
                        $this->m_items[] = new ItemRow($this->getDataDescriptions(), $item);
                    }
                }
            }
        }
    }

    // Retrieves the data stored within an itemrow from the current page.
    public function getCellData($row, $col)
    {
        $this->processItems();
        $adjust = ($this->getCurrentPage() - 1) * $this->getResultsPerPage();

        return $this->m_items[$row - $adjust]->getFormattedText($col);
    }

    // Returns the index of a column contained within the ItemDescriptions
    public function getColumnIndex($colName)
    {
        return $this->getItemDescriptions()->getColumnIndex($colName);
    }

    // Processes the bread crumb trail for the current search.
    public function processBreadCrumbTrail()
    {
        $node = ! empty($this->m_doc->source->navPath) ? $this->m_doc->source->navPath : null;
        if ($node) {
            $this->m_bct = new BreadCrumbTrail($node);
        } else {
            $this->m_bct = new BreadCrumbTrail(null);
        }
    }

    // Returns the bread crumb trail for the current search.
    public function getBreadCrumbTrail()
    {
        $this->processBreadCrumbTrail();

        return $this->m_bct;
    }

    // Whether the use is currently looking at the top level of the search
    public function getAtTopNode()
    {
        return $this->m_doc->source->atTopNode;
    }

    public function getProductsFromGlobalSearch()
    {
        return $this->m_doc->source->productsFromGlobalSearch;
    }

    // Returns true if products could not be found in the current context, but were found by modifying the user query.
    public function getItemsFoundByModifyingQuery()
    {
        return $this->m_doc->source->itemsFoundByModifyingQuery;
    }

    // Returns true if products/items were found through a secondary search.
    public function getItemsFoundWIthSecondarySearch()
    {
        return $this->m_doc->source->itemsFoundWithSecondarySearch;
    }

    // Returns the method in which the product listing was obtained.
    public function getProductRetrievalMethod()
    {
        return $this->m_doc->source->productRetrievalMethod;
    }

    // Returns the method in which the attribute listing was obtained.
    public function getAttributeRetrievalMethod()
    {
        return $this->m_doc->source->attributeRetrievalMethod;
    }

    public function getQuestion()
    {
        return $this->m_doc->source->question->question ?? '';
    }

    public function getOriginalQuestion()
    {
        return isset($this->m_doc->source->originalQuestion)
            ? $this->m_doc->source->originalQuestion
            : '';
    }

    public function getNormalizedQuestion()
    {
        return $this->m_doc->source->normalizedQuestion ?? '';
    }

    public function getIsCommand()
    {
        return $this->m_doc->source->question->isCommand;
    }

    // Processes a NavigateHierarchy based off of the xmlDoc
    public function processNavigateHierarchy()
    {
        if (! $this->m_bHierarachyProcessed) {
            $hier = $this->m_doc->source->navigateHierarchy->navHierNode;
            if ($hier) {
                $this->m_navHier = new NavigateHierarchy($hier);
            }
            $this->m_bHierarachyProcessed = true;
        }
    }

    // Returns the current NavigateHierarchy for the search.
    public function getNavigateHierarchy()
    {
        $this->processNavigateHierarchy();

        return $this->m_navHier;
    }

    // Returns the current NavigateHierarchy for the search.
    public function getNavPath()
    {
        return ! empty($this->m_doc->source->navPath) ? $this->m_doc->source->navPath : null;
    }

    // Processes the attributes into an AttributeInfo for the current search.
    public function processAttributes()
    {
        if ($this->m_attrsInfo == null) {
            $this->m_attrsInfo = isset($this->m_doc->source->attributes)
                ? new AttributesInfo($this->m_doc->source->attributes)
                : new AttributesInfo(null);
        }
    }

    // Returns the AttributeInfor for the current search based off of the xmlDoc.
    public function getAttributeInfo($attrNode)
    {
        $results = [];
        if ($attrNode) {
            $attrs = $attrNode->attribute;
            foreach ($attrs as $attr) {
                $results[] = new AttributeInfo($attr);
            }
        }

        return $results;
    }

    // Creates an AttributeInfo for
    public function processCommonAttributes()
    {
        if ($this->m_commonAttributes == null) {
            $this->m_commonAttributes = isset($this->m_doc->source->commonAttribute)
                ? new AttributesInfo($this->m_doc->source->commonAttribute)
                : new AttributesInfo(null);
        }
    }

    // Returns whether the Initial Display settings is limited against displaying a certain attribute.
    public function isInitialDispLimitedForAttrNames()
    {
        $this->processAttributes();

        return $this->m_attrsInfo->isInitialDispLimitedForAttrNames();
    }

    // Returns the initial display mode for an attribute's value
    public function getInitialDispLimitForAttrNames()
    {
        $this->processAttributes();

        return $this->m_attrsInfo->getInitialDispLimitForAttrNames();
    }

    public function getInitialDisplayList($attrType)
    {
        $this->processAttributes();

        return $this->m_attrsInfo->getInitialDisplayList($attrType);
    }

    // Returns a list of attribute names of the specified type.
    public function getAttributeNames($attrFilter, $displayMode)
    {
        $this->processAttributes();

        return $this->m_attrsInfo->getAttributeNames($attrFilter, $displayMode);
    }

    // Returns a vector of NavigateAttribute objects for the specified attribute name for a specified group.
    // NavigateAttribute objects contain additional information about each attribute value.
    public function isInitialDispLimitedForAttrValues($attrName)
    {
        $this->processAttributes();

        return $this->m_attrsInfo->isInitialDispLimitedForAttrValues($attrName);
    }

    // Returns the initial display mode for an attribute's value
    public function getInitialDispLimitForAttrValues($attrName)
    {
        $this->processAttributes();

        return $this->m_attrsInfo->getInitialDispLimitForAttrValues($attrName);
    }

    // Returns if the attribute is a range filter
    public function isRangeFilter($attrName)
    {
        $this->processAttributes();

        return $this->m_attrsInfo->isRangeFilter($attrName);
    }

    // Returns a vector of NavigateAttribute objects for the specified attribute name for a specified group.
    // NavigateAttribute objects contain additional information about each attribute value.
    public function getDetailedAttributeValues($attrName, $displayMode)
    {
        $this->processAttributes();

        return $this->m_attrsInfo->getDetailedAttributeValues($attrName, $displayMode);
    }

    // Returns a list of all attribute names in the current search
    public function getAttributeNamesFull()
    {
        return $this->getAttributeNames(1, 0);
    }

    // Returns a list of NavigateAttribute objects for the specified attribute name.
    // NavigateAttribute objects contain additional information about each attribute value.
    // Returns the full list.
    public function getDetailedAttributeValuesFull($attrName)
    {
        $this->processAttributes();

        return $this->m_attrsInfo->getDetailedAttributeValues($attrName, 0);
    }

    // Returns a corresponding AttributeInfo instance for an attribute
    public function getCommonAttrInfo($attrName)
    {
        $this->processCommonAttributes();
        if (isset($this->m_commonAttributes)) {
            return $this->m_commonAttributes->getDetailedAttributeValues($attrName, 0);
        }

        return null;
    }

    private $NODE_ATTRIB_SELECT = '////AttribSelect=';

    private $splitValSep = ';;;;';

    // Returns whehter an attribute was selected by the user.
    public function wasAttributeSelected($attrName)
    {
        foreach ($this->getBreadCrumbTrail()->getSearchPath() as $node) {
            if ($node->getType() == 2) {
                $path = $node->getPath();
                $idx = strpos($path, $this->NODE_ATTRIB_SELECT);
                if ($idx >= 0) {
                    //					$vals = path.Substring(idx + NODE_ATTRIB_SELECT.Length).Split(splitValSep, StringSplitOptions.None);
                    $vals = explode($this->splitValSep, substr($path, $idx + count($this->NODE_ATTRIB_SELECT)));
                    for ($i = 0; $i < count($vals); $i++) {
                        if (stripos($vals[$i], $attrName." = '") == 0) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    // Returns a vector of the attribute names that are common to the results and normally not displayed.
    // The combination of getAttributeNames and getCommonAttributeNames covers all the attributes for the set.
    public function getCommonAttributeNames($onlySelected)
    {
        $this->processCommonAttributes();
        $results = [];

        return $this->m_commonAttributes->getAttributeNames(1, 0);
    }

    // Returns a list of NavigateAttribute objects for the specified common attribute name.
    // NavigateAttribute objects conatin additional information about each attribute value.
    public function getDetailedCommonAttributeValues($attrName, $displayMode)
    {
        $this->processCommonAttributes();
        if (isset($this->m_commonAttributes)) {
            return $this->m_commonAttributes->getDetailedAttributeValues($attrName, 0);
        }

        return null;
    }

    // Returns a list of NavigateAttribute objects for the specified common attribute name.
    // NavigateAttribute objects conatin additional information about each attribute value.
    public function getDetailedCommonAttributeValuesFull($attrName)
    {
        return $this->getDetailedCommonAttributeValues($attrName, 0);
    }

    public function getCommentary()
    {
        if (! $this->m_commentary) {
            $node = isset($this->m_doc->source->commentary)
                ? $this->m_doc->source->commentary
                : '';
            $this->m_commentary = $node;
        }

        return $this->m_commentary;
    }

    public function splitCommentary($key, $end)
    {
        $commentary = $this->getCommentary();
        $result = '';
        $idx = strpos($commentary, $key);
        if ($idx !== false) {
            $result = substr($commentary, $idx + count(key));
            $idx = strpos($result, $end);
            if ($idx !== false) {
                $result = substr($result, 0, $idx);
            }
        }

        return $result;
    }

    private $SPELL_CORRECTION_PREFACE = 'Corrected Words:';

    private $COMMENTARY_SECTION_END = ';';

    // Gets suggested spell corrections for the current search terms
    public function getSpellCorrections()
    {
        return $this->splitCommentary($this->SPELL_CORRECTION_PREFACE, $this->COMMENTARY_SECTION_END);
    }

    private $LIST_SEP = ',';

    private $CORRECTION_SEP = ' is ';

    // Gets a list of any words that were corrected in the search
    public function getCorrectedWords()
    {
        $spells = explode($this->LIST_SEP, $this->getSpellCorrections());
        $results = [];
        for ($i = 0; $i < count(spells); $i++) {
            $parts = explode($spells[$i], $this->CORRECTION_SEP);
            $results[] = trim($parts[0]);
        }

        return $results;
    }

    // Checks for a correction for a search word. Will return null if there are no corrections.
    public function getCorrection($word)
    {
        $spells = explode($this->getSpellCorrections(), $this->LIST_SEP);
        for ($i = 0; $i < count($spells); $i++) {
            $parts = explode($spells[$i], $this->CORRECTION_SEP);
            if (strcmp(trim($parts[0]), $word) == 0) {
                return trim($parts[1]);
            }
        }

        return null;
    }

    private $RELAXATION_PREFACE = 'Ignored:';

    public function getRelaxedTerms()
    {
        $terms =
            explode($this->splitCommentary($this->RELAXATION_PREFACE, $this->COMMENTARY_SECTION_END), $this->LIST_SEP);
        $results = [];
        for ($i = 0; $i < count($terms); $i++) {
            $results[] = trim($terms[$i]);
        }

        return $results;
    }

    // Sets the RemoteResult display format to that contained within the xmlDoc
    public function processDisplayFormat()
    {
        if (! $this->m_displayFormat) {
            $this->m_displayFormat = $this->getReturnCode() === 0
                ? new DisplayFormat($this->m_doc->source->displayFormat)
                : new DisplayFormat($this->m_doc->displayFormat ?? null);
        }
    }

    public function isPresentationError()
    {
        $this->processDisplayFormat();

        return ! $this->m_displayFormat
            ? false
            : $this->m_displayFormat->isPresentationError();
    }

    public function isRedirect()
    {
        $this->processDisplayFormat();

        return ! $this->m_displayFormat
            ? false
            : $this->m_displayFormat->isRedirect();
    }

    public function getRedirect()
    {
        $this->processDisplayFormat();

        return $this->isRedirect()
            ? $this->getErrorMsg()
            : null;
    }

    private function getFirstChild($node)
    {
        foreach ($node->children() as $child) {
            if ($child) {
                return $child;
            }
        }

        return null;
    }

    // Figures the layout of the results. How to group them, etc.
    public function determineLayout()
    {
        $this->m_isGrouped = ! empty($this->m_doc->source->products->groups);
    }

    // Is this RemoteResult a GroupedResult
    public function isGroupedResult()
    {
        return $this->m_isGrouped;
    }

    // Creates a GroupedSetInfo instance for the current instance.
    public function processGroups()
    {
        if ($this->m_groupSet == null && $this->m_isGrouped) {
            $this->m_groupSet = new GroupedSetInfo($this->m_doc->source->products->groups, $this);
        }
    }

    // Returns a GroupedSetInfo for the current search results
    public function getGroupedResult()
    {
        $this->processGroups();

        return $this->m_groupSet;
    }

    // Returns an ItemRow from the currently displayed page
    public function getRow($pageRow)
    {
        $this->processItems();

        return $this->m_items[$pageRow];
    }

    // Returns a list of carveout objects for the current search result
    public function getCarveOuts()
    {
        if ($this->m_carveOuts == null) {
            $this->m_carveOuts = [];
            foreach ($this->m_doc->source->carveOuts as $carveOut) {
                $this->m_carveOuts[] = new CarveOut($this, $carveOut);
            }
        }

        return $this->m_carveOuts;
    }

    // Returns a ResultsRowGroup that contains the featured products. Null means none.
    public function getFeaturedProducts()
    {
        if ($this->m_featuredProducts == null) {
            $this->m_featuredProducts = new FeaturedProducts($this, $this->m_doc->source->featuredProducts);
        }

        return $this->m_featuredProducts;
    }

    // Returns a list of possible arrange by choices. The result set can be arranged by one of these choices.
    // The value GroupedResultSet.GROUP_NO_GROUPING is returned as a choice for not grouping.
    public function getArrangeByChoices()
    {
        if ($this->m_arrangedByChoices == null) {
            $this->m_arrangedByChoices = [];
            $cats = $this->getDetailedCategoriesFull();
            if (count($cats) > 0) {
                $this->m_arrangedByChoices[] = 'Category';
            }
            $attrNames = $this->getAttributeNamesFull();
            foreach ($attrNames as $name) {
                $vals = $this->getDetailedAttributeValuesFull($name);
                if (count($vals) > 1) {
                    $this->m_arrangedByChoices[] = $name;
                } elseif (count($vals) == 1 && $vals[0]->getProductCount() < $this->getTotalItems()) {
                    $this->m_arrangedByChoices[] = $name;
                }
            }
        }

        return $this->m_arrangedByChoices;
    }

    // Returns the original question asked.
    public function getOriginalQuestionAsked()
    {
        $originalQuestionAsked = '';
        $searchPath = $this->getBreadCrumbTrail()->getSearchPath();
        foreach ($searchPath as $path) {
            if ($path->getType() === 3) {
                $originalQuestionAsked = $path->getValue();
            }
        }

        return $originalQuestionAsked;
    }

    // Processes the latest version of banners into an Banners for the current search.
    private function processDisplayBanners()
    {
        if ($this->m_displaybanners == null) {
            $displayBanners = isset($this->m_doc->source->displayBanners)
                ? $this->m_doc->source->displayBanners
                : null;
            if ($displayBanners) {
                $this->m_displaybanners = [];
                foreach ($displayBanners as $displayBanner) {
                    $this->m_displaybanners[] = new DisplayBanner($displayBanner);
                }
            } else {
                $this->m_displaybanners = null;
            }
        }
    }

    // Returns if there is a banner associated with this Category/Attribute
    public function hasDisplayBanner($type)
    {
        $this->processDisplayBanners();

        return isset($this->m_displaybanners)
            ? $this->m_displaybanners->hasBanner($type)
            : false;
    }

    // Return Banner Information
    public function getDisplayBanner($type)
    {
        $this->processDisplayBanners();

        return isset($this->m_displaybanners)
            ? $this->m_displaybanners->getBanner($type)
            : null;
    }

    // Return Banner Information
    public function getDisplayBanners()
    {
        $this->processDisplayBanners();

        return isset($this->m_displaybanners)
            ? $this->m_displaybanners
            : null;
    }

    // Process state info
    private function processStateInfo()
    {
        if ($this->m_stateInfo == null) {
            $stateInfos = $this->m_doc->source->stateInfo ?? null;
            if ($stateInfos) {
                $this->m_stateInfo = [];
                foreach ($stateInfos as $index => $stateInfo) {
                    if (in_array($index, [0, 1])) {
                        continue;
                    }
                    $this->m_stateInfo[] = new StateInfo($stateInfo);
                }
            }
            else {
                $this->m_stateInfo = null;
            }
        }
    }

    // Returns the stateinfo
    public function getStateInfo()
    {
        $this->processStateInfo();

        return isset($this->m_stateInfo)
            ? $this->m_stateInfo
            : null;
    }

    // Process no results information
    private function processNoResultsInfo()
    {
        if ($this->m_noResultsInfo == null) {
            $this->m_noResultsInfo = isset($this->m_doc->source->noResults)
                ? $this->m_doc->source->noResults
                : null;
        }
    }

    // Return the no results page info
    public function getNoResultsPage()
    {
        $this->processNoResultsInfo();

        return isset($this->m_noResultsInfo)
            ? new NoResultsInfo($this->m_noResultsInfo)
            : null;
    }

    // Returns if there is a no results node
    public function hasNoResultsPage()
    {
        $this->processNoResultsPage();

        return isset($this->m_noResultsInfo)
            ? true
            : false;
    }
}
