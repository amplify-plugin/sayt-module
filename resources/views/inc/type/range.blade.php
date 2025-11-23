@pushonce('plugin-script')
    {{--            @if(strtolower($attribute->name) !== 'price')--}}
    {{--                @if ($attribute->isInitDispLimited)--}}
    {{--                    <ul class="d-block list-unstyled fw-normal pb-1 small"--}}
    {{--                        id="show_limited_attribute_{{$attrKey}}">--}}
    {{--                        @foreach(($attribute->initialAttributeValueList ?? []) as $initialAttrKey=>$colorAttr)--}}
    {{--                            @if($colorAttr->attributeValue != 'null')--}}
    {{--                                <li @if(isset($colorAttr->selected) && $colorAttr->selected == true) class="active shop-sidebar-checkbox"--}}
    {{--                                    @else class="shop-sidebar-checkbox" @endif >--}}
    {{--                                    <input type="checkbox" class="mr-2"--}}
    {{--                                           onchange="changedFilter(this)"--}}
    {{--                                           value="{{$colorAttr->seoPath}}"--}}
    {{--                                           @if(isset($colorAttr->selected) && $colorAttr->selected == true) checked @endif />--}}
    {{--                                    <a href="{{ frontendShopURL([$colorAttr->seoPath, ...$extraQuery]) }}">--}}
    {{--                                        {{$colorAttr->attributeValue}}<span--}}
    {{--                                            class="ml-1 product-counter">({{$colorAttr->productCount}})</span>--}}
    {{--                                    </a>--}}
    {{--                                </li>--}}
    {{--                            @endif--}}
    {{--                        @endforeach--}}
    {{--                    </ul>--}}
    {{--                @endif--}}
    {{--                <ul class="list-unstyled fw-normal pb-1 small @if($attribute->isInitDispLimited) d-none @else d-block @endif"--}}
    {{--                    id="show_all_attribute_{{$attrKey}}">--}}
    {{--                    @foreach(($attribute->attributeValueList ?? []) as $attributeKey=>$colorAttr)--}}
    {{--                        @if($colorAttr->attributeValue != 'null')--}}
    {{--                            <li @if(isset($colorAttr->selected) && $colorAttr->selected == true) class="active shop-sidebar-checkbox"--}}
    {{--                                @else class="shop-sidebar-checkbox" @endif>--}}
    {{--                                <input type="checkbox" class="mr-2"--}}
    {{--                                       onchange="changedFilter(this)"--}}
    {{--                                       value="{{$colorAttr->seoPath}}"--}}
    {{--                                       @if(isset($colorAttr->selected) && $colorAttr->selected == true) checked @endif />--}}
    {{--                                <a href="{{ frontendShopURL([$colorAttr->seoPath, ...$extraQuery]) }}">--}}
    {{--                                    {{$colorAttr->attributeValue}}<span class="ml-1 product-counter">({{$colorAttr->productCount}})</span>--}}
    {{--                                </a>--}}
    {{--                            </li>--}}
    {{--                        @endif--}}
    {{--                    @endforeach--}}
    {{--                </ul>--}}
    {{--                @if($attribute->isInitDispLimited)--}}
    {{--                    <button--}}
    {{--                        class="show_more_less_btn"--}}
    {{--                        type="button"--}}
    {{--                        onclick="toggleShowMoreLess(this, 'attribute_{{$attrKey}}');">--}}
    {{--                        {{ trans('SHOW MORE') }}...--}}
    {{--                    </button>--}}
    {{--                @endif--}}
    {{--            @endif--}}
    <script>
        // function priceRangeSliderObserver() {
        //     var changeCount = 0;
        //
        //     var priceRangeSlider = document.querySelector('#price_range_slider');
        //
        //     if (!priceRangeSlider) {
        //         window.setTimeout(priceRangeSliderObserver, 500);
        //         return;
        //     }
        //
        //     var observer = new MutationObserver(function(event) {
        //         changeCount++;
        //         if (changeCount > 1) {
        //             debounce(filterMaxMin, 1000);
        //         }
        //     });
        //
        //     observer.observe(priceRangeSlider, {
        //         attributes: true,
        //         attributeFilter: ['class'],
        //         childList: false,
        //         characterData: false,
        //     });
        // }
    </script>
@endpushonce
