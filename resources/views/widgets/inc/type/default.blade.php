@php
    /**
     * @var \Amplify\System\Sayt\Classes\AttributeInfo $attributeInfo
     */
@endphp
<div @class(["summary"])>
    <ul class="shop-sidebar-option-list list-unstyled fw-normal pb-1 small">
        @foreach($attributeInfo->getInitialList() as $attrValueKey => $attrValue)
            <li @class(['shop-sidebar-checkbox', 'active' => $attrValue->isSelected()]) >
                <input type="checkbox" class="mr-2"
                       onchange="changedFilter(this)"
                       value="{{$attrValue->getSEOPath()}}"
                    @checked($attrValue->isSelected())/>
                @if($attrValue->isDisplayAsLink())
                    <a href="{{ route('frontend.shop.index', [$attrValue->getSEOPath(), ...$extraQuery]) }}">
                        {{ $attrValue->getDisplayName() }}
                        <span class="ml-1 product-counter">({{$attrValue->getProductCount()}})</span>
                    </a>
                @else
                    <p>
                        {{ $attrValue->getDisplayName() }}
                        <span class="ml-1 product-counter">({{$attrValue->getProductCount()}})</span>
                    </p>
                @endif
            </li>
        @endforeach
    </ul>
    <a href="javascript:void(0);" role="button"
       class="show-hide-toggle-btn"
       onclick="toggleShowMoreLess(this, 'full', 'summary');">
        {{ trans('SHOW MORE') }}...
    </a>
</div>
<div @class(["full", 'd-none' => $attributeInfo->isInitialListExists()])>
    <ul class="shop-sidebar-option-list list-unstyled fw-normal pb-1 small">
        @foreach($attributeInfo->getFullList() as $attrValueKey => $attrValue)
            <li @class(['shop-sidebar-checkbox', 'active' => $attrValue->isSelected()]) >
                <input type="checkbox" class="mr-2"
                       onchange="changedFilter(this)"
                       value="{{$attrValue->getSEOPath()}}"
                    @checked($attrValue->isSelected())/>
                @if($attrValue->isDisplayAsLink())
                    <a href="{{ route('frontend.shop.index', [$attrValue->getSEOPath(), ...$extraQuery]) }}">
                        {{ $attrValue->getDisplayName() }}
                        <span class="ml-1 product-counter">({{$attrValue->getProductCount()}})</span>
                    </a>
                @else
                    <p class="mb-0">
                        {{ $attrValue->getDisplayName() }}
                        <span class="ml-1 product-counter">({{$attrValue->getProductCount()}})</span>
                    </p>
                @endif
            </li>
        @endforeach
    </ul>
    <a href="javascript:void(0);" role="button"
       class="show-hide-toggle-btn"
       onclick="toggleShowMoreLess(this, 'summary', 'full');">
        SHOW LESS...
    </a>
</div>
