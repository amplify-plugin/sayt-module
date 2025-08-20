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
    public function __construct(public string $label = 'In-Stock')
    {
        parent::__construct();
    }

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
        $eayAskResponse = store('eaProductsData');
        $attributeGroup = $eayAskResponse->getAttribute('Product Features');
        $filters = $eayAskResponse->getStateInfo();

        $checked = false;

        $currentSeoPath = '';

        foreach ($filters as $filter) {
            if ($filter->getValue() == 'In Stock') {
                $checked = true;
                $currentSeoPath = $filter->getSEOPath();
                break;
            }
        }

        if (!$checked) {
            foreach (($attributeGroup ?? []) as $entry) {
                if ($entry->getDisplayName() == 'In Stock') {
                    $currentSeoPath = $entry->getValue()->seoPath;
                    break;
                }
            }
        }

        $extraQuery = [
            'view' => request('view', config('amplify.frontend.shop_page_default_view')),
            'per_page' => request('per_page', getPaginationLengths()[0]),
            'sort_by' => request('sort_by', ''),
        ];

        return view('sayt::widgets.shop-in-stock-filter', compact('currentSeoPath', 'checked', 'extraQuery'));
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['widget']);

        return parent::htmlAttributes();
    }
}
