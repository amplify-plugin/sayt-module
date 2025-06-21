<?php

namespace Amplify\System\Sayt\Widgets;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @class ShopPagination
 */
class ShopPagination extends BaseComponent
{
    /**
     * @var array
     */
    public $options;

    public LengthAwarePaginator $paginator;

    private $result;

    private $emptyResult = false;

    private ?string $view;

    /**
     * Create a new component instance.
     *
     * @throws \ErrorException
     */
    public function __construct(?string $view = null, public string $prevLabel = '&lsaquo;', public string $nextLabel = '&rsaquo;')
    {
        parent::__construct();

        $this->result = new \stdClass;

        if ($view != null) {
            $this->view = $view;
        } else {
            $this->view = config('amplify.basic.pagination_view_path', 'sayt::widgets.shop-pagination');
        }

        $products = store()->eaProductsData->getProducts();

        (! isset($products))
            ? $this->emptyResult = true
            : $this->result = $products;
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return ! $this->emptyResult;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|Htmlable|string
    {
        $items = $this->result?->items ?? [];
        $paginateData = $this->result->itemDescription ?? new \stdClass;

        $totalCount = $paginateData->totalItems ?? 0;
        $resultsPerPage = $paginateData->resultsPerPage ?? getPaginationLengths()[0];
        $currentPage = $paginateData->currentPage ?? null;

        $this->paginator = (new LengthAwarePaginator(
            $items,
            $totalCount,
            $resultsPerPage,
            $currentPage,
            [
                'pageName' => 'page',
                'path' => '/'.request()->path(),

            ]
        ));

        return $this->paginator->withQueryString()->onEachSide(3)->links($this->view);
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['d-flex justify-content-center']);

        return parent::htmlAttributes();
    }
}
