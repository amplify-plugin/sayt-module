<?php

namespace Amplify\System\Sayt\Interfaces;

interface IFeaturedProducts
{
    // Returns the dataset for the FeaturedProducts
    public function getItems();

    // Returns the current number of products in the FeaturedProducts
    public function getProductCount();
}
