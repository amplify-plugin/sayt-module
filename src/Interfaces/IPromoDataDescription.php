<?php

namespace Amplify\System\Sayt\Interfaces;

interface IPromoDataDescription
{
    // Returns whether the data is displayable or not.
    public function getDisplayable();

    // Returns the column type of the current data.
    public function getColType();

    // Returns the HTML type of the current data.
    public function getHTMLType();

    // Returns the format type of the current data.
    public function getFormat();

    // Returns the tag name of the current data.
    public function getTagName();

    // Returns the column name of the current data.
    public function getColName();
}
