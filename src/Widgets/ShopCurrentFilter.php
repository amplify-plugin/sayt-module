<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Sayt\Classes\StateInfo;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class ShopCurrentFilter
 * @package Amplify\Widget\Components
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
        return $this->showFilter && !empty($this->filters);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('sayt::widgets.shop-current-filter');
    }
}
