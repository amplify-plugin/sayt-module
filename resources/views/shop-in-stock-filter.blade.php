<div {!! $htmlAttributes !!}>
    <div class="custom-card-in-stock d-flex justify-content-between align-center">
        <span class="custom-card-title-in-stock">{{ $label }}</span>
        <label class="switch">
            <input type="checkbox" @checked($checked) name="stock"
                   data-url="{{ frontendShopURL($currentSeoPath, $extraQuery) }}"
                   onchange="onInStockChecked($(this));">
            <span class="slider"></span>
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
