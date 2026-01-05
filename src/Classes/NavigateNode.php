<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Facade\Sayt;
use Amplify\System\Sayt\Interfaces\INavigateNode;

// A specific node within the product heirarchy.
class NavigateNode implements INavigateNode
{
    private $m_value;

    private $m_path;

    private $m_purePath;

    private $m_SEOPath;

    private $m_type;

    private $m_englishName;

    // Builds a node based off of the xml node provided
    public function __construct($node)
    {
        $this->m_value = $node->value;
        $this->m_path = $node->path;
        $this->m_purePath = $node->purePath;
        $this->setSeoPath($node->seoPath ?? '');
        $this->m_type = $node->navNodePathType ?? 1;
        $this->setEnglishName($node->englishName ?? '');
    }

    // Gets the value contained in the node.
    public function getValue()
    {
        return $this->m_value;
    }

    // Gets the path to the node in the hierarchy
    public function getPath()
    {
        return $this->m_path;
    }

    // Gets the pure path to the node in the hierarchy
    public function getPurePath()
    {
        return $this->m_purePath;
    }

    // Gets the Search Engine Optimization path for the node
    public function getSEOPath()
    {
        return $this->m_SEOPath;
    }

    // Returns the type of node
    public function getType()
    {
        return $this->m_type;
    }

    // Returns the natural language english version of the node name
    public function getEnglishName()
    {
        return $this->m_englishName;
    }

    private function setEnglishName(string $value)
    {
        $this->m_englishName = $value;

        if ($this->getType() == 3) {
            $this->m_englishName = "Search: {$value}";
        }

        if ($this->getType() == 2) {
            $value = trim($value, '()');
            $attribute = trim(substr($value, 0, strpos($value, ' =')));
            if ($attribute == '_InStock') {
                $this->m_englishName = 'In Stock';
                return;
            }
            $this->m_englishName = "{$attribute}: " . collect(explode(' or ', $value))
                    ->map(function ($item) {
                        [$key, $value] = explode(" = ", $item);
                        $key = trim($key, '\'');
                        $value = trim($value, '\'');
                        return $value;
                    })->implode(', ');
        }
    }

    private function setSeoPath($value)
    {
        $this->m_SEOPath = trim(trim(str_replace(Sayt::getDefaultCatPath(), '', $value)), "/");
    }

    // Returns the label associated with this node
    // eg: Item Category, User Search, Color, etc
    public function getLabel()
    {
        return $this->m_englishName;
    }
}
