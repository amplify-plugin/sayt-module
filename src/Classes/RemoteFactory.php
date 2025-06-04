<?php

namespace Amplify\System\Sayt\Classes;

// Create a new RemoteEasyAsk instance on website startup

class RemoteFactory
{
    public static function create(?string $hostName, ?string $dictionary, ?int $port = 80, ?string $protocol = 'http'): RemoteEasyAsk
    {
        return new RemoteEasyAsk($hostName, $port, $dictionary, $protocol);
    }
}
