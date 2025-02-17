<?php

namespace Amplify\System\Sayt\Classes;

// Contains data about display errors
class DisplayFormat
{
    private $m_OutputEngine = 0;

    private $m_Presentation = 0;

    private $m_Error = 0;

    // Builds a display from an appripriate xml node
    public function __construct($node)
    {
        if ($node) {
            $this->m_OutputEngine = $node->outputEngine;
            $this->m_Presentation = $node->presentation;
            $this->m_Error = $node->error;
        }
    }

    // Returns a numeric representation of the output engine
    public function getOutputEngine()
    {
        return $this->m_OutputEngine;
    }

    // Returns a numeric representation of the Presentation
    public function getPresentation()
    {
        return $this->m_Presentation;
    }

    // Returns a numeric representation of any errors that occur
    public function getError()
    {
        return $this->m_Error;
    }

    // Determines if an error has occured within the presentation
    public function isPresentationError()
    {
        return $this->getPresentation() == -1;
    }

    // Determines if the current page view is from a redirect or not.
    public function isRedirect()
    {
        return $this->getError() == 5;
    }
}
