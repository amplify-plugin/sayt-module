<div {!! $htmlAttributes  !!}>
    @if($entries->isEmpty())
        <x-shop-empty-result :message="'Sorry no ' .Str::plural(strtolower($searchAttribute)).' available. Try out our shop page.'"
                             :title="ucwords('No ' . \Str::plural($searchAttribute). ' available')"/>
    @else
        <div class="d-inline-block d-md-flex justify-content-between align-items-center gap-3 w-100">
            <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link active" href="#" data-filter="*">All</a></li>
                @foreach($groups as $group)
                    @php $group = ($group == '~') ? '@' : $group @endphp
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-filter=".{{ $group }}">
                            {{ $group }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="input-group" style="width: 250px">
            <span class="input-group-btn">
                <i class="icon-search"></i>
            </span>
                <input class="form-control" type="text" placeholder="Search Brands"/>
            </div>
        </div>
        <div class="isotope-grid filter-grid grid-no-gap cols-{{$itemsPerRow}} mt-3">
            <div class="gutter-sizer"></div>
            <div class="grid-sizer"></div>
            <!-- Product-->
            @foreach($entries as $key => $group)
                @php $key = $key == '~' ? '@' : $key @endphp
                @foreach($group as $entry)
                    <div class="grid-item {{ $key }}"
                         data-search="{{ $entry->getValue()?->attributeValue }}">
                        <div class="card" style="height: {{$imageHeight+65}}px">
                            <div class="card-body">
                                <a href="{{ frontendShopURL($entry->getSEOPath()) }}">
                                    <img
                                            class="card-img-top"
                                            style="height: {{ $imageHeight }}px"
                                            src="{{ $brandImage($entry->getValue()->attributeValue) }}"
                                            alt="{{ $entry->getValue()->attributeValue }}">
                                </a>
                                <h5 class="card-title">
                                    <a href="{{ frontendShopURL($entry->getSEOPath()) }}" class="text-decoration-none">
                                        {{ $entry->getValue()?->attributeValue ?? 'N/A' }}
                                    </a>
                                    @if($showProductCount)
                                        <span class="text-muted">
                                            ({{ $entry->getProductCount() }})
                                        </span>
                                    @endif
                                </h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    @endif
</div>
@pushonce('footer-script')
    <script>
        $(document).ready(function () {
            if ($('.filter-grid').length > 0) {
                $('input[type=text]').keyup(function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    $('.filter-grid').isotope({
                        filter: function () {
                            return $(this).attr('data-search').toLowerCase().includes(e.target.value.toLowerCase());
                        }
                    });
                });
            }
        });
    </script>
@endpushonce