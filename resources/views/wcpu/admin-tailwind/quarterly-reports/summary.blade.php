@extends('layouts.admin-tailwind')

@section('title', 'Quarterly Report Summary')

@push('styles')
<style>
    .scrollable-table-container {
        max-height: 400px;
        overflow-y: auto;
        border-radius: 0 0 0.75rem 0.75rem;
    }
    .scrollable-table-container thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f9fafb;
        box-shadow: 0 1px 2px rgba(0,0,0,0.06);
    }
    .scrollable-table-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .scrollable-table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    .scrollable-table-container::-webkit-scrollbar-thumb {
        background: #c4b5fd;
        border-radius: 3px;
    }
    .scrollable-table-container::-webkit-scrollbar-thumb:hover {
        background: #a78bfa;
    }
    /* Breakdown table: allow horizontal scroll on mobile */
    .breakdown-scroll {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 400px;
        border-radius: 0 0 0.75rem 0.75rem;
    }
    .breakdown-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 1px 2px rgba(0,0,0,0.06);
    }
    .breakdown-scroll table {
        min-width: 700px;
    }
    .breakdown-scroll::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .breakdown-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    .breakdown-scroll::-webkit-scrollbar-thumb {
        background: #c4b5fd;
        border-radius: 3px;
    }
    @media (min-width: 1024px) {
        .breakdown-scroll table {
            min-width: unset;
        }
    }
</style>
@endpush

@section('content')
<div class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Quarterly Report Summary</h1>
            <p class="text-gray-500 text-sm mt-1">Municipality-wide overview for Q{{ $quarter }} {{ $year }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <a href="{{ route('admin.quarterly-reports.summary.pdf', ['quarter' => $quarter, 'year' => $year, 'preview' => auth()->user()->isDepartmentAdmin() ? 1 : null]) }}"
               {!! auth()->user()->isDepartmentAdmin() ? 'target="_blank" rel="noopener"' : '' !!}
               class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg font-medium transition shadow-md w-full sm:w-auto text-sm">
                <i class="fas fa-print"></i> Preview / Print
            </a>
            <a href="{{ route('admin.quarterly-reports.index') }}" 
               class="inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-600 hover:bg-gray-50 px-5 py-2.5 rounded-lg font-medium transition w-full sm:w-auto text-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Quarter/Year Selector -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-8 border border-gray-100">
        <form action="{{ route('admin.quarterly-reports.summary') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="w-44">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Quarter</label>
                <select name="quarter" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-gray-50">
                    <option value="1" {{ $quarter == 1 ? 'selected' : '' }}>Q1 — Jan to Mar</option>
                    <option value="2" {{ $quarter == 2 ? 'selected' : '' }}>Q2 — Apr to Jun</option>
                    <option value="3" {{ $quarter == 3 ? 'selected' : '' }}>Q3 — Jul to Sep</option>
                    <option value="4" {{ $quarter == 4 ? 'selected' : '' }}>Q4 — Oct to Dec</option>
                </select>
            </div>
            <div class="w-36">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Year</label>
                <select name="year" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-gray-50">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition">
                <i class="fas fa-sync-alt mr-1.5"></i> Update
            </button>
        </form>
    </div>

    <!-- Statistics Cards -->
    @php $pct = $totalBarangays > 0 ? round(($submittedCount / $totalBarangays) * 100) : 0; @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-purple-600"></i>
                </div>
                <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full">{{ $pct }}%</span>
            </div>
            <p class="text-3xl font-bold text-gray-800 mb-1">{{ $submittedCount }} <span class="text-lg font-normal text-gray-400">/ {{ $totalBarangays }}</span></p>
            <p class="text-sm text-gray-500">Reports Submitted</p>
            <div class="mt-4 w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="mb-4">
                <div class="w-11 h-11 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800 mb-1">{{ $grandTotals['total_cases'] }}</p>
            <p class="text-sm text-gray-500">Total SWD Cases</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="mb-4">
                <div class="w-11 h-11 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-amber-500"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800 mb-1">{{ $totalBarangays - $submittedCount }}</p>
            <p class="text-sm text-gray-500">Pending Reports</p>
        </div>
    </div>

    <!-- Submission Status Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">Submission Status</h2>
            <p class="text-xs text-gray-400 mt-0.5">All barangay report submissions</p>
        </div>
        <div class="scrollable-table-container">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">Barangay</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">Cases</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($barangaySummary as $row)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="px-5 py-3">
                            <span class="font-medium text-gray-800 text-sm">{{ $row['barangay'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($row['submitted'])
                            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-0.5 rounded-full text-xs font-medium border border-green-200">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Submitted
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 px-2.5 py-0.5 rounded-full text-xs font-medium border border-amber-200">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span> Pending
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-700 text-sm">{{ $row['total_cases'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cases Breakdown Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-800">Cases Breakdown</h2>
                <p class="text-xs text-gray-400 mt-0.5">Cases per barangay by type and status</p>
            </div>
            <span class="text-[10px] text-gray-400 lg:hidden"><i class="fas fa-arrows-alt-h mr-1"></i>Scroll to see all</span>
        </div>
        <div class="breakdown-scroll">
            <table class="w-full text-xs">
                <thead>
                    <tr>
                        <th class="w-[120px] px-2 py-2 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200" rowspan="2">Barangay</th>
                        <th class="w-[45px] px-1 py-2 text-center text-[11px] font-semibold text-gray-500 uppercase border-b border-gray-200" rowspan="2">Total</th>
                        @if(count($caseTypes) > 0)
                        <th class="px-1 py-1.5 text-center text-[10px] font-semibold text-purple-600 bg-purple-50/70 border-b border-purple-100" colspan="{{ count($caseTypes) }}">By Type</th>
                        @endif
                        @if(count($allStatuses) > 0)
                        <th class="px-1 py-1.5 text-center text-[10px] font-semibold text-emerald-600 bg-emerald-50/70 border-b border-emerald-100" colspan="{{ count($allStatuses) }}">By Status</th>
                        @endif
                    </tr>
                    <tr>
                        @foreach($caseTypes as $ra => $name)
                        <th class="px-1 py-1.5 text-center text-[9px] font-medium text-purple-500 bg-purple-50/40 border-b border-gray-200" title="{{ $name }}">{{ $ra }}</th>
                        @endforeach
                        @foreach($allStatuses as $status)
                        <th class="px-1 py-1.5 text-center text-[9px] font-medium text-emerald-500 bg-emerald-50/40 border-b border-gray-200">{{ str_replace('Case ', '', $status) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($barangaySummary as $row)
                    @if($row['submitted'] && $row['total_cases'] > 0)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="px-2 py-2.5 font-medium text-gray-800 text-xs truncate">{{ $row['barangay'] }}</td>
                        <td class="px-1 py-2.5 text-center font-bold text-gray-700">{{ $row['total_cases'] }}</td>
                        @foreach($caseTypes as $ra => $name)
                        <td class="px-1 py-2.5 text-center {{ $row['abuse_types'][$ra] > 0 ? 'font-semibold text-purple-700' : 'text-gray-300' }}">
                            {{ $row['abuse_types'][$ra] ?: '—' }}
                        </td>
                        @endforeach
                        @foreach($allStatuses as $status)
                        <td class="px-1 py-2.5 text-center {{ $row['statuses'][$status] > 0 ? 'font-semibold text-emerald-700' : 'text-gray-300' }}">
                            {{ $row['statuses'][$status] ?: '—' }}
                        </td>
                        @endforeach
                    </tr>
                    @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 border-t-2 border-gray-200">
                        <td class="px-2 py-2.5 text-right font-bold text-gray-700">Total</td>
                        <td class="px-1 py-2.5 text-center font-bold text-gray-800">{{ $grandTotals['total_cases'] }}</td>
                        @foreach($caseTypes as $ra => $name)
                        <td class="px-1 py-2.5 text-center font-bold text-purple-700">{{ $grandTotals['abuse_types'][$ra] ?: '—' }}</td>
                        @endforeach
                        @foreach($allStatuses as $status)
                        <td class="px-1 py-2.5 text-center font-bold text-emerald-700">{{ $grandTotals['statuses'][$status] ?: '—' }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
