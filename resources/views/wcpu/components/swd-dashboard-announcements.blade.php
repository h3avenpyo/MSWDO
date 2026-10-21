@php
    $calendarItems = \App\Models\Wcpu\Event::forUser($announcementUser)->with('creator')->get()
        ->filter(fn ($event) => $announcementUser->isBcpcAdmin() ? $event->creator?->isBcpcAdmin() : $event->creator?->isVawcAdmin())
        ->map(fn ($event) => [
            'title' => $event->title, 'date' => $event->event_date->format('Y-m-d'),
            'sort' => $event->event_date->timestamp, 'description' => $event->description,
            'from' => $event->event_date->format('H:i'), 'to' => $event->end_time?->format('H:i'),
            'location' => $event->location, 'creator' => $event->creator?->full_name, 'image' => null,
        ]);
    $announcementFeed = $calendarItems->sortByDesc('sort')->values();
    $announcementPage = max(1, (int) request('announcements_page', 1));
    $announcementsPerPage = max(1, (int) ($announcementLimit ?? 3));
    $announcementsTwoColumns = (bool) ($announcementTwoColumns ?? false);
    $visibleAnnouncements = new \Illuminate\Pagination\LengthAwarePaginator(
        $announcementFeed->forPage($announcementPage, $announcementsPerPage)->values(), $announcementFeed->count(), $announcementsPerPage, $announcementPage,
        ['path' => request()->url(), 'pageName' => 'announcements_page']
    );
    $visibleAnnouncements->withQueryString();
@endphp
<section class="dashboard-announcements-box w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4" aria-labelledby="swd-announcements-heading">
    <h2 id="swd-announcements-heading" class="text-lg font-semibold text-gray-800 mb-3">Announcements</h2>
    <div class="dashboard-announcement-list {{ $announcementsTwoColumns ? 'grid grid-cols-1 sm:grid-cols-2 gap-2' : 'space-y-2' }} max-h-48 overflow-y-auto">
        @forelse($visibleAnnouncements as $index => $item)
        <button type="button" onclick="showSwdDashboardAnnouncement({{ $index }})" class="block w-full text-left border border-gray-200 rounded-lg px-3 py-2 hover:bg-purple-50 transition">
            <span class="block font-semibold text-gray-800 text-sm break-words">{{ $item['title'] }}</span>
            <span class="block text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($item['date'])->format('F j, Y') }}</span>
        </button>
        @empty
        <p class="text-sm text-gray-500">No announcements yet.</p>
        @endforelse
    </div>
    @if($visibleAnnouncements->hasPages())
    <nav class="dashboard-announcement-pages flex flex-wrap items-center justify-center gap-2 mt-3" aria-label="Announcement pages">
        @foreach($visibleAnnouncements->getUrlRange(1, $visibleAnnouncements->lastPage()) as $page => $url)
        <a href="{{ $url }}" @if($page === $visibleAnnouncements->currentPage()) aria-current="page" @endif class="px-3 py-1 rounded-lg border text-sm {{ $page === $visibleAnnouncements->currentPage() ? 'bg-purple-700 border-purple-700 text-white' : 'border-gray-300 text-gray-700' }}">{{ $page }}</a>
        @endforeach
    </nav>
    @endif
</section>
@push('scripts')
<script>
const swdDashboardAnnouncements = @json($visibleAnnouncements->items());
function showSwdDashboardAnnouncement(index) {
    const item = swdDashboardAnnouncements[index];
    if (!item) return;
    const details = document.createElement('div');
    details.className = 'text-left space-y-3';
    const fields = [['Title', item.title], ['Date', item.date], ['Time (from)', item.from], ['Time (to)', item.to], ['Description', item.description], ['Location / Barangay', item.location], ['Created by', item.creator]];
    fields.forEach(([label, value]) => {
        if (!value) return;
        const field = document.createElement('div');
        const heading = document.createElement('strong');
        heading.className = 'block text-sm text-gray-500';
        heading.textContent = label;
        const text = document.createElement('p');
        text.className = 'whitespace-pre-wrap break-words text-gray-800';
        text.textContent = value;
        field.append(heading, text);
        details.appendChild(field);
    });
    if (item.image) {
        const picture = document.createElement('img');
        picture.src = item.image;
        picture.alt = item.title;
        picture.className = 'max-w-full rounded-lg';
        details.appendChild(picture);
    }
    Swal.fire({ title: 'Announcement Details', html: details, showCloseButton: true, confirmButtonText: 'Close' });
}
</script>
@endpush
