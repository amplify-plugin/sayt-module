<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\IBreadCrumbTrail;

// Used to keep track of where the INavigateResults has been.
class BreadCrumbTrail implements IBreadCrumbTrail
{
    private $m_fullPath = null;

    private $m_pureCategoryPath = null;

    private $m_navNodes = [];

    // Builds the Breadcrumbtrail from top node to the provided xml node, to the Category node
    public function __construct($node)
    {
        if ($node != null) {
            $this->m_fullPath = $node->fullPath;
            $this->m_pureCategoryPath = $node->pureCategoryPath;
            $nodes = $node->navPathNodeList;
            if ($nodes) {
                foreach ($nodes as $navNode) {
                    $this->m_navNodes[] = new NavigateNode($navNode);
                }
            }
        }
    }

    // Returns the full path from the top node to the current node location.
    public function getFullPath()
    {
        return $this->m_fullPath;
    }

    // Returns the path from the top node to the category node.
    public function getPureCategoryPath()
    {
        return $this->m_pureCategoryPath;
    }

    // Returns the path being used by the current search.
    public function getSearchPath()
    {
        return $this->m_navNodes;
    }
}
