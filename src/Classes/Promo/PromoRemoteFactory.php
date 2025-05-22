<?php

namespace Amplify\System\Sayt\Classes\Promo;

// Create a new RemoteEasyAsk instance on website startup

class PromoRemoteFactory
{
    public static function create($hostName, $port, $dictionary, $protocol)
    {
        return new PromoRemoteEasyAsk($hostName, $port, $dictionary, $protocol);
    }
}
