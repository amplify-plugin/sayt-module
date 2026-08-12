<div {!! $htmlAttributes !!}>
    {{-- Keep tooltip CSS in this package view so other themes/projects inherit the fix. --}}
    <style>
        .tooltip.current-filter-tooltip {
            pointer-events: none !important;
        }
    </style>
    <section class="mb-1 widget widget-categories">
        <div class="d-flex justify-content-between border-bottom" style="margin-top: 1rem;">
            <p class="widget-title">Current Filters</p>
            <a href="{{ frontendShopURL($query) }}"
               data-toggle="tooltip"
               data-placement="left"
               data-container="body"
               data-boundary="window"
               data-template='<div class="tooltip current-filter-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
               title="Remove All"
               class="d-inline-flex align-items-center rounded text-danger text-decoration-none current-filter-remove"
               style="padding: 6px 0;"
               aria-label="Remove All"
               aria-current="page">
                <i class="filter-btn-icon pe-7s-close-circle text-danger font-weight-bold" aria-hidden="true"></i>
            </a>
        </div>
        <ul class="shop-sidebar-option-list mt-3 d-block list-unstyled fw-normal pb-1 small">
            @foreach($filters as $key => $filter)
                @php
                    $label = ($filter->getType() == 2) ? $filter->getName() . ": " . $filter->getValue() : $filter->getValue();
                    $removeLabel = 'Remove ' . ucwords($label);
                @endphp
                <li class="active">
                    <a href="{{ frontendShopURL([$filter->getSEOPath(), ...$extraQuery]) }}"
                       class="d-inline-flex align-items-center rounded active current-filter-remove"
                       data-toggle="tooltip"
                       data-placement="right"
                       data-container="body"
                       data-boundary="window"
                       data-template='<div class="tooltip current-filter-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                       title="{{ $removeLabel }}"
                       aria-label="{{ $removeLabel }}"
                       aria-current="page">
                        <i class="pe-7s-close-circle close-icon" aria-hidden="true"></i>
                        {{ ucwords($label) }}
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
</div>
