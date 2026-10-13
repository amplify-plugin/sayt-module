<div {!! $htmlAttributes !!}>
    {!! $before ?? '' !!}
    @if (strlen($label) > 0)
        {!! $label !!}
    @endif
    <div class="btn-group m-0" id="sort-entries"
         @empty($label) data-toggle="tooltip" data-placement="top" title="Sort By" @endempty
    >
        <button class="btn btn-outline-secondary dropdown-toggle"
                type="button" data-toggle="dropdown"
                aria-expanded="false">
            {{ $currentSortLabel }}
        </button>
        <div class="dropdown-menu">
            @foreach(eaResultSortBy() as $key => $value)
                <a class="dropdown-item"
                   href="{{ frontendShopURL([...$extraQuery, 'sort_by' => $key])}}">
                    {{ $value }}
                </a>
            @endforeach
        </div>
    </div>

    {!!  $after ?? '' !!}
</div>