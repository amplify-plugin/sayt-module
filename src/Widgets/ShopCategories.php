<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Cms\Models\Page;
use Amplify\System\Sayt\Classes\NavigateCategory;
use Amplify\System\Sayt\Facade\Sayt;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * @class ShopCategories
 */
class ShopCategories extends BaseComponent
{
    public int $gridCount = 4;

    public bool $displayProductCount = true;

    public $categories;

    public function __construct(public string $seoPath = '',
                                public string $viewMode = 'list',
                                public bool   $showProductCount = true,
                                public bool   $showCategoryImage = false,
                                int           $categoryEachLine = 4,
                                public int    $itemsPerCategory = 3,
                                public bool   $showOnlyCategory = true,
                                public bool   $redirectToShop = true,
                                public int    $subCategoryDepth = 1,
                                public bool   $priorityInitialCategory = false,
                                public bool   $collapsedCategory = false,
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
     * @throws \ErrorException
     */
    public function render(): View|Closure|string
    {
        $viewPath = $this->showOnlyCategory ? 'sayt::inc.categories' : 'sayt::inc.sub-categories';

        if (empty($this->seoPath)) {

            $pageModel = store('dynamicPageModel', new Page(['page_type' => 'static']));

            $categories = match ($pageModel->page_type) {
                'shop' => store()->eaProductsData->getCategories(),
                'single_product' => store()->eaProductDetail->getCategories(),
                default => store()->eaCategory
            };

        } else {
            $categories = Cache::remember("categories-{$this->seoPath}", DAY, function () {
                return Sayt::storeCategories($this->seoPath, [
                    'with_sub_category' => !$this->showOnlyCategory,
                    'product_count' => $this->displayProductCount ? 1 : false,
                    'sub_category_depth' => $this->showOnlyCategory ? 0 : $this->subCategoryDepth,
                ]);
            });
        }

        $this->categories = ($this->priorityInitialCategory && $categories->initialCategoriesExists()) ? $categories->getInitialCategories() : $categories->getCategories();

        return view('sayt::shop-categories', compact('viewPath'));
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
