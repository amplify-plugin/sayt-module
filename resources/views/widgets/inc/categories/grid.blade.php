@if($category->hasSubCategories())
    <div class="isotope-grid cols-6 mb-2">
        @foreach($category->getSubCategories() as $subCategory)
            <div class="grid-item">
                <div class="card">
                    <img class="card-img-top" src="{{ asset($subCategory->getImage() )}}" />
                    <div class="card-body">
                        <a href="{{ frontendShopURL($subCategory->getSEOPath()) }}">
                            <h3 class="card-title">
                                {{ $subCategory->getName() }}
                                <span>
                                    @if($displayProductCount)
                                        ({{ $subCategory->getProductCount() }})
                                    @endif
                                </span>
                            </h3>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
