<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\Widget\Abstracts\BaseComponent;
use Amplify\System\Helpers\UtilityHelper;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class ShopToolbar
 */
class ShopToolbar extends BaseComponent
{
    /**
     * @var array
     */
    public $options;

    private $pagination;

    public bool $showItemCount = false;

    public bool $showSortingOption = false;

    public bool $showPerPageOption = false;

    public bool $showProductViewChanger = false;

    /**
     * Create a new component instance.
     *
     * @throws \ErrorException
     */
    public function __construct(
        string $showItemCount = 'false',
        string $showPerPageOption = 'false',
        string $showSortingOption = 'false',
        string $showProductViewChanger = 'false'
    ) {
        parent::__construct();
        $this->showItemCount = UtilityHelper::typeCast($showItemCount, 'bool');
        $this->showPerPageOption = UtilityHelper::typeCast($showPerPageOption, 'bool');
        $this->showSortingOption = UtilityHelper::typeCast($showSortingOption, 'bool');
        $this->showProductViewChanger = UtilityHelper::typeCast($showProductViewChanger, 'bool');

        $this->pagination = store()->eaProductsData;
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return ! $this->pagination->noResultFound();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $view = match (config('amplify.basic.client_code')) {
            'ACT' => 'widget::client.cal-tool.product.shop-toolbar',
            'RHS' => 'widget::client.rhsparts.product.shop-toolbar',
            'STV' => 'widget::client.steven.product.shop-toolbar',
            'HAN' => 'widget::client.hanco.product.shop-toolbar',
            default => 'sayt::widgets.shop-toolbar',
        };

        return view($view, [
            'currentPage' => $this->pagination->getCurrentPage(),
            'productOrder' => request('order_by', 'asc'),
            'perPage' => request('per_page', $this->pagination->getResultsPerPage()),
        ]);
    }

    public function productView()
    {
        return request('view', config('amplify.frontend.shop_page_default_view'));
    }

    public function itemDescription()
    {
        $firstItemOnPage = number_format($this->pagination->getFirstItem() ?? 1);
        $lastItemOnPage = number_format($this->pagination->getLastItem() ?? 1);
        $totalItemsOnResult = number_format($this->pagination->getTotalItems() ?? 0);

        return match (config('amplify.basic.client_code')) {
            'ACT' => "Items {$firstItemOnPage} - {$lastItemOnPage} of {$totalItemsOnResult}",
            default => "Showing: {$firstItemOnPage} - {$lastItemOnPage} of {$totalItemsOnResult} items",
        };
    }

    public function htmlAttributes(): string
    {
        if (! $this->attributes->has('class')) {
            $this->attributes = $this->attributes->class(['row pb-3']);
        }

        return parent::htmlAttributes();
    }
}
