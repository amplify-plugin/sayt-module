@if($category->hasSubCategories())
    <ul>
        @foreach($category->getSubCategories() as $subCategory)
            <li>
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
