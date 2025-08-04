@if($category->hasSubCategories())
    <ul>
        @foreach($category->getSubCategories() as $subCategory)
            <li @class(['has-children' => $subCategory->hasSubCategories()])>
                <a @if($subCategory->hasSubCategories()) href="#" @else href="{{ $redirectPage($subCategory) }}" @endif>
                    {{ $subCategory->getName() }}
                </a>
                <span>@if($displayProductCount)
                        ({{ $subCategory->getProductCount() }})
                    @endif</span>
                    @foreach($subCategory as $subSubCategory)
                        @include('sayt::widgets.inc.categories.tree', ['category' => $subSubCategory])
                    @endforeach
            </li>
        @endforeach
    </ul>
@endif
