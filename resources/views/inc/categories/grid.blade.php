@if($category->hasSubCategories())
    <ul>
        <div class="isotope-grid grid-no-gap cols-{{ $itemsPerCategory }}">
            <div class="gutter-sizer"></div>
            <div class="grid-sizer"></div>
            @foreach($category->getSubCategories() as $subCategory)
                <div class="grid-item p-1">
                    <a href="{{ $redirectPage($subCategory) }}" class="text-decoration-none widget-link">
                        <div class="card p-2">
                            <div style="height: 128px; width: 100%; text-align: center">
                                <img class="card-img-top" src="{{ asset($subCategory->getImage() )}}"
                                     style="object-fit: contain; width: 100%; height: 100%" />
                            </div>
                            <div class="card-body p-0 mt-2" data-toggle="tooltip" data-placement="top"
                                 title="{{ $subCategory->getName() }}<strong> @if($displayProductCount)({{ $subCategory->getProductCount() }})@endif</strong>"
                                 data-html="true">
                                <p class="mb-0"
                                   style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient:vertical; overflow: hidden; ">
                                    {{ $subCategory->getName() }}
                                    @if($displayProductCount)
                                        <span class="text-muted">
                                            ({{ $category->getProductCount() }})
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </ul>
@endif
