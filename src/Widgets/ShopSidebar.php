<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\System\Helpers\UtilityHelper;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class ShopSidebar
 */
class ShopSidebar extends BaseComponent
{
    public function __construct(
        public bool $showCurrentFilters = true,
        public bool $showFilterToggle = true,
        public string $categoryGroupTitle = 'Categories',
        public string $attributeGroupTitle = 'Filters',
        public string $toggleIconClass = 'pe-7s-angle-up-circle'
    ) {

        parent::__construct();
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
     *
     * @throws \ErrorException
     */
    public function render(): View|Closure|string
    {
        $easyAskData = store()->eaProductsData;

        $extraQuery = [
            'view' => request('view', config('amplify.frontend.shop_page_default_view')),
            'per_page' => request('per_page', getPaginationLengths()[0]),
            'sort_by' => request('sort_by', ''),
        ];

        $categories = $easyAskData->getCategories();

        return view('sayt::widgets.shop-sidebar', compact('categories', 'extraQuery'));
    }
}
