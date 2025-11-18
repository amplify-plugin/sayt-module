<?php

namespace Amplify\System\Sayt\Interfaces;

interface IPromoOptions
{
    // Sets the dictionary file (.dxp) name that this EasyAsk instance will reference.
    public function setDictionary($name);

    // Gets the dictionary file name that this EasyAsk instance is referencing.
    public function getDictionary();

    // Sets the number of results to be displayed per page of the EasyAsk display.
    public function setResultsPerPage($val);

    // Gets the current number of results to be displayed per page.
    public function getResultsPerPage();

    // Sets the callout params value
    public function setCallOutParam($val);

    // Gets the call out params value
    public function getCallOutParam();

    // Sets the groupid parameter for the current instance.
    public function setGroupId($groupId);

    // Gets the groupid parameter for the current instance
    public function getGroupId();

    // Sets the customer id parameter for the current instance.
    public function setCustomerId($customerId);

    // Gets the customer id parameter for the current instance
    public function getCustomerId();
}
