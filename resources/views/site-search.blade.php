@pushOnce('footer-script')
    {{--    <script src="{{ mix('js/require.js', 'vendor/sayt') }}"></script>
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
        <script src="{{mix('js/sayt-store.js', 'vendor/sayt')}}"></script>--}}
    <script>
        $(document).ready(function () {
            let searchTimeout;
            let currentRequest = null;

            $('#question').on('input', function () {
                const element = $(this);
                const maxLength = parseInt(element.attr('maxlength'));
                const minLength = parseInt(element.attr('minlength'));
                const keyword = element.val().trim();

                element.dropdown('hide');

                clearTimeout(searchTimeout);

                // Abort previous request
                if (currentRequest) {
                    currentRequest.abort();
                    currentRequest = null;
                }

                if (keyword.length < minLength) {
                    errorMessage();
                    return;
                }

                if (keyword.length > maxLength) {
                    errorMessage(`The keyword must not be greater than ${maxLength} characters.`);
                    return;
                }


                searchTimeout = setTimeout(function () {
                    currentRequest = $.ajax({
                        url: '/sayt/search/',
                        method: 'GET',
                        data: {'keyword': keyword},
                        dataType: 'json',
                        success: function (response) {

                            errorMessage();

                            const container = element.siblings('.search-details');

                            console.log(container);

                            container.empty();

                            container.html(response.html);

                            element.dropdown('show');
                        },

                        error: function (xhr, status) {
                            if (status !== 'abort') {
                                element.dropdown('hide');
                                errorMessage(xhr.responseJSON?.message || xhr.statusText);
                            }
                        },

                        complete: function () {
                            currentRequest = null;
                        }
                    });
                }, 500);
            });
        });

        function errorMessage(message = '') {

            const element = $('#question');

            if (message.length > 0) {
                $('<span>', {
                    class: 'invalid-tooltip',
                    text: message,
                }).insertAfter(element);
                element.addClass('is-invalid');
            } else {
                element.next('.invalid-tooltip').remove();
                element.removeClass('is-invalid');
            }
        }
    </script>
@endPushOnce

{!!  $style ?? '' !!}

<form {!! $htmlAttributes !!}>
    <div class="search-box">
        {{ $slot }}
        <input type="text" class="form-control"
               placeholder="{{ $searchBoxPlaceholder() }}"
               name="q" value="{{ $showKeyword() }}"
               required
               min="{{ $saytConfiguration['minLength'] ?? '100' }}"
               minlength="{{ $saytConfiguration['minLength'] ?? '100' }}"
               maxlength="100" max="100" id="question"
               data-toggle="dropdown"
               aria-expanded="false"
        >
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
        <div class="dropdown-menu search-details w-100 p-0"></div>
    </div>
    <div class="search d-md-none" id="search-show-mobile">
        <i class="icon-search"></i>
    </div>
</form>
