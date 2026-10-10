@extends('layouts.financial')

@section('title', 'Financial Assistance Statistics - MSWDO Admin')
@section('page-title', 'Financial Assistance Module')

@section('page-styles')
<link href="{{ asset('css/financialstep1statistics.css') }}" rel="stylesheet">
@endsection

@section('content')
@php
$silangSealSrc = file_exists(public_path('images/silangseal.png'))
? asset('images/silangseal.png')
: (file_exists(public_path('images/silang.png')) ? asset('images/silang.png') : '');
$mswdoLogoSrc = file_exists(public_path('images/mswdo-logo.png'))
? asset('images/mswdo-logo.png')
: (file_exists(public_path('images/dswdlogo.png')) ? asset('images/dswdlogo.png') : '');
@endphp

<div class="container-fluid">

    <!-- ==============================================================
         1. SCREEN DASHBOARD INTERFACE (Hidden entirely in Print)
         ============================================================== -->
    <div class="screen-dashboard-container no-print">

        <!-- Step Wizard Header Card -->
        <div class="step-wizard-card stats-header-card animate-fade-in mb-4">
            <div class="stats-header-inner d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="stats-header-content">
                    <div class="stats-header-title-row d-flex align-items-center flex-wrap gap-2 mb-1">
                        <h2 class="wizard-heading mb-0 d-inline-flex align-items-center gap-2 text-white">
                            <i class="fas fa-chart-pie me-1" aria-hidden="true"></i>
                            <span>Financial Assistance Statistics &amp; Analytics</span>
                        </h2>
                        <span class="badge step-1-badge rounded-pill">Step 1</span>
                    </div>
                    <div class="stats-header-meta-row d-flex align-items-center flex-wrap gap-2 pt-1">
                        <p class="hero-subtitle mb-0">Demographic overview, intake volume trends, and assistance purpose
                            breakdown</p>
                        <span class="stats-period-badge">

                            <span>Period: {{ $selectedMonthLabel }}</span>
                        </span>
                    </div>
                </div>
                <div class="stats-header-actions d-flex align-items-center gap-2.5 flex-wrap no-print">
                    <!-- Month/Year Filter Form -->
                    <form method="GET" action="{{ route('admin.financial.financialstep1statistics') }}"
                        class="d-inline-flex m-0 p-0" id="monthFilterForm">
                        <div class="stats-filter-pill">
                            <i class="fas fa-filter stats-filter-icon" aria-hidden="true"></i>
                            <select name="month" id="monthFilterSelect" class="stats-filter-select"
                                onchange="document.getElementById('monthFilterForm').submit()"
                                title="Select month to filter statistics" aria-label="Select Month">
                                <option value="all" {{ $isAll ? 'selected' : '' }}>
                                    All Months (Overall)
                                </option>
                                @foreach($availableMonths as $val => $label)
                                <option value="{{ $val }}" {{ (!$isAll && $selectedMonth===$val) ? 'selected' : '' }}>
                                    {{ $label }} {{ $val === $currentMonthKey ? '(Current Month)' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <!-- Print Statistics Button -->
                    <button type="button" onclick="window.print()" class="btn-stats-print btn-print-statistics"
                        title="Print official statistics report" aria-label="Print Statistics">
                        <i class="fas fa-print" aria-hidden="true"></i>
                        <span>Print Statistics</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Metric Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="kpi-card shadow-sm">
                    <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Intake Cases</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($totalIntakes) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card shadow-sm">
                    <div class="kpi-icon bg-success bg-opacity-10 text-success">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Top Barangay</div>
                        <div class="fs-6 fw-bold text-dark text-truncate" style="max-width: 170px;"
                            title="{{ array_key_first($barangayBreakdown) ?? 'N/A' }}">
                            {{ array_key_first($barangayBreakdown) ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card shadow-sm">
                    <div class="kpi-icon bg-info bg-opacity-10 text-info">
                        <i class="fas fa-venus-mars"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Male vs Female</div>
                        <div class="fs-5 fw-bold text-dark">
                            {{ $genderBreakdown['Male'] ?? 0 }} <span class="fs-6 text-muted">M</span> / {{
                            $genderBreakdown['Female'] ?? 0 }} <span class="fs-6 text-muted">F</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card shadow-sm">
                    <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Primary Assistance Type</div>
                        <div class="fs-6 fw-bold text-dark text-truncate" style="max-width: 170px;"
                            title="{{ array_key_first($medicalConcernsSummary) ?? 'N/A' }}">
                            {{ array_key_first($medicalConcernsSummary) ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid Row 1 -->
        <div class="row g-4 mb-4">
            <!-- 1. Monthly Intake Cases Trend -->
            <div class="col-lg-8">
                <div class="chart-container-card">
                    <div class="chart-card-header">
                        <div>
                            <h3 class="chart-card-title"><i class="fas fa-chart-line text-primary me-2"></i>Number of
                                Intake
                                Cases per Month</h3>
                            <p class="chart-card-subtitle">Monthly volume of encoded financial assistance client intakes
                                (Past 12 Months)</p>
                        </div>
                        <span class="badge bg-light text-dark border">12 Month Trend</span>
                    </div>
                    <div class="chart-box">
                        <canvas id="monthlyIntakesChart" style="max-height: 320px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- 2. Comparison of Male and Female Beneficiaries -->
            <div class="col-lg-4">
                <div class="chart-container-card h-100">
                    <div class="chart-card-header">
                        <div>
                            <h3 class="chart-card-title"><i class="fas fa-users text-info me-2"></i>Male vs Female
                                Beneficiaries</h3>
                            <p class="chart-card-subtitle">Gender demographic breakdown</p>
                        </div>
                    </div>
                    <div class="chart-box d-flex align-items-center justify-content-center">
                        <canvas id="genderChart" style="max-height: 270px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid Row 2: Beneficiaries by Barangay -->
        <div class="chart-container-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-3 border-bottom">
                <div>
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill mb-2"
                        style="font-size: 0.75rem;">
                        <i class="fas fa-map-marker-alt me-1"></i> GEOGRAPHIC DISTRIBUTION
                    </span>
                    <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.01em;"><i
                            class="fas fa-map-marked-alt text-success me-2"></i>Beneficiaries by Barangay</h4>
                    <p class="text-muted small mb-0">Top barangays with the highest number of financial intake cases in
                        Silang.</p>
                </div>
                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-semibold small">
                    <i class="fas fa-city me-1"></i> Silang Barangays
                </span>
            </div>

            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="p-3 rounded-4 bg-light border">
                        <h6 class="fw-bold text-dark mb-3 small text-uppercase tracking-wider"><i
                                class="fas fa-chart-bar me-2 text-success"></i>Barangay Case Breakdown</h6>
                        <div style="height: 320px; position: relative;">
                            <canvas id="barangayChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="p-3 rounded-4 bg-light border">
                        <h6 class="fw-bold text-dark mb-3 small text-uppercase tracking-wider"><i
                                class="fas fa-trophy me-2 text-warning"></i>Top Barangays Ranking</h6>
                        <div class="d-flex flex-column gap-2" style="max-height: 320px; overflow-y: auto;">
                            @forelse($barangayBreakdown as $brgyName => $brgyCount)
                            @php
                            $brgyPercent = $totalIntakes > 0 ? number_format(($brgyCount / $totalIntakes) * 100, 1) : 0;
                            @endphp
                            <div
                                class="bg-white p-2 px-3 rounded-3 border d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                                    <div class="rounded-circle bg-success bg-opacity-10 text-success fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 28px; height: 28px; font-size: 0.75rem;">
                                        {{ $loop->iteration }}
                                    </div>
                                    <span class="fw-semibold text-dark small text-truncate" title="{{ $brgyName }}">{{
                                        $brgyName }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    <span class="fw-bold text-dark small">{{ number_format($brgyCount) }} <span
                                            class="text-muted fw-normal">cases</span></span>
                                    <span
                                        class="badge bg-success-subtle text-success border border-success-subtle fw-bold px-2 py-1 small">{{
                                        $brgyPercent }}%</span>
                                </div>
                            </div>
                            @empty
                            <div class="text-muted text-center py-4 small">No barangay data recorded yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Assistance Purpose & Medical Condition (Two Charts in One Section) -->
        <div class="chart-container-card p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-3 border-bottom">
                <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-1 rounded-pill mb-2"
                        style="font-size: 0.75rem;">
                        <i class="fas fa-heart me-1"></i> URI NG ASSISTANCE
                    </span>
                    <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.01em;"><i
                            class="fas fa-hand-holding-heart text-primary me-2"></i>Assistance Purpose / Uri ng Assistance</h4>
                    <p class="text-muted small mb-0">Mga uri ng tulong na hinihingi at medikal na sitwasyon ng mga benepisyaryo sa MSWDO Silang.</p>
                </div>
            </div>

            <div class="row g-4 align-items-stretch">
                <!-- Chart 1: Medical Condition -->
                <div class="col-lg-6">
                    <div class="p-3 rounded-4 bg-light border text-center h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0 text-start small text-uppercase tracking-wider">
                                <i class="fas fa-heartbeat me-2 text-danger"></i>Medical Condition / Dahilan
                            </h6>
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 small fw-semibold">Medical Concerns</span>
                        </div>
                        <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 280px; position: relative;">
                            <canvas id="dahilanChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Assistance Purpose / Uri ng Assistance -->
                <div class="col-lg-6">
                    <div class="p-3 rounded-4 bg-light border text-center h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0 text-start small text-uppercase tracking-wider">
                                <i class="fas fa-hand-holding-medical me-2 text-primary"></i>Assistance Purpose / Uri ng Assistance
                            </h6>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 small fw-semibold">Uri ng Tulong</span>
                        </div>
                        <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 280px; position: relative;">
                            <canvas id="assistanceTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /.screen-dashboard-container.no-print -->

    <!-- ==============================================================
         2. OFFICIAL PRINT-ONLY REPORT DOCUMENT (Pure Statistical Data)
            This section renders strictly during print; screen UI is hidden.
         ============================================================== -->
    <div class="print-report-document">

        <!-- Official Header & Letterhead -->
        <div class="report-header-banner">
            <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom border-2 border-dark">
                <div class="header-seal-col" style="width: 60px;">
                    @if($silangSealSrc)
                    <img src="{{ $silangSealSrc }}" alt="Silang Seal" class="report-seal-img">
                    @endif
                </div>
                <div class="text-center flex-grow-1 px-2">
                    <div class="govt-republic-text">Republic of the Philippines</div>
                    <div class="govt-province-text">Province of Cavite &bull; Municipality of Silang</div>
                    <div class="govt-office-text">Municipal Social Welfare and Development Office</div>
                    <h2 class="report-main-title">OFFICIAL STATISTICAL REPORT</h2>
                    <div class="report-sub-title">General Intake &amp; Financial Assistance Assessment Overview &bull;
                        {{ $isAll ? 'Overall Records' : $selectedMonthLabel }}</div>
                </div>
                <div class="header-seal-col text-end" style="width: 60px;">
                    @if($mswdoLogoSrc)
                    <img src="{{ $mswdoLogoSrc }}" alt="MSWDO Logo" class="report-seal-img">
                    @endif
                </div>
            </div>

            <!-- Report Meta Information Table -->
            <table class="report-meta-table mb-3">
                <tr>
                    <td class="report-meta-cell" style="width: 25%;">
                        <div class="report-meta-title">Date &amp; Time Generated:</div>
                        <div class="report-meta-val">{{ date('F d, Y &bull; h:i A') }}</div>
                    </td>
                    <td class="report-meta-cell" style="width: 35%;">
                        <div class="report-meta-title">Period Covered:</div>
                        <div class="report-meta-val" style="font-weight: 800; color: #1A237E;">
                            @if($isAll)
                            All Records / Overall Historical Data
                            @else
                            {{ $selectedMonthLabel }} ({{ $selectedMonth }})
                            @endif
                        </div>
                    </td>
                    <td class="report-meta-cell" style="width: 20%;">
                        <div class="report-meta-title">Total Intake Cases:</div>
                        <div class="report-meta-val" style="font-weight: 800; color: #1A237E;">{{
                            number_format($totalIntakes) }} Cases</div>
                    </td>
                    <td class="report-meta-cell text-end" style="width: 20%;">
                        <div class="report-meta-title">Reporting Agency:</div>
                        <div class="report-meta-val">MSWDO Silang (AICS)</div>
                    </td>
                </tr>
            </table>

            <!-- Executive Key Indicators Summary (4 Even Columns: 25% each) -->
            <div class="report-section section-summary mb-3">
                <table class="report-summary-box mb-0">
                    <tr>
                        <td class="report-summary-cell" style="width: 25%;">
                            <div class="report-summary-title">Total Intake Cases</div>
                            <div class="report-summary-val">{{ number_format($totalIntakes) }}</div>
                            <div class="report-summary-sub">Evaluated &amp; Encoded</div>
                        </td>
                        <td class="report-summary-cell" style="width: 25%;">
                            <div class="report-summary-title">Top Barangay</div>
                            <div class="report-summary-val">{{ array_key_first($barangayBreakdown) ?? 'N/A' }}</div>
                            <div class="report-summary-sub">{{ reset($barangayBreakdown) ?: 0 }} registered cases</div>
                        </td>
                        <td class="report-summary-cell" style="width: 25%;">
                            <div class="report-summary-title">Gender Demographics</div>
                            <div class="report-summary-val">{{ $genderBreakdown['Male'] ?? 0 }} M / {{
                                $genderBreakdown['Female'] ?? 0 }} F</div>
                            <div class="report-summary-sub">
                                @php
                                $mRatio = $totalIntakes > 0 ? number_format((($genderBreakdown['Male'] ?? 0) /
                                $totalIntakes) * 100, 0) : 0;
                                $fRatio = $totalIntakes > 0 ? number_format((($genderBreakdown['Female'] ?? 0) /
                                $totalIntakes) * 100, 0) : 0;
                                @endphp
                                {{ $mRatio }}% Male &bull; {{ $fRatio }}% Female
                            </div>
                        </td>
                        <td class="report-summary-cell" style="width: 25%;">
                            <div class="report-summary-title">Leading Assistance Type</div>
                            <div class="report-summary-val text-truncate"
                                title="{{ array_key_first($medicalConcernsSummary) ?? 'N/A' }}">
                                {{ array_key_first($medicalConcernsSummary) ?? 'N/A' }}
                            </div>
                            <div class="report-summary-sub">{{ reset($medicalConcernsSummary) ?: 0 }} reported cases
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Section 1: Intake Volume Breakdown (Full Width: 12% / 58% / 30%) -->
        <div class="report-section section-monthly mb-3">
            @if(!$isAll)
            <div class="report-section-heading">1. Intake Cases Breakdown by Date &bull; {{ $selectedMonthLabel }}</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 12%; text-align: center;">No.</th>
                        <th style="width: 58%;">Date Processed</th>
                        <th style="width: 30%; text-align: right;">Intake Cases Recorded</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $dIndex = 1;
                    $dSum = array_sum($dailyBreakdown);
                    @endphp
                    @forelse($dailyBreakdown as $dateKey => $dCount)
                    <tr>
                        <td style="text-align: center;">{{ $dIndex++ }}</td>
                        <td style="font-weight: 600;">{{ \Carbon\Carbon::parse($dateKey)->format('F d, Y (l)') }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($dCount) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748B;">No intake records found for {{
                            $selectedMonthLabel }}.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="report-table-total-row">
                        <td colspan="2" style="font-weight: 700;">Total Encoded for {{ $selectedMonthLabel }}</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($dSum) }}</td>
                    </tr>
                </tfoot>
            </table>
            @else
            <div class="report-section-heading">1. Monthly Intake Volume Breakdown (Past 12 Months)</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 12%; text-align: center;">No.</th>
                        <th style="width: 58%;">Month &amp; Year</th>
                        <th style="width: 30%; text-align: right;">Intake Cases Recorded</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $mIndex = 1;
                    $mSum = array_sum($monthlyIntakes);
                    @endphp
                    @forelse($monthlyIntakes as $monthLabel => $mCount)
                    <tr>
                        <td style="text-align: center;">{{ $mIndex++ }}</td>
                        <td style="font-weight: 600;">{{ $monthLabel }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($mCount) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748B;">No monthly data recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="report-table-total-row">
                        <td colspan="2" style="font-weight: 700;">Total Encoded Across All Months</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($mSum) }}</td>
                    </tr>
                </tfoot>
            </table>
            @endif
        </div>

        <!-- Section 2: Beneficiaries Demographics by Sex (Full Width: 12% / 58% / 30%) -->
        <div class="report-section section-gender mb-3">
            <div class="report-section-heading">2. Beneficiaries Demographic Breakdown by Sex &bull; {{ $isAll ?
                'Overall Records' : $selectedMonthLabel }}</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 12%; text-align: center;">No.</th>
                        <th style="width: 58%;">Sex / Demographic Category</th>
                        <th style="width: 30%; text-align: right;">Beneficiaries Recorded</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $gIndex = 1;
                    $gSum = array_sum($genderBreakdown);
                    @endphp
                    @forelse($genderBreakdown as $genderLabel => $gCount)
                    <tr>
                        <td style="text-align: center;">{{ $gIndex++ }}</td>
                        <td style="font-weight: 600;">{{ $genderLabel }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($gCount) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748B;">No gender data recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="report-table-total-row">
                        <td colspan="2" style="font-weight: 700;">Total Recorded ({{ $isAll ? 'Overall' :
                            $selectedMonthLabel }})</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($gSum) }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Demographic Distribution Summary Strip -->
            <div class="d-flex justify-content-between align-items-center p-2 mt-2 bg-light border rounded"
                style="font-size: 8pt;">
                <div>
                    <strong>Demographic Summary ({{ $isAll ? 'Overall' : $selectedMonthLabel }}):</strong>
                    Male: <span class="fw-bold">{{ number_format($genderBreakdown['Male'] ?? 0) }}</span> &bull;
                    Female: <span class="fw-bold">{{ number_format($genderBreakdown['Female'] ?? 0) }}</span>
                </div>
                <div>
                    <strong>Primary Beneficiary Group:</strong>
                    <span class="fw-bold text-dark">{{ ($genderBreakdown['Female'] ?? 0) >= ($genderBreakdown['Male'] ??
                        0) ? 'Female Beneficiaries' : 'Male Beneficiaries' }}</span>
                </div>
            </div>
        </div>

        <!-- Page Break for Part II (A4 Sheet 2) - Cleanly splits to second page -->
        <div class="report-page-break"></div>

        <!-- Page 2 Header -->
        <div class="report-page-header-mini mb-3">
            <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary">
                <div class="small fw-bold text-uppercase" style="font-size: 8pt; color: #1E293B;">
                    MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE &bull; MUNICIPALITY OF SILANG, CAVITE
                </div>
                <div class="small text-muted" style="font-size: 7.5pt;">
                    FINANCIAL ASSISTANCE MODULE &bull; STATISTICAL REPORT (PAGE 2) &bull; {{ $isAll ? 'ALL MONTHS' :
                    strtoupper($selectedMonthLabel) }}
                </div>
            </div>
        </div>

        <!-- Section 3: Geographic Distribution by Barangay (Full Width: 12% / 58% / 30%) -->
        <div class="report-section section-barangay mb-3">
            <div class="report-section-heading">3. Geographic Distribution (Top Barangays) &bull; {{ $isAll ? 'Overall
                Records' : $selectedMonthLabel }}</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 12%; text-align: center;">Rank</th>
                        <th style="width: 58%;">Barangay Name</th>
                        <th style="width: 30%; text-align: right;">Intake Cases Recorded</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $bIndex = 1;
                    $bSum = array_sum($barangayBreakdown);
                    @endphp
                    @forelse($barangayBreakdown as $bName => $bCount)
                    <tr>
                        <td style="text-align: center;">{{ $bIndex++ }}</td>
                        <td style="font-weight: 600;">{{ $bName }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($bCount) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748B;">No barangay records found.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="report-table-total-row">
                        <td colspan="2" style="font-weight: 700;">Top Barangays Subtotal ({{ $isAll ? 'Overall' :
                            $selectedMonthLabel }})</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($bSum) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Section 4: Uri ng Assistance / Assistance Purpose (Full Width: 12% / 58% / 30%) -->
        <div class="report-section section-reasons mb-3">
            <div class="report-section-heading">4. Uri ng Assistance / Assistance Purpose &bull; {{ $isAll ? 'Overall Records' : $selectedMonthLabel }}</div>
            <p class="report-table-subtext mb-2">Comprehensive distribution of assistance purposes and categories reported by beneficiaries upon intake assessment.</p>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 12%; text-align: center;">No.</th>
                        <th style="width: 58%;">Assistance Purpose / Uri ng Assistance</th>
                        <th style="width: 30%; text-align: right;">Bilang ng Kaso (Cases)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $pIndex = 1;
                    $pSum = 0;
                    @endphp
                    @forelse($assistancePurposeBreakdown as $purpose)
                    @php
                    $pCases = $purpose->total_cases;
                    $pSum += $pCases;
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $pIndex++ }}</td>
                        <td style="font-weight: 600;">{{ $purpose->assistance_type }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($pCases) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748B;">Walang naitatalang datos ng uri ng assistance sa kasalukuyan.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="report-table-total-row">
                        <td colspan="2" style="font-weight: 700;">Kabuuan / Total Assistance Requests Evaluated ({{
                            $isAll ? 'Overall' : $selectedMonthLabel }})</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($pSum) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Section 5: Dahilan ng Paghingi ng Tulong (Medical Concerns / Conditions) -->
        <div class="report-section section-reasons mb-3">
            <div class="report-section-heading">5. Dahilan ng Paghingi ng Tulong (Medical Conditions / Health Concerns) &bull; {{ $isAll ? 'Overall Records' : $selectedMonthLabel }}</div>
            <p class="report-table-subtext mb-2">Summary of medical conditions, illnesses, and healthcare concerns recorded during beneficiary intake assessment.</p>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 12%; text-align: center;">No.</th>
                        <th style="width: 58%;">Medical Condition / Dahilan</th>
                        <th style="width: 30%; text-align: right;">Bilang ng Kaso (Cases)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $rIndex = 1;
                    $rSum = 0;
                    @endphp
                    @forelse($reasonsAssistance as $reason)
                    @php
                    $rCases = $reason->total_cases;
                    $rSum += $rCases;
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $rIndex++ }}</td>
                        <td style="font-weight: 600;">{{ $reason->assistance_purpose }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($rCases) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748B;">Walang naitatalang datos ng medical condition sa kasalukuyan.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="report-table-total-row">
                        <td colspan="2" style="font-weight: 700;">Kabuuan / Total Medical Conditions Recorded ({{
                            $isAll ? 'Overall' : $selectedMonthLabel }})</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($rSum) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Section 6: Administrative Certification & Signatures -->
        <div class="report-section section-signatures">
            <div class="report-notes-box mb-3">
                <div class="fw-bold text-uppercase mb-1" style="font-size: 7.5pt; color: #1E293B;">Administrative Notes
                    &amp; Data Verification</div>
                <p class="mb-0" style="font-size: 7pt; color: #475569; line-height: 1.4;">
                    This document represents the consolidated electronic record extracted from the Municipal Social
                    Welfare and Development Office (MSWDO) General Intake Database under the Assistance to Individuals
                    in Crisis Situations (AICS) and Financial Assistance Assessment Programs. All statistical records
                    reflect direct encoding by authorized MSWDO personnel.
                </p>
            </div>

            <!-- Official Signatures Block -->
            <div class="report-signatures-section">
                <div class="row">
                    <div class="col-6 text-center">
                        <div class="signature-space"></div>
                        <div class="signature-line mx-auto"></div>
                        <div class="fw-bold text-dark mt-1" style="font-size: 9pt;">{{ session('admin_user_name') ??
                            'MSWDO Staff / Statistician' }}</div>
                        <div class="text-muted"
                            style="font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.03em;">Prepared by /
                            Reporting Officer</div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="signature-space"></div>
                        <div class="signature-line mx-auto"></div>
                        <div class="fw-bold text-dark mt-1" style="font-size: 9pt;">Municipal Social Welfare &amp;
                            Development Officer</div>
                        <div class="text-muted"
                            style="font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.03em;">Noted &amp;
                            Approved by</div>
                    </div>
                </div>
            </div>

            <!-- Official Document Footer -->
            <div class="report-official-footer d-flex justify-content-between align-items-center mt-3 pt-2 border-top text-muted"
                style="font-size: 7pt;">
                <span>MSWDO Silang, Cavite &bull; Financial Assistance Module &bull; Confidential Official Report
                    (Period: {{ $isAll ? 'All Months' : $selectedMonthLabel }})</span>
                <span>Document Printed on {{ date('F d, Y \a\t h:i A') }}</span>
            </div>
        </div>

    </div><!-- /.print-report-document -->

</div>
@endsection

@section('page-scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Pass Backend Aggregated Data to External JS -->
<script>
    window.statisticsData = {
        monthlyLabels: @json(array_keys($monthlyIntakes)),
        monthlyData: @json(array_values($monthlyIntakes)),
        barangayLabels: @json(array_keys($barangayBreakdown)),
        barangayData: @json(array_values($barangayBreakdown)),
        genderLabels: @json(array_keys($genderBreakdown)),
        genderData: @json(array_values($genderBreakdown)),
        medicalLabels: @json(array_keys($medicalConcernsSummary)),
        medicalData: @json(array_values($medicalConcernsSummary)),
        assistanceTypeLabels: @json(array_keys($assistanceTypeSummary)),
        assistanceTypeData: @json(array_values($assistanceTypeSummary))
    };
</script>

<!-- External Module Script -->
<script src="{{ asset('js/financialstep1statistics.js') }}"></script>
@endsection