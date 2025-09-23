<?php

namespace Amplify\System\Sayt;


use Amplify\System\Sayt\Widgets\ShopAttributeFilter;
use Amplify\System\Sayt\Widgets\ShopBanner;
use Amplify\System\Sayt\Widgets\ShopCategories;
use Amplify\System\Sayt\Widgets\ShopCurrentFilter;
use Amplify\System\Sayt\Widgets\ShopEmptyResult;
use Amplify\System\Sayt\Widgets\ShopInStockFilter;
use Amplify\System\Sayt\Widgets\ShopPageLength;
use Amplify\System\Sayt\Widgets\ShopResultInfo;
use Amplify\System\Sayt\Widgets\ShopSorting;
use Amplify\System\Sayt\Widgets\ShopViewStyle;
use Amplify\System\Sayt\Widgets\SiteSearch;
use Amplify\System\Sayt\Widgets\ShopPagination;
use Amplify\System\Sayt\Widgets\ShopSearchInResult;
use Amplify\System\Sayt\Widgets\ShopSidebar;
use Amplify\System\Sayt\Widgets\ShopToolbar;
use Amplify\Widget\Abstracts\Widget;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class WidgetProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $widgets = [
            ShopEmptyResult::class => [
                'name' => 'shop-empty-result',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['shop'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Product shop empty result widget',
            ],
            ShopPagination::class => [
                'name' => 'shop-pagination',
                'reserved' => true,
                'internal' => true,
                'model' => ['static_page'],
                '@inside' => null,
                '@client' => null,
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'EasyAsk Shop Pagination widget',
            ],
            ShopSearchInResult::class => [
                'name' => 'shop-search-in-result',
                'reserved' => true,
                'internal' => false,
                'model' => [],
                '@inside' => null,
                '@client' => null,
                '@attributes' => [
                    ['name' => 'title', 'type' => 'text', 'value' => 'Search With in Results'],
                    ['name' => 'btn-label', 'type' => 'text', 'value' => 'Go'],
                ],
                '@nestedItems' => [],
                'description' => 'Keyword search in filter results',
            ],
            ShopSidebar::class => [
                'name' => 'shop-sidebar',
                'reserved' => true,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => ['shop'],
                '@attributes' => [
                    [
                        'name' => ':show-current-filters',
                        'type' => 'boolean',
                        'value' => true,
                    ],
                    [
                        'name' => ':show-filter-toggle',
                        'type' => 'boolean',
                        'value' => true,
                    ],
                    [
                        'name' => 'category-group-title',
                        'type' => 'text',
                        'value' => 'Categories',
                    ],
                    [
                        'name' => 'attribute-group-title',
                        'type' => 'text',
                        'value' => 'Filters',
                    ], [
                        'name' => 'toggle-icon-class',
                        'type' => 'text',
                        'value' => 'pe-7s-angle-up-circle',
                    ],
                ],
                '@nestedItems' => [
                    [
                        'name' => 'x-slot:before-filter',
                        '@inside' => null,
                        '@attributes' => [],
                        '@nestedItems' => [],
                    ],
                    [
                        'name' => 'x-slot:after-filter',
                        '@inside' => null,
                        '@attributes' => [],
                        '@nestedItems' => [],
                    ],
                ],
                'description' => '',
            ],
            ShopToolbar::class => [
                'name' => 'shop-toolbar',
                'reserved' => true,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [
                    [
                        'name' => 'show-item-count',
                        'type' => 'boolean',
                        'value' => true,
                    ],
                    [
                        'name' => 'show-per-page-option',
                        'type' => 'boolean',
                        'value' => true,
                    ],
                    [
                        'name' => 'show-sorting-option',
                        'type' => 'boolean',
                        'value' => true,
                    ],
                    [
                        'name' => 'show-product-view-changer',
                        'type' => 'boolean',
                        'value' => true,
                    ],
                ],
                '@nestedItems' => [],
                'description' => 'show product shop page options',
            ],
            ShopAttributeFilter::class => [
                'name' => 'shop-attribute-filter',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['static_page'],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'show product shop page attribute options',
            ],
            ShopInStockFilter::class => [
                'name' => 'shop-in-stock-filter',
                'reserved' => true,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => ['shop'],
                '@attributes' => [
                    ['name' => 'label', 'type' => 'text', 'value' => 'In-Stock'],
                    ['name' => 'checked-pop-up', 'type' => 'text', 'value' => 'In Stock Only'],
                    ['name' => 'disabled-pop-up', 'type' => 'text', 'value' => 'No Result Available'],
                ],
                '@nestedItems' => [],
                'description' => 'show a product restriction filter to only available in stock',
            ],
            ShopCategories::class => [
                'name' => 'shop-categories',
                'reserved' => true,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => [],
                '@attributes' => [
                    ['name' => 'seo-path', 'type' => 'text', 'value' => ''],
                    ['name' => ':show-product-count', 'type' => 'boolean', 'value' => true],
                    ['name' => ':show-category-image', 'type' => 'boolean', 'value' => true, 'hint' => 'Show Category image aligned with title'],
                    ['name' => 'view-mode', 'type' => 'select',
                        'options' => [
                            'list' => 'Show sub-categories as list',
                            'grid' => 'Show sub-categories as grid card',
                            'tree' => 'Show sub-categories as nested tree',
                        ],
                        'value' => 'list',
                    ],
                    ['name' => ':category-each-line', 'type' => 'select',
                        'options' => [
                            '1' => '1 item',
                            '2' => '2 item',
                            '3' => '3 item',
                            '4' => '4 item',
                            '6' => '6 item',
                        ],
                        'value' => '4',
                        'hint' => 'Categories in line are aligned with bootstrap4 grid system',
                    ],
                    ['name' => ':items-per-category', 'type' => 'select',
                        'options' => [
                            '1' => '1 item',
                            '2' => '2 item',
                            '3' => '3 item',
                            '4' => '4 item',
                            '6' => '6 item',
                        ],
                        'value' => '6',
                        'hint' => 'number of sub-categories to display of each category are aligned with bootstrap4 grid system',
                    ],
                    ['name' => ':show-only-category', 'type' => 'boolean', 'value' => true],
                    ['name' => ':redirect-to-shop', 'type' => 'boolean', 'value' => true],
                    ['name' => ':sub-category-depth', 'type' => 'number', 'value' => 1],
                    ['name' => ':priority-initial-category', 'type' => 'boolean', 'value' => false],
                ],
                '@nestedItems' => [],
                'description' => 'Shop Categories list with their sub-categories. selecting like t',
            ],
            SiteSearch::class => [
                'name' => 'site.search',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => [],
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Login widget',
            ],
            ShopCurrentFilter::class => [
                'name' => 'shop-current-filter',
                'reserved' => true,
                'internal' => true,
                '@inside' => null,
                '@client' => null,
                'model' => ['shop'],
                '@attributes' => [
                    ['name' => ':show-filter', 'type' => 'boolean', 'value' => true],
                ],
                '@nestedItems' => [],
                'description' => 'show current filters in EasyAsk response',
            ],
            ShopBanner::class => [
                'name' => 'shop-banner',
                'reserved' => false,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => [],
                '@attributes' => [
                    ['name' => ':show-close-button', 'type' => 'boolean', 'value' => true],
                ],
                '@nestedItems' => [],
                'description' => '',
            ],
            ShopPageLength::class => [
                'name' => 'shop-page-length',
                'reserved' => false,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => [],
                '@attributes' => [
                    ['name' => 'label', 'type' => 'text', 'value' => "Show"],
                ],
                '@nestedItems' => [],
                'description' => '',
            ],
            ShopResultInfo::class => [
                'name' => 'shop-result-info',
                'reserved' => false,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => [],
                '@attributes' => [
                    ['name' => ':format', 'type' => 'text', 'value' => ""],
                ],
                '@nestedItems' => [],
                'description' => '',
            ],
            ShopSorting::class => [
                'name' => 'shop-sorting',
                'reserved' => false,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => [],
                '@attributes' => [
                    ['name' => 'label', 'type' => 'text', 'value' => "Sort By"],
                ],
                '@nestedItems' => [],
                'description' => '',
            ],
            ShopViewStyle::class => [
                'name' => 'shop-view-style',
                'reserved' => false,
                'internal' => false,
                '@inside' => null,
                '@client' => null,
                'model' => [],
                '@attributes' => [
                    ['name' => 'label', 'type' => 'text', 'value' => "View"],
                ],
                '@nestedItems' => [],
                'description' => '',
            ],
        ];

        foreach ($widgets as $namespace => $options) {
            Widget::register($namespace, $options['name'], $options);
        }

        $this->app->booted(function () {
            push_js('vendor/easyask-sayt/css/sayt.css', 'plugin-style');
        });
    }
}
