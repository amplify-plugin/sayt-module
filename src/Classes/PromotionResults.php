<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\iPromotionResults;

// Serves as the json document that holds promotion results
class PromotionResults implements IPromotionResults
{
    private $m_doc;

    private $m_itemDescriptions = null;

    private $m_items;

    // Creates a new instance.
    public function __construct()
    {
        $this->m_doc = new \DOMDocument;
    }

    // Loads a URL into the instance, then determines the appropriate results and layout.
    public function load($url)
    {
        $start = microtime(true);
        $json = $this->url_get_contents($url);
        $jsontime = microtime(true);
        $this->m_doc = json_decode($json);
        $objecttime = microtime(true);
    }

    public function url_get_contents($Url)
    {
        if (! function_exists('curl_init')) {
            exit('CURL is not installed!');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($output === false || ! ($this->valid_response_code($httpcode))) {
            echo curl_error($ch);
        }
        curl_close($ch);

        return $output;
    }

    public function valid_response_code($httpcode)
    {
        $resp = false;
        if (($httpcode == 200) || ($httpcode == 301) || ($httpcode == 302)) {
            $resp = true;
        }

        return $resp;
    }

    // If there is a return code json node in the doc, returns the contained code.
    public function getReturnCode()
    {
        $nodeRC = $this->m_doc->returnCode;

        return $nodeRC >= 0 ? $nodeRC : -1;
    }

    // If an error message currently exists in the PromotionResults, returns it.
    public function getErrorMsg()
    {
        $node = $this->m_doc->errorMsg;

        return $node != null ? $node : null;
    }

    // If a message currently exists in the PromotionResults, returns it.
    public function getMessage()
    {
        $node = $this->m_doc->message;

        return $node != null ? $node : null;
    }

    // If a commentary currently exists in the PromotionResults, returns it.
    public function getCommentary()
    {
        $node = $this->m_doc->commentary;

        return $node != null ? $node : null;
    }

    // If an original question exists in the PromotionResults, returns it.
    public function getOriginalQuestion()
    {
        $node = $this->m_doc->originalQuestion;

        return $node != null ? $node : null;
    }

    // If sql exists in the PromotionResults, returns it.
    public function getSql()
    {
        $node = $this->m_doc->sql;

        return $node != null ? $node : null;
    }

    // If echo exists in the PromotionResults, returns it.
    public function getEcho()
    {
        $node = $this->m_doc->echo;

        return $node != null ? $node : null;
    }

    // If requestIP exists in the PromotionResults, returns it.
    public function getRequestIP()
    {
        $node = $this->m_doc->requestIP;

        return $node != null ? $node : null;
    }

    // Creates an ItemDecriptions instance for the xmlDoc
    private function processItemDescriptions()
    {
        if ($this->m_itemDescriptions == null) {
            if (isset($this->m_doc->products)) {
                $node = $this->m_doc->products->itemDescription;
                if ($node) {
                    $this->m_itemDescriptions = new PromoItemDescriptions($node);
                } else {
                    $this->m_itemDescriptions = new PromoItemDescriptions(null);
                }
            } else {
                $this->m_itemDescriptions = new PromoItemDescriptions(null);
            }
        }
    }

    // Returns an ItemDescriptions instance for the xmlDoc
    public function getItemDescriptions()
    {
        $this->processItemDescriptions();

        return $this->m_itemDescriptions;
    }

    // Returns to total number of pages needed to hold the results of the INavigateResults.
    public function getPageCount()
    {
        return $this->getItemDescriptions()->getPageCount();
    }

    // Gets the index of the current page of results that the INavigateResults is displaying.
    public function getCurrentPage()
    {
        return $this->getItemDescriptions()->getCurrentPage();
    }

    public function getIsDrillDown()
    {
        return $this->getItemDescriptions()->getIsDrillDown();
    }

    // Gets the total number of items currently contained within the INavigateResults.
    public function getTotalItems()
    {
        return $this->getItemDescriptions()->getTotalItems();
    }

    // Returns the current number of results per page.
    public function getResultsPerPage()
    {
        return $this->getItemDescriptions()->getResultsPerPage();
    }

    // Returns the index of the first result item.
    public function getFirstItem()
    {
        return $this->getItemDescriptions()->getFirstItem();
    }

    // Returns the index of the last result item.
    public function getLastItem()
    {
        return $this->getItemDescriptions()->getLastItem();
    }

    // Returns the current sort order, if any, for the results
    public function getSortOrder()
    {
        return $this->getItemDescriptions()->getSortOrder();
    }

    // Returns a list of data descriptions for the xmlDoc
    public function getDataDescriptions()
    {
        return $this->getItemDescriptions()->getDataDescriptions();
    }

    public function getResultCount()
    {
        return $this->getTotalItems();
    }

    // Creates a list of itemRows based off of the search.
    public function processItems()
    {
        if ($this->m_items == null) {
            $this->m_items = [];

            $items = $this->m_doc->products->items;
            if ($items) {
                foreach ($items as $item) {
                    $this->m_items[] = new PromoItemRow($this->getDataDescriptions(), $item);
                }
            }
        }
    }

    // Retrieves the data stored within an itemrow from the current page.
    public function getCellData($row, $col)
    {
        $this->processItems();

        $adjust = ($this->getCurrentPage() - 1) * $this->getResultsPerPage();

        return $this->m_items[$row - $adjust]->getFormattedText($col);
    }

    // Returns the index of a column contained within the ItemDescriptions
    public function getColumnIndex($colName)
    {
        return $this->getItemDescriptions()->getColumnIndex($colName);
    }

    // Returns an ItemRow from the currently displayed page
    public function getRow($pageRow)
    {
        $this->processItems();

        return $this->m_items[$pageRow];
    }
}
