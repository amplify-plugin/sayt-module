<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\System\Sayt\Facade\Sayt;
use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

/**
 * @class Search
 */
class SiteSearch extends BaseComponent
{
    public function __construct(public bool $showSearchButton = true, public bool $templatePublished = false, public int $minLength = 3)
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
        $url = Sayt::getSaytUrl()
            ->withoutQueryParameters()
            ->withPath('/');

        $saytConfiguration = [
            'queryStr' => Sayt::getSaytUrl()
                ->withQueryParameter('customer', 'easayt')
                ->getAllQueryParameters(),
            'catPath' => "/" . Sayt::getDefaultCatPath(),
            'dct' => config('amplify.sayt.dictionary.dictionary'),
            'server' => (string)$url,
            'fields' => [
                'id' => config('amplify.sayt.sayt_product_id', 'Product_Id'),
                'image' => config('amplify.sayt.sayt_product_image', 'Product_Image'),
                'name' => config('amplify.sayt.sayt_product_name', 'Product_Name'),
                'code' => config('amplify.sayt.sayt_product_code', 'Product_Code'),
                'price' => config('amplify.sayt.sayt_product_price', 'Price'),
                'desc' => config('amplify.sayt.sayt_product_description', 'Short_Description'),
                'ptype' => config('amplify.sayt.sayt_product_type', 'Type_Id'),
                'sizes' => config('amplify.sayt.sayt_product_sizes', 'Sku_Sizes')
            ],
            'colorAttribute' => 'Available Colors',
            'ratingsAttribute' => 'Product Rating',
            'overlayFields' => true,
            'facetsExpanded' => 4,
            'suggestionLimit' => config('amplify.sayt.suggestion_limit'),
            'prompt' => config('amplify.sayt.search_box_placeholder', 'Search by EasyAsk'),
            'shopUrl' => frontendShopURL(),
            'defaultImage' => Str::startsWith(config('amplify.frontend.fallback_image_path'), 'http:')
                ? config('amplify.frontend.fallback_image_path')
                : asset(config('amplify.frontend.fallback_image_path')),
            'productUrlIdentifier' => config('amplify.frontend.easyask_single_product_index', 'id'),
            'minLength' => $this->minLength,
        ];

        if ($this->templatePublished && file_exists(public_path('assets/sayt-templates/leftprod.hbs'))) {
            $saytConfiguration['template'] = './../../../assets/sayt-templates/leftprod.hbs';
        }

        return view('sayt::site-search', compact('saytConfiguration'));
    }

    public function searchBoxPlaceholder()
    {
        return config('amplify.sayt.search_box_placeholder');
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['ea-search-area']);

        $this->attributes = $this->attributes->merge([
            'method' => "get",
            'action' => frontendShopURL('search'),
            'novalidate' => true,
//            'onsubmit' => "return Sayt.validateForm(event)",
        ]);

        return parent::htmlAttributes();
    }
}
