@php
    $activeReportPeriods = $activeReportPeriods ?? [];
    $isSwdDeskOfficer = auth()->user()?->position === 'Desk Officer'
        && auth()->user()?->defaultCaseDepartment() === 'VAWC';
    $isBcpcDeskOfficer = auth()->user()?->position === 'Desk Officer'
        && auth()->user()?->defaultCaseDepartment() === 'BCPC';
    $isVawcDeskOfficer = auth()->user()?->position === 'Desk Officer'
        && auth()->user()?->defaultCaseDepartment() === 'VAWC';
    $actionButtonSize = ($isSwdDeskOfficer || $isBcpcDeskOfficer) ? 'w-10 h-10 flex-none' : 'w-9 h-9';
@endphp

<style>
    @if($isBcpcDeskOfficer)
    .swal2-popup .swal2-styled {
        background-color: #C41E3A !important;
        color: #ffffff !important;
        min-width: 7rem;
        min-height: 2.5rem;
        border-radius: .5rem;
    }
    .swal2-popup .swal2-styled:hover { background-color: #A01830 !important; }
    @endif
    .desk-officer-quarterly-report-table [data-icon-action] {
        color: #ffffff !important;
    }
    .desk-officer-quarterly-report-table [title="View Report"] {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
    }
    .desk-officer-quarterly-report-table [title="View Report"]:is(:hover, :focus, :active) {
        background-color: #A01830 !important;
        border-color: #A01830 !important;
    }
    @media (max-width: 80rem) {
        .desk-officer-quarterly-report-table .quarterly-table-view { display: none !important; }
        .desk-officer-quarterly-report-table .quarterly-mobile-cards {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr);
            gap: .75rem;
            padding: .75rem !important;
        }
        .desk-officer-quarterly-report-table .quarterly-mobile-cards > * {
            min-width: 0;
            margin-top: 0 !important;
        }
        .desk-officer-quarterly-report-table .quarterly-report-card {
            display: flex;
            min-width: 0;
            height: 100%;
            flex-direction: column;
            padding: .75rem !important;
            border: 1px solid #dbe2e8 !important;
            border-radius: .625rem !important;
            box-shadow: 0 1px 4px rgba(15, 23, 42, .08) !important;
        }
        .desk-officer-quarterly-report-table .quarterly-report-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .75rem;
            margin-bottom: .75rem;
        }
        .desk-officer-quarterly-report-table .quarterly-report-card-marker {
            display: inline-flex;
            width: 2rem;
            height: 2rem;
            flex: 0 0 2rem;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            background: #ffe4e8;
            color: #e11d48;
            font-size: .75rem;
        }
        .desk-officer-quarterly-report-table .quarterly-report-card-count {
            display: inline-flex;
            width: 2rem;
            height: 2rem;
            flex: 0 0 2rem;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            background: #f3f4f6;
            color: #64748b;
            font-size: .75rem;
            font-weight: 700;
        }
        .desk-officer-quarterly-report-table .quarterly-report-card-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .625rem 1rem;
            margin-bottom: .75rem;
        }
        .desk-officer-quarterly-report-table .quarterly-report-card-details > div { min-width: 0; }
        .desk-officer-quarterly-report-table .quarterly-report-card-details .quarterly-report-submitter {
            grid-column: 1 / -1;
        }
        .desk-officer-quarterly-report-table .quarterly-report-period,
        .desk-officer-quarterly-report-table .quarterly-report-type {
            display: inline-flex;
            max-width: 100%;
            align-items: center;
            gap: .25rem;
            padding: .1875rem .4375rem;
            border-radius: 9999px;
            background: #ffe4e8;
            color: #e11d48;
            font-size: .625rem;
            font-weight: 600;
            line-height: 1.2;
        }
        .desk-officer-quarterly-report-table .quarterly-mobile-card-actions {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(2.5rem, 1fr));
            gap: .5rem;
            width: 100%;
            margin-top: auto;
            padding-top: .75rem !important;
        }
        .desk-officer-quarterly-report-table .quarterly-mobile-card-actions > :is(a, button, form) {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 100% !important;
            height: 2.5rem !important;
            min-width: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .desk-officer-quarterly-report-table .quarterly-mobile-card-actions form > button { width: 100% !important; height: 100% !important; }
        .desk-officer-quarterly-report-table .quarterly-mobile-card-actions :is(a, button):not(:disabled) {
            border-color: #C41E3A !important;
            background: #C41E3A !important;
            color: #ffffff !important;
            border-radius: .5rem !important;
        }
        .desk-officer-quarterly-report-table .quarterly-mobile-card-actions :is(a, button):not(:disabled):is(:hover, :focus, :active) {
            border-color: #A01830 !important;
            background: #A01830 !important;
        }
        .desk-officer-quarterly-report-table > [data-pagination-label] {
            padding: .75rem 1rem !important;
            gap: .75rem !important;
            background: #f8fafc;
        }
        .desk-officer-quarterly-report-table > [data-pagination-label] > div:first-child {
            font-size: .6875rem !important;
            color: #64748b !important;
        }
        .desk-officer-quarterly-report-table > [data-pagination-label] > div:last-child { gap: .375rem !important; }
        .desk-officer-quarterly-report-table > [data-pagination-label] a {
            width: 2rem !important;
            height: 2rem !important;
            border-radius: .5rem !important;
            font-size: .6875rem !important;
        }
    }
    @media (min-width: 48rem) and (max-width: 80rem) {
        .desk-officer-quarterly-report-table .quarterly-mobile-cards {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @if($archived ?? false)
    .desk-officer-quarterly-report-table .restore-confirm-form {
        width: 2.5rem;
        height: 2.5rem;
        flex: none;
    }
    .desk-officer-quarterly-report-table :is([title="View Report"], [title="Restore Report"]) {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        gap: 0 !important;
    }
    .desk-officer-quarterly-report-table :is([title="View Report"], [title="Restore Report"]) > i {
        margin: 0 !important;
    }
    @endif
</style>

@foreach($reportGroups as $department => $reports)
<section data-ajax-pagination="quarterly-{{ $department }}" data-ajax-page="{{ $reports->getPageName() }}" class="desk-officer-quarterly-report-table {{ $isBcpcDeskOfficer ? 'bcpc-quarterly-report-table' : '' }} bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 mb-6">
    @unless(($archived ?? false) && $isSwdDeskOfficer && !auth()->user()->isMultiCaseDepartment())
    <h2 class="px-6 py-4 text-lg font-semibold text-purple-800">{{ ($archived ?? false) && auth()->user()->isMultiCaseDepartment() ? ($department === 'VAWC' ? 'SWD' : $department) . ' Quarterly Reports' : (($archived ?? false) && $isBcpcDeskOfficer && !auth()->user()->isMultiCaseDepartment() ? 'Quarterly Reports' : (auth()->user()->isMultiCaseDepartment() && $department === 'VAWC' ? 'SWD' : $department) . ' Case Reports') }}</h2>
    @endunless
    <div class="quarterly-table-view hidden md:block overflow-x-auto">
        <table class="w-full table-fixed">
            <colgroup>
                <col class="w-[13%]">
                <col class="w-[18%]">
                <col class="w-[8%]">
                <col class="w-[20%]">
                <col class="w-[17%]">
                <col class="w-[24%]">
            </colgroup>
            <thead class="bg-gradient-to-r from-purple-700 to-purple-600 text-white"><tr>
                <th class="px-6 py-4 text-left">Period</th><th class="px-6 py-4 text-left">Barangay</th><th class="px-6 py-4 text-center">Cases</th><th class="px-6 py-4 text-left">Generated By</th><th class="px-6 py-4 text-left">Date</th><th class="px-6 py-4 text-center">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($reports as $report)
                @php($restoreDisabled = ($isBcpcDeskOfficer || $isVawcDeskOfficer) && !empty(($activeReportPeriods[$department] ?? [])[$report->quarter . '-' . $report->year]))
                <tr class="hover:bg-purple-50/50 transition">
                    <td class="px-6 py-4">Q{{ $report->quarter }} {{ $report->year }}</td>
                    <td class="px-6 py-4">{{ $report->barangay?->name }}</td>
                    <td class="px-6 py-4 text-center">{{ $report->report_data['total_cases'] ?? 0 }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($report->user_id === Auth::id())
                            <span class="inline-flex items-center gap-1 text-purple-700 font-medium">
                                <i class="fas fa-user-circle text-xs"></i> You
                            </span>
                        @else
                            <span class="text-gray-500">
                                {{ $report->user ? $report->user->first_name . ' ' . substr($report->user->last_name ?? '', 0, 1) . '.' : 'Unknown' }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $report->created_at?->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-center align-middle whitespace-nowrap">
                        <div class="inline-flex items-center justify-center gap-2">
                            @if($isBcpcDeskOfficer)
                            <a href="{{ route('desk-officer.quarterly-reports.show', ['id' => $report->id, 'archived' => ($archived ?? false) ? 1 : null]) }}" data-icon-action="labelled" title="View Report" class="inline-flex {{ $actionButtonSize }} items-center justify-center bg-blue-600 hover:bg-[#A01830] text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-eye text-sm"></i><span class="sr-only">View</span></a>
                            @unless($archived ?? false)
                            <a href="{{ route('desk-officer.quarterly-reports.edit', $report->id) }}" data-icon-action="labelled" title="Edit Report" class="inline-flex {{ $actionButtonSize }} items-center justify-center bg-purple-700 hover:bg-purple-800 text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-edit text-sm"></i><span class="sr-only">Edit</span></a>
                            <button type="button" data-quarterly-preview="{{ route('desk-officer.quarterly-reports.download', ['id' => $report->id, 'preview' => 1]) }}" data-period="Q{{ $report->quarter }} {{ $report->year }}" data-icon-action="labelled" title="Print Report" class="inline-flex {{ $actionButtonSize }} items-center justify-center bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-print text-sm"></i><span class="sr-only">Print Report</span></button>
                            @endunless
                            @else
                            <a href="{{ route('desk-officer.quarterly-reports.show', ['id' => $report->id, 'archived' => ($archived ?? false) ? 1 : null]) }}" data-icon-action="labelled" title="View Report" class="inline-flex {{ $actionButtonSize }} items-center justify-center bg-blue-600 hover:bg-[#A01830] text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-eye text-sm"></i><span class="sr-only">View</span></a>
                            @endif
                            @if($archived ?? false)
                            <form method="POST" class="restore-confirm-form inline-flex items-center justify-center m-0 p-0" action="{{ route('desk-officer.quarterly-reports.restore', $report->id) }}">
                                @csrf
                                <button type="submit" {{ $restoreDisabled ? 'disabled' : '' }} data-icon-action="labelled" title="{{ $restoreDisabled ? 'An active report already exists for this quarter and year' : 'Restore Report' }}" class="inline-flex {{ $actionButtonSize }} items-center justify-center {{ $restoreDisabled ? 'bg-red-100 text-red-800 cursor-not-allowed' : 'bg-purple-700 hover:bg-purple-800 text-white' }} rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-undo text-sm"></i><span class="sr-only">Restore</span></button>
                            </form>
                            @else
                            @if($isVawcDeskOfficer)
                            <a href="{{ route('desk-officer.quarterly-reports.edit', $report->id) }}" data-icon-action="labelled" title="Edit Report" class="inline-flex {{ $actionButtonSize }} items-center justify-center bg-purple-700 hover:bg-purple-800 text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-edit text-sm"></i><span class="sr-only">Edit</span></a>
                            @endif
                            @if($department !== 'BCPC' && !(auth()->user()->isMultiCaseDepartment() && $isBcpcDeskOfficer))
                            <button type="button" data-quarterly-preview="{{ route('desk-officer.quarterly-reports.download', ['id' => $report->id, 'preview' => 1]) }}" data-period="Q{{ $report->quarter }} {{ $report->year }}" data-icon-action="labelled" title="Print Report" class="inline-flex {{ $actionButtonSize }} items-center justify-center bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-print text-sm"></i><span class="sr-only">Print Report</span></button>
                            @endif
                            <form method="POST" class="quarterly-archive-form inline-flex items-center justify-center {{ $actionButtonSize }} m-0 p-0" action="{{ route('desk-officer.quarterly-reports.archive', $report->id) }}">
                                @csrf
                                <button type="submit" data-icon-action="labelled" title="Archive Report" class="inline-flex w-full h-full items-center justify-center {{ auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC' ? 'bg-[#C41E3A] hover:bg-[#A01830] text-white border border-[#C41E3A]' : 'bg-red-50 hover:bg-red-100 text-red-600 border border-red-200' }} rounded-lg transition shadow-sm p-0 m-0"><i class="fas fa-archive text-sm"></i><span class="sr-only">Archive</span></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No {{ ($archived ?? false) ? 'archived ' : '' }}{{ $department === 'VAWC' ? 'SWD' : $department }} quarterly reports found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="quarterly-mobile-cards md:hidden p-3 space-y-3">
        @forelse($reports as $report)
            @php($restoreDisabled = ($isBcpcDeskOfficer || $isVawcDeskOfficer) && !empty(($activeReportPeriods[$department] ?? [])[$report->quarter . '-' . $report->year]))
            <article class="quarterly-report-card bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                <div class="quarterly-report-card-header">
                    <div class="flex min-w-0 items-center gap-2.5">
                        <span class="quarterly-report-card-marker" aria-hidden="true"><i class="fas fa-map-marker-alt"></i></span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-gray-800">{{ $report->barangay?->name ?? 'N/A' }}</p>
                            <p class="truncate text-[10px] text-gray-400">{{ $report->created_at?->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <span class="quarterly-report-card-count" title="{{ $report->report_data['total_cases'] ?? 0 }} cases" aria-label="{{ $report->report_data['total_cases'] ?? 0 }} cases">{{ $report->report_data['total_cases'] ?? 0 }}</span>
                </div>

                <dl class="quarterly-report-card-details text-sm">
                    <div>
                        <dt class="mb-1 text-[10px] text-gray-500">Period</dt>
                        <dd><span class="quarterly-report-period"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Q{{ $report->quarter }} {{ $report->year }}</span></dd>
                    </div>
                    <div>
                        <dt class="mb-1 text-[10px] text-gray-500">Type</dt>
                        <dd><span class="quarterly-report-type">{{ ($report->type ?? 'VAWC') === 'VAWC' ? 'SWD' : ($report->type ?? 'BCPC') }}</span></dd>
                    </div>
                    <div class="quarterly-report-submitter">
                        <dt class="mb-1 text-[10px] text-gray-500">Submitted By</dt>
                        <dd class="truncate text-xs font-medium text-gray-700">{{ trim(($report->user?->first_name ?? '') . ' ' . ($report->user?->last_name ?? '')) ?: 'Unknown' }}</dd>
                        <dd class="truncate text-[10px] text-gray-400">{{ $report->user?->email ?? '' }}</dd>
                    </div>
                </dl>

                <div class="quarterly-mobile-card-actions flex items-center justify-center gap-2 pt-3 border-t border-gray-100">
                    @if($isBcpcDeskOfficer)
                    <a href="{{ route('desk-officer.quarterly-reports.show', ['id' => $report->id, 'archived' => ($archived ?? false) ? 1 : null]) }}" data-icon-action="labelled" title="View Report" class="inline-flex {{ $actionButtonSize }} items-center justify-center bg-blue-600 hover:bg-[#A01830] text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-eye text-sm"></i><span class="sr-only">View</span></a>
                    @unless($archived ?? false)
                    <a href="{{ route('desk-officer.quarterly-reports.edit', $report->id) }}" data-icon-action="labelled" title="Edit Report" class="inline-flex {{ $actionButtonSize }} items-center justify-center bg-purple-700 hover:bg-purple-800 text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-edit text-sm"></i><span class="sr-only">Edit</span></a>
                    <button type="button" data-quarterly-preview="{{ route('desk-officer.quarterly-reports.download', ['id' => $report->id, 'preview' => 1]) }}" data-period="Q{{ $report->quarter }} {{ $report->year }}" data-icon-action="labelled" title="Print Report" class="inline-flex {{ $actionButtonSize }} items-center justify-center bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-print text-sm"></i><span class="sr-only">Print Report</span></button>
                    @endunless
                    @else
                    <a href="{{ route('desk-officer.quarterly-reports.show', ['id' => $report->id, 'archived' => ($archived ?? false) ? 1 : null]) }}" data-icon-action="labelled" title="View Report" class="inline-flex {{ $actionButtonSize }} items-center justify-center bg-blue-600 hover:bg-[#A01830] text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-eye text-sm"></i><span class="sr-only">View</span></a>
                    @endif
                    @if($archived ?? false)
                    <form method="POST" class="restore-confirm-form inline-flex items-center justify-center m-0 p-0" action="{{ route('desk-officer.quarterly-reports.restore', $report->id) }}">
                        @csrf
                        <button type="submit" {{ $restoreDisabled ? 'disabled' : '' }} data-icon-action="labelled" title="{{ $restoreDisabled ? 'An active report already exists for this quarter and year' : 'Restore Report' }}" class="inline-flex {{ $actionButtonSize }} items-center justify-center {{ $restoreDisabled ? 'bg-red-100 text-red-800 cursor-not-allowed' : 'bg-purple-700 hover:bg-purple-800 text-white' }} rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-undo text-sm"></i><span class="sr-only">Restore</span></button>
                    </form>
                    @else
                    @if($isVawcDeskOfficer)
                    <a href="{{ route('desk-officer.quarterly-reports.edit', $report->id) }}" data-icon-action="labelled" title="Edit Report" class="inline-flex {{ $actionButtonSize }} items-center justify-center bg-purple-700 hover:bg-purple-800 text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-edit text-sm"></i><span class="sr-only">Edit</span></a>
                    @endif
                    @if($department !== 'BCPC' && !(auth()->user()->isMultiCaseDepartment() && $isBcpcDeskOfficer))
                    <button type="button" data-quarterly-preview="{{ route('desk-officer.quarterly-reports.download', ['id' => $report->id, 'preview' => 1]) }}" data-period="Q{{ $report->quarter }} {{ $report->year }}" data-icon-action="labelled" title="Print Report" class="inline-flex {{ $actionButtonSize }} items-center justify-center bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-print text-sm"></i><span class="sr-only">Print Report</span></button>
                    @endif
                    <form method="POST" class="quarterly-archive-form inline-flex items-center justify-center {{ $actionButtonSize }} m-0 p-0" action="{{ route('desk-officer.quarterly-reports.archive', $report->id) }}">
                        @csrf
                        <button type="submit" data-icon-action="labelled" title="Archive Report" class="inline-flex w-full h-full items-center justify-center {{ auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC' ? 'bg-[#C41E3A] hover:bg-[#A01830] text-white border border-[#C41E3A]' : 'bg-red-50 hover:bg-red-100 text-red-600 border border-red-200' }} rounded-lg transition shadow-sm p-0 m-0"><i class="fas fa-archive text-sm"></i><span class="sr-only">Archive</span></button>
                    </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="col-span-full flex min-h-32 flex-col items-center justify-center px-4 py-10 text-center text-gray-500">
                <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-[#C41E3A]" aria-hidden="true">
                    <i class="fas fa-archive text-xl"></i>
                </div>
                <p class="text-sm font-semibold text-gray-700">No {{ ($archived ?? false) ? 'archived ' : '' }}{{ $department === 'VAWC' ? 'SWD' : $department }} quarterly reports found.</p>
            </div>
        @endforelse
    </div>
    @if($reports->hasPages())@include('components.archived-pagination', ['paginator' => $reports, 'label' => ($archived ?? false) ? 'archived reports' : 'reports'])@endif
</section>
@endforeach
@push('scripts')
<script>
const quarterlyPhoneWidth = Math.min(window.innerWidth || Infinity, window.screen?.width || Infinity);
const isQuarterlyPhoneView = window.matchMedia('(max-width: 47.9375rem)').matches
    || (/Android|iPhone|iPod/i.test(navigator.userAgent) && quarterlyPhoneWidth <= 480);
document.querySelectorAll('.quarterly-table-view').forEach(view => {
    view.style.display = isQuarterlyPhoneView ? 'none' : '';
});

const submitQuarterlyReportAction = async (form, successTitle) => {
    if (typeof Swal === 'undefined') return form.submit();
    Swal.fire({ title: 'Processing...', text: 'Please wait while your action is completed.', allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
    try {
        const response = await fetch(form.action, { method: form.method || 'POST', body: new FormData(form), credentials: 'same-origin', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) throw new Error(data.message || 'Unable to complete this action.');
        await Swal.fire({ icon: 'success', title: successTitle, text: data.message, confirmButtonText: 'OK', confirmButtonColor: '#C41E3A' });
        window.location.reload();
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'Action Not Completed', text: error.message || 'Unable to complete this action.', confirmButtonText: 'OK', confirmButtonColor: '#C41E3A' });
    }
};
document.querySelectorAll('.quarterly-mobile-cards').forEach(cards => {
    cards.style.display = isQuarterlyPhoneView ? 'grid' : '';
});

window.portalPaginationElements('[data-quarterly-preview]').forEach(button => button.addEventListener('click', () => {
    const openPreview = () => window.open(button.dataset.quarterlyPreview, '_blank', 'noopener');
    if (typeof Swal === 'undefined') return openPreview();
    Swal.fire({
        title: 'Print Report?',
        text: `A print preview for ${button.dataset.period || 'this report'} will open first.`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Open Preview',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#C41E3A'
    }).then(result => { if (result.isConfirmed) openPreview(); });
}));

window.portalPaginationElements('.delete-archived-report-form').forEach(form => form.addEventListener('submit', event => {
    event.preventDefault();
    const submit = () => submitQuarterlyReportAction(form, 'Report Deleted');
    const period = form.dataset.period || 'this report';
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Delete archived report?',
            text: `${period} will be permanently deleted and cannot be restored.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#C41E3A'
        }).then(result => { if (result.isConfirmed) submit(); });
    } else if (confirm(`Permanently delete ${period}? This cannot be undone.`)) {
        submit();
    }
}));

@if(auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'VAWC')
document.addEventListener('submit', async event => {
    const form = event.target.closest('.send-to-admin-form');
    if (!form) return;
    event.preventDefault();
    if (typeof Swal !== 'undefined') {
        const result = await Swal.fire({
            title: 'Send report to Admin?',
            text: 'This report will be sent to the Admin for review.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Send to Admin',
            confirmButtonColor: '#7c3aed'
        });
        if (result.isConfirmed) form.submit();
    } else if (confirm('Send this report to Admin for review?')) {
        form.submit();
    }
});
@endif
window.portalPaginationElements('.quarterly-archive-form').forEach(form => form.addEventListener('submit', async event => {
    event.preventDefault();
    if (typeof Swal !== 'undefined') {
        const result = await Swal.fire({
            title: 'Archive Case Report?',
            text: 'This report will be moved to the archived reports list.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Archive',
            confirmButtonColor: '#dc2626'
        });
        if (result.isConfirmed) submitQuarterlyReportAction(form, 'Report Archived');
    } else {
        if (confirm('Archive this case report?')) form.submit();
    }
}));
window.portalPaginationElements('.restore-confirm-form').forEach(form => form.addEventListener('submit', async event => {
    event.preventDefault();
    if (typeof Swal !== 'undefined') {
        const result = await Swal.fire({
            title: 'Restore archived report?',
            text: 'This report will be returned to the active reports list.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Restore',
            confirmButtonColor: '#7c3aed'
        });
        if (result.isConfirmed) submitQuarterlyReportAction(form, 'Report Restored');
    } else {
        if (confirm('Restore this report to the active reports list?')) form.submit();
    }
}));
</script>
@endpush
