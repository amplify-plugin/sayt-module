@pushOnce('footer-script')
    <script src="{{ mix('js/require.js', 'vendor/sayt') }}"></script>
    <script>
        const AMPLIFY_SAYT_CAT_PATH = '{!! \Sayt::getDefaultCatPath() !!}';
        var studioStoreOptions = @json($saytConfiguration);
        $(document).ready(function () {
            $('#search-show-mobile').click(function () {
                $(this).closest('.search-item').toggleClass('show');
                $('#question').trigger('focus');
            });

            $('#search-tools').click(function () {
                $('#question').val('');
                $('#search-tools').closest('.search-item').removeClass('show');
            });

            $('#clear-search').click(function () {
                $('#question').val('');
            });
        });
    </script>
    <script src="{{mix('js/sayt-store.js', 'vendor/sayt')}}"></script>
@endPushOnce

{!!  $style ?? '' !!}

<form {!! $htmlAttributes !!}>
    <div class="search-box ea-search-input-wrapper">
        {{ $slot }}
        <input type="text"
{{--               onkeydown="Sayt.validateField(this, event)"--}}
               class="ea-input-area form-control"
               placeholder="{{ $searchBoxPlaceholder() }}"
               name="q"
               required
               min="{{ $saytConfiguration['minLength'] ?? '100' }}"
               minlength="{{ $saytConfiguration['minLength'] ?? '100' }}"
               maxlength="255"
               max="255"
               id="question"
        >
        <div class="invalid-tooltip">
            Please enter at least {{ $saytConfiguration['minLength'] ?? '100' }} characters for search.
        </div>
        <button id="search"
                type="submit"
                @class(["border-0 btn bg-transparent", 'd-none'  => !$showSearchButton])
{{--        onclick="return Sayt.validateForm(event)"--}}
        >
            <i class="icon-search pb-1" style="font-size: 1.2rem"></i>
        </button>

        <div class="search-tools">
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
