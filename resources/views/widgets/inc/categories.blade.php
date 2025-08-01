@php
    /**
     * @var \Amplify\System\Sayt\Classes\NavigateCategory $category
     */
@endphp

<div class="widget widget-categories">
    <div @class(["widget-image", "d-none" => !$displayCategoryImage])>
        <img src="{{ asset($category->getImage()) }}" class="widget-title-image" alt="Category" />
    </div>
    <div class="widget-body">
        <a href="{{ frontendShopURL($category->getSEOPath()) }}" class="widget-title-link">
            <p class="widget-body-heading">
                {{ $category->getName() }}
                <span class="text-muted">@if($displayProductCount)
                        ({{ $category->getProductCount() }})
                    @endif</span>
            </p>
        </a>
    </div>
</div>
