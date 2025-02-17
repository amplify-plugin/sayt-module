<?php

namespace Amplify\System\Sayt\Classes;

// Contains data about all the banners

class DisplayBanners
{
    private $m_displaybanners = [];

    // Builds a list of attributesinfo based off of an appropriate xml node.
    public function __construct($node)
    {
        if ($node) {
            $this->m_displaybanners = $this->getDisplayBanners($node);
        }
    }

    // Returns a list of Banner objects that contains all the attributes contained within an xml node
    private function getDisplayBanners($node)
    {
        $results = [];
        if ($node) {
            $banners = isset($node->banner) ? $node->banner : $node;
            foreach ($banners as $banner) {
                $results[] = new DisplayBanner($banner);
            }
        }

        return $results;
    }

    // Returns the Banner object for a certain type.
    public function getBanner($type)
    {
        foreach ($this->m_displaybanners as $banner) {
            if (strcmp($type, $banner->getTriggerType()) == 0) {
                return $banner;
            }
        }

        return null;
    }

    // Returns the Banner object for a certain type.
    public function hasBanner($type)
    {
        foreach ($this->m_displaybanners as $banner) {
            if (strcmp($type, $banner->getTriggerType()) == 0) {
                return true;
            }
        }

        return false;
    }
}
