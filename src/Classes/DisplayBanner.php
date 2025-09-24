<?php

namespace Amplify\System\Sayt\Classes;

// Contains data about banner
use Illuminate\Support\HtmlString;

class DisplayBanner
{
    private $m_Name = '';

    private $m_Desc = '';

    public $m_Zone = '';

    private $m_Url = '';

    private $m_Image = '';

    private $m_Alt = '';

    private $m_Id = '';

    private $m_Threshold = 0;

    public $m_TriggerType = 0;

    private $m_TriggerValue = '';

    private $m_Count = 0;

    private $m_Exact = false;

    private $m_Html = '';

    // Builds a display from an appropriate xml node
    public function __construct($node)
    {
        if ($node) {
            $this->m_Name = $node->name;
            $this->m_Desc = $node->desc;
            $this->m_Zone = $node->zone;
            $this->m_Url = $node->url;
            $this->m_Image = $node->image;
            $this->m_Alt = $node->alt;
            $this->m_Id = $node->id;
            $this->m_Threshold = $node->threshold;
            $this->m_TriggerType = $node->triggerType;
            $this->m_TriggerValue = $node->triggerValue;
            $this->m_Count = $node->count;
            $this->m_Exact = $node->exact;
            $this->m_Html = $node->html;
        }
    }

    // Returns a name
    public function getName()
    {
        return $this->m_Name;
    }

    // Returns a description
    public function getDescription()
    {
        return $this->m_Desc;
    }

    // Returns a Zone
    public function getZone()
    {
        return $this->m_Zone;
    }

    // Returns a url
    public function getUrl()
    {
        return $this->m_Url;
    }

    // Returns a image
    public function getImage()
    {
        return $this->m_Image;
    }

    // Returns an alternate notation
    public function getAlt()
    {
        return $this->m_Alt;
    }

    // Returns a numeric representation of the id
    public function getId()
    {
        return $this->m_Id;
    }

    // Returns a string representation of the threshold
    public function getThreshold()
    {
        return $this->m_Threshold;
    }

    // Returns a numeric representation of the trigger Type
    public function getTriggerType()
    {
        return $this->m_TriggerType;
    }

    // Returns a string representation of the trigger Value
    public function getTriggerValue()
    {
        return $this->m_TriggerValue;
    }

    // Returns a numeric representation of the count
    public function getCount()
    {
        return $this->m_Count;
    }

    // Returns a boolean representation of the exact
    public function getExact()
    {
        return $this->m_Exact;
    }

    // Returns the html
    public function getHtml()
    {
        return new HtmlString($this->m_Html);
    }

    // Returns the generated seoPath from html
    public function getActionSeoPath()
    {
        $seoPath = '';
        $htmlDoc = new \DOMDocument;
        //		dd($this->m_Html);
        // return $this->m_Html;
        if ($this->m_Html) {
            $htmlDoc->loadHTML($this->m_Html);

            $aNodes = $htmlDoc->getElementsByTagName('a');
            foreach ($aNodes as $aNode) {
                $seoPath = $aNode->getAttribute('ea-seo-path');
            }
        }

        return $seoPath;
    }
}
