@extends('layouts.admin-tailwind')
@section('title', 'Archived Quarterly Reports - iSERVE SILANG Admin')
@push('styles')
<style>
    .archived-quarterly-reports-stable,
    .archived-quarterly-reports-stable :is(table, button, a),
    #archived-quarterly-reports-header {
        animation: none !important;
        transition: none !important;
    }

    @media (max-width: 47.9375rem) {
        .archived-quarterly-table-view {
            display: none !important;
        }

        .archived-quarterly-mobile-cards {
            display: block !important;
        }

        .archived-quarterly-card-actions {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .5rem;
        }

        .archived-quarterly-card-actions>form {
            display: contents;
        }

        .archived-quarterly-card-actions> :is(a, button),
        .archived-quarterly-card-actions>form>button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            min-width: 0;
            min-height: 3rem;
            padding: .5rem !important;
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
        }

        .archived-quarterly-card-actions> :is(a, button):not(:disabled):hover,
        .archived-quarterly-card-actions>form>button:not(:disabled):hover {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
        }

        .archived-quarterly-card-actions i {
            color: #ffffff !important;
        }

        .archived-quarterly-card-actions [data-restore-report-button] {
            font-size: 0 !important;
        }

        .archived-quarterly-card-actions [data-restore-report-button] i {
            font-size: .875rem !important;
        }
    }
</style>
@if(auth()->user()->isDepartmentAdmin())
<style>
    @media (max-width: 40rem) {
        html body.portal-ui.admin-reference-mobile-nav #mainContent>header.admin-reference-mobile-header>div {
            column-gap: .75rem !important;
        }

        html body.portal-ui.admin-reference-mobile-nav #mainContent>header.admin-reference-mobile-header>div>.admin-reference-mobile-leading {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
        }

        html body.portal-ui.admin-reference-mobile-nav #mainContent>header.admin-reference-mobile-header .admin-reference-mobile-title {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            text-align: left !important;
        }

        html body.portal-ui.admin-reference-mobile-nav #mainContent>header.admin-reference-mobile-header #dashboardMenuToggle {
            display: inline-flex !important;
            position: relative !important;
            flex: 0 0 2.5rem !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            transform: none !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
        }

        html body.portal-ui.admin-reference-mobile-nav #mainContent>header.admin-reference-mobile-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }

        html body.portal-ui.admin-reference-mobile-nav #mainContent>header.admin-reference-mobile-header button[aria-label="Notifications"]>i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }
    }
</style>
@endif
@endpush
@section('content')
<style>
    [data-restore-report-button]:disabled {
        background-color: #FEE2E2 !important;
        border-color: #FCA5A5 !important;
        color: #991B1B !important;
        opacity: 1 !important;
        filter: none !important;
    }

    [data-restore-report-button]:not(:disabled) {
        background-color: #C41E3A !important;
    }

    [data-restore-report-button]:not(:disabled):hover {
        background-color: #A01830 !important;
    }
</style>
<header id="archived-quarterly-reports-header"
    class="admin-reference-mobile-header sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm portal-page-header">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="admin-reference-mobile-leading min-w-0">
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar"
                aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="admin-reference-mobile-title">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Archived Quarterly Reports</h2>
                <p class="admin-reference-mobile-subtitle text-sm text-gray-500 hidden sm:block">View archived quarterly
                    reports</p>
            </div>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
<div class="archived-quarterly-reports-stable w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    @if(session('success'))<p class="p-4 mb-4 bg-green-50 text-green-700 rounded-lg border border-green-200">{{
        session('success') }}</p>@endif
    @if(session('error'))<p class="p-4 mb-4 bg-red-50 text-red-700 rounded-lg border border-red-200">{{ session('error')
        }}</p>@endif

    <form id="archived-quarterly-report-filter-form" method="GET"
        action="{{ route('admin.quarterly-reports.archived') }}"
        class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-6">
        <input type="hidden" name="section" value="quarterly">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:flex xl:items-center gap-2 sm:gap-3">
            <div class="relative min-w-0 sm:col-span-2 xl:flex-1 xl:min-w-[240px]"><i
                    class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i><input
                    type="search" name="search" id="archived-report-search" value="{{ request('search') }}"
                    placeholder="Search archived reports..."
                    class="w-full h-10 pl-9 pr-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <select name="quarter" id="archived-report-quarter"
                class="w-full xl:w-[130px] h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                <option value="">All Quarters</option>@foreach([1,2,3,4] as $quarter)<option value="{{ $quarter }}" {{
                    (string) request('quarter')===(string) $quarter ? 'selected' : '' }}>Q{{ $quarter }}</option>
                @endforeach
            </select>
            <select name="year" id="archived-report-year"
                class="w-full xl:w-[115px] h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                <option value="">All Years</option>@foreach(range(2026, 2030) as $year)<option value="{{ $year }}" {{
                    (string) request('year')===(string) $year ? 'selected' : '' }}>{{ $year }}</option>@endforeach
            </select>
            <select name="barangay_id" id="archived-report-barangay"
                class="w-full xl:w-[160px] h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                <option value="">All Barangays</option>@foreach($barangays as $barangay)<option
                    value="{{ $barangay->id }}" {{ (string) request('barangay_id')===(string) $barangay->id ? 'selected'
                    : '' }}>{{ $barangay->name }}</option>@endforeach
            </select>
            <button type="submit"
                class="inline-flex h-10 items-center justify-center gap-2 px-4 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition shrink-0"><i
                    class="fas fa-filter"></i><span>Filter</span></button>
            @if(request()->filled('search') || request()->filled('quarter') || request()->filled('year') ||
            request()->filled('barangay_id'))<a
                href="{{ route('admin.quarterly-reports.archived', ['section' => 'quarterly']) }}"
                class="inline-flex h-10 items-center justify-center gap-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition shrink-0"><i
                    class="fas fa-times"></i><span>Clear</span></a>@endif
        </div>
    </form>

    <section
        class="desk-officer-quarterly-report-table bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="archived-quarterly-table-view hidden md:block overflow-x-auto">
            <table class="w-full table-fixed">
                <colgroup>
                    <col class="w-[15%]">
                    <col class="w-[12%]">
                    <col class="w-[10%]">
                    <col class="w-[9%]">
                    <col class="w-[16%]">
                    <col class="w-[19%]">
                    <col class="w-[19%]">
                </colgroup>
                <thead class="bg-gradient-to-r from-purple-700 to-purple-600 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Barangay</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Period</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Type</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Cases</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Archived Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Submitted By</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                    @php($restoreBlocked = !empty(($activeReportPeriods ?? [])[$report->barangay_id . '-' .
                    $report->quarter . '-' . $report->year]))
                    <tr class="hover:bg-purple-50/50 transition" data-archived-report
                        data-quarter="{{ $report->quarter }}" data-year="{{ $report->year }}">
                        <td class="px-6 py-4 font-medium text-gray-800 break-words">{{ $report->barangay?->name ?? 'N/A'
                            }}</td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">Q{{ $report->quarter }} {{ $report->year }}
                        </td>
                        <td class="px-6 py-4 text-center"><span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">{{
                                str_replace('VAWC', 'SWD', $report->type ?? 'BCPC') }}</span></td>
                        <td class="px-6 py-4 text-center font-medium">{{ $report->report_data['total_cases'] ?? 0 }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{
                            $report->archived_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 break-words">{{ trim(($report->user?->first_name ??
                            '') . ' ' . ($report->user?->last_name ?? '')) ?: ($report->user?->name ?? 'N/A') }}</td>
                        <td class="px-6 py-4 text-center align-middle whitespace-nowrap">
                            <div class="inline-flex items-center justify-center gap-2">
                                <a href="{{ route('admin.quarterly-reports.show', ['id' => $report->id, 'archived' => 1]) }}"
                                    title="View Report" data-admin-archive-view="report" data-swd-archive-view="report"
                                    data-modal-title="Quarterly Report Details"
                                    class="inline-flex w-10 h-10 items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm"><i
                                        class="fas fa-eye text-sm"></i><span class="sr-only">View Report</span></a>
                                <button type="button" data-restore-report-button {{ $restoreBlocked ? 'disabled' : '' }}
                                    @unless($restoreBlocked)
                                    onclick='openRestoreModal({{ $report->id }}, @json(($report->barangay?->name ?? '
                                    Barangay') . ' - Q' . $report->quarter . ' ' . $report->year))' @endunless title="{{
                                    $restoreBlocked ? 'An active report already exists for this barangay, quarter, and
                                    year' : 'Restore Report' }}" class="inline-flex w-10 h-10 items-center
                                    justify-center {{ $restoreBlocked ? '!bg-red-100 hover:!bg-red-100 !text-red-800
                                    !border !border-red-200 cursor-not-allowed pointer-events-none' : 'bg-red-600
                                    hover:bg-red-700 text-white' }} rounded-lg transition shadow-sm"><i
                                        class="fas fa-undo text-sm"></i><span class="sr-only">Restore</span></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">No archived quarterly reports
                            found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="archived-quarterly-mobile-cards md:hidden p-3 space-y-3">
            @forelse($reports as $report)
            @php($restoreBlocked = !empty(($activeReportPeriods ?? [])[$report->barangay_id . '-' . $report->quarter .
            '-' . $report->year]))
            <article class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm" data-archived-report
                data-quarter="{{ $report->quarter }}" data-year="{{ $report->year }}">
                <div class="flex items-center justify-between gap-3 pb-3 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-medium text-gray-500">Period</p>
                        <p class="text-sm font-bold text-gray-800">Q{{ $report->quarter }} {{ $report->year }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-gray-500">Cases</p>
                        <p class="text-sm font-bold text-purple-700">{{ $report->report_data['total_cases'] ?? 0 }}</p>
                    </div>
                </div>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 py-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">Barangay</dt>
                        <dd class="mt-0.5 font-medium text-gray-800 break-words">{{ $report->barangay?->name ?? 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Type</dt>
                        <dd class="mt-0.5 font-medium text-gray-800">{{ str_replace('VAWC', 'SWD', $report->type ??
                            'BCPC') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Archived Date</dt>
                        <dd class="mt-0.5 font-medium text-gray-800">{{
                            $report->archived_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Submitted By</dt>
                        <dd class="mt-0.5 font-medium text-gray-800 break-words">{{ trim(($report->user?->first_name ??
                            '') . ' ' . ($report->user?->last_name ?? '')) ?: ($report->user?->name ?? 'N/A') }}</dd>
                    </div>
                </dl>
                <div
                    class="archived-quarterly-card-actions flex items-center justify-center gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.quarterly-reports.show', ['id' => $report->id, 'archived' => 1]) }}"
                        title="View Report" data-admin-archive-view="report" data-swd-archive-view="report"
                        data-modal-title="Quarterly Report Details"
                        class="inline-flex w-10 h-10 items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm"><i
                            class="fas fa-eye text-sm"></i><span class="sr-only">View Report</span></a><button
                        type="button" data-restore-report-button {{ $restoreBlocked ? 'disabled' : '' }}
                        @unless($restoreBlocked)
                        onclick='openRestoreModal({{ $report->id }}, @json(($report->barangay?->name ?? ' Barangay')
                        . ' - Q' . $report->quarter . ' ' . $report->year))' @endunless title="{{ $restoreBlocked ? 'An
                        active report already exists for this barangay, quarter, and year' : 'Restore Report' }}"
                        class="inline-flex w-10 h-10 items-center justify-center {{ $restoreBlocked ? '!bg-red-100
                        hover:!bg-red-100 !text-red-800 !border !border-red-200 cursor-not-allowed pointer-events-none'
                        : 'bg-red-600 hover:bg-red-700 text-white' }} rounded-lg transition shadow-sm"><i
                            class="fas fa-undo text-sm"></i></button></div>
            </article>
            @empty
            <div class="text-center py-10 text-gray-500">No archived quarterly reports found.</div>
            @endforelse
        </div>
        @include('components.archived-pagination', ['paginator' => $reports, 'label' => 'reports'])
    </section>
</div>

<div id="restoreModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeRestoreModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center"><i
                        class="fas fa-undo text-red-600 text-xl"></i></div>
            </div>
            <h3 class="text-lg font-bold text-gray-800 text-center mb-2">Restore Report</h3>
            <p class="text-gray-500 text-sm text-center mb-6">Are you sure you want to restore <strong
                    id="restoreReportName" class="text-gray-700"></strong> back to active quarterly reports?</p>
            <div class="flex gap-3"><button type="button" onclick="closeRestoreModal()"
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition text-sm">Cancel</button>
                <form id="restoreForm" method="POST" class="flex-1">@csrf<button type="submit"
                        data-restore-report-button
                        class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition text-sm"><i
                            class="fas fa-undo mr-1"></i> Restore</button></form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('admin-tailwind.components.archive-view-modal')
@if(session('restored_report'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
    Swal.fire({ icon: 'success', title: 'Restored!', text: @json(session('restored_report')), confirmButtonText: 'OK' });
});
</script>
@endif
<script>
    function openRestoreModal(id, name) { document.getElementById('restoreReportName').textContent = name; document.getElementById('restoreForm').action = "{{ url('admin/quarterly-reports') }}/" + id + "/restore"; document.getElementById('restoreModal').classList.remove('hidden'); }
function closeRestoreModal() { document.getElementById('restoreModal').classList.add('hidden'); }
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeRestoreModal(); });
const archivedSearch = document.getElementById('archived-report-search'), archivedQuarter = document.getElementById('archived-report-quarter'), archivedYear = document.getElementById('archived-report-year'), archivedBarangay = document.getElementById('archived-report-barangay'), archivedFilterForm = document.getElementById('archived-quarterly-report-filter-form');
function filterArchivedReports() { const search = (archivedSearch?.value || '').toLowerCase().trim(), quarter = archivedQuarter?.value || '', year = archivedYear?.value || ''; document.querySelectorAll('[data-archived-report]').forEach(item => item.classList.toggle('hidden', !((!search || item.textContent.toLowerCase().includes(search)) && (!quarter || item.dataset.quarter === quarter) && (!year || item.dataset.year === year)))); }
archivedSearch?.addEventListener('input', filterArchivedReports); archivedQuarter?.addEventListener('change', filterArchivedReports); archivedYear?.addEventListener('change', filterArchivedReports);
archivedBarangay?.addEventListener('change', () => archivedFilterForm?.requestSubmit());
archivedYear?.addEventListener('change', () => archivedFilterForm?.requestSubmit());
</script>
@endpush