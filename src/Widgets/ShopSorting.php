<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Sayt\Classes\RemoteResults;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

class ShopSorting extends BaseComponent
{
    private RemoteResults $pagination;

    /**
     * Create a new component instance.
     *
     * @throws \ErrorException
     */
    public function __construct(
        public bool $render = true,
        public string $label = '',
        public string $placeholder = ''
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
        return view('sayt::shop-sort-option');
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['shop-toolbar-sort-option']);

        return parent::htmlAttributes();
    }
}
