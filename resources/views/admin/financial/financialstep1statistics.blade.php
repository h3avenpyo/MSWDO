@extends('layouts.financial')

@section('title', 'Financial Assistance Statistics - MSWDO Admin')
@section('page-title', 'Financial Assistance Module')

@section('page-styles')
<link href="{{ asset('css/financialstep1statistics.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">



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
                        <h3 class="chart-card-title"><i class="fas fa-chart-line text-primary me-2"></i>Number of Intake
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

        <!-- 3. Comparison of Male and Female Beneficiaries -->
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
                            class="fas  me-2 text-warning"></i>Most Barangays</h6>
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

    <!-- 5. TAGALOG SECTION: Dahilan ng Paghingi ng Tulong -->
    <div class="chart-container-card p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-3 border-bottom">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-1 rounded-pill mb-2"
                    style="font-size: 0.75rem;">
                    <i class="fas fa-heart me-1"></i> SEKSYON NG MGA DAHILAN
                </span>
                <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.01em;"><i
                        class="fas fa-hand-holding-heart text-primary me-2"></i>Dahilan ng Paghingi ng Tulong</h4>
                <p class="text-muted small mb-0">Mga rason at uri ng tulong na idinulog ng mga benepisyaryo sa MSWDO
                    Silang.</p>
            </div>
        </div>

        <div class="row g-4 align-items-center">
            <div class="col-lg-5">
                <div class="p-3 rounded-4 bg-light border text-center">
                    <h6 class="fw-bold text-dark mb-3 text-start small text-uppercase tracking-wider"><i
                            class="fas fa-chart-pie me-2 text-primary"></i>Distribusyon ng mga Dahilan</h6>
                    <div style="height: 240px; position: relative;">
                        <canvas id="dahilanChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="p-3 rounded-4 bg-light border">
                    <h6 class="fw-bold text-dark mb-3 small text-uppercase tracking-wider"><i
                            class="fas fa-list-ul me-2 text-primary"></i>Mga Pangunahing Dahilan at Bilang ng Kaso</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead>
                                <tr class="border-bottom"
                                    style="font-size: 0.75rem; text-transform: uppercase; color: #64748B;">
                                    <th class="ps-2">Dahilan / Assistance Concern</th>
                                    <th class="text-center">Bilang ng Kaso</th>
                                    <th class="text-end pe-2">Bahagdan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reasonsAssistance as $reason)
                                @php
                                $percent = $totalIntakes > 0 ? number_format(($reason->total_cases / $totalIntakes) *
                                100, 1) : 0;
                                @endphp
                                <tr class="border-bottom-subtle">
                                    <td class="ps-2 py-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary bg-opacity-10 p-1 d-flex align-items-center justify-content-center"
                                                style="width: 24px; height: 24px;">
                                                <i class="fas fa-check text-primary" style="font-size: 0.7rem;"></i>
                                            </div>
                                            <span class="fw-semibold text-dark small">{{ $reason->assistance_purpose
                                                }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-dark small">{{
                                        number_format($reason->total_cases) }}</td>
                                    <td class="text-end pe-2">
                                        <span
                                            class="badge bg-white text-dark border fw-bold px-2 py-1 small shadow-xs">{{
                                            $percent }}%</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4 small">Walang naitatalang datos
                                        ng dahilan sa kasalukuyan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
        medicalData: @json(array_values($medicalConcernsSummary))
    };
</script>

<!-- External Module Script -->
<script src="{{ asset('js/financialstep1statistics.js') }}"></script>
@endsection