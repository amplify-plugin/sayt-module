<div {!! $htmlAttributes !!}>
    {!! $before ?? '' !!}
    @if(strlen($label) > 0)
        {!! $label !!}
    @endif
    <select onchange="Sayt.sortByChange(event)" class="form-control" id="sort-entries" data-toggle="tooltip"
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
