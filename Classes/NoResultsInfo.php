<?php

/**
 * Created by PhpStorm.
 * User: srinivask
 * Date: 5/22/2018
 * Time: 2:34 PM
 */

namespace Amplify\System\Sayt\Classes;

class NoResultsInfo
{
    private $m_node = null;

    private $m_message = '';

    private $m_searches = [];

    // Builds the noResults info off of a noResults node
    public function __construct($node)
    {
        $this->m_node = $node;
        $this->processNoResults();
    }

    // Processes the noreults node
    private function processNoResults()
    {
        if ($this->m_node != null) {
            $this->m_message = $this->m_node->message;
            $this->m_searches = $this->m_node->searches;
        }
    }

    public function getMessage()
    {
        return $this->m_message;
    }

    public function getSearches()
    {
        return $this->m_searches;
    }
}
