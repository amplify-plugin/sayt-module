<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\IStateInfo;

// Implements IDataDescription
// Contains various information about the current data in a node
class StateInfo implements IStateInfo
{
    private $m_type;

    private $m_name;

    private $m_value;

    private $m_path;

    private $m_seoPath;

    // Builds the StateInfo of a certain node
    public function __construct($node)
    {
        $this->m_type = $node->type;
        $this->m_name = $node->name ?? '';
        $this->m_value = $node->value;
        $this->m_path = $node->path;
        $this->m_seoPath = $node->seoPath;
    }

    // Returns the type of the current data.
    public function getType()
    {
        return $this->m_type;
    }

    // Returns the name of the current data.
    public function getName()
    {
        return $this->m_name;
    }

    // Returns the value of the current data.
    public function getValue()
    {
        return $this->m_value;
    }

    // Returns the path of the current data.
    public function getPath()
    {
        return $this->m_path;
    }

    // Returns the SEO path of the current data.
    public function getSEOPath()
    {
        return $this->m_seoPath;
    }
}
