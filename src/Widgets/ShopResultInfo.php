<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Sayt\Classes\RemoteResults;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

class ShopResultInfo extends BaseComponent
{
    private RemoteResults $pagination;

    /**
     * Create a new component instance.
     *
     * @throws \ErrorException
     */
    public function __construct(
        public string $format = "Showing {first} to {last} of {count} items",
        public bool   $render = true
    )
    {
        parent::__construct();

        $this->pagination = store()->eaProductsData;
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        if (!$this->render) {
            return false;
        }

        return !$this->pagination->noResultFound();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('sayt::shop-result-info');
    }

    public function itemDescription(): string
    {
        $firstItemOnPage = number_format($this->pagination->getFirstItem() ?? 1);
        $lastItemOnPage = number_format($this->pagination->getLastItem() ?? 1);
        $totalItemsOnResult = number_format($this->pagination->getTotalItems() ?? 0);

        return strtr($this->format, [
            '{first}' => $firstItemOnPage,
            '{last}' => $lastItemOnPage,
            '{count}' => $totalItemsOnResult,
        ]);
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['shop-toolbar-item-count-description']);

        return parent::htmlAttributes();
    }
}
