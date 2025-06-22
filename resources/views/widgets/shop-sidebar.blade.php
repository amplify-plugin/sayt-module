@php
    /**
     * @var \Amplify\System\Sayt\Classes\AttributesInfo $eaattributes
     * @var \Amplify\System\Sayt\Classes\CategoriesInfo $categories
     */
@endphp
<aside {!! $htmlAttributes !!}>
    <span class="sidebar-close hidden-lg-up">
        <i class="icon-x pe-7s-close"></i>
    </span>

    {!! $beforeFilter ?? '' !!}

    @if (count($filters) > 0 && $showCurrentFilters)
        <section class="mb-1 widget widget-categories">
            <div class="d-flex justify-content-between border-bottom" style="margin-top: 1rem;">
                <p class="widget-title">Current Filters</p>
                <a href="{{ route('frontend.shop.index') }}"
                   data-toggle="tooltip" data-placement="top" title="Remove All"
                   class="d-inline-flex align-items-center rounded text-danger text-decoration-none"
                   style="padding: 6px 0;"
                   aria-current="page">
                    <i class="filter-btn-icon pe-7s-close-circle text-danger font-weight-bold"></i>
                </a>
            </div>
            <ul class="mt-3 d-block list-unstyled fw-normal pb-1 small">
                @foreach($filters as $key => $filter)
                    @php
                        $label = ($filter->getType() == 2) ? $filter->getName() . ": " . $filter->getValue() : $filter->getValue();
                    @endphp
                    <li class="active">
                        <a href="{{ route('frontend.shop.index', [$filter->getSEOPath(), ...$extraQuery]) }}"
                           class="d-inline-flex align-items-center rounded active"
                           data-toggle="tooltip"
                           data-placement="top"
                           title="Remove {{ ucwords($label) }}"
                           aria-current="page">
                            <i class="pe-7s-close-circle close-icon"></i>
                            {{ ucwords($label) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if($showFilterToggle)
        @if($categories->categoriesExists() || (isset($eaattributes) && count($eaattributes) > 0))
            <section class="mb-1 widget widget-categories widget-collapse">
                <a
                    class="d-flex justify-content-between my-3 text-muted text-decoration-none widget-title"
                    data-current-state="expanded"
                    onclick="toggleAllFilters(event, this);">
                    COLLAPSE ALL <i class="filter-btn-icon pr-0 pe-7s-angle-down-circle"></i>
                </a>
            </section>
        @endif
    @endif

    @if($categories->categoriesExists())
        <section class="mb-1 widget widget-categories shop-sidebar-category-widget">
            <div class="widget-wrapper shop-sidebar-category-title"
                 data-toggle="collapse"
                 href="#category{{ $categories->getSuggestedCategoryID() }}"
                 role="button"
                 aria-expanded="true"
                 aria-controls="category{{ $categories->getSuggestedCategoryID() }}"
            >
                <h3 class="widget-title"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="{{ $categories->getSuggestedCategoryTitle($categoryGroupTitle) }}">
                    {{ $categories->getSuggestedCategoryTitle($categoryGroupTitle) }}
                </h3>
                <a class="toggle-btn"
                   title="Expand or Collapse">
                    <i class="toggle-btn-icon {{ $toggleIconClass ?? null }}"></i>
                </a>
            </div>
            <div id="category{{$categories->getSuggestedCategoryID()}}" class="collapse show filter-section">
                @if ($categories->initialCategoriesExists())
                    <div @class(['categorySummary'])>
                        <ul class="list-unstyled fw-normal pb-1 small">
                            @foreach($categories->getInitialCategories() as $initialCatkey => $category)
                                <li class="shop-sidebar-checkbox">
                                    <input type="checkbox"
                                           onchange="changedFilter(this)"
                                           value="{{ $category->getSEOPath() }}"
                                           class="mr-2">
                                    <a href="{{ route('frontend.shop.index', [$category->getSEOPath(), ...$extraQuery]) }}">
                                        {{$category->getName()}}
                                        <span
                                            class="ml-1 product-counter">({{ $category->getProductCount() }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <a href="javascript:void(0);" role="button" class="show-hide-toggle-btn"
                           onclick="toggleShowMoreLess(this, '.categoryFull', '.categorySummary');">
                            SHOW MORE...
                        </a>
                    </div>
                @endif
                <div @class(['categoryFull', 'd-none' => $categories->initialCategoriesExists()])>
                    <ul class="list-unstyled fw-normal pb-1 small"
                        id="show_all_{{$categories->getSuggestedCategoryID()}}">
                        @foreach($categories->getDetailedCategories() as $catKey=>$category)
                            <li class="shop-sidebar-checkbox">
                                <input type="checkbox"
                                       onchange="changedFilter(this)"
                                       value="{{ $category->getSEOPath() }}"
                                       class="mr-2">
                                <a href="{{ route('frontend.shop.index', [$category->getSEOPath(), ...$extraQuery]) }}">
                                    {{$category->getName()}}
                                    <span
                                        class="ml-1 product-counter">({{ $category->getProductCount() }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <a href="javascript:void(0);" role="button" class="show-hide-toggle-btn"
                       onclick="toggleShowMoreLess(this, '.categorySummary', '.categoryFull');">
                        SHOW LESS...
                    </a>
                </div>
            </div>
        </section>
    @endif

{{--    <x-shop-attribute-filter :group-title="$attributeGroupTitle" :extra-query="$extraQuery"/>--}}

    {!! $afterFilter ?? '' !!}

</aside>

@pushOnce('plugin-script')
    <script>
        function toggleShowMoreLess(element, show, hide) {
            $(element).closest(show).each(function() {
                $(this).toggleClass('d-none');
            });
            $(element).closest(hide).each(function() {
                $(this).toggleClass('d-none');
            });
        }

        function changedFilter(el) {
            el.nextElementSibling.click();
        }

        // function updateQueryStringParameter(uri, key, value) {
        //     var re = new RegExp('([?&])' + key + '=.*?(&|$)', 'i');
        //     var separator = uri.indexOf('?') !== -1 ? '&' : '?';
        //     if (uri.match(re)) {
        //         return uri.replace(re, '$1' + key + '=' + value + '$2');
        //     } else {
        //         return uri + separator + key + '=' + value;
        //     }
        // }
        //
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

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                if (window.innerWidth <= 768) {
                    return; // Skip height adjustment on mobile view
                }

                let productGridHeight = 0;
                let element = document.getElementsByClassName('x-product-list');
                if (element.length > 0) {
                    productGridHeight = element[0].clientHeight;
                }
                element = document.getElementsByClassName('x-shop-toolbar');
                if (element.length > 0) {
                    productGridHeight += element[0].clientHeight;
                    if (productGridHeight > screen.height) {
                        element = document.getElementsByClassName('x-shop-sidebar');
                        if (element.length > 0) {
                            element[0].style.height = productGridHeight.toString() + 'px';
                            element[0].style.overflowY = 'auto';
                        }
                    }
                }
            }, 1000);
        });
    </script>
@endpushOnce
