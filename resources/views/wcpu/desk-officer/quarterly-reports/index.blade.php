@extends('layouts.desk-officer')
@section('title', 'Quarterly Reports')
@push('styles')
<style>
    @media (max-width: 767px), (hover: none) and (pointer: coarse) {
        .quarterly-report-actions-container,
        .quarterly-report-actions { width: 100%; }
        .quarterly-generate-report-button {
            display: inline-flex;
            width: 100%;
            height: 2.5rem;
            align-items: center;
            justify-content: center;
            padding-right: .75rem;
            padding-left: .75rem;
        }
    }
    @if(auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment())
    @media (max-width: 47.9375rem) and (orientation: portrait) {
        .quarterly-print-all-button {
            display: inline-flex;
            width: 100%;
            height: 2.5rem;
            min-height: 2.5rem;
            align-items: center;
            justify-content: center;
            padding-right: .75rem;
            padding-left: .75rem;
        }
    }
    @endif
</style>
@endpush
@section('content')

@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'Quarterly Reports', 'subtitle' => 'View and manage quarterly reports'])
<div class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
        <h1 class="text-2xl font-bold text-gray-800">Quarterly Reports</h1>
        @endunless
        <div class="quarterly-report-actions-container flex flex-wrap items-center gap-4">
            <div class="quarterly-report-actions flex flex-wrap items-center gap-2">
            @unless(auth()->user()->isMultiCaseDepartment() || (auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
            <a href="{{ route('desk-officer.quarterly-reports.archived') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm {{ auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) ? 'border border-[#C41E3A] bg-[#C41E3A] text-white hover:bg-[#A01830]' : 'border border-purple-200 text-purple-700 hover:bg-purple-50' }}">
                @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
                <i class="fas fa-archive text-amber-500"></i>
                @endunless
                <span>Archived Reports</span>
            </a>
            @endunless
            @if(auth()->user()->isMultiCaseDepartment())
            <button type="button" onclick="openReportTypeModal()" class="quarterly-generate-report-button px-5 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium transition shadow-sm">Generate New Report</button>
            <button type="button" onclick="printAllQuarterlyReports()" class="quarterly-print-all-button px-5 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium transition shadow-sm">Print All</button>
            @else
            <a href="{{ route('desk-officer.quarterly-reports.create') }}" class="quarterly-generate-report-button px-5 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium transition shadow-sm">Generate New Report</a>
            @endif
            </div>
        </div>
    </div>
    @if(session('success'))<p class="p-4 mb-4 bg-green-50 text-green-700 rounded-lg">{{ session('success') }}</p>@endif
    @if(session('error'))<p class="p-4 mb-4 bg-red-50 text-red-700 rounded-lg">{{ session('error') }}</p>@endif
    @include('desk-officer.quarterly-reports._tables')
</div>
@endsection
@push('scripts')
@if(auth()->user()->isMultiCaseDepartment())
<script>
async function printAllQuarterlyReports() {
    const result = await Swal.fire({ title: 'Print All Reports', input: 'select', inputOptions: { BCPC: 'BCPC Case Reports', VAWC: 'SWD Case Reports' }, inputPlaceholder: 'Select department', showCancelButton: true, confirmButtonText: 'Print', inputValidator: value => !value ? 'Select a department.' : undefined });
    if (!result.isConfirmed) return;
    const counts = @json(collect($reportGroups)->map(fn ($group) => $group->total()));
    if (!counts[result.value]) {
        await Swal.fire({ icon: 'info', title: 'No Reports to Print', text: 'There are no quarterly reports to print.' });
        return;
    }
    window.open(@json(route('desk-officer.quarterly-reports.print-all')) + '?type=' + encodeURIComponent(result.value), '_blank', 'noopener');
}
</script>
@endif
@if(request('select_type'))
<script>document.addEventListener('DOMContentLoaded', () => openReportTypeModal());</script>
@endif
@endpush
