<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\Widget\Abstracts\BaseComponent;
use App\Helpers\UtilityHelper;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class ShopSidebar
 */
class ShopSidebar extends BaseComponent
{
    public bool $showCurrentFilters;

    public bool $showFilterToggle;

    public function __construct(string $showCurrentFilters = 'true',
        string $showFilterToggle = 'true',
        public string $categoryGroupTitle = 'Categories',
        public string $attributeGroupTitle = 'Filters',
        public string $toggleIconClass = 'pe-7s-angle-up-circle'
    ) {

        $this->showCurrentFilters = UtilityHelper::typeCast($showCurrentFilters, 'bool');
        $this->showFilterToggle = UtilityHelper::typeCast($showFilterToggle, 'bool');

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

        $filters = $easyAskData->getStateInfo();

        if ($filters == null) {
            $filters = [];
        }

        $extraQuery = [
            'view' => request('view', config('amplify.frontend.shop_page_default_view')),
            'per_page' => request('per_page', getPaginationLengths()[0]),
            'sort_by' => request('sort_by', ''),
        ];

        $eaattributes = $easyAskData->getAttributes()?->attribute ?? [];
        $categories = $easyAskData->getCategories();

        $view = match (config('amplify.basic.client_code')) {
            'ACT' => 'widget::client.cal-tool.product.shop-sidebar',
            default => 'sayt::widgets.shop-sidebar',
        };

        return view($view, compact('categories', 'eaattributes', 'filters', 'extraQuery'));
    }
}
