<?php

namespace Amplify\System\Sayt\Interfaces;

// This interface provides access to 'promotion' results. This result set contains three sets of data:
// 1) a product listing 2) a list of categories related to the products 3) a list of attributes related
// to the products. NOTE: Depending on the query or action performed, category and/or attribute data
// may not be present.
interface IPromotionResults
{
    public function getReturnCode();

    public function getErrorMsg();

    public function getMessage();

    public function getCommentary();

    public function getOriginalQuestion();

    public function getSql();

    public function getEcho();

    public function getRequestIP();

    // Returns to total number of pages needed to hold the results of the INavigateResults.
    public function getPageCount();

    // Gets the index of the current page of results that the INavigateResults is displaying.
    public function getCurrentPage();

    public function getIsDrillDown();

    // Gets the total number of items currently contained within the INavigateResults.
    public function getTotalItems();

    // Returns the current results per page.
    public function getResultsPerPage();

    // Returns the index of the first item on the page.
    public function getFirstItem();

    // Returns the index of the last item on the page.
    public function getLastItem();

    // Returns the current sort order being used by INavigateResults.
    public function getSortOrder();

    public function getDataDescriptions();

    // Returns the numerical index of a column.
    public function getColumnIndex($colName);

    // Gets Gets the data contained within a specific location of the results.
    public function getCellData($row, $col);

    // Returns a row from the currently displayed page.
    public function getRow($pageRow);
}
