<li class="shop-category-item">
    <a href="{{ $redirectPage($category) }}">
        {!! $category->getName() !!}
    </a>
    @if($displayProductCount)
        <span class="text-muted">({{ $category->getProductCount() }})</span>
    @endif
</li>
