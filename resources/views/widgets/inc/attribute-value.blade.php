{{--    @foreach($eaattributes as $key => $attr)--}}
{{--        @php--}}
{{--            //NULL Attribute Value are escaped from sidebar--}}
{{--            if (strtolower($attr->name) !== 'price'){--}}
{{--                if(isset($attr->attributeValueList) && count($attr->attributeValueList) == 1){--}}
{{--                    $firstAttrValue = current($attr->attributeValueList);--}}
{{--                    if ($firstAttrValue->attributeValue == 'null') {--}}
{{--                            continue;--}}
{{--                        }--}}
{{--                }--}}
{{--            }--}}
{{--        @endphp--}}
{{--        <section class="widget widget-categories widget-attributes">--}}
{{--            <div class="widget-wrapper"--}}
{{--                 data-toggle="collapse"--}}
{{--                 href="#attribute_{{$key}}"--}}
{{--                 role="button"--}}
{{--                 aria-expanded="false"--}}
{{--                 aria-controls="attribute_{{$key}}"--}}
{{--            >--}}
{{--                <p class="widget-title"--}}
{{--                   data-toggle="tooltip"--}}
{{--                   data-placement="top"--}}
{{--                   title="{{$attr->name ?? ''}}">--}}
{{--                    {{$attr->name ?? ''}}--}}
{{--                </p>--}}
{{--                <a class="toggle-btn"--}}
{{--                   title="Expand or Collapse">--}}
{{--                    <i class="toggle-btn-icon {{ $toggleIconClass ?? null }}"></i>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <div class="collapse filter-section @if($key > 7) collapsed @endif" id="attribute_{{$key}}">--}}
{{--                @if(strtolower($attr->name) !== 'price')--}}
{{--                    @if ($attr->isInitDispLimited)--}}
{{--                        <ul class="d-block list-unstyled fw-normal pb-1 small"--}}
{{--                            id="show_limited_attribute_{{$key}}">--}}
{{--                            @foreach(($attr->initialAttributeValueList ?? []) as $initialAttrKey=>$colorAttr)--}}
{{--                                @if($colorAttr->attributeValue != 'null')--}}
{{--                                    <li @if(isset($colorAttr->selected) && $colorAttr->selected == true) class="active shop-sidebar-checkbox"--}}
{{--                                        @else class="shop-sidebar-checkbox" @endif >--}}
{{--                                        <input type="checkbox" class="mr-2"--}}
{{--                                               onchange="changedFilter(this)"--}}
{{--                                               value="{{$colorAttr->seoPath}}"--}}
{{--                                               @if(isset($colorAttr->selected) && $colorAttr->selected == true) checked @endif />--}}
{{--                                        <a href="{{ route('frontend.shop.index', [$colorAttr->seoPath, ...$extraQuery]) }}">--}}
{{--                                            {{$colorAttr->attributeValue}}<span--}}
{{--                                                class="ml-1 product-counter">({{$colorAttr->productCount}})</span>--}}
{{--                                        </a>--}}
{{--                                    </li>--}}
{{--                                @endif--}}
{{--                            @endforeach--}}
{{--                        </ul>--}}
{{--                    @endif--}}
{{--                    <ul class="list-unstyled fw-normal pb-1 small @if($attr->isInitDispLimited) d-none @else d-block @endif"--}}
{{--                        id="show_all_attribute_{{$key}}">--}}
{{--                        @foreach(($attr->attributeValueList ?? []) as $attributeKey=>$colorAttr)--}}
{{--                            @if($colorAttr->attributeValue != 'null')--}}
{{--                                <li @if(isset($colorAttr->selected) && $colorAttr->selected == true) class="active shop-sidebar-checkbox"--}}
{{--                                    @else class="shop-sidebar-checkbox" @endif>--}}
{{--                                    <input type="checkbox" class="mr-2"--}}
{{--                                           onchange="changedFilter(this)"--}}
{{--                                           value="{{$colorAttr->seoPath}}"--}}
{{--                                           @if(isset($colorAttr->selected) && $colorAttr->selected == true) checked @endif />--}}
{{--                                    <a href="{{ route('frontend.shop.index', [$colorAttr->seoPath, ...$extraQuery]) }}">--}}
{{--                                        {{$colorAttr->attributeValue}}<span class="ml-1 product-counter">({{$colorAttr->productCount}})</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                            @endif--}}
{{--                        @endforeach--}}
{{--                    </ul>--}}
{{--                    @if($attr->isInitDispLimited)--}}
{{--                        <button--}}
{{--                            class="show_more_less_btn"--}}
{{--                            type="button"--}}
{{--                            onclick="toggleShowMoreLess(this, 'attribute_{{$key}}');">--}}
{{--                            SHOW MORE...--}}
{{--                        </button>--}}
{{--                    @endif--}}
{{--                @else--}}
{{--                    @php $priceAttributes =  $attr->attributeValueList ?? [] @endphp--}}
{{--                    <div class="pb-3">--}}
{{--                        <form id="range_slider_224435542" class="price-range-slider" method="GET"--}}
{{--                              data-start-min="{{(float) $priceAttributes[0]->minValue ?? 0}}"--}}
{{--                              data-start-max="{{(float) $priceAttributes[0]->maxValue ?? 0}}"--}}
{{--                              data-min="{{(float) $priceAttributes[0]->minRangeValue ?? 0}}"--}}
{{--                              data-max="{{(float) $priceAttributes[0]->maxRangeValue ?? 0}}" data-step="1">--}}

{{--                            <footer class="ui-range-slider-footer d-flex justify-content-between pt-2 pb-3">--}}
{{--                                <div class="ui-range-value-min">$<span></span>--}}
{{--                                    <input type="hidden" name="min">--}}
{{--                                </div>--}}
{{--                                <div class="ui-range-value-max">$<span></span>--}}
{{--                                    <input type="hidden" name="max">--}}
{{--                                </div>--}}
{{--                            </footer>--}}
{{--                            <div id="price_range_slider" class="ui-range-slider mx-2"></div>--}}
{{--                            <p class="mt-3 text-muted">--}}
{{--                                Available Product--}}
{{--                                <span class="product-counter">--}}
{{--                                            ({{ $priceAttributes[0]->productCount }})--}}
{{--                                        </span>--}}
{{--                            </p>--}}
{{--                        </form>--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--        </section>--}}
{{--    @endforeach--}}
