<div {!! $htmlAttributes !!}>
    {!! $before ?? '' !!}
    @if (strlen($label) > 0)
        {!! $label !!}
    @endif
    <select onchange="changeSortBy(event)" class="form-control" id="sort-entries" data-toggle="tooltip"
            data-placement="top" title="Sort By">
        @if(!empty($placeholder))
            <option value="" disabled>{{ $placeholder }}</option>
        @endif
        @foreach(eaResultSortBy() as $key => $value)
            <option value="{{$key}}" @if(request('sort_by') == $key) selected @endif >
                {{$value}}
            </option>
        @endforeach
    </select>
    {!!  $after ?? '' !!}
</div>

@push('footer-script')
    <script>
        function changeSortBy(event) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort_by', event.target.value);
            url.searchParams.delete('page');
            window.location.assign(url.toString());
        }
    </script>
@endpush