<div {!! $htmlAttributes !!}>
    <x-shop-result-info :render="$showItemCount" class="col"/>
    <x-shop-sorting :render="$showSortingOption" class="col"/>
    <x-shop-page-length :render="$showPerPageOption" class="col mt-4 mt-md-0"/>
    <x-shop-view-style :render="$showProductViewChanger" class="col"/>
</div>
