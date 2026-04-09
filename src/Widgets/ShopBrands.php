<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\Frontend\Abstracts\BaseComponent;
use Amplify\System\Backend\Models\Brand;
use Amplify\System\Backend\Models\Manufacturer;
use Amplify\System\Sayt\Classes\NavigateAttribute;
use Amplify\System\Sayt\Facade\Sayt;
use Closure;
use Illuminate\Contracts\View\View;

/**
 * @class ShopBrands
 */
class ShopBrands extends BaseComponent
{
    public array $models;

    public function __construct(public string $seoPath = '',
                                public bool   $showProductCount = true,
                                public int    $itemsPerRow = 6,
                                public string $searchAttribute = 'Brands',
                                public string $model = 'Manufacturer',
                                public int $imageHeight = 155

    )
    {
        parent::__construct();

    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return config('amplify.sayt.enabled', true);
    }

    /**
     * Get the view / contents that represent the component.
     * @throws \ErrorException
     * @throws \Exception
     */
    public function render(): View|Closure|string
    {
        $query = match ($this->model) {
            'Brand' => Brand::query()->selectRaw('LOWER(`title`) as `name`, `image`'),
            default => Manufacturer::query()->selectRaw('LOWER(`name`) as `name`, `image`'),
        };

        $this->models = $query->get()->pluck('image', 'name')->toArray();

        $entries = collect(Sayt::storeBrands($this->seoPath, $this->searchAttribute, ['product_count' => $this->showProductCount])?->getFullList() ?? [])
            ->groupBy(function (NavigateAttribute $item) {
                return match (strtoupper(substr($item->getValue()->attributeValue, 0, 1))) {
                    'A', 'B', 'C', 'D', 'E', 'F', 'G' => 'A-G',
                    'H', 'I', 'J', 'K', 'L', 'M', 'N' => 'H-N',
                    'O', 'P', 'Q', 'R', 'S', 'T', 'U' => 'O-U',
                    'V', 'W', 'X', 'Y', 'Z' => 'V-Z',
                    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' => '0-9',
                    default => '~',
                };
            });

        $groups = $entries->keys()->sort();

        return view('sayt::shop-brands', compact('groups', 'entries'));
    }

    public function brandImage($name): ?string
    {
        return $this->models[strtolower($name)] ?? null;
    }
}
