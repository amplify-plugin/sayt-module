<div {!! $htmlAttributes !!}>
    <x-shop-result-info :render="$showItemCount" class="col"/>
    <x-shop-sorting :render="$showSortingOption" class="col"/>
    <x-shop-page-length :render="$showPerPageOption" class="col mt-4 mt-md-0"/>
    <x-shop-view-style :render="$showProductViewChanger" class="col"/>
</div>

@pushonce('footer-script')
    <script>
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

            let queryString = (new URLSearchParams(queries)).toString();

            return (uri.origin + uri.pathname) + (queryString.length > 0 ? `?${queryString}` : '');
        }
    </script>
@endpushonce
