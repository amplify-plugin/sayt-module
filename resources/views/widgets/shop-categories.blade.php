<div {!! $htmlAttributes !!}>
    <div class="row">
        @foreach($categories as $category)
            <div @class(["col-lg-{$gridCount} col-md-6 col-12", $viewMode])>
                @include($viewPath)
            </div>
        @endforeach
    </div>
</div>
