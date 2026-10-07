<?php

return [
    'sanitize_product_name_callbacks' => [],
    'product_search_by_id_prefix' => 'Products.Product Id',
    'search_box_placeholder' => 'Search by EasyAsk',
    'default_catalog' => null,
    'use_product_restriction' => false,
    'use_multiple_catalog' => false,
    'catalog_cache_key' => 'site_catalog',
    'dictionary' => [
        'host' => env('EA_HOST', 'demoV16.easyaskondemand1.com'),
        'port' => env('EA_PORT', null),
        'dictionary' => env('EA_DICTIONARY', 'amplify-demo'),
        'protocol' => env('EA_PROTOCOL', 'http'),
    ],
    'suggestion_limit'=> 5,
    'enabled' => env('EA_ENABLED', true),
];
