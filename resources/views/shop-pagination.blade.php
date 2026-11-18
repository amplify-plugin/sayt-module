@if ($paginator->hasPages())
    <div {!! $htmlAttributes !!}>
        <nav>
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item previous disabled" aria-disabled="true"
                        aria-label="@lang('pagination.previous')">
                        <span class="page-link" aria-hidden="true">
                            {!! $prevLabel !!}
                        </span>
                    </li>
                @else
                    <li class="page-item previous">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                           aria-label="@lang('pagination.previous')">
                            {!! $prevLabel !!}
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @if(request()->isMobile())
                    @php
                        $current = $paginator->currentPage();
                        $last = $paginator->lastPage();
                        $onEachSide = 1;

                        $pages = collect()
                            ->push(1)
                            ->merge(range(max(2, $current - $onEachSide), min($last - 1, $current + $onEachSide)))
                            ->push($last)
                            ->unique()
                            ->sort()
                            ->values();

                        $slots = collect();
                        foreach ($pages as $page) {
                            if ($slots->isNotEmpty() && $page - $slots->last() > 1) {
                                $slots->push('...');
                            }
                            $slots->push($page);
                        }
                    @endphp
                    @foreach ($slots as $slot)
                        @if ($slot === '...')
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link">{{ $slot }}</span>
                            </li>
                        @elseif ($slot == $current)
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $slot }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $paginator->url($slot) }}">
                                    {{ number_format($slot) }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @else
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link">{{ $element }}</span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">
                                            {{ number_format($page) }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endif
                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item next">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
                           aria-label="@lang('pagination.next')">
                            {!! $nextLabel !!}
                        </a>
                    </li>
                @else
                    <li class="page-item next disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                        <span class="page-link" aria-hidden="true">
                            {!! $nextLabel !!}
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
