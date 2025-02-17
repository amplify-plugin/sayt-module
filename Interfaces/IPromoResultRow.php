<?php

namespace Amplify\System\Sayt\Interfaces;

interface IPromoResultRow
{
    // Gets the data in a column of the row
    public function getCellData($col);

    // The number of columns in the row.
    public function size();
}
