<?php

namespace Amplify\System\Sayt\Classes;

class AttributesInfo
{
    private $m_node;

    private $m_bInitialDispLimitedForAttrNames = false;

    private $m_initialDispLimitForAttrNames = 1;

    private $m_initialAttributeNames = [];

    /**
     * @var AttributeInfo[]
     */
    private $m_attributes = [];

    // Builds a list of attributesinfo based off of an appropriate xml node.
    public function __construct($node = null)
    {
        $this->m_node = $node;

        if ($node) {
            $this->m_attributes = $this->getAttributeInfo($node);
            $this->m_bInitialDispLimitedForAttrNames = $node?->isInitDispLimited ?? false;
            $this->m_initialDispLimitForAttrNames = $node?->initDispLimit ?? '';
            $initLists = $node?->initialAttrNameOrder ?? null;
            if ($initLists != null) {
                foreach ($initLists as $initList) {
                    $attrType = $initList->attrType;
                    $initNames = $initList->attributeName;
                    if (count($initNames) > 0) {
                        $this->m_initialAttributeNames[$attrType] = $initNames;
                    }
                }
            }
        }
    }

    public function getNode()
    {
        return $this->m_node;
    }

    // Returns a list of AttributeInfo that contains all the attributes contained within an xml node
    private function getAttributeInfo($node)
    {
        $results = [];
        if ($node) {
            $attrs = $node->attribute ?? $node;
            foreach ($attrs as $attr) {
                $results[] = new AttributeInfo($attr);
            }
        }

        return $results;
    }

    // Returns whether the attribute list is limited to the initial display
    public function isInitialDispLimitedForAttrNames(): bool
    {
        return $this->m_bInitialDispLimitedForAttrNames;
    }

    // Returns initial display limit for the attribute list.
    public function getInitialDispLimitForAttrNames()
    {
        return $this->m_initialDispLimitForAttrNames;
    }

    // Returns a list attribute names which correspond to a certain attribute type.
    public function getInitialDisplayList($attrType)
    {
        return $this->m_initialAttributeNames[$attrType] ?? [];
    }

    // Returns a list of attribute names for a certain display mode which are also of a certain attribute type.
    public function getAttributeNames($attrFilter, $displayMode)
    {
        if ($displayMode == 1) {
            return $this->m_initialAttributeNames[$attrFilter];
        } else {
            $result = [];
            foreach ($this->m_attributes as $attrInfo) {
                if (($attrInfo->getAttrType() & $attrFilter) != 0) {
                    $result[] = $attrInfo->getName();
                }
            }

            return $result;
        }
    }

    // Returns the AttributeInfo object for a certain attribute.
    private function getAttrInfo($attrName)
    {
        foreach ($this->m_attributes as $attrInfo) {
            if (strcmp($attrName, $attrInfo->getName()) == 0) {
                return $attrInfo;
            }
        }

        return null;
    }

    // Returns whether a specific attribute is limited the initial display or not.
    public function isInitialDispLimitedForAttrValues($attrName)
    {
        $attrInfo = $this->getAttrInfo($attrName);

        return $attrInfo == null ? false : $attrInfo->getIsLimited();
    }

    // Returns the initial display limit integer for a specific attribute.
    public function getInitialDispLimitForAttrValues($attrName)
    {
        $attrInfo = $this->getAttrInfo($attrName);

        return $attrInfo == null ? -1 : $attrInfo->getLimit();
    }

    // Returns if the attribute is a range filter.
    public function isRangeFilter($attrName)
    {
        $attrInfo = $this->getAttrInfo($attrName);

        return $attrInfo == null ? -1 : $attrInfo->getIsRangeFilter();
    }

    // Returns a list of NavigateAttributes for a certain display mode that correspond to a certain attribute name.
    public function getDetailedAttributeValues($attrName, $displayMode)
    {
        $result = [];
        $attrInfo = $this->getAttrInfo($attrName);
        if ($attrInfo != null) {
            if ($displayMode == 1 && ! $attrInfo->getIsLimited()) {
                $displayMode = 0;
            }
            $result = $displayMode == 0 ? $attrInfo->getFullList() : $attrInfo->getInitialList();
        }

        return $result;
    }

    public function attributesExists(): bool
    {
        return count($this->m_attributes) > 0;
    }

    public function getInitialDispAttributes()
    {
        $initialAttributeNames = $this->getInitialDisplayList(array_key_first($this->m_initialAttributeNames));

        $initialAttributes = [];

        foreach ($this->m_attributes as $attrInfo) {
            if (in_array($attrInfo->getName(), $initialAttributeNames)) {
                $initialAttributes[] = $attrInfo;
            }
        }

        return $initialAttributes;
    }

    public function getFullAttributes()
    {
        return $this->m_attributes;
    }
}
