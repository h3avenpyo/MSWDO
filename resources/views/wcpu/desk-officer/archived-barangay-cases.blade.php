@extends('layouts.desk-officer')

@section('title', 'Archived Barangay Cases')

@push('styles')
<style>
    .archived-barangay-cases-page table tbody :is(a, button) {
        background-color: #C41E3A !important;
        border-color: #C41E3A !important;
        color: #ffffff !important;
        transition: none !important;
    }

    .archived-barangay-cases-page table tbody :is(a, button):is(:hover, :focus, :active) {
        background-color: #A01830 !important;
        border-color: #A01830 !important;
        color: #ffffff !important;
    }

    @media (max-width: 47.9375rem) {
        .archived-cases-table-view {
            display: none !important;
        }

        .archived-cases-mobile-cards {
            display: grid !important;
            gap: .75rem;
            padding: .75rem;
        }

        .archived-case-card {
            min-width: 0;
            padding: 1rem;
        }

        .archived-case-card-actions {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
            width: 100%;
        }

        .archived-case-card-actions> :is(a, form) {
            display: inline-flex;
            width: 100%;
            height: 3rem;
            min-width: 3rem;
            min-height: 3rem;
        }

        .archived-case-card-actions :is(a, button) {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            padding: 0;
        }

        .archived-case-card-actions>form>button {
            width: 100% !important;
            height: 3rem !important;
            min-width: 3rem !important;
            min-height: 3rem !important;
        }
    }

    @media (min-width: 37.5rem) and (max-width: 80rem) {
        .archived-cases-table-view {
            display: block !important;
        }

        .archived-cases-mobile-cards {
            display: none !important;
        }
    }

    /* Dashboard-style red navbar for mobile/tablet, matching /admin-dashboard */
    @media (max-width: 80rem) {
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mobileTopBar {
            display: none !important;
        }
    }

    @media (max-width: 47.9375rem) {
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mobileTopBar {
            display: none !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mobileMenuToggle,
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebarCloseBtn,
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky),
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header {
            background: #C41E3A !important;
            border-bottom: 1px solid #99172E !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky)>div,
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header>div {
            align-items: flex-start !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding-left: max(.5rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky)>div>div:first-of-type,
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header>div>div:first-of-type {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky)>div>div:first-of-type>div,
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header>div>div:first-of-type>div {
            min-width: 0 !important;
            flex: 1 1 auto !important;
            text-align: left !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav :is(#dashboardMenuToggle, #pageMobileMenuToggle) {
            position: relative !important;
            left: 0 !important;
            margin-left: 0 !important;
            transform: none !important;
            display: inline-flex !important;
            flex: 0 0 2.5rem !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
            align-items: center !important;
            justify-content: center !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav :is(#dashboardMenuToggle, #pageMobileMenuToggle) i {
            color: inherit !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) :is(h1, h2, h3),
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header :is(h1, h2, h3) {
            color: #ffffff !important;
            opacity: 1 !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p,
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header p {
            display: block !important;
            margin-top: .125rem !important;
            color: #ffffff !important;
            opacity: 1 !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p :is(span, strong),
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header p :is(span, strong) {
            color: #ffffff !important;
            opacity: 1 !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) div:first-of-type :is(span, a),
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header div:first-of-type :is(span, a) {
            color: #ffffff !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky)>div>div:last-child,
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header>div>div:last-child {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mainContent>header button[aria-label="Notifications"],
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mainContent>header button[aria-label="Notifications"]>i,
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"]>i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #mainContent>header #notifications-unread-count,
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header #notifications-unread-count {
            width: 1.25rem !important;
            height: 1.25rem !important;
            top: -.25rem !important;
            right: -.25rem !important;
            font-size: .75rem !important;
            line-height: 1 !important;
        }

        /* Sidebar dropdown drawer below header */
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar {
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: auto !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            max-height: calc(100vh - 60px) !important;
            overflow-y: auto !important;
            z-index: 999 !important;
            border-radius: 0 0 14px 14px !important;
            box-shadow: 0 14px 28px rgba(0, 0, 0, .22) !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(-8px) !important;
            transition: transform .25s ease, opacity .25s ease, visibility .25s ease !important;
            background: #C41E3A !important;
            background-image: none !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar.sidebar-collapsed,
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar .sidebar-header {
            display: none !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar .sidebar-text {
            opacity: 1 !important;
            max-width: none !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar #sidebarCloseBtn {
            display: none !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar nav :is(a, button),
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar .sidebar-link {
            background: #C41E3A !important;
            color: #ffffff !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar nav :is(a, button) :is(i, svg, span),
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar .sidebar-link :is(i, svg, span) {
            color: #ffffff !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar nav :is(a, button):hover,
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar .sidebar-link:hover {
            background: #99172E !important;
            color: #ffffff !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar .sidebar-link:is(.bg-purple-600, .bg-purple-700\/70),
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #sidebar nav [x-show]> :is(a, button).bg-purple-600 {
            background: #99172E !important;
            color: #ffffff !important;
            box-shadow: inset 3px 0 0 #ffffff !important;
        }
    }

    @media (max-width: 40rem) {
        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header div:has(> img[alt="User Profile"]) {
            display: none !important;
        }

        html body.portal-ui.desk-officer-archived-cases-mobile-nav #desk-officer-dashboard-header button:has(> img[alt="User Profile"]) {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'Archived Barangay Cases', 'subtitle' =>
auth()->user()->isMultiCaseDepartment() ? 'Archived BCPC and SWD cases from ' . $barangay->name . '.' : 'View archived
BCPC cases'])
<div class="archived-barangay-cases-page w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC',
    'VAWC'], true))
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Archived Barangay Cases</h1>
            <p class="text-sm text-gray-500 mt-1">Archived {{ ($department ?? 'VAWC') === 'BCPC' ? 'BCPC' : 'SWD' }}
                cases for {{ $barangay->name }}.</p>
        </div>
    </div>
    @endunless

    @if(session('success'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">{{ session('success') }}
    </div>
    @endif

    <!-- Search and Filter Bar -->
    <form method="GET" action="{{ route('desk-officer.archived-barangay-cases.index') }}"
        class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" id="archived-case-search" value="{{ request('search') }}"
                    placeholder="Search archived cases (Case #, Victim, Abuse Type)..."
                    class="w-full h-10 pl-9 pr-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div class="flex items-center gap-2">
                <select name="status" id="archived-status-filter"
                    class="h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                    <option value="">All Status</option>
                    <option value="Case Filed" {{ request('status')==='Case Filed' ? 'selected' : '' }}>Case Filed
                    </option>
                    <option value="Case Referred" {{ request('status')==='Case Referred' ? 'selected' : '' }}>Case
                        Referred</option>
                    <option value="Case closed" {{ in_array(request('status'), ['Case closed', 'Case Closed'
                        , 'Complete case' ], true) ? 'selected' : '' }}>Case Closed</option>
                </select>
                <button type="submit"
                    class="inline-flex h-10 items-center justify-center gap-2 px-4 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition">
                    <i class="fas fa-filter"></i>
                    <span>Filter</span>
                </button>
                @if(request()->filled('search') || request()->filled('status'))
                <a href="{{ route('desk-officer.archived-barangay-cases.index') }}"
                    class="inline-flex h-10 items-center justify-center gap-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">
                    <i class="fas fa-times"></i>
                    <span>Clear</span>
                </a>
                @endif
            </div>
        </div>
    </form>

    @foreach($caseGroups as $department => $cases)
    <section class="mb-6 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        @if(auth()->user()->isMultiCaseDepartment())
        <div class="border-b border-gray-100 px-4 py-4">
            <h2 class="text-lg font-semibold text-[#991b1b]">{{ $department === 'VAWC' ? 'SWD' : $department }} Cases
            </h2>
        </div>
        @endif
        <div class="archived-cases-table-view overflow-x-auto">
            <table class="min-w-[900px] w-full table-fixed text-left text-sm">
                <thead class="bg-[#C41E3A] text-white">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold">Case #</th>
                        <th class="px-4 py-3 text-xs font-semibold">Victim Name</th>
                        <th class="px-4 py-3 text-xs font-semibold">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold">Archived</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cases as $case)
                    <tr data-archive-search="{{ $case->case_number }} {{ $case->victim_name }} {{ $case->abuse_type }}"
                        class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-gray-800">#{{ $case->case_number }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ censorLastName($case->victim_name) }}</td>
                        <td class="px-4 py-3"><span
                                class="inline-block rounded px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-700">{{
                                $case->status }}</span></td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{
                            $case->deleted_at?->timezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ ($department ?? 'VAWC') === 'BCPC' ? route('desk-officer.cases.show', ['hash' => encrypt($case->id), 'archived' => 1]) : route('cases.show', ['id' => $case->id, 'archived' => 1]) }}"
                                    title="View Case"
                                    class="inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-[#C41E3A] text-white hover:bg-[#A01830]">
                                    <i class="fas fa-eye" aria-hidden="true"></i><span class="sr-only">View</span>
                                </a>
                                <form method="POST"
                                    action="{{ route('desk-officer.archived-barangay-cases.restore', $case->id) }}"
                                    class="restore-case-form" data-case-number="{{ $case->case_number }}">
                                    @csrf
                                    <button type="submit" title="Restore Case"
                                        class="inline-flex h-9 w-9 justify-center items-center rounded-lg bg-purple-700 text-white hover:bg-purple-800">
                                        <i class="fas fa-undo" aria-hidden="true"></i><span
                                            class="sr-only">Restore</span>
                                    </button>
                                </form>
                                @if(in_array(($department ?? 'VAWC'), ['BCPC', 'VAWC'], true) &&
                                !(in_array(auth()->user()?->defaultCaseDepartment(), ['VAWC', 'BCPC'], true)))
                                <form method="POST"
                                    action="{{ route('desk-officer.archived-barangay-cases.force-delete', $case->id) }}"
                                    class="delete-archived-case-form" data-case-number="{{ $case->case_number }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Delete Case"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#C41E3A] text-white hover:bg-[#A01830]"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-gray-500">No archived barangay cases.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="archived-cases-mobile-cards hidden">
            @forelse($cases as $case)
            <article data-archive-search="{{ $case->case_number }} {{ $case->victim_name }} {{ $case->abuse_type }}"
                data-archive-status="{{ $case->status }}"
                class="archived-case-card rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-start justify-between gap-3 border-b border-gray-100 pb-3">
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500">Case Number</p>
                        <p class="font-semibold text-gray-800 break-all">{{ $case->case_number }}</p>
                    </div><span
                        class="shrink-0 self-start rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium leading-tight text-gray-700 text-center">{{
                        $case->status }}</span>
                </div>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 py-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">Victim</dt>
                        <dd class="font-medium text-gray-800 break-words">{{ censorLastName($case->victim_name) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Archived At</dt>
                        <dd class="font-medium text-gray-800">{{ $case->deleted_at?->timezone('Asia/Manila')->format('M
                            d, Y h:i A') }}</dd>
                    </div>
                </dl>
                <div class="archived-case-card-actions border-t border-gray-100 pt-3">
                    <a href="{{ ($department ?? 'VAWC') === 'BCPC' ? route('desk-officer.cases.show', ['hash' => encrypt($case->id), 'archived' => 1]) : route('cases.show', ['id' => $case->id, 'archived' => 1]) }}"
                        title="View Case" class="rounded-lg bg-[#C41E3A] text-white"><i class="fas fa-eye"></i><span
                            class="sr-only">View</span></a>
                    <form method="POST" action="{{ route('desk-officer.archived-barangay-cases.restore', $case->id) }}"
                        class="restore-case-form" data-case-number="{{ $case->case_number }}">@csrf<button type="submit"
                            title="Restore Case" class="rounded-lg bg-[#C41E3A] text-white"><i
                                class="fas fa-undo"></i><span class="sr-only">Restore</span></button></form>
                    @if(in_array(($department ?? 'VAWC'), ['BCPC', 'VAWC'], true) &&
                    !(in_array(auth()->user()?->defaultCaseDepartment(), ['VAWC', 'BCPC'], true)))<form method="POST"
                        action="{{ route('desk-officer.archived-barangay-cases.force-delete', $case->id) }}"
                        class="delete-archived-case-form" data-case-number="{{ $case->case_number }}">@csrf
                        @method('DELETE')<button type="submit" title="Delete Case"
                            class="rounded-lg bg-[#C41E3A] text-white"><i class="fas fa-trash"></i><span
                                class="sr-only">Delete</span></button></form>@endif
                </div>
            </article>
            @empty
            <div class="rounded-xl border border-gray-200 bg-white p-8 text-center text-gray-500">No archived barangay
                cases.</div>
            @endforelse
        </div>
        @if($cases->hasPages())
        @include('components.archived-pagination', ['paginator' => $cases, 'label' => 'archived cases'])
        @endif
    </section>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.restore-case-form').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const submit = () => form.submit();
        const caseNumber = form.dataset.caseNumber || 'this case';
        const confirmationText = `Are you sure you want to restore this case?\nCase Number: ${caseNumber}`;

        if (window.Swal) {
            Swal.fire({
                title: 'Are you sure you want to restore this case?',
                text: `Case Number: ${caseNumber}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Restore',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#7c3aed',
            }).then((result) => {
                if (result.isConfirmed) submit();
            });
            return;
        }

        if (window.confirm(confirmationText)) submit();
    });
});

document.querySelectorAll('.delete-archived-case-form').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const submit = () => form.submit();
        const caseNumber = form.dataset.caseNumber || 'this case';
        if (window.Swal) {
            Swal.fire({ title: 'Delete archived case?', text: `Case Number: ${caseNumber}. This cannot be undone.`, icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete', cancelButtonText: 'Cancel', confirmButtonColor: '#C41E3A' }).then((result) => {
                if (result.isConfirmed) submit();
            });
            return;
        }
        if (window.confirm(`Permanently delete ${caseNumber}? This cannot be undone.`)) submit();
    });
});
// Live search and status filter on table rows
const searchInput = document.getElementById('archived-case-search');
const statusSelect = document.getElementById('archived-status-filter');
function filterRows() {
    const q = (searchInput?.value || '').toLowerCase().trim();
    const st = (statusSelect?.value || '').toLowerCase().trim();
    document.querySelectorAll('.archived-barangay-cases-page table tbody tr').forEach(row => {
        if (row.cells.length < 5) return; // skip empty rows
        const text = row.textContent.toLowerCase();
        const statusCell = row.cells[2]?.textContent.toLowerCase() || '';
        const matchesSearch = !q || text.includes(q);
        const matchesStatus = !st || statusCell.includes(st) || (st === 'case closed' && (statusCell.includes('complete') || statusCell.includes('closed')));
        row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
    });
}
if (searchInput) searchInput.addEventListener('input', filterRows);
if (statusSelect) statusSelect.addEventListener('change', filterRows);
</script>
@endpush