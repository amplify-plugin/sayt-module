@php
    /**
     * @var \Amplify\System\Sayt\Classes\CategoriesInfo $categories
     */
@endphp
<aside {!! $htmlAttributes !!}>
    <span class="sidebar-close hidden-lg-up">
        <i class="icon-x pe-7s-close"></i>
    </span>

    {!! $beforeFilter ?? '' !!}

        <x-shop-current-filter :show-filter="$showCurrentFilters" :extra-query="$extraQuery"/>

    @if($showFilterToggle)
        <section class="mb-1 widget widget-categories">
            <a
                class="d-flex justify-content-between text-muted text-decoration-none widget-title"
                data-current-state="expanded"
                onclick="toggleAllFilters(event, this);">
                {{ trans('COLLAPSE ALL') }} <i class="filter-btn-icon {{ $toggleIconClass ?? null }} pr-0 "></i>
            </a>
        </section>
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
            <div id="category{{$categories->getSuggestedCategoryID()}}"
                 class="collapse show filter-section more-less-container">
                @if ($categories->initialCategoriesExists())
                    <div @class(['summary'])>
                        <ul class="shop-sidebar-option-list list-unstyled fw-normal pb-1 small">
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
                        @if($categories->getInitialCategories())
                            <a href="javascript:void(0);" role="button" class="show-hide-toggle-btn"
                               onclick="toggleShowMoreLess(this, 'full', 'summary');">
                                {{ trans('SHOW MORE...') }}
                            </a>
                        @endif
                    </div>
                @endif
                <div @class(['full', 'd-none' => $categories->initialCategoriesExists()])>
                    <ul class="shop-sidebar-option-list list-unstyled fw-normal pb-1 small"
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
                    @if($categories->initialCategoriesExists())
                        <a href="javascript:void(0);" role="button" class="show-hide-toggle-btn"
                           onclick="toggleShowMoreLess(this, 'summary', 'full');">
                            {{ trans('SHOW LESS') }}
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif

        <x-shop-attribute-filter :group-title="$attributeGroupTitle" :extra-query="$extraQuery"
                                 :toggle-icon-class="$toggleIconClass" />
    {!! $afterFilter ?? '' !!}

</aside>

@pushonce('plugin-script')
    <script>
        function toggleShowMoreLess(element, show, hide) {
            let rootNode = $(element).closest('.more-less-container');
            rootNode.find(`.${show}:first`).toggleClass('d-none');
            rootNode.find(`.${hide}:first`).toggleClass('d-none');
        }

        function changedFilter(el) {
            el.nextElementSibling.click();
        }

        function toggleAllFilters(event, element) {
            event.preventDefault();
            element = $(element);
            if (element.data('current-state') === 'collapsed') {
                $('.filter-section').each(function() {
                    var div = $(this);
                    div.addClass('d-block');
                    if (div.hasClass('d-none')) {
                        div.removeClass('d-none');
                    }
                });
                element.data('current-state', 'expanded');
                element.html('{{ trans('COLLAPSE ALL') }} <i class="filter-btn-icon pe-7s-angle-up"></i>');
            } else {
                $('.filter-section').each(function() {
                    var div = $(this);
                    div.addClass('d-none');
                    if (div.hasClass('d-block')) {
                        div.removeClass('d-block');
                    }
                });
                element.data('current-state', 'collapsed');
                element.html('{{ trans('EXPAND ALL') }} <i class="filter-btn-icon pe-7s-angle-down"></i>');
            }
        }

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
@endpushonce
