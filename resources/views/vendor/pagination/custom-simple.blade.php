<div class="sc-pagination-controls">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <button type="button" class="sc-page-btn" disabled aria-label="Previous page">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            <span>Previous</span>
        </button>
    @else
        <a href="{{ $paginator->appends(request()->query())->previousPageUrl() }}" class="sc-page-btn" aria-label="Previous page">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            <span>Previous</span>
        </a>
    @endif

    {{-- Current Page --}}
    <button type="button" class="sc-page-btn active" aria-current="page">{{ $paginator->currentPage() }}</button>

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->appends(request()->query())->nextPageUrl() }}" class="sc-page-btn" aria-label="Next page">
            <span>Next</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
    @else
        <button type="button" class="sc-page-btn" disabled aria-label="Next page">
            <span>Next</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>
    @endif
</div>