@if($paginator->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
        <div class="text-sm text-gray-600">Showing {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} {{ $label ?? 'records' }}</div>
        <div class="flex items-center gap-2">
            <a href="{{ $paginator->previousPageUrl() ?: '#' }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $paginator->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}"><i class="fas fa-chevron-left text-gray-600"></i><span class="sr-only">Previous page</span></a>
            @php($current = $paginator->currentPage())
            @php($last = $paginator->lastPage())
            @php($start = max(1, $current - 2))
            @php($end = min($last, $current + 2))
            @if($start > 1)<a href="{{ $paginator->url(1) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm">1</a>@if($start > 2)<span class="px-2 text-gray-400">...</span>@endif @endif
            @for($page = $start; $page <= $end; $page++)
                <a href="{{ $paginator->url($page) }}" class="inline-flex items-center justify-center w-9 h-9 {{ $current == $page ? 'bg-[#C41E3A] text-white border border-[#C41E3A] font-medium' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }} rounded-lg text-sm">{{ $page }}</a>
            @endfor
            @if($end < $last) @if($end < $last - 1)<span class="px-2 text-gray-400">...</span>@endif <a href="{{ $paginator->url($last) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm">{{ $last }}</a>@endif
            <a href="{{ $paginator->nextPageUrl() ?: '#' }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $paginator->currentPage() == $paginator->lastPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}"><i class="fas fa-chevron-right text-gray-600"></i><span class="sr-only">Next page</span></a>
        </div>
    </div>
@endif
