<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\IGroupedResultSet;

// Contains info about a set of IGroupedResults
class GroupedSetInfo implements IGroupedResultSet
{
    private $m_node;

    private $m_res = null;

    private $m_groups = null;

    private $m_layout = [];

    private $m_type;

    private $m_criteria = null;

    private $m_totalGroups;

    private $m_paginate = false;

    private $m_breakGroups = false;

    private $m_maxRowsPerGroup = -1;

    private $m_totalRows = 0;

    // Creates a GroupedSetInfo instance. Pulls defualt information from an attribute node and the results.
    public function __construct($node, $res)
    {
        $this->m_node = $node;
        $this->m_res = $res;
        $this->m_type = $node->type;
        $this->m_criteria = $node->name;
        $this->m_totalGroups = $node->totalGroups;
        $this->m_paginate = $node->paginate ?? null;
        $this->m_breakGroups = $node->breakGroups ?? null;
        $this->m_maxRowsPerGroup = $node->maxRowsPerGroup;
    }

    // If the GroupedSetInfo is empty, it is populated with GroupResult instances from nodes
    // related to the xml node provided in the constructor.
    private function processGroups()
    {
        if ($this->m_groups == null) {
            $this->m_groups = [];
            foreach ($this->m_node->group as $grpNode) {
                $this->m_groups[] = new GroupInfo($this->m_res, $grpNode, $this);
            }
            $this->m_layout = GroupedPageLayout::layoutPages($this->m_paginate
                ? $this->m_res->getResultsPerPage()
                : -1,
                $this->m_groups, $this->m_breakGroups);
            $this->m_totalRows = 0;
            foreach ($this->m_groups as $group) {
                $this->m_totalRows += $group->getTotalNumberOfRows();
            }
        }
    }

    // Returns a GroupedResult located at index i.
    public function getGroup($i)
    {
        $this->processGroups();

        return $this->m_groups[$i];
    }

    // Returns the total number of groups in the set.
    public function getNumberOfGroups()
    {
        return $this->m_totalGroups;
    }

    // Returns the type of search
    public function getGroupCriteriaType()
    {
        return $this->m_type;
    }

    // Returns the criteria of the GroupedResultSet, either the name of the attribute or current category level.
    public function getGroupCriteria()
    {
        return $this->m_criteria;
    }

    // Returns the maximum number of rows of IGroupedResults this set can hold
    public function getMaximumRowsPerGroup()
    {
        return $this->m_maxRowsPerGroup;
    }

    // Returns the index of the first group on the current page.
    public function getStartGroup()
    {
        $this->processGroups();
        $values = [];
        $values[] = $this->m_res->getCurrentPage();
        $values[] = count($this->m_layout);
        $page = min($values);
        $group = $this->m_layout[$page - 1]->getStartGroup();

        return $this->m_groups->IndexOf($group);
    }

    // Returns the index of the last group on the current page.
    public function getEndGroup()
    {
        $this->processGroups();
        $values = [];
        $values[] = $this->m_res->getCurrentPage();
        $values[] = count($this->m_layout);
        $page = min($values);
        $group = $this->m_layout[$page - 1]->getEndGroup();

        return $this->m_groups->IndexOf($group);
    }

    // Returns the index of the last row contained within the provided group on the current page.
    public function getGroupEndRow($group)
    {
        $this->processGroups();
        $page = $this->m_res->getCurrentPage();
        $gr = $this->m_layout[$page - 1]->getEndGroup();
        if ($gr == $group) {
            return $this->m_layout[$page - 1]->getEndRow();
        }

        return $group->getNumberOfRows();
    }

    // Returns the index of the first row contained within the provided group on the current page.
    public function getGroupStartRow($group)
    {
        $this->processGroups();
        $page = $this->m_res->getCurrentPage();
        $gr = $this->m_layout[$page - 1]->getStartGroup();
        if ($gr == $group) {
            return $this->m_layout[$page - 1]->getStartRow();
        }

        return 1;
    }

    // Returns the total number of pages that the current GroupedResults have.
    public function getPageCount()
    {
        $this->processGroups();

        return count($this->m_layout);
    }

    // Returns the total number of rows contained within the current GroupedResults.
    public function getTotalNumberOfRows()
    {
        $this->processGroups();

        return $this->m_totalRows;
    }

    // Returns the node string for a specific group within the set.
    public function getNodeString($group)
    {
        $criteria = $group->getGroupValue();
        if ($this->m_type == 1) {
            foreach ($this->m_res->getDetailedCategories() as $cat) {
                if (strcasecmp($criteria, $cat->getName)) {
                    return $cat->getNodeString();
                }
            }
        } elseif ($this->m_type == 2) {
            foreach ($this->m_res->getDetailedAttributeValues($this->getGroupCriteria()) as $attr) {
                if (strcasecmp($criteria, $attr->getValue())) {
                    return $attr->getNodeString();
                }
            }
        }
    }
}
