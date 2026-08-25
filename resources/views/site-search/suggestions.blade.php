<div style="width: 30%">
    <span class="p-3 d-block font-weight-bold border-bottom">Suggestions</span>
    <div class="w-100 p-3 border-bottom-0">
        <div @class(["suggestions border-bottom mb-3", "d-none" => $suggestions->getSuggestions() > 0])>
            <span class="font-weight-bold">Search Terms</span>
            <ul>
                @foreach($suggestions as $suggestion)
                    <li><a href="{{ frontendShopURL(['search', 'q' => $suggestion->getValue()]) }}"
                           class="text-decoration-none text-capitalize">
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
</div>