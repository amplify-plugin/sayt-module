<?php

namespace Amplify\System\Sayt\Classes;

// Contains a list of EasyAsk categories and provides methods to easily access
// the categories and pertaining data for the current search as well as the intial values.

use ArrayIterator;
use Traversable;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 */
class CategoriesInfo implements \IteratorAggregate, \JsonSerializable, \Countable
{
    private $m_node = null;

    private $m_categories = [];

    private $m_initialCategories = [];

    private $m_suggestedCategoryTitle = 'Categories';

    private $m_suggestedCategoryID = '';

    private $m_detailedSuggestedCategory = 'Categories';

    private $m_detailedSuggestedProductCount = -1;

    private $m_detailedSuggestedIDs = '';

    private $m_detailedSuggestedNodeString = '';

    private $m_detailedSuggestedSEOPath = '';

    private $m_initialDisplayLimitForCategories = -1;

    // Builds the category info off of a category node
    public function __construct($node)
    {
        $this->m_node = $node;
        $this->processCategories();
    }

    // Processes the category node and adds the node to a list of existing categories
    // As well as maintains a list of initial categories for when the user is on the main page.
    private function processCategories()
    {
        if ($this->m_node != null) {
            $catNode = $this->m_node->categories;
            if ($catNode != null) {
                $temp = $catNode->suggestedCategoryTitle;
                if ($temp != null) {
                    $this->m_suggestedCategoryTitle = $temp;
                }
                $temp = $catNode->suggestedCategoryID;
                if ($temp != null) {
                    $this->m_suggestedCategoryID = $temp;
                }
                $temp = $catNode->detailedSuggestedCategory ?? null;
                if ($temp != null) {
                    $this->m_detailedSuggestedCategory = $temp;
                    $this->m_detailedSuggestedProductCount = $temp->productCount;
                    $this->m_detailedSuggestedIDs = $temp->ids;
                    $this->m_detailedSuggestedNodeString = $temp->nodeString;
                    $this->m_detailedSuggestedSEOPath = $temp->seoPath;
                }
                $cats = $catNode->categoryList ?? null;
                if ($cats != null && count($cats) > 0) {
                    foreach ($cats as $cat) {
                        $this->m_categories[] = new NavigateCategory($cat);
                    }
                }
                $cats = $catNode->initialCategoryList ?? null;
                if ($cats != null && count($cats) > 0) {
                    $this->m_initialDisplayLimitForCategories = $catNode->InitDispLimit;
                    foreach ($cats as $cat) {
                        $this->m_initialCategories[] = new NavigateCategory($cat);
                    }
                }
            }
        }
    }

    // Returns a list of categories nodes.
    // Will return the initial list of nodes if the Display mode is still initial.
    // Otherwise, will return the current list of categories.
    public function getCategories($nDisplayMode = 0)
    {
        return ($nDisplayMode == 1)
            ? $this->m_initialCategories
            : $this->m_categories;
    }

    public function getNode(): mixed
    {
        return $this->m_node;
    }

    // Gets a list of the current category nodes

    /**
     * @return NavigateCategory[]
     */
    public function getDetailedCategories(): array
    {
        return $this->getCategories(0);
    }

    // Gets a list of the current category nodes

    /**
     * @return NavigateCategory[]
     */
    public function getInitialCategories(): array
    {
        return $this->getCategories(1);
    }

    // Returns a suggested category title based off of parent nodes
    public function getSuggestedCategoryTitle(?string $default = null): string
    {
        return $this->m_suggestedCategoryTitle ?? $default;
    }

    // Returns a suggested category ID based off of parent nodes
    public function getSuggestedCategoryID(): string
    {
        return $this->m_suggestedCategoryID;
    }

    // Returns a detailed suggested category based off of parent nodes
    public function getDetailedSuggestedCategory(): string
    {
        return $this->m_detailedSuggestedCategory;
    }

    // Returns a detailed suggested category count based off of parent nodes
    public function getDetailedSuggestedProductCount(): int
    {
        return $this->m_detailedSuggestedProductCount;
    }

    // Returns a detailed suggested category IDs based off of parent nodes
    public function getDetailedSuggestedIDs(): string
    {
        return $this->m_detailedSuggestedIDs;
    }

    // Returns a detailed suggested category node string based off of parent nodes
    public function getDetailedSuggestedNodeString(): string
    {
        return $this->m_detailedSuggestedNodeString;
    }

    // Returns a detailed suggested Search Engine Optimization path based off of parent nodes
    public function getDetailedSuggestedSEOPath(): string
    {
        return $this->m_detailedSuggestedSEOPath;
    }

    // Returns the display limit for initial list
    public function getInitDisplayLimitForCategories(): int
    {
        return $this->m_initialDisplayLimitForCategories;
    }

    public function categoriesExists(): bool
    {
        return count($this->m_categories) > 0;
    }

    public function initialCategoriesExists(): bool
    {
        return count($this->m_initialCategories) > 0;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     *
     * @since 5.4
     */
    public function jsonSerialize(): mixed
    {
        return $this->m_node?->categories ?? new self(null);
    }

    /**
     * Retrieve an external iterator
     *
     * @return ArrayIterator An instance of an object implementing
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->getDetailedCategories());
    }

    /**
     * Count elements of an object
     * @link https://php.net/manual/en/countable.count.php
     * @return int<0,max> The custom count as an integer.
     * <p>
     * The return value is cast to an integer.
     * </p>
     */
    public function count(): int
    {
        return count($this->getDetailedCategories());
    }
}
