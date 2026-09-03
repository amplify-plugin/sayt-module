<div class="products-wrapper">
    <div class="products-header">
        <span>Products @if($total > 0)
                ({{ number_format($total) }})
            @endif</span>
        <a href="{{ frontendShopURL(['search', 'q' => urlencode($query)]) }}"
           class="search-footer-link">
            View All Results
            <i class="icon-arrow-right"></i>
        </a>
    </div>
    @empty($products)
        @include('sayt::site-search.no-results')
    @else
        <div @class(['products', 'grid' => !empty($products), 'd-none' => empty($products)])>
            @each('sayt::site-search.item', $products , 'product')
        </div>
    @endempty
</div>