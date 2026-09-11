@extends('layouts.financial')

@section('title', 'Step 2 Statistics & Analytics - MSWDO Admin')
@section('page-title', 'Step 2: Statistics & Analytics')

@section('page-styles')
<link href="{{ asset('css/financialstep2-statistics.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1A237E;">
                <i class="fas fa-chart-pie me-2"></i>Step 2: Statistics &amp; Analytics
            </h4>
            <p class="text-muted small mb-0">Real-time beneficiary distributions, demographic breakdown, and sector financial assistance reports.</p>
        </div>
        <div class="d-flex align-items-center gap-2 no-print">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Report
            </button>
            <a href="{{ route('admin.financial.financialstep2') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold shadow-xs">
                <i class="fas fa-arrow-left me-1"></i> Back to Step 2
            </a>
        </div>
    </div>

    <!-- Quick Metric Summary Cards (Clean border-0 shadow-xs cards matching Step 2 standard) -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1 text-uppercase">Total Beneficiaries</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($totalBeneficiaries) }}</h4>
                        <div class="text-muted text-xs mt-1">
                            <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>{{ number_format($totalClaimed) }}</span> Claimed
                            @if($totalBeneficiaries > 0)
                            ({{ round(($totalClaimed / $totalBeneficiaries) * 100, 1) }}%)
                            @endif
                        </div>
                    </div>
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1 text-uppercase">Total Assistance Granted</p>
                        <h4 class="fw-bold mb-0 text-success">&#8369;{{ number_format($totalAmount, 2) }}</h4>
                        <div class="text-muted text-xs mt-1">
                            Claimed: <strong class="text-dark">&#8369;{{ number_format($totalClaimedAmount, 2) }}</strong>
                        </div>
                    </div>
                    <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-hand-holding-usd fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1 text-uppercase">Top Barangay</p>
                        <h4 class="fw-bold mb-0 text-dark text-truncate" style="max-width: 170px;" title="{{ $topBarangay ?? 'N/A' }}">
                            {{ $topBarangay ?? 'N/A' }}
                        </h4>
                        <div class="text-muted text-xs mt-1">
                            <strong class="text-dark">{{ number_format($topBarangayCount) }}</strong> beneficiaries recorded
                        </div>
                    </div>
                    <div class="rounded-circle bg-info-subtle text-info p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-map-marker-alt fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1 text-uppercase">Top Sector</p>
                        <h4 class="fw-bold mb-0 text-warning text-truncate" style="max-width: 170px;" title="{{ $topSector ?? 'N/A' }}">
                            {{ $topSector ?? 'N/A' }}
                        </h4>
                        <div class="text-muted text-xs mt-1">
                            <strong class="text-dark">{{ number_format($topSectorCount) }}</strong> recipients
                        </div>
                    </div>
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-layer-group fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 1: Beneficiaries per Barangay (Clean Borderless Card) -->
    <div class="card border-0 shadow-xs rounded-4 bg-white mb-4 overflow-hidden">
        <div class="chart-card-header">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-success-subtle text-success fw-bold px-2.5 py-1 rounded-pill text-2xs text-uppercase tracking-wider">
                        <i class="fas fa-map-marker-alt me-1"></i> Geographic Breakdown
                    </span>
                </div>
                <h3 class="chart-card-title">
                    <i class="fas fa-map-marked-alt text-success me-2"></i>Beneficiaries per Barangay
                </h3>
                <p class="chart-card-subtitle">Number of beneficiaries who received financial assistance per Barangay in Silang</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap no-print">
                <!-- Live Search Filter -->
                <div class="position-relative search-box-wrapper">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-2.5 text-muted text-xs"></i>
                    <input type="text" id="barangaySearchInput" class="form-control form-control-sm rounded-pill ps-4 pe-3 bg-light border-0 text-xs" placeholder="Search barangay...">
                </div>

                <!-- View Limits (Top 10 / Top 25 / All) -->
                <div class="btn-group btn-group-sm rounded-pill bg-light p-0.5 shadow-none" role="group" aria-label="Barangay View Limit">
                    <button type="button" class="btn btn-sm rounded-pill px-2.5 py-1 text-xs fw-semibold btn-barangay-limit active" data-limit="10">Top 10</button>
                    <button type="button" class="btn btn-sm rounded-pill px-2.5 py-1 text-xs fw-semibold btn-barangay-limit" data-limit="25">Top 25</button>
                    <button type="button" class="btn btn-sm rounded-pill px-2.5 py-1 text-xs fw-semibold btn-barangay-limit" data-limit="all">All ({{ count($barangayStats) }})</button>
                </div>

                <!-- Sort Toggle -->
                <button type="button" id="btnSortBarangays" class="btn btn-sm btn-light border-0 shadow-xs rounded-pill px-2.5 py-1 fw-semibold text-xs text-muted" title="Toggle Sort: Highest vs A-Z">
                    <i class="fas fa-sort-amount-down me-1 text-success"></i> <span id="barangaySortLabel">Highest</span>
                </button>

                <span id="barangayActiveCountBadge" class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 text-xs fw-semibold">
                    {{ count($barangayStats) }} Active
                </span>
            </div>
        </div>

        <div class="p-4">
            <div class="row g-4 align-items-stretch">
                <!-- Barangay Horizontal Bar Chart -->
                <div class="col-lg-7">
                    <div class="p-3 rounded-4 bg-light h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark mb-0 small text-uppercase tracking-wider">
                                <i class="fas fa-chart-bar text-success me-2"></i>Barangay Recipient Chart
                            </h6>
                            <span id="barangayChartShowingText" class="text-muted text-xs">Showing top 10 barangays</span>
                        </div>
                        <!-- Responsive scrollable viewport prevents squashed bars and unreadable labels -->
                        <div class="chart-scroll-wrapper flex-grow-1" id="barangayChartScrollContainer">
                            <div class="barangay-chart-inner" id="barangayChartInner" style="position: relative; width: 100%; min-height: 380px;">
                                <canvas id="barangayBarChart"></canvas>
                            </div>
                        </div>
                        <div id="barangayChartEmpty" class="text-muted text-center py-5 small d-none">
                            <i class="fas fa-search me-1 fa-2x d-block mb-2 text-muted opacity-50"></i>
                            No barangays matching your search filter.
                        </div>
                    </div>
                </div>

                <!-- Ranked Barangay Leaderboard List -->
                <div class="col-lg-5">
                    <div class="p-3 rounded-4 bg-light h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0 small text-uppercase tracking-wider">
                                <i class="fas fa-ranking-star text-warning me-2"></i>Ranked Barangay Directory
                            </h6>
                            <span id="barangayListCountText" class="text-muted text-xs">{{ count($barangayStats) }} records</span>
                        </div>
                        <div class="barangay-list-container flex-grow-1" id="barangayListContainer">
                            @forelse($barangayStats as $brgyName => $bData)
                            <div class="barangay-card-item" data-name="{{ strtolower($brgyName) }}">
                                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                                    <div class="barangay-rank-badge {{ $loop->iteration === 1 ? 'bg-warning text-dark' : ($loop->iteration === 2 ? 'bg-secondary text-white' : ($loop->iteration === 3 ? 'bg-success-subtle text-success' : 'bg-white text-muted shadow-xs')) }}">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div class="text-truncate">
                                        <div class="fw-bold text-dark small text-truncate" title="{{ $brgyName }}">{{ $brgyName }}</div>
                                        <div class="text-muted text-2xs">{{ $bData['formatted_amount'] }} granted</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    <span class="fw-bold text-dark small">{{ number_format($bData['beneficiaries']) }} <span class="text-muted fw-normal">cases</span></span>
                                    <span class="badge bg-success-subtle text-success fw-bold px-2 py-1 text-xs rounded-pill">
                                        {{ $bData['percentage'] }}%
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="text-muted text-center py-4 small">No barangay assistance records found.</div>
                            @endforelse
                        </div>
                        <div id="barangayListEmpty" class="text-muted text-center py-4 small d-none">
                            <i class="fas fa-search me-1 d-block mb-1"></i> No matching barangays found.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Sector Breakdown & Gender Demographic (Clean Borderless Cards) -->
    <div class="row g-4 mb-4">
        <!-- Financial Assistance by Sector (Highest to Lowest) -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-xs rounded-4 bg-white h-100 d-flex flex-column mb-0 overflow-hidden">
                <div class="chart-card-header">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1 rounded-pill text-2xs text-uppercase tracking-wider">
                                <i class="fas fa-sort-amount-down me-1"></i> Ranked Distribution
                            </span>
                        </div>
                        <h3 class="chart-card-title">
                            <i class="fas fa-layer-group text-primary me-2"></i>Financial Assistance by Sector
                        </h3>
                        <p class="chart-card-subtitle">Sectors receiving financial assistance, arranged highest to lowest</p>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap no-print">
                        <!-- Live Search Filter -->
                        <div class="position-relative search-box-wrapper">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-2.5 text-muted text-xs"></i>
                            <input type="text" id="sectorSearchInput" class="form-control form-control-sm rounded-pill ps-4 pe-3 bg-light border-0 text-xs" placeholder="Search sector...">
                        </div>

                        <!-- Metric Switcher (Cases vs Amount) -->
                        <div class="btn-group btn-group-sm rounded-pill bg-light p-0.5 shadow-none" role="group" aria-label="Sector Metric">
                            <button type="button" class="btn btn-sm rounded-pill px-2.5 py-1 text-xs fw-semibold btn-sector-metric active" data-metric="cases">Cases</button>
                            <button type="button" class="btn btn-sm rounded-pill px-2.5 py-1 text-xs fw-semibold btn-sector-metric" data-metric="amount">Grants (₱)</button>
                        </div>

                        <!-- View Limits (Top 5 / Top 10 / All) -->
                        <div class="btn-group btn-group-sm rounded-pill bg-light p-0.5 shadow-none" role="group" aria-label="Sector View Limit">
                            <button type="button" class="btn btn-sm rounded-pill px-2 py-1 text-xs fw-semibold btn-sector-limit" data-limit="5">Top 5</button>
                            <button type="button" class="btn btn-sm rounded-pill px-2 py-1 text-xs fw-semibold btn-sector-limit active" data-limit="10">Top 10</button>
                            <button type="button" class="btn btn-sm rounded-pill px-2 py-1 text-xs fw-semibold btn-sector-limit" data-limit="all">All</button>
                        </div>

                        <span id="sectorActiveCountBadge" class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 text-xs fw-semibold">
                            {{ count($sectorRanked) }} Sectors
                        </span>
                    </div>
                </div>

                <div class="p-3 flex-grow-1 d-flex flex-column">
                    <div class="p-3 rounded-4 bg-light mb-3 flex-grow-1 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span id="sectorChartShowingText" class="text-muted text-xs">Showing top 10 sectors</span>
                            <span id="sectorChartMetricBadge" class="badge bg-white text-dark shadow-xs rounded-pill px-2 py-0.5 text-2xs">Beneficiary Count</span>
                        </div>
                        <div class="chart-scroll-wrapper flex-grow-1" id="sectorChartScrollContainer" style="max-height: 320px;">
                            <div class="sector-chart-inner" id="sectorChartInner" style="position: relative; width: 100%; min-height: 260px;">
                                <canvas id="sectorBarChart"></canvas>
                            </div>
                        </div>
                        <div id="sectorChartEmpty" class="text-muted text-center py-4 small d-none">
                            <i class="fas fa-search me-1 fa-2x d-block mb-2 text-muted opacity-50"></i>
                            No sectors matching your search filter.
                        </div>
                    </div>

                    <!-- Sector Leaderboard Preview -->
                    <div class="sector-list-container" id="sectorListContainer" style="max-height: 210px;">
                        @forelse($sectorRanked as $index => $item)
                        <div class="sector-card-item" data-name="{{ strtolower($item['sector']) }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                                    <div class="sector-rank-badge {{ $index === 0 ? 'bg-warning text-dark' : ($index === 1 ? 'bg-secondary text-white' : ($index === 2 ? 'bg-primary-subtle text-primary' : 'bg-white text-muted shadow-xs')) }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <span class="fw-bold text-dark small text-truncate" title="{{ $item['sector'] }}">
                                        {{ $item['sector'] }}
                                    </span>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <div class="fw-bold text-dark small">
                                        {{ number_format($item['beneficiaries']) }} <span class="text-muted fw-normal">recipients</span>
                                    </div>
                                    <div class="text-success text-2xs fw-bold">
                                        {{ $item['formatted_amount'] }}
                                    </div>
                                </div>
                            </div>
                            <div class="sector-progress">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $item['percentage'] }}%; border-radius: 999px;" aria-valuenow="{{ $item['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        @empty
                        <div class="text-muted text-center py-3 small">No sector records found.</div>
                        @endforelse
                    </div>
                    <div id="sectorListEmpty" class="text-muted text-center py-3 small d-none">
                        <i class="fas fa-search me-1 d-block mb-1"></i> No matching sectors found.
                    </div>
                </div>
            </div>
        </div>

        <!-- Male vs Female Beneficiaries -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-xs rounded-4 bg-white h-100 d-flex flex-column mb-0 overflow-hidden">
                <div class="chart-card-header">
                    <div>
                        <h3 class="chart-card-title">
                            <i class="fas fa-venus-mars text-danger me-2"></i>Male vs. Female Beneficiaries
                        </h3>
                        <p class="chart-card-subtitle">Gender demographic breakdown of recipients</p>
                    </div>
                    <span class="badge bg-light text-secondary rounded-pill px-2.5 py-1 text-xs">
                        {{ number_format($totalBeneficiaries) }} Total
                    </span>
                </div>
                <div class="p-3 d-flex flex-column justify-content-center align-items-center flex-grow-1" style="min-height: 280px; position: relative;">
                    <div style="position: relative; width: 100%; height: 240px;">
                        <canvas id="genderDonutChart"></canvas>
                    </div>
                </div>
                <div class="p-3 border-top bg-light mt-auto">
                    <div class="gender-pill-box">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary" style="width: 12px; height: 12px;"></div>
                            <span class="fw-semibold text-dark small">Male</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark small">{{ number_format($maleCount) }}</span>
                            <span class="badge bg-primary-subtle text-primary rounded-pill text-xs">{{ $malePercentage }}%</span>
                        </div>
                    </div>
                    <div class="gender-pill-box mb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle" style="width: 12px; height: 12px; background-color: #E11D48;"></div>
                            <span class="fw-semibold text-dark small">Female</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark small">{{ number_format($femaleCount) }}</span>
                            <span class="badge bg-danger-subtle text-danger rounded-pill text-xs">{{ $femalePercentage }}%</span>
                        </div>
                    </div>
                    @if($otherGenderCount > 0)
                    <div class="gender-pill-box mt-2 mb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-secondary" style="width: 12px; height: 12px;"></div>
                            <span class="fw-semibold text-dark small">Other / Unspecified</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark small">{{ number_format($otherGenderCount) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Most Common Medical Concerns & Assistance Reasons -->
    <div class="card border rounded-3 bg-white mb-4 shadow-none">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Most Common Medical Concerns &amp; Reasons</h5>
                    <p class="text-muted small mb-0">Frequently reported medical conditions and assistance reasons among beneficiaries, ordered highest to lowest.</p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap no-print">
                    <!-- Live Search Filter -->
                    <input type="text" id="medicalSearchInput" class="form-control form-control-sm" placeholder="Search medical concern..." style="width: 200px;">

                    <!-- Metric Switcher (Cases vs Amount) -->
                    <div class="btn-group btn-group-sm" role="group" aria-label="Medical Metric">
                        <button type="button" class="btn btn-outline-secondary btn-medical-metric active" data-metric="cases">Cases</button>
                        <button type="button" class="btn btn-outline-secondary btn-medical-metric" data-metric="amount">Grants (₱)</button>
                    </div>

                    <!-- View Limits (Top 5 / Top 10 / Top 20 / All) -->
                    <div class="btn-group btn-group-sm" role="group" aria-label="Medical View Limit">
                        <button type="button" class="btn btn-outline-secondary btn-medical-limit" data-limit="5">Top 5</button>
                        <button type="button" class="btn btn-outline-secondary btn-medical-limit active" data-limit="10">Top 10</button>
                        <button type="button" class="btn btn-outline-secondary btn-medical-limit" data-limit="20">Top 20</button>
                        <button type="button" class="btn btn-outline-secondary btn-medical-limit" data-limit="all">All</button>
                    </div>

                    <span id="medicalActiveCountBadge" class="badge bg-light text-secondary border fw-normal py-1.5 px-2.5">
                        {{ count($medicalRanked) }} Concerns
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Chart Subtitle / Status -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Medical Concerns Distribution</span>
                <span id="medicalChartShowingText" class="text-muted small">Showing top 10 medical concerns</span>
            </div>

            <!-- Responsive Chart Viewport (Full width, clear focus, no nested gray boxes) -->
            <div class="chart-scroll-wrapper mb-4" id="medicalChartScrollContainer" style="max-height: 440px; overflow-y: auto;">
                <div class="medical-chart-inner" id="medicalChartInner" style="position: relative; width: 100%; min-height: 320px;">
                    <canvas id="medicalBarChart"></canvas>
                </div>
            </div>
            <div id="medicalChartEmpty" class="text-muted text-center py-5 small d-none border rounded bg-light">
                No medical concerns matching your search filter.
            </div>

            <!-- Summary Breakdown Table (Clean, practical government office tabular format) -->
            <div class="border-top pt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Summary Directory</span>
                    <span id="medicalListCountText" class="text-muted small">{{ count($medicalRanked) }} records</span>
                </div>
                <div class="table-responsive border rounded-2" style="max-height: 280px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0" id="medicalListContainer">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 60px;" class="text-center text-muted fw-semibold">#</th>
                                <th class="fw-semibold text-dark">Medical Concern / Reason</th>
                                <th class="text-end fw-semibold text-dark" style="width: 140px;">Beneficiaries</th>
                                <th class="text-end fw-semibold text-dark" style="width: 110px;">Share</th>
                                <th class="text-end fw-semibold text-dark" style="width: 170px;">Assistance Granted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($medicalRanked as $index => $item)
                            <tr class="medical-card-item" data-name="{{ strtolower($item['concern']) }}">
                                <td class="text-center text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-medium text-dark small">{{ $item['concern'] }}</td>
                                <td class="text-end small">{{ number_format($item['beneficiaries']) }} <span class="text-muted">cases</span></td>
                                <td class="text-end text-muted small">{{ $item['percentage'] }}%</td>
                                <td class="text-end fw-semibold text-dark small">{{ $item['formatted_amount'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-muted text-center py-3 small">No medical records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="medicalListEmpty" class="text-muted text-center py-3 small d-none border rounded mt-2">
                    No matching medical concerns found.
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('page-scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Pass Backend Aggregated Data to Chart Controller -->
<script>
    window.statisticsData = {
        totalBeneficiaries: {{ (int) $totalBeneficiaries }},
        barangayStats: @json(array_values($barangayStats)),
        genderLabels: @json(array_keys($genderBreakdown)),
        genderCounts: @json(array_values($genderBreakdown)),
        sectorRanked: @json($sectorRanked),
        medicalRanked: @json($medicalRanked)
    };
</script>

<!-- Step 2 Statistics Script -->
<script src="{{ asset('js/financialstep2-statistics.js') }}"></script>
@endsection
