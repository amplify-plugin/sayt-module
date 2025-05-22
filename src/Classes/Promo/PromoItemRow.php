<?php

namespace Amplify\System\Sayt\Classes\Promo;

use Amplify\System\Sayt\Interfaces\iPromoResultRow;

// Contains product data in columns. Used to display products.
class PromoItemRow implements IPromoResultRow
{
    private $m_items;

    // Creates the ItemRow
    public function __construct($desc, $item)
    {
        $this->m_items = [];
        foreach ($desc as $dd) {
            $tagname = $dd->getTagName();
            $val = isset($item->$tagname) ? $item->$tagname : '';
            if ($val) {
                $this->m_items[] = $val;
            } else {
                $this->m_items[] = '';
            }
        }
    }

    // Returns the data contained in a specific column
    public function getFormattedText($col)
    {
        return $col >= 0 ? (string) $this->m_items[$col] : '';
    }

    // Returns the data contained in a specific column
    public function getCellData($col)
    {
        return $this->m_items[$col];
    }

    // Returns the amount of columns contained within the row
    public function size()
    {
        return count($this->m_items);
    }
}
