<div style="width: 31%" class="card">
    <a href="{{ frontendSingleProductURL($product) }}" class="text-decoration-none">
        <div class="card-img-top w-100" style="height: 128px;">
            <img src="{{ $product->Product_Image }}"
                 class="img-fluid w-100 h-100"
                 style="object-fit: contain"
                 alt="{{ $product->Product_Name }}">
        </div>
        <div class="card-body p-2">
            <p class="card-title product-name"
               data-toggle="tooltip"
               title="{{ $product->Product_Name }}"
               style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden">
                {{ $product->Product_Name }}
            </p>
            <small class="card-text product-description">{{ $product->Product_Description }}</small>
            <p class="font-weight-bold text-black text-center">{{ currency_format($product->Msrp->toFloat(), withSymbol: true) }}</p>
        </div>
    </a>
</div>