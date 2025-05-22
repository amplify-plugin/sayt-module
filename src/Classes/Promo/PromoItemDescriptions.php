<?php

namespace Amplify\System\Sayt\Classes\Promo;

// Contains information about the current product items.

class PromoItemDescriptions
{
    private $m_isDrillDownActive = false;

    private $m_pageCount = -1;

    private $m_currentPage = -1;

    private $m_totalItems = -1;

    private $m_resultsPerPage = -1;

    private $m_firstItem = -1;

    private $m_lastItem = -1;

    private $m_sortOrder = '';

    private $m_descs = null;

    // Makes a new list of item descriptions and populates from an xml node
    public function __construct($node)
    {
        if (! $node) {
            $this->m_descs = [];
        } else {
            $this->m_isDrillDownActive = $node->isDrillDownActive;
            $this->m_pageCount = $node->pageCount;
            $this->m_currentPage = $node->currentPage;
            $this->m_totalItems = $node->totalItems;
            $this->m_resultsPerPage = isset($node->resultsPerPage) ? $node->resultsPerPage : 0;
            $this->m_firstItem = isset($node->firstItem) ? $node->firstItem : 0;
            $this->m_lastItem = isset($node->lastItem) ? $node->lastItem : 0;
            $this->m_sortOrder = isset($node->sortOrder) ? $node->sortOrder : '';

            $itemDescs = isset($node->dataDescription) ? $node->dataDescription : null;
            $this->m_descs = [];
            if (isset($itemDescs)) {
                foreach ($itemDescs as $desc) {
                    $this->m_descs[] = new PromoDataDescription($desc);
                }
            }
        }
    }

    public function getIsDrillDown()
    {
        return $this->m_isDrillDownActive;
    }

    // Returns the total number of results pages
    public function getPageCount()
    {
        return $this->m_pageCount;
    }

    // Returns the current displayed result page
    public function getCurrentPage()
    {
        return $this->m_currentPage;
    }

    // Returns the total number of product items
    public function getTotalItems()
    {
        return $this->m_totalItems;
    }

    // Returns the current number of items per page
    public function getResultsPerPage()
    {
        return $this->m_resultsPerPage;
    }

    // Returns the index of the first item on the current results page
    public function getFirstItem()
    {
        return $this->m_firstItem;
    }

    // Returns the index of the last item on the current results page
    public function getLastItem()
    {
        return $this->m_lastItem;
    }

    // Returns the sort order currently emplyed by the results
    public function getSortOrder()
    {
        return $this->m_sortOrder;
    }

    // Returns a list containing the descriptions for all the items
    public function getDataDescriptions()
    {
        return $this->m_descs;
    }

    // Returns the index of a specific item description within the list
    // Returns -1 if the item description is not found
    public function getColumnIndex($colName)
    {
        for ($i = 0; $i < count($this->m_descs); $i++) {
            $desc = $this->m_descs[$i];
            if (strcmp($colName, $desc->getColName()) == 0) {
                return $i;
            }
        }

        return -1;
    }
}
