@if ($paginator->hasPages())
    <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 1rem;">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span style="padding: 0.5rem 1rem; border: 1px solid #E5E7EB; border-radius: 6px; background: #F8FAFC; color: #6B7280; cursor: not-allowed;">&laquo; Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="padding: 0.5rem 1rem; border: 1px solid #E5E7EB; border-radius: 6px; background: #FFFFFF; color: #1F2937; text-decoration: none;">&laquo; Previous</a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding: 0.5rem 1rem; border: 1px solid #E5E7EB; border-radius: 6px; background: #F8FAFC; color: #6B7280;">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding: 0.5rem 1rem; border: 1px solid #1A237E; border-radius: 6px; background: #1A237E; color: white;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding: 0.5rem 1rem; border: 1px solid #E5E7EB; border-radius: 6px; background: #FFFFFF; color: #1F2937; text-decoration: none;">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="padding: 0.5rem 1rem; border: 1px solid #E5E7EB; border-radius: 6px; background: #FFFFFF; color: #1F2937; text-decoration: none;">Next &raquo;</a>
        @else
            <span style="padding: 0.5rem 1rem; border: 1px solid #E5E7EB; border-radius: 6px; background: #F8FAFC; color: #6B7280; cursor: not-allowed;">Next &raquo;</span>
        @endif
    </div>
@endif
