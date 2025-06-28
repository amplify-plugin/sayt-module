@php
    /**
     * @var \Amplify\System\Sayt\Classes\NavigateCategory $category
     */
@endphp
<style>
    .widget-body > ul {
        height: 250px !important;
        overflow-y: auto;
    }
</style>
<div {!! $htmlAttributes !!}>
    <div class="row">
        @foreach($categories as $category)
            <div class="col-lg-{{$gridCount}} col-md-6 col-12">
                <div class="widget widget-categories">
                    <div class="widget-title border-bottom d-flex justify-content-between align-center p-1">
                        <a href="{{ frontendShopURL($category->getSEOPath()) }}"
                           class="text-decoration-none align-center d-flex justify-content-start gap-3">
                            @if(!$displayCategoryImage)
                                <span style="width: 35px; height: 35px;">
                                    <img src="{{ asset($category->getImage()) }}"
                                         style="width: 100%; height: 100%; object-fit: contain" />
                                </span>
                            @endif
                            <h3 class="mb-0">{{ $category->getName() }}</h3>
                        </a>
                        <i type="button" data-toggle="collapse" data-target="#collapse-{{$category->getID()}}"
                           aria-expanded="true" aria-controls="collapse-{{$category->getID()}}"
                           class="mt-2 pe-7s-angle-down"
                           style="font-size: 24px; font-weight: bolder"></i>
                    </div>
                    <div @class(["collapse show widget-body", $videMode, 'd-none' => !$category->hasSubCategories()]) id="collapse-{{$category->getID()}}">
                        @include("sayt::widgets.inc.categories.grid")
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
