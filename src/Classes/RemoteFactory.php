<?php

namespace Amplify\System\Sayt\Classes;

class RemoteFactory
{
    public static function createStudio(?string $hostName, ?string $dictionary, ?string $port, ?string $protocol = 'http'): RemoteEasyAsk
    {
        return new RemoteEasyAsk($hostName, $port, $dictionary, $protocol);
    }
    public static function createSuggestion(?string $hostName, ?string $dictionary, ?string $port, ?string $protocol = 'http'): RemoteAutoComplete
    {
        return new RemoteAutoComplete($hostName, $port, $dictionary, $protocol);
    }
}
