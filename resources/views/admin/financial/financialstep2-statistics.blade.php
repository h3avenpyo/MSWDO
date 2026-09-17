@extends('layouts.financial')

@section('title', 'Step 2 Statistics & Analytics - MSWDO Admin')
@section('page-title', 'Step 2: Statistics & Analytics')

@section('page-styles')
<!-- Google Fonts: Public Sans -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<link href="{{ asset('css/financialstep2-statistics.css') }}?v={{ file_exists(public_path('css/financialstep2-statistics.css')) ? filemtime(public_path('css/financialstep2-statistics.css')) : time() }}" rel="stylesheet">
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

        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1" style="color: #1A237E;">
                    <i class="fas fa-chart-pie me-2"></i>Step 2: Statistics &amp; Analytics
                </h4>
                <p class="text-muted small mb-0">Real-time beneficiary distributions, demographic breakdown, and sector financial assistance reports.</p>
            </div>
            <div class="d-flex align-items-center gap-2 no-print">
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3.5 py-2 fw-semibold shadow-xs d-inline-flex align-items-center gap-2 btn-print-statistics" onclick="window.print()" title="Print official statistics report">
                    <i class="fas fa-print"></i>
                    <span>Print Statistics</span>
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
                    <div class="flex-grow-1 min-w-0 me-2">
                        <p class="text-muted small fw-bold mb-1 text-uppercase">Total Beneficiaries</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($totalBeneficiaries) }}</h4>
                        <div class="text-muted text-xs mt-1">
                            <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>{{ number_format($totalClaimed) }}</span> Claimed
                            @if($totalBeneficiaries > 0)
                            ({{ round(($totalClaimed / $totalBeneficiaries) * 100, 1) }}%)
                            @endif
                        </div>
                    </div>
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1 min-w-0 me-2">
                        <p class="text-muted small fw-bold mb-1 text-uppercase">Total Assistance Granted</p>
                        <h4 class="fw-bold mb-0 text-success">&#8369;{{ number_format($totalAmount, 2) }}</h4>
                        <div class="text-muted text-xs mt-1">
                            Claimed: <strong class="text-dark">&#8369;{{ number_format($totalClaimedAmount, 2) }}</strong>
                        </div>
                    </div>
                    <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="fas fa-hand-holding-usd fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1 min-w-0 me-2">
                        <p class="text-muted small fw-bold mb-1 text-uppercase">Top Barangay</p>
                        <h4 class="fw-bold mb-0 text-dark text-break" style="font-size: 1.25rem; line-height: 1.25;" title="{{ $topBarangay ?? 'N/A' }}">
                            {{ $topBarangay ?? 'N/A' }}
                        </h4>
                        <div class="text-muted text-xs mt-1">
                            <strong class="text-dark">{{ number_format($topBarangayCount) }}</strong> beneficiaries recorded
                        </div>
                    </div>
                    <div class="rounded-circle bg-info-subtle text-info p-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="fas fa-map-marker-alt fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1 min-w-0 me-2">
                        <p class="text-muted small fw-bold mb-1 text-uppercase">Top Sector</p>
                        <h4 class="fw-bold mb-0 text-warning text-break" style="font-size: 1.25rem; line-height: 1.25;" title="{{ $topSector ?? 'N/A' }}">
                            {{ $topSector ?? 'N/A' }}
                        </h4>
                        <div class="text-muted text-xs mt-1">
                            <strong class="text-dark">{{ number_format($topSectorCount) }}</strong> recipients
                        </div>
                    </div>
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
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
                    <div class="report-sub-title">Step 2: Financial Assistance, Sector Allocation &amp; Demographic Analytics Overview</div>
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
                    <td class="report-meta-cell" style="width: 25%;">
                        <div class="report-meta-title">Total Assistance Granted:</div>
                        <div class="report-meta-val" style="font-weight: 800; color: #166534;">&#8369;{{ number_format($totalAmount, 2) }}</div>
                    </td>
                    <td class="report-meta-cell" style="width: 25%;">
                        <div class="report-meta-title">Total Beneficiaries:</div>
                        <div class="report-meta-val" style="font-weight: 800; color: #1A237E;">{{ number_format($totalBeneficiaries) }} Recipients</div>
                    </td>
                    <td class="report-meta-cell text-end" style="width: 25%;">
                        <div class="report-meta-title">Reporting Agency:</div>
                        <div class="report-meta-val">MSWDO Silang (Step 2 - AICS)</div>
                    </td>
                </tr>
            </table>

            <!-- Executive Key Indicators Summary (4 Even Columns: 25% each) -->
            <div class="report-section section-summary mb-3">
                <table class="report-summary-box mb-0">
                    <tr>
                        <td class="report-summary-cell" style="width: 25%;">
                            <div class="report-summary-title">Total Beneficiaries</div>
                            <div class="report-summary-val">{{ number_format($totalBeneficiaries) }}</div>
                            <div class="report-summary-sub">
                                <span style="color: #166534; font-weight: 700;">{{ number_format($totalClaimed) }} Claimed</span>
                                @if($totalBeneficiaries > 0)
                                ({{ round(($totalClaimed / $totalBeneficiaries) * 100, 1) }}%)
                                @endif
                            </div>
                        </td>
                        <td class="report-summary-cell" style="width: 25%;">
                            <div class="report-summary-title">Total Assistance Granted</div>
                            <div class="report-summary-val">&#8369;{{ number_format($totalAmount, 2) }}</div>
                            <div class="report-summary-sub">Claimed: &#8369;{{ number_format($totalClaimedAmount, 2) }}</div>
                        </td>
                        <td class="report-summary-cell" style="width: 25%;">
                            <div class="report-summary-title">Top Barangay</div>
                            <div class="report-summary-val" style="font-size: 11pt; line-height: 1.2; word-break: break-word;" title="{{ $topBarangay ?? 'N/A' }}">{{ $topBarangay ?? 'N/A' }}</div>
                            <div class="report-summary-sub">{{ number_format($topBarangayCount) }} recorded recipients</div>
                        </td>
                        <td class="report-summary-cell" style="width: 25%;">
                            <div class="report-summary-title">Top Sector</div>
                            <div class="report-summary-val" style="font-size: 11pt; line-height: 1.2; word-break: break-word;" title="{{ $topSector ?? 'N/A' }}">{{ $topSector ?? 'N/A' }}</div>
                            <div class="report-summary-sub">{{ number_format($topSectorCount) }} recipients</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Section 1: Geographic Distribution by Barangay (Top Barangays) -->
        <div class="report-section section-barangay mb-3">
            <div class="report-section-heading">1. Geographic Distribution of Beneficiaries (Per Barangay)</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 8%; text-align: center;">Rank</th>
                        <th style="width: 38%;">Barangay Name</th>
                        <th style="width: 18%; text-align: right;">Beneficiaries</th>
                        <th style="width: 16%; text-align: right;">Claimed</th>
                        <th style="width: 20%; text-align: right;">Assistance Granted</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $bRank = 1;
                        $topBarangaysLimit = 12;
                        $allBarangaysCount = count($barangayStats);
                        $topBarangays = array_slice($barangayStats, 0, $topBarangaysLimit, true);
                        $otherBarangays = array_slice($barangayStats, $topBarangaysLimit, null, true);
                        $otherBarangaysCount = 0;
                        $otherBarangaysBeneficiaries = 0;
                        $otherBarangaysClaimed = 0;
                        $otherBarangaysAmount = 0.0;
                        foreach ($otherBarangays as $ob) {
                            $otherBarangaysCount++;
                            $otherBarangaysBeneficiaries += $ob['beneficiaries'];
                            $otherBarangaysClaimed += $ob['claimed'];
                            $otherBarangaysAmount += $ob['amount'];
                        }
                    @endphp
                    @forelse($topBarangays as $bName => $bData)
                        <tr>
                            <td style="text-align: center;">{{ $bRank++ }}</td>
                            <td style="font-weight: 600;">{{ $bData['name'] }}</td>
                            <td style="text-align: right; font-weight: 600;">{{ number_format($bData['beneficiaries']) }}</td>
                            <td style="text-align: right; color: #166534; font-weight: 600;">{{ number_format($bData['claimed']) }}</td>
                            <td style="text-align: right; font-weight: 600;">&#8369;{{ number_format($bData['amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748B;">No barangay records found.</td>
                        </tr>
                    @endforelse

                    @if($otherBarangaysCount > 0)
                        <tr>
                            <td style="text-align: center; color: #64748B;">—</td>
                            <td style="font-weight: 600; color: #64748B;">Other Barangays ({{ $otherBarangaysCount }} remaining)</td>
                            <td style="text-align: right; font-weight: 600; color: #64748B;">{{ number_format($otherBarangaysBeneficiaries) }}</td>
                            <td style="text-align: right; font-weight: 600; color: #64748B;">{{ number_format($otherBarangaysClaimed) }}</td>
                            <td style="text-align: right; font-weight: 600; color: #64748B;">&#8369;{{ number_format($otherBarangaysAmount, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="report-table-total-row">
                        <td colspan="2" style="font-weight: 700;">Total Recorded ({{ $allBarangaysCount }} Barangays)</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($totalBeneficiaries) }}</td>
                        <td style="text-align: right; font-weight: 700; color: #166534;">{{ number_format($totalClaimed) }}</td>
                        <td style="text-align: right; font-weight: 700;">&#8369;{{ number_format($totalAmount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Section 2: Beneficiaries Demographics by Sex -->
        <div class="report-section section-gender mb-3">
            <div class="report-section-heading">2. Beneficiaries Demographic Breakdown by Sex</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 10%; text-align: center;">No.</th>
                        <th style="width: 46%;">Sex / Demographic Category</th>
                        <th style="width: 22%; text-align: right;">Beneficiaries Recorded</th>
                        <th style="width: 22%; text-align: right;">Percentage Share</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $gIndex = 1; 
                        $gSum = array_sum($genderBreakdown); 
                    @endphp
                    @forelse($genderBreakdown as $genderLabel => $gCount)
                        @php
                            $gShare = $totalBeneficiaries > 0 ? round(($gCount / $totalBeneficiaries) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ $gIndex++ }}</td>
                            <td style="font-weight: 600;">{{ $genderLabel }}</td>
                            <td style="text-align: right; font-weight: 600;">{{ number_format($gCount) }}</td>
                            <td style="text-align: right; font-weight: 600;">{{ $gShare }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748B;">No gender data recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="report-table-total-row">
                        <td colspan="2" style="font-weight: 700;">Total Recorded</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($gSum) }}</td>
                        <td style="text-align: right; font-weight: 700;">100%</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Demographic Distribution Summary Strip -->
            <div class="d-flex justify-content-between align-items-center p-2 mt-2 bg-light border rounded" style="font-size: 8pt;">
                <div>
                    <strong>Demographic Summary:</strong> 
                    Male: <span class="fw-bold">{{ number_format($maleCount) }}</span> ({{ $malePercentage }}%) &bull; 
                    Female: <span class="fw-bold">{{ number_format($femaleCount) }}</span> ({{ $femalePercentage }}%)
                    @if($otherGenderCount > 0)
                    &bull; Other / Unspecified: <span class="fw-bold">{{ number_format($otherGenderCount) }}</span>
                    @endif
                </div>
                <div>
                    <strong>Primary Beneficiary Group:</strong> 
                    <span class="fw-bold text-dark">{{ $femaleCount >= $maleCount ? 'Female Beneficiaries' : 'Male Beneficiaries' }}</span>
                </div>
            </div>
        </div>

        <!-- Page Break for Part II (A4 Sheet 2) -->
        <div class="report-page-break"></div>

        <!-- Page 2 Header -->
        <div class="report-page-header-mini mb-3">
            <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary">
                <div class="small fw-bold text-uppercase" style="font-size: 8pt; color: #1E293B;">
                    MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE &bull; MUNICIPALITY OF SILANG, CAVITE
                </div>
                <div class="small text-muted" style="font-size: 7.5pt;">
                    STEP 2: FINANCIAL ASSISTANCE &amp; ANALYTICS &bull; STATISTICAL REPORT (PAGE 2)
                </div>
            </div>
        </div>

        <!-- Section 3: Financial Assistance by Sector (Ranked Distribution) -->
        <div class="report-section section-sectors mb-3">
            <div class="report-section-heading">3. Financial Assistance by Sector (Ranked Distribution)</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 8%; text-align: center;">Rank</th>
                        <th style="width: 42%;">Target Sector / Client Category</th>
                        <th style="width: 18%; text-align: right;">Beneficiaries</th>
                        <th style="width: 14%; text-align: right;">Share</th>
                        <th style="width: 18%; text-align: right;">Assistance Granted</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sRank = 1;
                        $sectorLimit = 10;
                        $allSectorsCount = count($sectorRanked);
                        $topSectors = array_slice($sectorRanked, 0, $sectorLimit);
                        $otherSectors = array_slice($sectorRanked, $sectorLimit);
                        $otherSectorsCount = 0;
                        $otherSectorsBeneficiaries = 0;
                        $otherSectorsAmount = 0.0;
                        foreach ($otherSectors as $os) {
                            $otherSectorsCount++;
                            $otherSectorsBeneficiaries += $os['beneficiaries'];
                            $otherSectorsAmount += $os['amount'];
                        }
                        $totalSectorBeneficiaries = array_sum(array_column($sectorRanked, 'beneficiaries'));
                        $totalSectorAmount = array_sum(array_column($sectorRanked, 'amount'));
                    @endphp
                    @forelse($topSectors as $item)
                        <tr>
                            <td style="text-align: center;">{{ $sRank++ }}</td>
                            <td style="font-weight: 600;">{{ $item['sector'] }}</td>
                            <td style="text-align: right; font-weight: 600;">{{ number_format($item['beneficiaries']) }}</td>
                            <td style="text-align: right; color: #475569;">{{ $item['percentage'] }}%</td>
                            <td style="text-align: right; font-weight: 600;">{{ $item['formatted_amount'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748B;">No sector records found.</td>
                        </tr>
                    @endforelse

                    @if($otherSectorsCount > 0)
                        <tr>
                            <td style="text-align: center; color: #64748B;">—</td>
                            <td style="font-weight: 600; color: #64748B;">Other Sectors ({{ $otherSectorsCount }} remaining)</td>
                            <td style="text-align: right; font-weight: 600; color: #64748B;">{{ number_format($otherSectorsBeneficiaries) }}</td>
                            <td style="text-align: right; color: #64748B;">
                                {{ $totalBeneficiaries > 0 ? round(($otherSectorsBeneficiaries / $totalBeneficiaries) * 100, 1) : 0 }}%
                            </td>
                            <td style="text-align: right; font-weight: 600; color: #64748B;">&#8369;{{ number_format($otherSectorsAmount, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="report-table-total-row">
                        <td colspan="2" style="font-weight: 700;">Total Evaluated ({{ $allSectorsCount }} Sectors)</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($totalSectorBeneficiaries) }}</td>
                        <td style="text-align: right; font-weight: 700;">—</td>
                        <td style="text-align: right; font-weight: 700;">&#8369;{{ number_format($totalSectorAmount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Section 4: Most Common Medical Concerns & Assistance Reasons -->
        <div class="report-section section-medical mb-3">
            <div class="report-section-heading">4. Most Common Medical Concerns &amp; Assistance Reasons</div>
            <p class="report-table-subtext mb-2">Frequently reported medical conditions, diagnostic requests, and emergency assistance reasons evaluated for Step 2 assistance.</p>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 8%; text-align: center;">Rank</th>
                        <th style="width: 42%;">Medical Concern / Assistance Reason</th>
                        <th style="width: 18%; text-align: right;">Beneficiaries</th>
                        <th style="width: 14%; text-align: right;">Share</th>
                        <th style="width: 18%; text-align: right;">Assistance Granted</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $mRank = 1;
                        $medicalLimit = 10;
                        $allMedCount = count($medicalRanked);
                        $topMedical = array_slice($medicalRanked, 0, $medicalLimit);
                        $otherMedical = array_slice($medicalRanked, $medicalLimit);
                        $otherMedCount = 0;
                        $otherMedBeneficiaries = 0;
                        $otherMedAmount = 0.0;
                        foreach ($otherMedical as $om) {
                            $otherMedCount++;
                            $otherMedBeneficiaries += $om['beneficiaries'];
                            $otherMedAmount += $om['amount'];
                        }
                        $totalMedBeneficiaries = array_sum(array_column($medicalRanked, 'beneficiaries'));
                        $totalMedAmount = array_sum(array_column($medicalRanked, 'amount'));
                    @endphp
                    @forelse($topMedical as $item)
                        <tr>
                            <td style="text-align: center;">{{ $mRank++ }}</td>
                            <td style="font-weight: 600;">{{ $item['concern'] }}</td>
                            <td style="text-align: right; font-weight: 600;">{{ number_format($item['beneficiaries']) }}</td>
                            <td style="text-align: right; color: #475569;">{{ $item['percentage'] }}%</td>
                            <td style="text-align: right; font-weight: 600;">{{ $item['formatted_amount'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748B;">No medical records found.</td>
                        </tr>
                    @endforelse

                    @if($otherMedCount > 0)
                        <tr>
                            <td style="text-align: center; color: #64748B;">—</td>
                            <td style="font-weight: 600; color: #64748B;">Other Concerns ({{ $otherMedCount }} remaining)</td>
                            <td style="text-align: right; font-weight: 600; color: #64748B;">{{ number_format($otherMedBeneficiaries) }}</td>
                            <td style="text-align: right; color: #64748B;">
                                {{ $totalBeneficiaries > 0 ? round(($otherMedBeneficiaries / $totalBeneficiaries) * 100, 1) : 0 }}%
                            </td>
                            <td style="text-align: right; font-weight: 600; color: #64748B;">&#8369;{{ number_format($otherMedAmount, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="report-table-total-row">
                        <td colspan="2" style="font-weight: 700;">Total Evaluated ({{ $allMedCount }} Concerns)</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($totalMedBeneficiaries) }}</td>
                        <td style="text-align: right; font-weight: 700;">—</td>
                        <td style="text-align: right; font-weight: 700;">&#8369;{{ number_format($totalMedAmount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Section 5: Administrative Certification & Signatures -->
        <div class="report-section section-signatures">
            <div class="report-notes-box mb-3">
                <div class="fw-bold text-uppercase mb-1" style="font-size: 7.5pt; color: #1E293B;">Administrative Notes &amp; Data Verification</div>
                <p class="mb-0" style="font-size: 7pt; color: #475569; line-height: 1.4;">
                    This document represents the consolidated official electronic record extracted from the Municipal Social Welfare and Development Office (MSWDO) Financial Assistance &amp; Step 2 Payroll Database under the Assistance to Individuals in Crisis Situations (AICS) Program. All statistical and disbursement records reflect validated intakes and approved financial assistance grants.
                </p>
            </div>

            <!-- Official Signatures Block -->
            <div class="report-signatures-section">
                <div class="row">
                    <div class="col-6 text-center">
                        <div class="signature-space"></div>
                        <div class="signature-line mx-auto"></div>
                        <div class="fw-bold text-dark mt-1" style="font-size: 9pt;">{{ session('financial_step2_authorized_user') ?? session('admin_user_name') ?? 'MSWDO Staff / Statistician' }}</div>
                        <div class="text-muted" style="font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.03em;">Prepared by / Reporting Officer</div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="signature-space"></div>
                        <div class="signature-line mx-auto"></div>
                        <div class="fw-bold text-dark mt-1" style="font-size: 9pt;">Municipal Social Welfare &amp; Development Officer</div>
                        <div class="text-muted" style="font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.03em;">Noted &amp; Approved by</div>
                    </div>
                </div>
            </div>

            <!-- Official Document Footer -->
            <div class="report-official-footer d-flex justify-content-between align-items-center mt-3 pt-2 border-top text-muted" style="font-size: 7pt;">
                <span>MSWDO Silang, Cavite &bull; Financial Assistance Step 2 Module &bull; Confidential Official Report</span>
                <span>Document Printed on {{ date('F d, Y \a\t h:i A') }}</span>
            </div>
        </div>

    </div><!-- /.print-report-document -->

</div><!-- /.container-fluid -->
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
