<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Sayt\Classes\CategoriesInfo;
use Amplify\System\Sayt\Facade\Sayt;
use Amplify\Widget\Abstracts\BaseComponent;
use App\Helpers\UtilityHelper;
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

    public int $itemsPerCategory = 3;

    public bool $displayProductCount = true;

    public bool $displayCategoryImage = false;

    public ?CategoriesInfo $categories;

    public function __construct(public string $seoPath = '',
                                public string $viewMode = 'list',
                                string        $showProductCount = 'true',
                                string        $categoryEachLine = '4',
                                string        $showCategoryImage = 'false',
                                string        $itemsPerCategory = '6')
    {
        parent::__construct();

        $this->displayProductCount = UtilityHelper::typeCast($showProductCount, 'boolean');

        $this->displayCategoryImage = UtilityHelper::typeCast($showCategoryImage, 'boolean');

        $this->gridCount = ceil(12 / UtilityHelper::typeCast($categoryEachLine, 'int'));

        $this->itemsPerCategory = UtilityHelper::typeCast($itemsPerCategory, 'int');
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
        $this->categories = Cache::remember(Session::token().'-ea-categories', DAY, function () {
            return empty($this->seoPath)
                ? store()->eaCategory
                : Sayt::storeCategories($this->seoPath, ['with_sub_category' => true]);
        });

        return view('sayt::widgets.shop-categories');
    }
}
