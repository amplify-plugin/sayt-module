<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Sayt\Classes\CategoriesInfo;
use Amplify\System\Sayt\Facade\Sayt;
use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\System\Helpers\UtilityHelper;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/**
 * @class ShopCategories
 */
class ShopCategories extends BaseComponent
{
    public int $gridCount = 4;

    public bool $displayProductCount = true;

    public bool $displayCategoryImage = false;

    public ?CategoriesInfo $categories;

    public function __construct(public string $seoPath = '',
                                public string $viewMode = 'list',
                                public bool   $showProductCount = true,
                                public bool   $showCategoryImage = false,
                                int           $categoryEachLine = 4,
                                public int    $itemsPerCategory = 3,
                                public bool   $showOnlyCategory = true,
                                public bool   $redirectToShop = false,
    )
    {
        parent::__construct();

        $this->displayProductCount = UtilityHelper::typeCast($showProductCount, 'boolean');

        $this->displayCategoryImage = UtilityHelper::typeCast($showCategoryImage, 'boolean');

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
            : Sayt::storeCategories($this->seoPath, ['with_sub_category' => !$this->showOnlyCategory]);

        return view('sayt::widgets.shop-categories', compact('viewPath'));
    }

    public function redirectPage(string $params)
    {
        return $this->redirectToShop ? frontendShopURL($params) : route('frontend.categories', $params);
    }
}
