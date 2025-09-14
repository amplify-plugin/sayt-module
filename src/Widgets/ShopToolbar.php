<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class ShopToolbar
 */
class ShopToolbar extends BaseComponent
{
    private $pagination;

    /**
     * Create a new component instance.
     *
     * @throws \ErrorException
     */
    public function __construct(
        public bool $showItemCount = false,
        public bool $showPerPageOption = false,
        public bool $showSortingOption = false,
        public bool $showProductViewChanger =false
    ) {
        parent::__construct();

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
        return view('sayt::shop-toolbar');
    }


    public function htmlAttributes(): string
    {
        if (! $this->attributes->has('class')) {
            $this->attributes = $this->attributes->class(['row pb-3']);
        }

        return parent::htmlAttributes();
    }
}
