<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Sayt\Classes\AttributesInfo;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class ShopAttributeFilter
 */
class ShopAttributeFilter extends BaseComponent
{
    public AttributesInfo $attributesInfo;

    public function __construct(public ?string $groupTitle = null, public array $extraQuery = [], public string $toggleIconClass = '')
    {
        $easyAskData = store()->eaProductsData;

        $this->attributesInfo = $easyAskData->getAttributes();

        parent::__construct();
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return $this->attributesInfo->attributesExists();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('sayt::widgets.shop-attribute-filter', [
            'eaattributes' => [],
        ]);
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['shop-sidebar-attribute-widget']);

        return parent::htmlAttributes();
    }
}
