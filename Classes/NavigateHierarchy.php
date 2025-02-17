<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\INavigateHierarchy;

// Represents a search advisor category hierarchy node.
class NavigateHierarchy implements INavigateHierarchy
{
    private $m_name;

    private $m_path;

    private $m_subNodes;

    private $m_isSelected;

    private $m_ids = null;

    // Builds the navigate hierarchy based off of an appropriate xml node
    public function __construct($node)
    {
        $this->m_name = $node;
        $this->m_path = $node->navHierPath;
        $this->m_isSelected = $node->isSelected;
        $subNodes = $node->navSubNodes->navHierNode;

        $this->m_subNodes = [];
        if ($subNodes != null) {
            foreach ($subNodes as $sub) {
                $this->m_subNodes[] = new NavigateHierarchy($sub);
            }
        }
        $idList = $node->ids;
        $this->m_ids = [];
        if ($idList) {
            $ids = split(',', $idList);
            foreach ($ids as $id) {
                $this->m_ids[] = $id;
            }
        }
    }

    // The hierarchy node name, that is, the category name corresponding to this node.
    public function getName()
    {
        return $this->m_name;
    }

    // The category path from root node to this node. The path is seperated by the standard path delimiter (////).
    // The path can be used to perform a category expand to this node.
    public function getPath()
    {
        return $this->m_path;
    }

    // Returns a list of NavigateHierarchy corresponding to the sub nodes of this node. A node will have sub nodes if it is
    // the selected node and that category has children, or if it is a sibling node that is on the path to the selected node.
    public function getSubNodes()
    {
        return $this->m_subNodes;
    }

    // Whether this node corresponds to the current suggested category.
    public function isSelected()
    {
        return $this->m_isSelected;
    }

    // Returns a list of IDs (as Strings) that correspong to the category named in this object. The list will contain at least
    // 1 entry and possibibly more if there are multiple categories with the same name at the same level. The list can be
    // empty for the toplevel node 'All Products'
    public function getIDs()
    {
        return $this->m_ids;
    }
}
