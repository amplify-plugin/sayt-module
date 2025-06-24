<div {!! $htmlAttributes !!}>
    <div class="custom-card-in-stock d-flex justify-content-between align-center">
        <span class="custom-card-title-in-stock">{{ $label }}</span>
        <label class="switch">
            <input type="checkbox" {{ $defaultState }} name="stock" value="yes" onchange="onInStockChecked(event);" id="instock">
            <span class="slider"></span>
        </label>
    </div>
</div>

@pushonce('footer-script')
    <script>
        function onInStockChecked(e) {
            console.log(e.target.value);
            window.location = updateQueryStringParameter('stock', e.target.value);
        }

        if (typeof updateQueryStringParameter === 'undefined') {
            function updateQueryStringParameter(key, value) {

                let uri = new URL(window.location.href);
                let queries = {};

                uri.searchParams.forEach((value, query) => queries[query] = value);
                queries[key] = value;

                if (queries.hasOwnProperty('sort_by') || queries.hasOwnProperty('per_page')) {
                    if (queries.hasOwnProperty('page')) {
                        delete queries.page;
                    }
                }

                return (uri.origin + uri.pathname + '?') + (new URLSearchParams(queries)).toString();

            }
        }
    </script>
@endpushonce
