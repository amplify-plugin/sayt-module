<?php

namespace Amplify\System\Sayt\Classes;

// Contains info and objects pertinent to the the results that will be displayed
// on the screen for a certain search.
use const EasyAsk\Impl\gr;
use const EasyAsk\Impl\i;

class GroupedPageLayout
{
    private $m_groupStart = null;

    private $m_groupEnd = null;

    private $m_startRow = -1;

    private $m_endRow = -1;

    // Creates a list of GroupedPageLayouts that represent the 'pages' of a search result.
    public static function layoutPages($pageSize, $groups, $breakGroups)
    {
        $result = [];
        if ($pageSize > 0) {
            for ($i = 0; $i < count($groups); $i++) {
                $gr = $groups[i];
                $groupStart = $gr;
                $startRow = 1;
                $totalRows = 0;
                $groupRows = $gr->getNumberOfRows();
                while (gr != null) {
                    while ($pageSize > $totalRows + $groupRows) {
                        $totalRows = $totalRows + $groupRows;
                        if (($i + 1) < count($groups)) {
                            $gr = $groups[$i++];
                            $groupRows = $gr->getNumberOfRows();
                        } else {
                            $gpl = new GroupedPageLayout;
                            $gpl->m_groupStart = $groupStart;
                            $gpl->m_startRow = $startRow;
                            $gpl->m_groupEnd = $gr;
                            $gpl->m_endRow = $gr->getNumberOfRows();
                            $result[] = $gpl;
                            $gr = null;
                            break;
                        }
                    }
                    if ($gr != null) {
                        $gpl = new GroupedPageLayout;
                        $gpl->m_groupStart = $groupStart;
                        $gpl->m_startRow = $startRow;
                        $gpl->m_groupEnd = $gr;
                        if ($breakGroups) {
                            if ($gr == $groupStart) {
                                $gpl->m_endRow = $pageSize - $totalRows + $startRow - 1;
                            } else {
                                $gpl->m_endRow = $pageSize - $totalRows;
                            }
                        } else {
                            $gpl->m_endRow = $gr->getNumberOfRows();
                        }
                        $result[] = $gpl;
                        $totalRows = 0;
                        if ($gpl->m_startRow == $gr->getNumberOfRows()) {
                            if (($i + 1) < count($groups)) {
                                $gr = $groups[$i++];
                                $groupRows = $gr->getNumberOfRows();
                                $startRow = 1;
                                $groupStart = $gr;
                            } else {
                                $gr = null;
                            }
                        } else {
                            $groupStart = $gr;
                            $startRow = $gpl->m_endRow + 1;
                            $groupRows = $gr->getNumberOfRows() - ($startRow - 1);
                        }
                    }
                }
            }
        } else {
            if (count($groups) > 0) {
                $gpl = new GroupedPageLayout;
                $gr = $groups[0];
                $gpl->m_groupStart = $gr;
                $gpl->m_startRow = 1;
                $gr = $groups[count($groups) - 1];
                $gpl->m_groupEnd = $gr;
                $gpl->m_endRow = $gr->getNumberOfRows();
                $result[] = $gpl;
            }
        }

        return $result;
    }

    public function getStartGroup()
    {
        return $this->m_groupStart;
    }

    public function getEndGroup()
    {
        return $this->m_groupEnd;
    }

    public function getStartRow()
    {
        return $this->m_startRow;
    }

    public function getEndRow()
    {
        return $this->m_endRow;
    }
}
