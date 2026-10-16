<form method="POST" action="{{ $archiveUrl }}" class="inline-flex" onclick="event.stopPropagation()" data-confirm-action="archive" data-confirm-message="Archive this case? It will be removed from the case list." @if(auth()->user()->isBcpcAdmin()) data-confirm-title="Archive Case?" @endif>
    @csrf
    @method('DELETE')
    <button type="submit" title="Archive Case" class="inline-flex items-center justify-center border rounded-lg text-sm {{ $buttonClass ?? 'border-amber-500 text-amber-700 hover:bg-amber-50' }} {{ auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC' ? 'w-9 h-9' : '' }}">
        <i class="fas fa-archive" aria-hidden="true"></i><span class="sr-only">Archive</span>
    </button>
</form>
