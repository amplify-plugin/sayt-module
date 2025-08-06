<div class="widget widget-categories">
    @if($showCategoryImage)
        <div class="widget-image">
            <img src="{{ asset($category->getImage()) }}" class="widget-title-image" alt="Category" />
        </div>
    @endif
    <div class="widget-body">
        <a href="{{ $redirectPage($category) }}" class="widget-title-link">
            <p class="widget-body-heading">
                {{ $category->getName() }}
                @if($displayProductCount)
                    <span class="text-muted">
                        ({{ $category->getProductCount() }})
                </span>
                @endif
            </p>
        </a>
    </div>
</div>
