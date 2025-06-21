<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class ShopEmptyResult
 */
class ShopEmptyResult extends BaseComponent
{
    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return true;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $templateBrandColor = template_option(key: 'primary_color', default: '#002767');

        return view('sayt::widgets.shop-empty-result-image', compact('templateBrandColor'));
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['padding-top-1x row justify-content-center']);

        return parent::htmlAttributes();
    }
}
