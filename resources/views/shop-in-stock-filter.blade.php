<div {!! $htmlAttributes !!}>
    <div class="custom-card-in-stock d-flex justify-content-between align-center">
        <span class="custom-card-title-in-stock">{{ $label }}</span>
        <label class="switch" data-toggle="tooltip"
               data-title="@if($disabled) {{ $disabledPopUp }} @elseif($checked) {{ $checkedPopUp }} @else {{ 'All Products' }} @endif">
            <input type="checkbox" @checked($checked) name="stock"
                   @disabled($disabled)
                   data-url="{{ frontendShopURL([$currentSeoPath, ...$extraQuery]) }}"
                   onchange="onInStockChecked($(this));">
            <span @class(["slider", "disabled" => $disabled])></span>
        </label>
    </div>
</div>

@pushonce('footer-script')
    <script>
        function onInStockChecked(element) {
            window.location = element.attr('data-url');
        }
    </script>
@endpushonce
