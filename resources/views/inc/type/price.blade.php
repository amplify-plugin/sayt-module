<div @class(["more-less-container collapse filter-section", "show" => ($attributeInfo->hasSelectedAttribute() || ($initialAttrExpended && $initialAttrExpendedLimit > $attrKey))]) id="attribute_{{$attrKey}}">
    {{--@php $priceAttributes =  $attribute->attributeValueList ?? [] @endphp--}}
    {{--                <div class="pb-3">--}}
    {{--                    <form id="range_slider_224435542" class="price-range-slider" method="GET"--}}
    {{--                          data-start-min="{{(float) $priceAttributes[0]->minValue ?? 0}}"--}}
    {{--                          data-start-max="{{(float) $priceAttributes[0]->maxValue ?? 0}}"--}}
    {{--                          data-min="{{(float) $priceAttributes[0]->minRangeValue ?? 0}}"--}}
    {{--                          data-max="{{(float) $priceAttributes[0]->maxRangeValue ?? 0}}" data-step="1">--}}

    {{--                        <footer class="ui-range-slider-footer d-flex justify-content-between pt-2 pb-3">--}}
    {{--                            <div class="ui-range-value-min">$<span></span>--}}
    {{--                                <input type="hidden" name="min">--}}
    {{--                            </div>--}}
    {{--                            <div class="ui-range-value-max">$<span></span>--}}
    {{--                                <input type="hidden" name="max">--}}
    {{--                            </div>--}}
    {{--                        </footer>--}}
    {{--                        <div id="price_range_slider" class="ui-range-slider mx-2"></div>--}}
    {{--                        <p class="mt-3 text-muted">--}}
    {{--                            Available Product--}}
    {{--                            <span class="product-counter">--}}
    {{--                                            ({{ $priceAttributes[0]->productCount }})--}}
    {{--                                        </span>--}}
    {{--                        </p>--}}
    {{--                    </form>--}}
    {{--                </div>--}}
</div>
