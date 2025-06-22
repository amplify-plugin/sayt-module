<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\INavigateAttribute;

// Represents a seach advisor attribute.
class NavigateAttribute implements INavigateAttribute
{
    private $m_displayAsLink = false;

    private $m_name;

    private $m_productCount;

    private $m_type;

    private $m_value;

    private $m_nodeString;

    private $m_valueType;

    private $m_minValue;

    private $m_maxValue;

    private $m_minRangeValue;

    private $m_maxRangeValue;

    private $m_rangeRound;

    private $m_seoPath;

    private $m_selected;

    // Generates a NavigateAttribute based off of an attribute xml node
    public function __construct($name, $node)
    {
        $this->m_name = $name;
        $this->m_displayAsLink = $node->displayAsLink;
        $this->m_productCount = $node->productCount;
        $this->m_type = $node->attrType;
        $this->m_nodeString = $node->nodeString;
        $this->m_valueType = $node->valueType;
        $this->m_minValue = $node->minValue ?? 0;
        $this->m_maxValue = $node->maxValue ?? 0;
        $this->m_minRangeValue = $node->minRangeValue ?? 0;
        $this->m_maxRangeValue = $node->maxRangeValue ?? 0;
        $this->m_rangeRound = $node->rangeRound ?? 0;
        $this->m_value = $node;
        $this->m_seoPath = $node->seoPath;
        $this->m_selected = $node->selected ?? false;
    }

    // Whether this attribute should be displayed as a link
    public function isDisplayAsLink()
    {
        return $this->m_displayAsLink;
    }

    // Gets the name of the attribute
    public function getName()
    {
        return $this->m_name;
    }

    // The number of products contained within this attribute
    public function getProductCount()
    {
        return $this->m_productCount;
    }

    // Gets the type of attribute this is. (Color, Size, Product Type, etc)
    public function getType()
    {
        return $this->m_type;
    }

    // Gets the value contained within this INavigateAttribute. The data used to sort/seperate.
    public function getValue()
    {
        return $this->m_value;
    }

    // Gets the valueType contained within this INavigateAttribute. The data used to sort/seperate.
    public function getValueType()
    {
        return $this->m_valueType;
    }

    // Returns ths node location which this attribute is located under.
    public function getNodeString()
    {
        return $this->m_nodeString;
    }

    // Returns the min Value of the selection.
    public function getMinValue()
    {
        return $this->m_minValue;
    }

    // Returns the max Value of the selection.
    public function getMaxValue()
    {
        return $this->m_maxValue;
    }

    // Returns the min Value of the range.
    public function getMinRangeValue()
    {
        return $this->m_minRangeValue;
    }

    // Returns the max Value of the range.
    public function getMaxRangeValue()
    {
        return $this->m_maxRangeValue;
    }

    // Returns the round value of the range.
    public function getRangeRound()
    {
        return $this->m_rangeRound;
    }

    // Returns ths dispaly name of this attribute.
    public function getDisplayName()
    {
        return $this->m_value->attributeValue;
    }

    // Returns ths seoPath of this attribute.
    public function getSEOPath()
    {
        return $this->m_seoPath;
    }

    // Returns if this attribute is selected.
    public function isSelected()
    {
        return $this->m_selected;
    }

    private $splitPathSep = '////';

    private $splitValSep = ';;;;';

    // removes references to this node from the path sent in.
    public function removeFromPath($path)
    {
//        $nodes = split($splitPathSep, $path);
//
//        $key = getName()." = '".getValue()."'";
//        for ($i = 0; $i < count($nodes); $i++) {
//            if (strpos($nodes[$i], 'AttribSelect=')) {
//                $sbNewVal = '';
//                $nodeVal = strstr($nodes[$i], count('AttribSelect='));
//                $vals = split($splitValSep, $nodeVal);
//                for ($j = 0; $j < size($vals); $j++) {
//                    if (strcmp($vals[$j], $key) != 0) {
//                        if (size($sbNewVal) > 0) {
//                            $sbNewVal = $sbNewVal.';;;;';
//                        }
//                        $sbNewVal = $sbNewVal.$vals[j];
//                    }
//                }
//                if (count($sbNewVal) > 0) {
//                    if (count($sb) > 0) {
//                        $sb = $sb.'////';
//                    }
//                    $sb = $sb.'AttribSelect=';
//                    $sb = $sb.$sbNewVal;
//                }
//            } else {
//                if (count($sb) > 0) {
//                    $sb = $sb.'////';
//                }
//                $sb = $sb.$nodes[$i];
//            }
//        }
//
//        return $sb;
    }
}
