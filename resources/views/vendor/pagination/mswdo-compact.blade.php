@if ($paginator->hasPages())
    <nav class="mswdo-pagination-nav" aria-label="Pagination Navigation">
        <ul class="mswdo-pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="mswdo-page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="mswdo-page-link" aria-hidden="true">
                        <i class="fas fa-chevron-left me-1 small"></i> Previous
                    </span>
                </li>
            @else
                <li class="mswdo-page-item">
                    <a class="mswdo-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="fas fa-chevron-left me-1 small"></i> Previous
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="mswdo-page-item disabled" aria-disabled="true">
                        <span class="mswdo-page-link mswdo-dots">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="mswdo-page-item active" aria-current="page">
                                <span class="mswdo-page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="mswdo-page-item">
                                <a class="mswdo-page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="mswdo-page-item">
                    <a class="mswdo-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        Next <i class="fas fa-chevron-right ms-1 small"></i>
                    </a>
                </li>
            @else
                <li class="mswdo-page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="mswdo-page-link" aria-hidden="true">
                        Next <i class="fas fa-chevron-right ms-1 small"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
