@pushOnce('footer-script')
    <script src="{{asset('vendor/easyask-sayt/js/require.js') }}"></script>
    <script src="{{asset("sayt-store.js")}}"></script>
    <script>
        $(document).ready(function() {
            $('#search-show-mobile').click(function() {
                $(this).closest('.search-item').toggleClass('show');
                $('#question').trigger('focus');
            });

            // let searchClose = document.getElementById('tes');

            $('#search-tools').click(function() {
                $('#question').val('');
                $('#search-tools').closest('.search-item').removeClass('show');
            });

            // let clearSearch = document.getElementById('tes');

            $('#clear-search').click(function() {
                $('#question').val('');
            });
        });
    </script>
@endPushOnce

{!!  $style ?? '' !!}

<form method="get" action="{{ frontendShopURL('search') }}" {!! $htmlAttributes !!}>
    <div class="d-flex search-box align-items-center ea-search-input-wrapper search-form">
        {{ $slot }}
        <input type="text"
               class="ea-input-area form-control"
               placeholder="{{ $searchBoxPlaceholder() }}"
               name="q"
               id="question"
        >

        <button id="search" @class(["border-0 btn bg-transparent", 'd-none'  => !$showSearchButton]) type="submit">
            <i class="icon-search pb-1" style="font-size: 1.2rem"></i>
        </button>

        <div class="search-tools gap-3 d-flex align-items-center d-md-none">
            <span id="clear-search" class="clear-search text-uppercase">Clear</span>
            <span id="search-tools" class="close-search">
                <i class="icon-cross"></i>
            </span>
        </div>
    </div>
    <div class="search d-md-none" id="search-show-mobile">
        <i class="icon-search"></i>
    </div>
</form>
