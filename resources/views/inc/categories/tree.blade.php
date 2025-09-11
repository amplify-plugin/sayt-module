@if($category->hasSubCategories())
    <ul>
        @foreach($category->getSubCategories() as $subCategory)
            <li @class(['has-children' => $subCategory->hasSubCategories()])>
                <a @if($subCategory->hasSubCategories()) href="#" @else href="{{ $redirectPage($subCategory) }}" @endif>
                    {{ $subCategory->getName() }}
                </a>
                @if($displayProductCount)
                    <span>
                        ({{ $subCategory->getProductCount() }})
                </span>
                @endif
                @foreach($subCategory as $subSubCategory)
                    @include('sayt::inc.categories.tree', ['category' => $subSubCategory])
                @endforeach
            </li>
        @endforeach
    </ul>
@endif
