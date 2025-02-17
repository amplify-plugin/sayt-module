<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\IFeaturedProducts;

// Contains the featured products
class FeaturedProducts implements IFeaturedProducts
{
    private $m_node = null;

    private $m_res = null;

    private $m_items = null;

    private $m_productCount = -1;

    // Builds a featured items instance off of an appropriate xml node
    public function __construct($res, $node)
    {
        $this->m_node = $node;
        $this->m_res = $res;
        if ($this->m_node != null) {
            $this->m_productCount = $node->productCount;
        }
    }

    // Adds all featured products contained within the xml node to a list.
    // There will be no data in this instance until this method is run.
    private function processItems()
    {
        if ($this->m_items == null) {
            $this->m_items = [];
            if ($this->m_node != null) {
                foreach ($this->m_node->item as $item) {
                    $this->m_items[] = new ItemRow($this->m_res->getDataDescriptions(), $item);
                }
            }
        }
    }

    // Returns the list of featured items
    public function getItems()
    {
        $this->processItems();

        return $this->m_items;
    }

    // Returns a count of the current featured products.
    public function getProductCount()
    {
        return $this->m_productCount;
    }
}
