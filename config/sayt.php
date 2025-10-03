<?php

return [
    'sayt_product_id' => 'Product_Id',
    'sayt_product_image' => 'Product_Image',
    'sayt_product_name' => 'Product_Name',
    'sayt_product_price' => 'Price',
    'sayt_product_description' => 'Short_Description',
    'sayt_product_type' => 'Type_Id',
    'sayt_product_sizes' => 'Sku_Sizes',
    'sanitize_product_name_callbacks' => [

    ],
    'product_search_by_id_prefix' => 'Products.Product Id',
    'search_box_placeholder' => 'Search Product all',
    'default_catalog' => null,
    'use_product_restriction' => false,
    'dictionary' => [
        'host' => env('EA_HOST', 'demoV16.easyaskondemand1.com'),
        'port' => env('EA_PORT', null),
        'dictionary' => env('EA_DICTIONARY', 'amplify-demo'),
        'protocol' => env('EA_PROTOCOL', 'http'),
    ]
];
