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

    private $eaSearchResult;

    /**
     * Create a new component instance.
     *
     * @throws \ErrorException
     */
    public function __construct(public ?string $view = null,
                                public string  $prevLabel = '&lsaquo;',
                                public string  $nextLabel = '&rsaquo;',
                                public int     $linkEachSide = 1)
    {
        parent::__construct();

        $this->eaSearchResult = store()->eaProductsData;
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return $this->eaSearchResult->getTotalItems() > getPaginationLengths()[0];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|Htmlable|string
    {
        if (empty($this->view)) {
            $this->view = config('amplify.basic.pagination_view_path', 'sayt::shop-pagination');
        }

        $this->paginator = (new LengthAwarePaginator(
            $this->eaSearchResult->getProducts(),
            $this->eaSearchResult->getTotalItems(),
            $this->eaSearchResult->getResultsPerPage(),
            $this->eaSearchResult->getCurrentPage(), [
                'path' => parse_url(frontendShopURL($this->eaSearchResult->getCurrentSeoPath()), PHP_URL_PATH)
            ]
        ));

        return $this->paginator->withQueryString()->onEachSide($this->linkEachSide)->links($this->view);
    }

    public function htmlAttributes(): string
    {
        $this->attributes = $this->attributes->class(['d-flex justify-content-center']);

        return parent::htmlAttributes();
    }
}
