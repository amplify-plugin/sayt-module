<a href="{{ $redirectPage($category) }}" class="widget-link">
    <div class="widget widget-categories m-2">
        @if($showCategoryImage)
            <div class="widget-image">
                <img src="{{ asset($category->getImage()) }}" class="widget-title-image" alt="Category" />
            </div>
        @endif
        <div class="widget-body">
            <p class="widget-body-heading">
                {!! $category->getName()  !!}
                @if($displayProductCount)
                    <span class="text-muted">
                        ({{ $category->getProductCount() }})
                </span>
                @endif
            </p>
        </div>
    </div>
</a>
