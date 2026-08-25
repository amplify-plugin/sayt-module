<div class="border-top p-3">
    <a href="{{ frontendShopURL(['search', 'q' => urlencode($query)]) }}"
       class="align-items-baseline d-flex justify-content-between text-black text-decoration-none">
        <span>Search for "<b>{{ $query }}</b>" in all products</span>
        <i class="icon-arrow-right" style="font-size: 150%; font-weight: bold"></i>
    </a>
</div>