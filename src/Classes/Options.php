<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\IOptions;

// Used by RemoteEasyAsk to set global values for the instance.
class Options implements IOptions
{
    private $m_dictionary;

    private $m_navigateHierarcy = false;

    private $m_subCategories = false;

    private $m_toplevelProducts = false;

    private $m_returnSKUs = false;

    private $m_resultsPerPage = -1;

    private $m_currentPage = 1;

    private $m_sortOrder;

    private $m_grouping;

    private $m_calloutParam;

    // groupid and customerid are added for the sake of customized pricing
    private $m_groupId;

    private $m_customerId;

    private $m_customer;

    private $m_cusShipTo;

    private $m_curWhsId;

    private $m_altWhsIds;

    private $m_loginId;

    private $m_avail;

    private $m_subCategoryDepth = 1;

    private $m_includeProductCount = false;

    // Builds the options based off of the current dictionary
    public function __construct($dictionary)
    {
        $this->m_dictionary = $dictionary;
    }

    // Sets the dictionary for the EasyAsk instance.
    public function setDictionary($name)
    {
        $this->m_dictionary = $name;

        return $this;
    }

    // Gets the dictionary for the EasyAsk instance.
    public function getDictionary()
    {
        return $this->m_dictionary;
    }

    // Sets whether the instance has a Navigation Hierarchy
    public function setNavigateHierarchy($val)
    {
        $this->m_navigateHierarcy = $val;

        return $this;
    }

    // Returns if the instance has a Navigation Hierarchy
    public function getNavigateHierarchy()
    {
        return $this->m_navigateHierarcy;
    }

    // Sets whether the instance has sub categories
    public function setSubCategories($val)
    {
        $this->m_subCategories = (bool)$val ? '1' : '0';

        return $this;
    }

    // Returns if the instance has sub categories
    public function getSubCategories()
    {
        return $this->m_subCategories;
    }

    // Set whether the instance has top level products
    public function setToplevelProducts($val)
    {
        $this->m_toplevelProducts = $val;

        return $this;
    }

    // Returns if the instance has top level products
    public function getToplevelProducts()
    {
        return $this->m_toplevelProducts;
    }

    /**
     * Sets the current page
     */
    public function setCurrentPage($val)
    {
        $this->m_currentPage = $val;

        return $this;
    }

    /**
     * Returns the current page
     */
    public function getCurrentPage(): int
    {
        return $this->m_currentPage;
    }

    // Sets the current results per page
    public function setResultsPerPage($val)
    {
        $this->m_resultsPerPage = $val;

        return $this;
    }

    // Returns the current results per page
    public function getResultsPerPage()
    {
        return $this->m_resultsPerPage;
    }

    // Sets a sort order for the current results
    public function setSortOrder($val)
    {
        if (!empty($val) && stripos($val, 'Relevance') === false) {
            $sortDirection = explode(' - ', $val);
            if (!empty($sortDirection[1])) {
                $this->m_sortOrder = $sortDirection[0] . ',' . ($sortDirection[1] == 'ASC' ? 't' : 'f');
            }
        }

        return $this;
    }

    public function setStockAvail($val)
    {
        if (!empty($val) && $val === 'yes') {
            $this->m_avail = 1;
        }

        return $this;
    }

    public function getStockAvail()
    {
        return $this->m_avail;
    }

    // Gets the sort order of the current results
    public function getSortOrder()
    {
        return $this->m_sortOrder ?? '';
    }

    // Sets the Stock Keeping Unit for the instance
    public function setReturnSKUs($val)
    {
        $this->m_returnSKUs = $val;

        return $this;
    }

    // Return the Stock Keeping Unit for the instance
    public function getReturnSKUs()
    {
        return $this->m_returnSKUs;
    }

    // Set the grouping for the current results
    public function setGrouping($val)
    {
        $this->m_grouping = $val;

        return $this;
    }

    // Gets the grouping of the current result
    public function getGrouping()
    {
        return $this->m_grouping;
    }

    // Sets the Call out Parameter for the current instance
    public function setCallOutParam($val)
    {
        $this->m_calloutParam = $val;

        return $this;
    }

    // Gets the call out parameter for the current instance
    public function getCallOutParam()
    {
        return $this->m_calloutParam;
    }

    // Sets the groupid parameter for the current instance.
    public function setGroupId($groupId)
    {
        $this->m_groupId = $groupId;

        return $this;
    }

    // Gets the groupid parameter for the current instance
    public function getGroupId()
    {
        return $this->m_groupId;
    }

    // Sets the customerid parameter for the current instance.
    public function setCustomerId($customerId)
    {
        $this->m_customerId = $customerId;

        return $this;
    }

    // Gets the customerid parameter for the current instance
    public function getCustomerId()
    {
        return $this->m_customerId;
    }

    // Sets the customer parameter for the current instance.
    public function setCustomer($customer)
    {
        $this->m_customer = $customer;

        return $this;
    }

    // Gets the customer parameter for the current instance
    public function getCustomer()
    {
        return $this->m_customer;
    }

    public function getCustomerShipTo()
    {
        return $this->m_cusShipTo;
    }

    public function setCustomerShipTo($val)
    {
        $this->m_cusShipTo = $val;

        return $this;
    }

    public function getCurrentWarehouse()
    {
        return $this->m_cusShipTo;
    }

    public function setCurrentWarehouse($val)
    {
        $this->m_curWhsId = $val;

        return $this;
    }

    public function getAlternativeWarehouseIds()
    {
        return $this->m_altWhsIds;
    }

    public function setAlternativeWarehouseIds($val)
    {
        $this->m_altWhsIds = $val;

        return $this;
    }

    public function getLoginId()
    {
        return $this->m_loginId;
    }

    public function setLoginId($val)
    {
        $this->m_loginId = $val;

        return $this;
    }

    public function setSubCategoryDepth($depth = 0)
    {
        $this->m_subCategoryDepth = (string)$depth;

        return $this;
    }

    public function getSubCategoryDepth()
    {
        return $this->m_subCategoryDepth;
    }

    public function setIncludeProductCount(bool $val = false)
    {
        $this->m_includeProductCount = $val ? '1' : false;

        return $this;
    }

    public function getIncludeProductCount()
    {
        return $this->m_includeProductCount;
    }
}
