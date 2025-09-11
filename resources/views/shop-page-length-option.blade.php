<div {!! $htmlAttributes !!}>
    <select onchange="onPerPage(event)" class="form-control" id="sorting" data-toggle="tooltip"
            data-placement="top" title="Items Per Page">
        <option value="" disabled>Per Page --</option>
        @foreach(getPaginationLengths() as $value)
            <option value="{{$value}}" @if($perPage == $value) selected @endif>
                {{$value}}
            </option>
        @endforeach
    </select>
</div>
@pushonce('footer-script')
    <script>
        function onPerPage(e) {
            window.location = updateQueryStringParameter('per_page', e.target.value);
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
