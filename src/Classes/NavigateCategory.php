<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\INavigateCategory;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

// Represents a search advisor category
class NavigateCategory implements INavigateCategory
{
    private $m_productCount = -1;

    private $m_name;

    private $m_ids = null;

    private $m_subCategories = null;

    private $m_nodeString = null;

    private $m_seoPath = null;

    private $d_image = null;

    private $m_id;

    private $imageProcessed = false;

    // Generates the Navigate Category based off of a category xml node
    public function __construct($def)
    {
        $this->m_name = $def->name;
        $this->m_productCount = $def->productCount;
        $this->m_nodeString = $def->nodeString;
        $this->m_seoPath = $def->seoPath;
        $this->m_ids = explode(',', $def->ids);
        $this->m_id = $this->m_ids[0] ?? null;

        if (! empty($def->subCategories)) {
            $this->m_subCategories = [];
            foreach ($def->subCategories as $sub) {
                $this->m_subCategories[] = new NavigateCategory($sub);
            }
        }
    }

    // Returns the category name
    public function getName()
    {
        return $this->m_name;
    }

    // Returns the total number of products that have this category. Returns -1 if this is unkown.
    public function getProductCount()
    {
        return $this->m_productCount;
    }

    // returns a list of ids (as Strings) that correspond to the category named in this object.
    // The list will contain at least 1 entry and possibly more if there are multiple categories
    // with the same name at the same level. The ids will be only from categories that are referenced by
    // products in the result set, that is, if there are 5 categories with the same name, but only 3 of them are
    // referenced in the result set, then the list will contain 3 Strings not 5
    public function getIDs()
    {
        return $this->m_ids;
    }

    public function getID()
    {
        return $this->m_id;
    }

    // Returns a list of NavigateCategory corresponding to the subcategories of this node
    // (if any exist and the AdvisorOptions requested that the be generated) or null.
    public function getSubCategories(): ?array
    {
        return $this->m_subCategories ?? [];
    }


    // Returns a string corresponding to the path segment for this category. See NavigateNode.toString()
    public function getNodeString()
    {
        return $this->m_nodeString;
    }

    // Returns a string corresponding to the full path to this category
    public function getSEOPath()
    {
        return $this->m_seoPath;
    }

    public function getImage(): ?string
    {
        if (! $this->imageProcessed) {

            $cachedCategories = Cache::remember('site-db-categories', DAY, function (): array {
                return Category::all()->toArray();
            });

            $dbCategory = collect($cachedCategories)->firstWhere('id', '=', $this->m_id);

            $this->imageProcessed = true;

            $this->d_image = $dbCategory?->image ?? config('amplify.frontend.fallback_image_path');
        }

        return $this->d_image;
    }
}
