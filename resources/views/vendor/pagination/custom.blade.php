@if ($paginator->hasPages())
    <style>
        .ssg-pagination { display:flex; justify-content:center; gap:0.5rem; margin-top:1rem; flex-wrap:wrap; }
        .ssg-pagination .pg-btn,
        .ssg-pagination .pg-active,
        .ssg-pagination .pg-dots {
            padding:0.5rem 1rem; border:1px solid #E5E7EB; border-radius:6px;
            font-size:0.85rem; line-height:1.2; white-space:nowrap; user-select:none;
        }
        .ssg-pagination .pg-active { background:#1A237E; color:#fff; border-color:#1A237E; }
        .ssg-pagination .pg-btn { background:#fff; color:#1F2937; text-decoration:none; }
        .ssg-pagination .pg-btn:hover { background:#F1F5F9; }
        .ssg-pagination .pg-dots { background:#F8FAFC; color:#6B7280; cursor:default; }
        .ssg-pagination .pg-disabled { background:#F8FAFC; color:#6B7280; cursor:not-allowed; }
        @media(max-width:640px){
            .ssg-pagination { gap:0.35rem; }
            .ssg-pagination .pg-btn,
            .ssg-pagination .pg-active,
            .ssg-pagination .pg-dots {
                padding:0.4rem 0.55rem; font-size:0.8rem; min-width:34px; text-align:center;
            }
            .ssg-pagination .pg-prev-text,
            .ssg-pagination .pg-next-text { display:none; }
            .ssg-pagination .pg-prev-arrow,
            .ssg-pagination .pg-next-arrow { display:inline; }
        }
        @media(min-width:641px){
            .ssg-pagination .pg-prev-arrow,
            .ssg-pagination .pg-next-arrow { display:none; }
        }
    </style>
    <div class="ssg-pagination">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="pg-btn pg-disabled" aria-disabled="true">
                <span class="pg-prev-arrow">&lsaquo;</span>
                <span class="pg-prev-text">&laquo; Previous</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pg-btn">
                <span class="pg-prev-arrow">&lsaquo;</span>
                <span class="pg-prev-text">&laquo; Previous</span>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pg-dots">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pg-active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pg-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pg-btn">
                <span class="pg-next-text">Next &raquo;</span>
                <span class="pg-next-arrow">&rsaquo;</span>
            </a>
        @else
            <span class="pg-btn pg-disabled" aria-disabled="true">
                <span class="pg-next-text">Next &raquo;</span>
                <span class="pg-next-arrow">&rsaquo;</span>
            </span>
        @endif
    </div>
@endif
