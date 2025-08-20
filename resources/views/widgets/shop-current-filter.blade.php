<div {!! $htmlAttributes !!}>
    <section class="mb-1 widget widget-categories">
        <div class="d-flex justify-content-between border-bottom" style="margin-top: 1rem;">
            <p class="widget-title">Current Filters</p>
            <a href="{{ frontendShopURL() }}"
               data-toggle="tooltip" data-placement="top" title="Remove All"
               class="d-inline-flex align-items-center rounded text-danger text-decoration-none"
               style="padding: 6px 0;"
               aria-current="page">
                <i class="filter-btn-icon pe-7s-close-circle text-danger font-weight-bold"></i>
            </a>
        </div>
        <ul class="shop-sidebar-option-list mt-3 d-block list-unstyled fw-normal pb-1 small">
            @foreach($filters as $key => $filter)
                @php
                    $label = ($filter->getType() == 2) ? $filter->getName() . ": " . $filter->getValue() : $filter->getValue();
                @endphp
                <li class="active">
                    <a href="{{ frontendShopURL([$filter->getSEOPath(), ...$extraQuery]) }}"
                       class="d-inline-flex align-items-center rounded active"
                       data-toggle="tooltip"
                       data-placement="top"
                       title="Remove {{ ucwords($label) }}"
                       aria-current="page">
                        <i class="pe-7s-close-circle close-icon"></i>
                        {{ ucwords($label) }}
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
</div>
