<div {!! $htmlAttributes !!}>
    {!! $before ?? '' !!}
    @if(strlen($label) > 0)
        {!! $label !!}
    @endif
    <div class="btn-group m-0" id="per-page-entries" data-toggle="tooltip"
         data-placement="top" title="Results Per Page">
        <button class="btn btn-outline-secondary dropdown-toggle"
                type="button" data-toggle="dropdown"
                aria-expanded="false">
            {{ $perPage }}
        </button>
        <div class="dropdown-menu">
            @foreach(getPaginationLengths() as $length)
            <a class="dropdown-item"
               href="{{ frontendShopURL([...$extraQuery, 'per_page' => $length])}}">
                {{ $length }}
            </a>
            @endforeach
        </div>
    </div>
    {!!  $after ?? '' !!}
</div>
