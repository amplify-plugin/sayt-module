<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Sayt\Classes\RemoteResults;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class ShopBanner
 */
class ShopBanner extends BaseComponent
{
    /**
     * HTML Type = 3
     * IMAGE Type = 5
     */

    public RemoteResults $easyAsk;
    public function __construct(public bool $showCloseButton = true, public string $zone = '', public int $triggerType = 5)
    {
        parent::__construct();

        $this->easyAsk = store('eaProductsData', null);
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return $this->easyAsk->hasDisplayBanners();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $banners = $this->easyAsk->getDisplayBanner($this->triggerType);

        return view('sayt::shop-banner', compact('banners'));
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(["alert", "alert-dismissible", "fade", "show"]);
        $this->attributes = $this->attributes->merge(['role' => "alert"]);

        return parent::htmlAttributes();
    }
}
