@php
    /**
     * @var \Amplify\System\Sayt\Classes\NavigateCategory $category
     */
@endphp
<div {!! $htmlAttributes !!}>
    <div class="row">
        @foreach($categories as $category)
            <div @class(["col-lg-{$gridCount} col-md-6 col-12", $viewMode])>
                <div class="widget widget-categories">
                    <div class="widget-title">
                        <a href="{{ frontendShopURL($category->getSEOPath()) }}"
                           class="widget-title-link">
                            @if(!$displayCategoryImage)
                                <span>
                                    <img src="{{ asset($category->getImage()) }}" class="widget-title-image" />
                                </span>
                            @endif
                            <h3 class="widget-title-link-heading">{{ $category->getName() }}</h3>
                        </a>
                        <i type="button" data-toggle="collapse" data-target="#collapse-{{$category->getID()}}"
                           aria-expanded="true" aria-controls="collapse-{{$category->getID()}}"
                           class="mt-2 pe-7s-angle-down"
                           style="font-size: 24px; font-weight: bolder"></i>
                    </div>
                    <div @class(["collapse show widget-body", $viewMode]) id="collapse-{{$category->getID()}}">
                        @if($category->hasSubCategories())
                            @include("sayt::widgets.inc.categories.{$viewMode}")
                        @else
                            <div class="m-4 border border-warning p-1 rounded">
                                <div class="alert alert-warning alert-dismissible fade show text-center mb-0">
                                    <strong><i class="icon-bell"></i>&nbsp;&nbsp;</strong>
                                    {{ $category->getName() }} doesn't have Sub-category.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
