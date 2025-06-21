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
        function ($value) {
            return $value;
        },
    ],
];
