<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\iPromoOptions;

// Used by RemoteEasyAsk to set global values for the instance.
class PromoOptions implements IPromoOptions
{
    private $m_dictionary = null;

    private $m_resultsPerPage = -1;

    private $m_calloutParam = null;

    // groupid and customerid are added for the sake of customized pricing
    private $m_groupId = null;

    private $m_customerId = null;

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
}
