@if($category->hasSubCategories())
    <ul>
        @foreach($category->getSubCategories() as $subCategory)
            <li>
                <a href="{{ $redirectPage($subCategory->getSEOPath()) }}">
                    {{ $subCategory->getName() }}
                </a>
                <span>
            @if($displayProductCount)
                        ({{ $subCategory->getProductCount() }})
                    @endif
        </span>
            </li>
        @endforeach
    </ul>
@endif
