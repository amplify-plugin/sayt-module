<div class="product-card">
    <a href="{{ frontendSingleProductURL($product) }}" class="text-decoration-none">
        <div class="product-image">
            <img src="{{ assets_image($product->Product_Image) }}"
                 alt="{{ $product->Product_Name }}">
        </div>
        <div class="product-info">
            <p class="product-title" title="{{ $product->Product_Name }}">
                {{ $product->Product_Name }}
            </p>

            {{--            <p class="product-desc" title="{{ $product->Product_Description }}">
                            {{ $product->Product_Description }}
                        </p>--}}

            <span class="product-price" title="{{ $product->Msrp }}">
                {{ currency_format($product->Msrp->toFloat(), withSymbol: true) }}
            </span>
        </div>
    </a>
</div>