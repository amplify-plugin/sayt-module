<div @class(["categories", "d-none" => empty($categories)])>
    <span>Categories</span>
    <ul>
        @foreach($categories as $category)
            <li><a href="{{ frontendShopURL($category->getSEOPath()) }}"
                   class="text-decoration-none text-capitalize">
                    {{ $category->getName() }}
                </a>
            </li>
        @endforeach
    </ul>
</div>