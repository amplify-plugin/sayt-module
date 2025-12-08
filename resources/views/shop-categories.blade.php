<div {!! $htmlAttributes !!}>
    <div class="row">
        @foreach($categories as $category)
            <div @class(["col-lg-{$gridCount} col-md-6 col-12 px-0", $viewMode])>
                @include($viewPath)
            </div>
        @endforeach
    </div>
</div>

@if($viewMode == 'list' && !$showOnlyCategory)
    @pushonce('footer-script')
        <script>
            function getElementHeight(element) {
                const computedStyle = getComputedStyle(element);
                const offsetHeight = element.offsetHeight;

                const marginTop = parseFloat(computedStyle.marginTop);
                const marginBottom = parseFloat(computedStyle.marginBottom);

                return offsetHeight + marginTop + marginBottom;
            }

            document.addEventListener('DOMContentLoaded', () => {
                let maxItem = {{ $itemsPerCategory }};
                let listItem = getElementHeight(document.querySelector('li.shop-category-item'));
                let listWrappers = document.querySelectorAll('ul.shop-category-list');

                listWrappers.forEach((listWrapper) => {
                    listWrapper.style.overflowY = 'auto';
                    listWrapper.style.height = (maxItem * listItem).toString()+'px';
                });
            });
        </script>
    @endpushonce
@endif
