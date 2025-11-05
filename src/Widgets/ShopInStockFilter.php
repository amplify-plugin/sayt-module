<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

/**
 * @class ShopInStockFilter
 */
class ShopInStockFilter extends BaseComponent
{
    public function __construct(public string $label = 'In-Stock',
                                public        $checkedPopUp = 'In Stock Only',
                                public string $disabledPopUp = 'No Result Available')
    {
        parent::__construct();
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return config('amplify.sayt.use_product_restriction', false);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $eayAskResponse = store()->eaProductsData;

        $currentSeoPath = $eayAskResponse->getCurrentSeoPath();

        $specialAttributes = $eayAskResponse->getAttributes()->getFullAttributes(true);

        $inStockAttributeExists = array_filter($specialAttributes, fn($attr) => $attr->getName() == '_InStock');

        $disabled = empty($inStockAttributeExists);

        $checked = Str::contains($currentSeoPath, PRODUCT_IN_STOCK_CHEKCED);

        $currentSeoPath = (!$checked)
            ? ("{$currentSeoPath}/".PRODUCT_IN_STOCK_CHEKCED)
            : Str::replace('//', '/', Str::replace(PRODUCT_IN_STOCK_CHEKCED, '', $currentSeoPath));

        $extraQuery = [
            'view' => active_shop_view(),
            'per_page' => request('per_page', getPaginationLengths()[0]),
            'sort_by' => request('sort_by', ''),
        ];

        return view('sayt::shop-in-stock-filter', compact('currentSeoPath', 'checked', 'extraQuery', 'disabled'));
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['widget']);

        return parent::htmlAttributes();
    }
}
