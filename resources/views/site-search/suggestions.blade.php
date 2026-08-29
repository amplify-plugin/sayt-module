<div class="suggestions-wrapper">
    <span class="suggestions-header">
        Suggestions for "<em>{{ $query }}</em>".
    </span>
    <div class="suggestions">
        <ul class="suggestion-terms">
            @foreach($suggestions as $suggestion)
                <li class="suggestion-term">
                    <a href="{{ frontendShopURL(['search', 'q' => $suggestion->getValue()]) }}"
                       class="text-decoration-none">
                        <em>{{ $suggestion->getValue() }}</em>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <div @class(["categories", "d-none" => empty($categories)])>
        <span class="font-weight-bold">Categories</span>
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
</div>