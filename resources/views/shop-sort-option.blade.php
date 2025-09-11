<div {!! $htmlAttributes !!}>
    <div class="col mt-4 mt-md-0 shop-toolbar-sort-option">
        <select onchange="onSortPage(event)" class="form-control" id="sortby" data-toggle="tooltip"
                data-placement="top" title="Sort By">
            <option value="" disabled>Sort By ---</option>
            @foreach(getPaginationSortBy() as $key => $value)
                <option value="{{$key}}" @if(request('sort_by') == $key) selected @endif >
                    {{$value}}
                </option>
            @endforeach
        </select>
    </div>
</div>
@pushonce('footer-script')
    <script>
        function onSortBy(e) {
            window.location = updateQueryStringParameter('sort_by', e.target.value);
        }
    </script>
@endpushonce

@pushonce('footer-script')
    <script>
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
    </script>
@endpushonce
