@php
    /**
     * @var \Amplify\System\Sayt\Classes\NavigateAttribute $initAttrValue
     * @var \Amplify\System\Sayt\Classes\NavigateAttribute $fullAttrValue
     */
@endphp
<div @class(["more-less-container collapse filter-section", "show" => $attributeInfo->hasSelectedAttribute()]) id="attribute_{{$attrKey}}">
    <div @class(["summary"])>
        <ul class="shop-sidebar-option-list list-unstyled fw-normal pb-1 small">
            @foreach($attributeInfo->getInitialList() as $initAttrValueKey => $initAttrValue)
                <li @class(['shop-sidebar-checkbox', 'active' => $initAttrValue->isSelected()]) >
                    <input @if($initAttrValue->isSingleValued()) type="radio" @else type="checkbox" @endif class="mr-2"
                           onchange="changedFilter(this)"
                           value="{{$initAttrValue->getSEOPath()}}"
                        @checked($initAttrValue->isSelected())/>
                    <a href="{{ frontendShopURL([$initAttrValue->getSEOPath(), ...$extraQuery]) }}">
                        {{ $initAttrValue->getDisplayName() }}
                        <span class="ml-1 product-counter">({{$initAttrValue->getProductCount()}})</span>
                    </a>
                </li>
            @endforeach
        </ul>
        @if(count($attributeInfo->getFullList()) > count($attributeInfo->getInitialList()) && $attributeInfo->isInitialListExists())
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
                <li @class(['shop-sidebar-checkbox', 'active' => $fullAttrValue->isSelected()]) >
                    <input @if($fullAttrValue->isSingleValued()) type="radio" @else type="checkbox" @endif class="mr-2"
                           onchange="changedFilter(this)"
                           value="{{$fullAttrValue->getSEOPath()}}"
                        @checked($fullAttrValue->isSelected())/>
                    <a href="{{ frontendShopURL([$fullAttrValue->getSEOPath(), ...$extraQuery]) }}">
                        {{ $fullAttrValue->getDisplayName() }}
                        <span class="ml-1 product-counter">({{$fullAttrValue->getProductCount()}})</span>
                    </a>
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
</div>
