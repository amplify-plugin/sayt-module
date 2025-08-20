<div @class(["summary"])>
    <ul class="shop-sidebar-option-list list-unstyled fw-normal pb-1 small">
        @foreach($attributeInfo->getInitialList() as $initAttrValueKey => $initAttrValue)
            @if($initAttrValue->getName() == 'Product Features' && in_array($initAttrValue->getDisplayName(), ['In Stock'], true))
                @continue
            @endif

            <li @class(['shop-sidebar-checkbox', 'active' => $initAttrValue->isSelected()]) >
                <input type="checkbox" class="mr-2"
                       onchange="changedFilter(this)"
                       value="{{$initAttrValue->getSEOPath()}}"
                    @checked($initAttrValue->isSelected())/>
                @if($initAttrValue->isDisplayAsLink())
                    <a href="{{ frontendShopURL([$initAttrValue->getSEOPath(), ...$extraQuery]) }}">
                        {{ $initAttrValue->getDisplayName() }}
                        <span class="ml-1 product-counter">({{$initAttrValue->getProductCount()}})</span>
                    </a>
                @else
                    <p>
                        {{ $initAttrValue->getDisplayName() }}
                        <span class="ml-1 product-counter">({{$initAttrValue->getProductCount()}})</span>
                    </p>
                @endif
            </li>
        @endforeach
    </ul>
    @if(count($attributeInfo->getFullList()) > count($attributeInfo->getInitialList()) && !empty($attributeInfo->getInitialList()))
        <a href="javascript:void(0);" role="button"
           class="show-hide-toggle-btn"
           onclick="toggleShowMoreLess(this, 'full', 'summary');">
            {{ trans('SHOW MORE...') }}
        </a>
    @endif
</div>
<div @class(["full", 'd-none' => $attributeInfo->isInitialListExists()])>
    <ul class="shop-sidebar-option-list list-unstyled fw-normal pb-1 small">
        @foreach($attributeInfo->getFullList() as $fullAttrValueKey => $fullAttrValue)
            @if($fullAttrValue->getName() == 'Product Features' && in_array($fullAttrValue->getDisplayName(), ['In Stock'], true))
                @continue
            @endif
            <li @class(['shop-sidebar-checkbox', 'active' => $fullAttrValue->isSelected()]) >
                <input type="checkbox" class="mr-2"
                       onchange="changedFilter(this)"
                       value="{{$fullAttrValue->getSEOPath()}}"
                    @checked($fullAttrValue->isSelected())/>
                @if($fullAttrValue->isDisplayAsLink())
                    <a href="{{ frontendShopURL([$fullAttrValue->getSEOPath(), ...$extraQuery]) }}">
                        {{ $fullAttrValue->getDisplayName() }}
                        <span class="ml-1 product-counter">({{$fullAttrValue->getProductCount()}})</span>
                    </a>
                @else
                    <p class="mb-0">
                        {{ $fullAttrValue->getDisplayName() }}
                        <span class="ml-1 product-counter">({{$fullAttrValue->getProductCount()}})</span>
                    </p>
                @endif
            </li>
        @endforeach
    </ul>

    @if($attributeInfo->isInitialListExists())
        <a href="javascript:void(0);" role="button"
           class="show-hide-toggle-btn"
           onclick="toggleShowMoreLess(this, 'summary', 'full');">
            {{ trans('SHOW LESS...') }}
        </a>
    @endif
</div>
