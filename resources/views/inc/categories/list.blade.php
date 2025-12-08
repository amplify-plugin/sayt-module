@if($category->hasSubCategories())
    <ul class="shop-category-list">
        @foreach($category->getSubCategories() as $subCategory)
            <li class="shop-category-item">
                <a href="{{ $redirectPage($subCategory) }}">
                    {{ $subCategory->getName() }}
                </a>
                @if($displayProductCount)
                    <span class="text-muted">
                        ({{ $category->getProductCount() }})
                    </span>
                @endif
            </li>
        @endforeach
    </ul>
@endif
