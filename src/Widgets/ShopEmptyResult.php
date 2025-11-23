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
    public function __construct(public ?string $message = null)
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
        $templateBrandColor = theme_option(key: 'primary_color', default: '#002767');

        if (empty($this->message)) {
            $this->message = "Your search did not match any products. Please try different keywords.";
        }

        return view('sayt::shop-empty-result-image', compact('templateBrandColor'));
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['padding-top-1x row justify-content-center']);

        return parent::htmlAttributes();
    }
}
