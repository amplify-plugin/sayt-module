<?php

namespace Amplify\System\Sayt\Interfaces;

interface IStateInfo
{
    // Returns the type of the current data.
    public function getType();

    // Returns the name of the current data.
    public function getName();

    // Returns the value of the current data.
    public function getValue();

    // Returns the path of the current data.
    public function getPath();

    // Returns the SEO Path of the current data.
    public function getSEOPath();
}
