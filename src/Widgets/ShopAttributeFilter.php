<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Sayt\Classes\AttributesInfo;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class ShopAttributeFilter
 * @package Amplify\Widget\Components
 *
 */
class ShopAttributeFilter extends BaseComponent
{
    public function __construct(public AttributesInfo $attribute, public ?string $attributeGroupTitle = null)
    {
        parent::__construct();
    }

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
        return view('widget::shop-attribute-filter');
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['shop-sidebar-attribute-widget']);

        return parent::htmlAttributes();
    }
}
