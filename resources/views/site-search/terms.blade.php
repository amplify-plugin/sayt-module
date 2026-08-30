<div @class(["suggestions", 'd-none' => !$suggestions->hasSuggestion()])>
    <span>Search Terms</span>
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