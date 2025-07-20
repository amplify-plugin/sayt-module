<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Helpers\UtilityHelper;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class Search
 */
class SiteSearch extends BaseComponent
{
    public function __construct(public bool $showSearchButton = true)
    {
        parent::__construct();
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        if (customer_check()) {
            return customer(true)->can('shop.browse');
        }

        return true;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('sayt::widgets.site-search');
    }

    public function searchBoxPlaceholder()
    {
        return config('amplify.search.search_box_placeholder');
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['ea-search-area search-item']);

        return parent::htmlAttributes();
    }
}
