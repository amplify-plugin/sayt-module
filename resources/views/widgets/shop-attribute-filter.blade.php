@php
    /**
     * @var \Amplify\System\Sayt\Classes\AttributesInfo $attributesInfo
     */
@endphp
<div {!!  $htmlAttributes !!}>
    <p @class(["widget-title shop-sidebar-attribute-title", "d-none" => strlen($groupTitle) == 0])>
        {{ $groupTitle ?? '' }}
    </p>
    @if($attributesInfo->isInitialDispLimitedForAttrNames())
        <div @class(["summary"])>
            @foreach($attributesInfo->getInitialDispAttributes() as $attrKey => $attributeInfo)
                @include('sayt::widgets.inc.attribute-value')
            @endforeach
            <a href="javascript:void(0);" role="button"
               class="mb-0 btn btn-block btn-link btn-sm text-decoration-none show-hide-toggle-btn"
               onclick="toggleShowMoreLess(this, 'full', 'summary');">
                ALL FILTERS
            </a>
        </div>
    @endif

    <div @class(["full", 'd-none' => $attributesInfo->isInitialDispLimitedForAttrNames()])>
        @foreach($attributesInfo->getFullAttributes() as $attrKey => $attributeInfo)
            @include('sayt::widgets.inc.attribute-value')
        @endforeach
        @if($attributesInfo->isInitialDispLimitedForAttrNames())
            <a href="javascript:void(0);" role="button"
               class="mb-0 btn btn-block btn-link btn-sm text-decoration-none show-hide-toggle-btn"
               onclick="toggleShowMoreLess(this, 'summary', 'full');">
                SUGGESTIONS
            </a>
        @endif
    </div>
</div>

@pushonce('plugin-script')
    <script>
        {{--function toggleAllFilters(event, element) {--}}
        {{--    event.preventDefault();--}}
        {{--    element = $(element);--}}
        {{--    if (element.data('current-state') === 'collapsed') {--}}
        {{--        $('.filter-section').each(function() {--}}
        {{--            var div = $(this);--}}
        {{--            div.addClass('d-block');--}}
        {{--            if (div.hasClass('d-none')) {--}}
        {{--                div.removeClass('d-none');--}}
        {{--            }--}}
        {{--        });--}}
        {{--        element.data('current-state', 'expanded');--}}
        {{--        element.html('COLLAPSE ALL <i class="filter-btn-icon pe-7s-angle-up-circle"></i>');--}}
        {{--    } else {--}}
        {{--        $('.filter-section').each(function() {--}}
        {{--            var div = $(this);--}}
        {{--            div.addClass('d-none');--}}
        {{--            if (div.hasClass('d-block')) {--}}
        {{--                div.removeClass('d-block');--}}
        {{--            }--}}
        {{--        });--}}
        {{--        element.data('current-state', 'collapsed');--}}
        {{--        element.html('EXPAND ALL <i class="filter-btn-icon pe-7s-angle-down-circle"></i>');--}}
        {{--    }--}}
        {{--}--}}

        {{--let timer;--}}
        {{--const debounce = function(fn, d) {--}}
        {{--    if (timer) {--}}
        {{--        clearTimeout(timer);--}}
        {{--    }--}}
        {{--    timer = setTimeout(fn, d);--}}
        {{--};--}}

        {{--function toggleSection(element, target_id) {--}}
        {{--    var x = $('#' + target_id);--}}
        {{--    if (x.hasClass('d-none')) {--}}
        {{--        x.addClass('d-block');--}}
        {{--        x.removeClass('d-none');--}}
        {{--        $(element).find('i:first').removeClass('pe-7s-angle-down-circle');--}}
        {{--        $(element).find('i:first').addClass('pe-7s-angle-up-circle');--}}
        {{--    } else {--}}
        {{--        x.addClass('d-none');--}}
        {{--        x.removeClass('d-block');--}}
        {{--        $(element).find('i:first').removeClass('pe-7s-angle-up-circle');--}}
        {{--        $(element).find('i:first').addClass('pe-7s-angle-down-circle');--}}
        {{--    }--}}
        {{--}--}}

        {{--function updateRangeSliderQueryString(key, value) {--}}
        {{--    let prev_query_string = '{{request('ea_server_products') ?? ''}}';--}}
        {{--    let search = window.location.search.substring(1);--}}
        {{--    if (search && search.length) {--}}
        {{--        search = JSON.parse('{"'--}}
        {{--            + decodeURI(search).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g, '":"')--}}
        {{--            + '"}');--}}
        {{--        let ea_server_products = search.ea_server_products;--}}
        {{--        if (ea_server_products && ea_server_products.length) {--}}
        {{--            ea_server_products = ea_server_products.split('/');--}}
        {{--            let filtered_query_string = ea_server_products.filter(ele => {--}}
        {{--                let single_string = ele.toLowerCase();--}}
        {{--                return single_string.startsWith('price') === false;--}}
        {{--            });--}}
        {{--            prev_query_string = filtered_query_string.join('/');--}}
        {{--        }--}}
        {{--    }--}}
        {{--    let uri = window.location.hash.length > 0--}}
        {{--        ? window.location.origin.concat(window.location.pathname).concat(window.location.hash.substring(1))--}}
        {{--        : window.location.href;--}}
        {{--    var re = new RegExp('([?&])' + key + '=.*?(&|$)', 'i');--}}
        {{--    var separator = uri.indexOf('?') !== -1 ? '&' : '?';--}}
        {{--    if (uri.match(re)) {--}}
        {{--        let new_value = prev_query_string.length > 0 ? prev_query_string.concat('/' + value) : value;--}}
        {{--        return uri.replace(re, '$1' + key + '=' + new_value + '$2');--}}
        {{--    } else {--}}
        {{--        return uri + separator + key + '=' + value;--}}
        {{--    }--}}
        {{--}--}}

        {{--function filterMaxMin() {--}}
        {{--    //e.preventDefault();--}}
        {{--    // let buttonProperty       = document.querySelector("#filter-btn-224523");--}}
        {{--    // buttonProperty.innerHTML = 'Please wait....';--}}
        {{--    // buttonProperty.disabled  = true;--}}
        {{--    let min_range_value = {{!empty($priceAttributes[0]->minRangeValue) ? (float) $priceAttributes[0]->minRangeValue : 0}};--}}
        {{--    let max_range_value = {{!empty($priceAttributes[0]->maxRangeValue) ? (float) $priceAttributes[0]->maxRangeValue : 0}};--}}
        {{--    let current_min_value = parseFloat(document.querySelector('.ui-range-value-min input').value);--}}
        {{--    let current_max_value = parseFloat(document.querySelector('.ui-range-value-max input').value);--}}
        {{--    let sliderValue--}}
        {{--        = `Price:\${current_min_value}-\${current_max_value}-\${min_range_value}-\${max_range_value}`;--}}
        {{--    window.location =--}}
        {{--        updateRangeSliderQueryString('ea_server_products', sliderValue);--}}
        {{--}--}}
    </script>
@endpushonce
