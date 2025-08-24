<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Sayt\Classes\CategoriesInfo;
use Amplify\System\Sayt\Classes\NavigateCategory;
use Amplify\System\Sayt\Facade\Sayt;
use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\System\Helpers\UtilityHelper;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class ShopCategories
 */
class ShopCategories extends BaseComponent
{
    public int $gridCount = 4;

    public bool $displayProductCount = true;

    public ?CategoriesInfo $categories;

    public function __construct(public string $seoPath = '',
                                public string $viewMode = 'list',
                                public bool   $showProductCount = true,
                                public bool   $showCategoryImage = false,
                                int           $categoryEachLine = 4,
                                public int    $itemsPerCategory = 3,
                                public bool   $showOnlyCategory = true,
                                public bool   $redirectToShop = true,
                                public int    $subCategoryDepth = 1,
    )
    {
        parent::__construct();

        $this->displayProductCount = $showProductCount;

        $this->gridCount = ceil(12 / $categoryEachLine);

    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return true;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $viewPath = $this->showOnlyCategory ? 'sayt::widgets.inc.categories' : 'sayt::widgets.inc.sub-categories';

        $this->categories = empty($this->seoPath)
            ? store()->eaCategory
            : Sayt::storeCategories($this->seoPath, [
                'with_sub_category' => !$this->showOnlyCategory,
                'product_count' => $this->displayProductCount ? 1 : false,
                'sub_category_depth' => $this->showOnlyCategory ? 0 : $this->subCategoryDepth,
            ]);

        return view('sayt::widgets.shop-categories', compact('viewPath'));
    }

    public function redirectPage(NavigateCategory $category)
    {
        if (!$category->hasSubCategories()) {
            return frontendShopURL($category->getSEOPath());
        }

        $query = request()->all();

        return $this->redirectToShop
            ? frontendShopURL($category->getSEOPath())
            : route('frontend.categories', [$category->getSEOPath(), ...$query]);
    }
}
