<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class ShopInStockFilter
 */
class ShopInStockFilter extends BaseComponent
{
    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return config('amplify.search.use_product_restriction', false);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('sayt::widgets.shop-in-stock-filter');
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['widget']);

        return parent::htmlAttributes();
    }
}
