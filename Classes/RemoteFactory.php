<?php

namespace Amplify\System\Sayt\Classes;

// Create a new RemoteEasyAsk instance on website startup

class RemoteFactory
{
    public static function create($hostName, $port, $dictionary, $protocol)
    {
        return new RemoteEasyAsk($hostName, $port, $dictionary, $protocol);
    }
}
