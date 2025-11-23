<div {!! $htmlAttributes !!}>
    {!! $before ?? '' !!}
    @if(strlen($label) > 0)
        {!! $label !!}
    @endif
    <select onchange="Sayt.perPageChange(event)" class="form-control" id="per-page-entries" data-toggle="tooltip"
            data-placement="top" title="Results Per Page">
        @if(!empty($placeholder))
            <option value="" disabled>{{ $placeholder }}</option>
        @endif
        @foreach(getPaginationLengths() as $value)
            <option value="{{$value}}" @if($perPage == $value) selected @endif>
                {{$value}}
            </option>
        @endforeach
    </select>
    {!!  $after ?? '' !!}
</div>
