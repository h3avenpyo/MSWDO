{{-- Previous --}}
@if ($paginator->onFirstPage())
    <button class="sc-page-btn" disabled>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Previous
    </button>
@else
    <a href="{{ $paginator->appends(request()->query())->previousPageUrl() }}" class="sc-page-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Previous
    </a>
@endif

{{-- Current Page --}}
<button class="sc-page-btn active">{{ $paginator->currentPage() }}</button>

{{-- Next --}}
@if ($paginator->hasMorePages())
    <a href="{{ $paginator->appends(request()->query())->nextPageUrl() }}" class="sc-page-btn">
        Next
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    </a>
@else
    <button class="sc-page-btn" disabled>
        Next
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    </button>
@endif