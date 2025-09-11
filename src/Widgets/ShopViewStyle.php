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
        public bool $render = true
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

        return view('sayt::shop-view-style-option', [
            'currentPage' => $this->pagination->getCurrentPage(),
        ]);
    }

    public function productView()
    {
        return request('view', config('amplify.frontend.shop_page_default_view'));
    }
}
