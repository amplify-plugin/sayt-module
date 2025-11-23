<section class="widget widget-categories widget-attributes">
    <div class="widget-wrapper"
         data-toggle="collapse"
         href="#attribute_{{$attrKey}}"
         role="button"
         aria-expanded="@if($attributeInfo->hasSelectedAttribute() || ($initialAttrExpended && $initialAttrExpendedLimit > $attrKey)) true @else false @endif"
         aria-controls="attribute_{{$attrKey}}"
    >
        <p class="widget-title"
           data-toggle="tooltip"
           data-placement="top"
           title="{{$attributeInfo->getName() ?? ''}}">
            {{$attributeInfo->getName() ?? ''}}
        </p>
        <a class="toggle-btn"
           title="Expand or Collapse">
            <i class="toggle-btn-icon {{ $toggleIconClass ?? null }}"></i>
        </a>
    </div>
    @if($attributeInfo->getAttrType() == 4)
        @include('sayt::inc.type.color')

    @elseif($attributeInfo->getAttrType() == 3)
        @include('sayt::inc.type.price')

    @elseif($attributeInfo->getAttrType() == 2)
        @include('sayt::inc.type.range')

    @else
        @include('sayt::inc.type.default')
    @endif
</section>
