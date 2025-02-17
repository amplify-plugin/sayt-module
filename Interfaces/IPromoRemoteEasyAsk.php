<?php

namespace Amplify\System\Sayt\Interfaces;

interface IPromoRemoteEasyAsk
{
    // Returns IPromotions when the user performs a lookup for promotions for a product
    public function getPromotions($productId, $type);

    // Sets the options for the RemoteEasyAsk
    public function setOptions($val);

    // Gets the current Options being used by the RemoteEasyAsk
    public function getOptions();
}
