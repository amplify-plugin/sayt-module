<div {!! $htmlAttributes !!}>
    <div class="custom-card-in-stock d-flex justify-content-between align-center">
        <span class="custom-card-title-in-stock">{{ $label }}</span>
        <label class="switch">
            <input type="checkbox" {{ $defaultState }} name="stock" value="yes" onchange="onInStockChecked(event);"
                   id="instock">
            <span class="slider"></span>
        </label>
    </div>
</div>

@pushonce('footer-script')
    <script>
        function onInStockChecked(e) {
            window.location = (e.target.checked)
                ? updateQueryStringParameter('stock', e.target.value)
                : updateQueryStringParameter('stock', null);
        }

        if (typeof updateQueryStringParameter === 'undefined') {
            function updateQueryStringParameter(key, value) {

                let uri = new URL(window.location.href);
                let queries = {};

                uri.searchParams.forEach((value, query) => queries[query] = value);

                queries[key] = value;

                if (key === 'stock' && value == null) {
                    delete queries[key];
                }

                if (queries.hasOwnProperty('sort_by') || queries.hasOwnProperty('per_page')) {
                    if (queries.hasOwnProperty('page')) {
                        delete queries.page;
                    }
                }

                let queryString = (new URLSearchParams(queries)).toString();

                return (uri.origin + uri.pathname) + (queryString.length > 0 ? `?${queryString}` : '');

            }
        }
    </script>
@endpushonce
