<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Sayt\Classes\RemoteResults;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ShopViewStyle extends BaseComponent
{
    private RemoteResults $pagination;

    /**
     * Create a new component instance.
     *
     * @throws \ErrorException
     */
    public function __construct(
        public bool $render = true,
        public string $label = ''
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
        if (! $this->render) {
            return false;
        }

        return !$this->pagination->noResultFound();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $extraQuery = [
            $this->pagination->getCurrentSeoPath(),
            'per_page' => request('per_page', getPaginationLengths()[0]),
            'sort_by' => request('sort_by', ''),
            'page' =>  $this->pagination->getCurrentPage()
        ];

        return view('sayt::shop-view-style-option', compact('extraQuery'));
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['shop-toolbar-item-view-changer']);

        return parent::htmlAttributes();
    }

    public function productView()
    {
        return active_shop_view();
    }
}
