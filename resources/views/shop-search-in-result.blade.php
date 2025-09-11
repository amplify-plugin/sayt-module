<section {!! $htmlAttributes !!}>
    <div class="custom-card">
        <p class="custom-card-title">{{ $title }}</p>
        <div class="custom-input-group">
            <div class="custom-input-container">
                <span class="custom-icon"><i class="icon-search pb-1" style="font-size: 1.2rem;"></i></span>
                <input type="text"
                       id="{{ $uuid }}"
                       placeholder="{{$searchBoxPlaceholder() }}">
            </div>
            <button class="custom-btn" type="button" role="button"
                    onclick="searchInResults(event)">{{ $btnLabel }}</button>
        </div>
    </div>
</section>

@pushonce('footer-script')
    <script>
        function searchInResults() {
            let scope = "{!! $currentUrl() !!}";
            let search = document.getElementById('{{ $uuid }}').value;
            window.location.replace(`${scope}&q=${search}`);
        }

        $(function() {
            $('#{{$uuid}}').on('keydown', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    searchInResults(e);
                }
            });
        });
    </script>
@endpushonce
