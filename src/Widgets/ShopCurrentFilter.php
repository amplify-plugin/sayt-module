<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\Frontend\Abstracts\BaseComponent;
use Amplify\System\Sayt\Classes\StateInfo;
use Amplify\System\Sayt\Facade\Sayt;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

/**
 * @class ShopCurrentFilter
 * @package Amplify\Frontend\Components
 *
 */
class ShopCurrentFilter extends BaseComponent
{
    public array $filters = [];

    public $ignored = ['In Stock'];

    public function __construct(public bool $showFilter = false, public array $extraQuery = [])
    {
        parent::__construct();

        $this->filters = store('eaProductsData')->getStateInfo();

        $this->filters = array_filter($this->filters, fn(StateInfo $stateInfo) => !in_array($stateInfo->getValue(), $this->ignored));
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return config('amplify.sayt.enabled', true) && $this->showFilter && !empty($this->filters);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $currentSeoPath = store()->eaProductsData->getCurrentSeoPath();

        $query = [
            'view' => active_shop_view(),
            'per_page' => results_per_page(),
            'sort_by' => request('sort_by', ''),
        ];

        array_unshift($query, Str::contains($currentSeoPath, PRODUCT_IN_STOCK_CHEKCED) ? Sayt::getDefaultCatPath() . '/' . PRODUCT_IN_STOCK_CHEKCED : Sayt::getDefaultCatPath());

        return view('sayt::shop-current-filter', compact('query'));
    }
}
