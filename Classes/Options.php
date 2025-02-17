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

    // Builds the options based off of the current dictionary
    public function __construct($dictionary)
    {
        $this->m_dictionary = $dictionary;
    }

    // Sets the dictionary for the EasyAsk instance.
    public function setDictionary($name)
    {
        $this->m_dictionary = $name;
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
    }

    // Returns if the instance has a Navigation Hierarchy
    public function getNavigateHierarchy()
    {
        return $this->m_navigateHierarcy;
    }

    // Sets whether the instance has sub categories
    public function setSubCategories($val)
    {
        $this->m_subCategories = $val;
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
    }

    // Returns the current results per page
    public function getResultsPerPage()
    {
        return $this->m_resultsPerPage;
    }

    // Sets a sort order for the current results
    public function setSortOrder($val)
    {
        $this->m_sortOrder = $val;
    }

    // Gets the sort order of the current results
    public function getSortOrder()
    {
        return urlencode(($this->m_sortOrder ?? ''));
    }

    // Sets the Stock Keeping Unit for the instance
    public function setReturnSKUs($val)
    {
        $this->m_returnSKUs = $val;
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
    }

    // Gets the customer parameter for the current instance
    public function getCustomer()
    {
        return $this->m_customer;
    }
}
