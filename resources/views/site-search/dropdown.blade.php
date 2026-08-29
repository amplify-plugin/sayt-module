<div class="search-results">
    @empty($products)
        @include('sayt::site-search.no-results')
    @else
        @include('sayt::site-search.products')
        @include('sayt::site-search.suggestions')
    @endempty
</div>