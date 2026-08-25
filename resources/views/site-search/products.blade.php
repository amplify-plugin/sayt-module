<div style="width: 70%" class="border border-top-0 border-bottom-0">
    <span class="border-bottom d-block font-weight-bold p-3">
        Products @if(!empty($total))
            ({{ number_format($total) }})
        @endif
    </span>
    <div class="w-100 d-flex gap-3 flex-wrap p-3" style="height: 300px; overflow-y: auto">
        @foreach($products as $product)
            @include('sayt::site-search.item')
        @endforeach
    </div>
</div>