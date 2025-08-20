<div {!!  $htmlAttributes !!}>
    <p @class(["widget-title shop-sidebar-attribute-title", "d-none" => strlen($groupTitle) == 0])>
        {{ $groupTitle ?? '' }}
    </p>
    <div>
        @if($attributesInfo->isInitialDispLimitedForAttrNames())
            <div @class(["attribute-filter-summary"])>
                @foreach($attributesInfo->getInitialDispAttributes() as $attrKey => $attributeInfo)
                    @include('sayt::widgets.inc.attribute-value')
                @endforeach
                @if(count($attributesInfo->getFullAttributes()) > $attributesInfo->getInitialDispLimitForAttrNames())
                    <a href="javascript:void(0);" role="button"
                       class="my-0 btn btn-block btn-link btn-sm text-decoration-none show-hide-toggle-btn"
                       onclick="toggleShowMoreLess(this, 'attribute-filter-full', 'attribute-filter-summary');">
                        {{ trans('SHOW MORE...') }}
                    </a>
                @endif
            </div>
        @endif
        <div @class(["attribute-filter-full", 'd-none' => $attributesInfo->isInitialDispLimitedForAttrNames()])>
            @foreach($attributesInfo->getFullAttributes() as $attrKey => $attributeInfo)
                @include('sayt::widgets.inc.attribute-value')
            @endforeach
            @if($attributesInfo->initialAttributesExists())
                <a href="javascript:void(0);" role="button"
                   class="my-0 btn btn-block btn-link btn-sm text-decoration-none show-hide-toggle-btn"
                   onclick="toggleShowMoreLess(this, 'attribute-filter-summary', 'attribute-filter-full');">
                    {{ trans('SHOW LESS...') }}
                </a>
            @endif
        </div>
    </div>
</div>
