<li @class(['has-children' => $category->hasSubCategories()])>
    <a href="{{ $redirectPage($category) }}">
        {!! $category->getName() !!}
    </a>

    @if($displayProductCount)
        <span>({{ $category->getProductCount() }})</span>
    @endif

    @if($category->hasSubCategories())
        <ul>
            @foreach($category->getSubCategories() as $subCategory)
                @include('sayt::inc.categories.tree', ['category' => $subCategory])
            @endforeach
        </ul>
    @endif
</li>