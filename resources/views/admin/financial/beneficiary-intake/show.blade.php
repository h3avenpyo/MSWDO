@extends('layouts.financial')

@section('title', 'General Intake Sheet - ' . $intake->control_number)
@section('page-title', 'General Intake Sheet Overview')

@section('page-styles')
<link href="{{ asset('css/beneficiary-intake/show.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid no-print">

    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1A237E;">GENERAL INTAKE SHEET: {{ $intake->control_number }}</h4>
            <p class="text-muted small mb-0">Processed on {{ $intake->date_processed ?
                $intake->date_processed->format('F d, Y') : 'N/A' }} • Client Status: <strong>{{ $intake->client_type ??
                    'New' }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-print me-1"></i> Print Hardcopy Form
            </button>
            <a href="{{ route('admin.beneficiary-intake.edit', $intake) }}"
                class="btn btn-primary btn-sm rounded-pill px-3" style="background: #1A237E; border: none;">
                <i class="fas fa-edit me-1"></i> Edit Record
            </a>
            <a href="{{ route('admin.beneficiary-intake.index') }}"
                class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Control Header Card -->
    <div class="detail-card">
        <div class="detail-header-bar d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill mb-1">MSWDO Silang Intake
                    Record</span>
                <h5 class="fw-bold mb-0 text-white">{{ $intake->beneficiary_full_name }}</h5>
            </div>
            <div class="text-end">
                <div class="text-white-50 small">Control No.</div>
                <div class="fs-5 fw-bold text-white">{{ $intake->control_number }}</div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-2">
                    <div class="info-label">Client Type</div>
                    <div class="info-value"><span class="badge bg-primary text-white px-3 py-1 rounded-pill">{{
                            $intake->client_type ?? 'New' }}</span></div>
                </div>
                <div class="col-md-3">
                    <div class="info-label">Date Processed</div>
                    <div class="info-value">{{ $intake->date_processed ? $intake->date_processed->format('F d, Y') :
                        'N/A' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="info-label">Time Start / End</div>
                    <div class="info-value">{{ $intake->time_start ?? '--:--' }} - {{ $intake->time_end ?? '--:--' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Encoder</div>
                    <div class="info-value">{{ $intake->encoderUser?->name ?? session('admin_user_name') ?? 'System
                        Admin' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section I: Beneficiary Info Card -->
    <div class="detail-card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4 pb-2 border-bottom text-dark">
                <i class="fas fa-user me-2 text-primary"></i> IMPORMASYON NG BENEPISYARYO <span
                    class="text-muted fs-6">(Beneficiary Info)</span>
            </h5>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="info-label">Buong Pangalan (Full Name)</div>
                    <div class="info-value">{{ $intake->beneficiary_full_name }}</div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Mobile Number</div>
                    <div class="info-value">{{ $intake->beneficiary_contact_number ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Kapanganakan / Edad</div>
                    <div class="info-value">
                        {{ $intake->beneficiary_birthday ? $intake->beneficiary_birthday->format('M d, Y') : 'N/A' }}
                        @if($intake->beneficiary_age !== null)
                        ({{ $intake->beneficiary_age }} yrs old)
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="info-label">House No. / Street / Barangay / City</div>
                    <div class="info-value">{{ $intake->beneficiary_address_formatted }}</div>
                </div>
                <div class="col-md-3">
                    <div class="info-label">Kasarian (Gender) &amp; Civil Status</div>
                    <div class="info-value">{{ $intake->beneficiary_sex ?? 'N/A' }} • {{
                        $intake->beneficiary_civil_status ?? 'N/A' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="info-label">Trabaho &amp; Buwanang Kita</div>
                    <div class="info-value">
                        {{ $intake->beneficiary_occupation ?? 'None' }}
                        @if($intake->beneficiary_monthly_salary !== null)
                        (₱{{ number_format($intake->beneficiary_monthly_salary, 2) }})
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section II: Representative Info Card (if present) -->
    @if($intake->has_representative)
    <div class="detail-card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4 pb-2 border-bottom text-dark">
                <i class="fas fa-user-friends me-2 text-primary"></i> IMPORMASYON NG KINATAWAN <span
                    class="text-muted fs-6">(Representative Info)</span>
            </h5>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="info-label">Representative Name</div>
                    <div class="info-value">{{ $intake->representative_full_name }}</div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Relasyon sa Benepisyaryo</div>
                    <div class="info-value"><span
                            class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill">{{
                            $intake->rep_relationship ?? 'N/A' }}</span></div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Mobile Number</div>
                    <div class="info-value">{{ $intake->rep_contact_number ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="info-label">Address</div>
                    <div class="info-value">{{ $intake->representative_address_formatted }}</div>
                </div>
                <div class="col-md-3">
                    <div class="info-label">Kapanganakan / Edad</div>
                    <div class="info-value">
                        {{ $intake->rep_birthday ? $intake->rep_birthday->format('M d, Y') : 'N/A' }}
                        @if($intake->rep_age !== null)
                        ({{ $intake->rep_age }} yrs old)
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-label">Trabaho &amp; Buwanang Kita</div>
                    <div class="info-value">
                        {{ $intake->rep_occupation ?? 'N/A' }}
                        @if($intake->rep_monthly_salary !== null)
                        (₱{{ number_format($intake->rep_monthly_salary, 2) }})
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Section III: DSWD Category & Assessment -->
    <div class="detail-card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4 pb-2 border-bottom text-dark">
                <i class="fas fa-list-check me-2 text-primary"></i> DSWD CATEGORY &amp; SOCIAL WORKER ASSESSMENT
            </h5>

            <div class="row g-4 mb-4">
                <div class="col-md-5">
                    <div class="info-label">Beneficiary Sub-Categories</div>
                    <div class="info-value mt-1">
                        @if(!empty($intake->beneficiary_categories) && is_array($intake->beneficiary_categories))
                        @foreach($intake->beneficiary_categories as $catItem)
                        <span class="badge bg-light text-dark border me-1 mb-1 px-2 py-1">{{ $catItem }}</span>
                        @endforeach
                        @else
                        <span class="badge bg-light text-dark border">{{ $intake->display_category }}</span>
                        @endif
                        @if($intake->beneficiary_category_other)
                        <div class="small text-muted mt-1">Other: {{ $intake->beneficiary_category_other }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="info-label">Social Worker's Assessment</div>
                    <div class="p-3 bg-light rounded-3 border mt-1 font-monospace text-dark"
                        style="white-space: pre-wrap; font-size: 0.9rem;">
                        {{ $intake->social_worker_assessment ?? 'No assessment notes encoded.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section IV: Family Composition -->
    <div class="detail-card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3 pb-2 border-bottom text-dark">
                <i class="fas fa-users me-2 text-primary"></i> KOMPOSISYON NG PAMILYA <span
                    class="text-muted fs-6">(Family Composition)</span>
            </h5>

            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Buong Pangalan (Name)</th>
                            <th>Relasyon</th>
                            <th>Edad (Age)</th>
                            <th>Trabaho (Occupation)</th>
                            <th>Buwanang Kita (Monthly Salary)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($intake->family_composition ?? [] as $fam)
                        <tr>
                            <td class="fw-semibold">{{ $fam['name'] ?? 'N/A' }}</td>
                            <td>{{ $fam['relationship'] ?? 'N/A' }}</td>
                            <td>{{ $fam['age'] ?? 'N/A' }}</td>
                            <td>{{ $fam['occupation'] ?? 'N/A' }}</td>
                            <td>{{ !empty($fam['salary']) ? '₱' . number_format($fam['salary'], 2) : 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No family members listed.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section V: Assistance Purpose & Interview Details -->
    <div class="detail-card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3 pb-2 border-bottom text-dark">
                <i class="fas fa-hand-holding-heart me-2 text-primary"></i> ASSISTANCE PURPOSE / MEDICAL CONDITION &amp;
                INTERVIEW DETAILS
            </h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-label">Assistance Purpose / Medical Condition</div>
                    <div class="info-value">{{ $intake->display_assistance_purpose }}</div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Interviewed by</div>
                    <div class="info-value">{{ $intake->interviewed_by ?? $intake->encoderUser?->name ?? 'MSWD Staff' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- PRINTABLE CONTAINER FORMATTED EXACTLY LIKE PHYSICAL GENERAL INTAKE SHEET (GIS) HARDCOPY FORM -->
@php
    $silangLogo = '';
    $dswdLogo = '';
    if (file_exists(public_path('images/silang.png'))) {
        $silangLogo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/silang.png')));
    } elseif (file_exists(public_path('images/silangseal.png'))) {
        $silangLogo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/silangseal.png')));
    }
    if (file_exists(public_path('images/dswd.png'))) {
        $dswdLogo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/dswd.png')));
    } elseif (file_exists(public_path('images/dswdlogo.png'))) {
        $dswdLogo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/dswdlogo.png')));
    }

    $dateProcessed = $intake->date_processed ?? now();
    $dateMonth = $dateProcessed ? $dateProcessed->format('m') : 'MM';
    $dateDay = $dateProcessed ? $dateProcessed->format('d') : 'DD';
    $dateYear = $dateProcessed ? $dateProcessed->format('Y') : '2026';

    $cats = $intake->beneficiary_categories ?? [];
    if (!empty($intake->beneficiary_category) && !in_array($intake->beneficiary_category, $cats)) {
        $cats[] = $intake->beneficiary_category;
    }
    $otherCategory = $intake->beneficiary_category_other ?? '';
@endphp

<div class="print-container">
    
    <!-- 1. Header Area: Logos, Title, Time Start, Date -->
    <div class="gis-header-container">
        <!-- Top Row: Logos, Title, Date -->
        <div class="gis-header-row">
            <div class="gis-logos">
                @if($silangLogo)
                    <img src="{{ $silangLogo }}" class="gis-logo-img" alt="Silang Seal">
                @endif
                @if($dswdLogo)
                    <img src="{{ $dswdLogo }}" class="gis-logo-img" alt="DSWD Logo">
                @endif
            </div>
            <div class="gis-title-box">
                <h1 class="gis-title">GENERAL INTAKE SHEET</h1>
            </div>
            <div class="gis-date-box-wrapper">
                <span class="gis-meta-label">Date:</span>
                <table class="gis-date-table">
                    <tr>
                        <td class="gis-date-cell-m {{ $dateMonth === 'MM' ? 'gis-date-placeholder' : '' }}">{{ $dateMonth }}</td>
                        <td class="gis-date-cell-d {{ $dateDay === 'DD' ? 'gis-date-placeholder' : '' }}">{{ $dateDay }}</td>
                        <td class="gis-date-cell-y">{{ $dateYear }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Middle Row: Time Start (positioned below title, ending before Date) -->
        <div class="gis-time-start-row">
            <div class="gis-time-start-group">
                <span class="gis-meta-label">Time Start:</span>
                <span class="gis-meta-box gis-box-time-start">{{ $intake->time_start ?? '' }}</span>
            </div>
        </div>
    </div>

    <!-- Control Number & Client Status -->
    <div class="gis-ctrl-row">
        <span class="gis-ctrl-label">Control Number</span>
        <span class="gis-ctrl-line">{{ $intake->control_number }}</span>
        <div class="gis-status-group">
            <span class="gis-checkbox-item">
                <span class="gis-checkbox">{{ ($intake->client_type !== 'Returning') ? '✓' : '' }}</span>
                <span>New</span>
            </span>
            <span class="gis-checkbox-item">
                <span class="gis-checkbox">{{ ($intake->client_type === 'Returning') ? '✓' : '' }}</span>
                <span>Returning</span>
            </span>
        </div>
    </div>

    <!-- 2. Section I: Beneficiary Information -->
    <div class="gis-banner gis-banner-black">
        IMPORMASYON NG BENEPISYARYO (Beneficiary's Identifying Information)
    </div>
    
    <!-- Row 1: Names -->
    <table class="gis-line-table">
        <tr class="gis-val-row">
            <td style="width: 28%;" class="gis-val-cell">{!! $intake->beneficiary_last_name ? e($intake->beneficiary_last_name) : '&nbsp;' !!}</td>
            <td style="width: 36%;" class="gis-val-cell">{!! $intake->beneficiary_first_name ? e($intake->beneficiary_first_name) : '&nbsp;' !!}</td>
            <td style="width: 26%;" class="gis-val-cell">{!! $intake->beneficiary_middle_name ? e($intake->beneficiary_middle_name) : '&nbsp;' !!}</td>
            <td style="width: 10%;" class="gis-val-cell">{!! $intake->beneficiary_extension_name ? e($intake->beneficiary_extension_name) : '&nbsp;' !!}</td>
        </tr>
        <tr class="gis-lbl-row">
            <td class="gis-lbl-cell">Apelyido <span class="gis-sublbl">(Last Name)</span></td>
            <td class="gis-lbl-cell">Unang Pangalan <span class="gis-sublbl">(First Name)</span></td>
            <td class="gis-lbl-cell">Gitnang Pangalan <span class="gis-sublbl">(Middle Name)</span></td>
            <td class="gis-lbl-cell">Ext. <span class="gis-sublbl">(Sr., Jr., III)</span></td>
        </tr>
    </table>

    <!-- Row 2: Address -->
    <table class="gis-line-table">
        <tr class="gis-val-row">
            <td style="width: 26%;" class="gis-val-cell">{!! $intake->beneficiary_street_address ? e($intake->beneficiary_street_address) : '&nbsp;' !!}</td>
            <td style="width: 22%;" class="gis-val-cell">{!! $intake->beneficiary_barangay ? e($intake->beneficiary_barangay) : '&nbsp;' !!}</td>
            <td style="width: 22%;" class="gis-val-cell">{!! $intake->beneficiary_city ? e($intake->beneficiary_city) : 'SILANG' !!}</td>
            <td style="width: 18%;" class="gis-val-cell">{!! $intake->beneficiary_province ? e($intake->beneficiary_province) : 'CAVITE' !!}</td>
            <td style="width: 12%;" class="gis-val-cell">{!! $intake->beneficiary_region ? e($intake->beneficiary_region) : 'IV-A' !!}</td>
        </tr>
        <tr class="gis-lbl-row">
            <td class="gis-lbl-cell">House No./Street/Purok <span class="gis-sublbl">(Ex. 123 San)</span></td>
            <td class="gis-lbl-cell">Barangay <span class="gis-sublbl">(Ex. Batasan)</span></td>
            <td class="gis-lbl-cell">City/Municipality <span class="gis-sublbl">(Ex. Quezon City)</span></td>
            <td class="gis-lbl-cell">Province/District <span class="gis-sublbl">(Ex. Dist II)</span></td>
            <td class="gis-lbl-cell">Region <span class="gis-sublbl">(Ex. NCR)</span></td>
        </tr>
    </table>

    <!-- Row 3: Demographics -->
    <table class="gis-line-table">
        <tr class="gis-val-row">
            <td style="width: 19%;" class="gis-val-cell">{!! $intake->beneficiary_contact_number ? e($intake->beneficiary_contact_number) : '&nbsp;' !!}</td>
            <td style="width: 13%;" class="gis-val-cell">{!! $intake->beneficiary_birthday ? e($intake->beneficiary_birthday->format('m/d/Y')) : '&nbsp;' !!}</td>
            <td style="width: 7%;" class="gis-val-cell">{!! $intake->beneficiary_age !== null ? e($intake->beneficiary_age) : '&nbsp;' !!}</td>
            <td style="width: 8%;" class="gis-val-cell">{!! $intake->beneficiary_sex ? e(strtoupper(substr($intake->beneficiary_sex, 0, 1))) : '&nbsp;' !!}</td>
            <td style="width: 15%;" class="gis-val-cell">{!! $intake->beneficiary_civil_status ? e(strtoupper(substr($intake->beneficiary_civil_status, 0, 1))) : '&nbsp;' !!}</td>
            <td style="width: 18%;" class="gis-val-cell">{!! $intake->beneficiary_occupation ? e($intake->beneficiary_occupation) : 'N/A' !!}</td>
            <td style="width: 20%;" class="gis-val-cell">{!! $intake->beneficiary_monthly_salary ? e(number_format($intake->beneficiary_monthly_salary, 2)) : 'N/A' !!}</td>
        </tr>
        <tr class="gis-lbl-row">
            <td class="gis-lbl-cell">Numero ng Telepono <span class="gis-sublbl">(Mobile No.)</span></td>
            <td class="gis-lbl-cell">Kapanganakan <span class="gis-sublbl">(Birthdate)</span></td>
            <td class="gis-lbl-cell">Edad <span class="gis-sublbl">(Age)</span></td>
            <td class="gis-lbl-cell">Kasarian <span class="gis-sublbl">(Gender)</span></td>
            <td class="gis-lbl-cell">Civil Status <span class="gis-sublbl">(Katayuang Sibil)</span></td>
            <td class="gis-lbl-cell">Trabaho <span class="gis-sublbl">(Occupation)</span></td>
            <td class="gis-lbl-cell">Buwanang Kita <span class="gis-sublbl">(Monthly Salary)</span></td>
        </tr>
    </table>

    <!-- 3. Section II: Representative Information -->
    <div class="gis-banner gis-banner-black">
        IMPORMASYON NG KINATAWAN (Representative's Identifying Information)
    </div>
    
    <!-- Row 1: Names -->
    <table class="gis-line-table">
        <tr class="gis-val-row">
            <td style="width: 28%;" class="gis-val-cell">{!! $intake->rep_last_name ? e($intake->rep_last_name) : '&nbsp;' !!}</td>
            <td style="width: 36%;" class="gis-val-cell">{!! $intake->rep_first_name ? e($intake->rep_first_name) : '&nbsp;' !!}</td>
            <td style="width: 26%;" class="gis-val-cell">{!! $intake->rep_middle_name ? e($intake->rep_middle_name) : '&nbsp;' !!}</td>
            <td style="width: 10%;" class="gis-val-cell">{!! $intake->rep_extension_name ? e($intake->rep_extension_name) : '&nbsp;' !!}</td>
        </tr>
        <tr class="gis-lbl-row">
            <td class="gis-lbl-cell">Apelyido <span class="gis-sublbl">(Last Name)</span></td>
            <td class="gis-lbl-cell">Unang Pangalan <span class="gis-sublbl">(First Name)</span></td>
            <td class="gis-lbl-cell">Gitnang Pangalan <span class="gis-sublbl">(Middle Name)</span></td>
            <td class="gis-lbl-cell">Ext. <span class="gis-sublbl">(Sr., Jr., III)</span></td>
        </tr>
    </table>

    <!-- Row 2: Address -->
    <table class="gis-line-table">
        <tr class="gis-val-row">
            <td style="width: 26%;" class="gis-val-cell">{!! $intake->rep_street_address ? e($intake->rep_street_address) : '&nbsp;' !!}</td>
            <td style="width: 22%;" class="gis-val-cell">{!! $intake->rep_barangay ? e($intake->rep_barangay) : '&nbsp;' !!}</td>
            <td style="width: 22%;" class="gis-val-cell">{!! $intake->has_representative ? e($intake->rep_city ?? 'SILANG') : '&nbsp;' !!}</td>
            <td style="width: 18%;" class="gis-val-cell">{!! $intake->has_representative ? e($intake->rep_province ?? 'CAVITE') : '&nbsp;' !!}</td>
            <td style="width: 12%;" class="gis-val-cell">{!! $intake->has_representative ? e($intake->rep_region ?? 'IV-A') : '&nbsp;' !!}</td>
        </tr>
        <tr class="gis-lbl-row">
            <td class="gis-lbl-cell">House No./Street/Purok <span class="gis-sublbl">(Ex. 123 San)</span></td>
            <td class="gis-lbl-cell">Barangay <span class="gis-sublbl">(Ex. Batasan)</span></td>
            <td class="gis-lbl-cell">City/Municipality <span class="gis-sublbl">(Ex. Quezon City)</span></td>
            <td class="gis-lbl-cell">Province/District <span class="gis-sublbl">(Ex. Dist II)</span></td>
            <td class="gis-lbl-cell">Region <span class="gis-sublbl">(Ex. NCR)</span></td>
        </tr>
    </table>

    <!-- Row 3: Demographics -->
    <table class="gis-line-table">
        <tr class="gis-val-row">
            <td style="width: 19%;" class="gis-val-cell">{!! $intake->rep_contact_number ? e($intake->rep_contact_number) : '&nbsp;' !!}</td>
            <td style="width: 13%;" class="gis-val-cell">{!! $intake->rep_birthday ? e($intake->rep_birthday->format('m/d/Y')) : '&nbsp;' !!}</td>
            <td style="width: 7%;" class="gis-val-cell">{!! $intake->rep_age !== null ? e($intake->rep_age) : '&nbsp;' !!}</td>
            <td style="width: 8%;" class="gis-val-cell">{!! $intake->rep_sex ? e(strtoupper(substr($intake->rep_sex, 0, 1))) : '&nbsp;' !!}</td>
            <td style="width: 15%;" class="gis-val-cell">{!! $intake->rep_civil_status ? e(strtoupper(substr($intake->rep_civil_status, 0, 1))) : '&nbsp;' !!}</td>
            <td style="width: 18%;" class="gis-val-cell">{!! $intake->rep_occupation ? e($intake->rep_occupation) : '&nbsp;' !!}</td>
            <td style="width: 20%;" class="gis-val-cell">{!! $intake->rep_monthly_salary ? e(number_format($intake->rep_monthly_salary, 2) . '/monthly') : '&nbsp;' !!}</td>
        </tr>
        <tr class="gis-lbl-row">
            <td class="gis-lbl-cell">Numero ng Telepono <span class="gis-sublbl">(Mobile No.)</span></td>
            <td class="gis-lbl-cell">Kabanganakan <span class="gis-sublbl">(Birthdate)</span></td>
            <td class="gis-lbl-cell">Edad <span class="gis-sublbl">(Age)</span></td>
            <td class="gis-lbl-cell">Kasarian <span class="gis-sublbl">(Gender)</span></td>
            <td class="gis-lbl-cell">Civil Status <span class="gis-sublbl">(Katayuang Sibil)</span></td>
            <td class="gis-lbl-cell">Trabaho <span class="gis-sublbl">(Occupation)</span></td>
            <td class="gis-lbl-cell">Buwanang Kita <span class="gis-sublbl">(Monthly Salary)</span></td>
        </tr>
    </table>

    <!-- Row 4: Relationship to Beneficiary & Time End -->
    <table class="gis-line-table" style="margin-top: 1px;">
        <tr class="gis-val-row">
            <td style="width: 48%;" class="gis-val-cell">{!! $intake->rep_relationship ? e($intake->rep_relationship) : '&nbsp;' !!}</td>
            <td style="width: 52%; border-bottom: none !important; text-align: right; vertical-align: middle;">
                <div style="display: flex; align-items: center; justify-content: flex-end;">
                    <span class="gis-meta-label">Time End:</span>
                    <span class="gis-meta-box gis-box-time">{{ $intake->time_end ?? '' }}</span>
                </div>
            </td>
        </tr>
        <tr class="gis-lbl-row">
            <td class="gis-lbl-cell" style="text-align: center;">Relasyon sa Benepisyaryo <span class="gis-sublbl">(Relationship to the Beneficiary)</span></td>
            <td style="border: none !important;"></td>
        </tr>
    </table>

    <!-- 4. Section III: DSWD Use Only Section (Maroon Banner) -->
    <div class="gis-banner gis-banner-maroon">
        Huwag susulatan ang DSWD lamang ang pwede gumamit (Do not write below this part for DSWD's use only)
    </div>
    
    <table class="gis-dswd-table">
        <tr>
            <td class="gis-dswd-col-left">
                <div class="gis-dswd-title">Beneficiary Category</div>
                <div class="gis-dswd-subtitle">Specify Sub-Category</div>
                <div class="gis-check-item">
                    <span class="gis-checkbox">{{ in_array('Solo Parents', $cats) ? '✓' : '' }}</span>
                    <span class="gis-check-text">Solo Parents</span>
                </div>
                <div class="gis-check-item">
                    <span class="gis-checkbox">{{ in_array('Indigenous People', $cats) ? '✓' : '' }}</span>
                    <span class="gis-check-text">Indigenous People</span>
                </div>
                <div class="gis-check-item">
                    <span class="gis-checkbox">{{ in_array('PWD', $cats) ? '✓' : '' }}</span>
                    <span class="gis-check-text">PWD</span>
                </div>
                <div class="gis-check-item">
                    <span class="gis-checkbox">{{ in_array('4PS DSWD Beneficiary', $cats) ? '✓' : '' }}</span>
                    <span class="gis-check-text">4PS DSWD Beneficiary</span>
                </div>
                <div class="gis-check-item">
                    <span class="gis-checkbox">{{ in_array('LGBTQIA+', $cats) ? '✓' : '' }}</span>
                    <span class="gis-check-text">LGBTQIA+</span>
                </div>
                <div class="gis-check-item">
                    <span class="gis-checkbox">{{ in_array('Psychosocial/Mental/Learning Disability', $cats) ? '✓' : '' }}</span>
                    <span class="gis-check-text">Psychosocial/Mental/Learning Disability</span>
                </div>
                <div class="gis-check-item">
                    <span class="gis-checkbox">{{ in_array('Stateless Person/Asylum Seekers/Refugees', $cats) ? '✓' : '' }}</span>
                    <span class="gis-check-text">Stateless Person/Asylum Seekers/Refugees</span>
                </div>
                <div class="gis-check-item" style="display: flex; align-items: flex-end;">
                    <span class="gis-checkbox">{{ (in_array('Others', $cats) || in_array('Other', $cats) || !empty($otherCategory)) ? '✓' : '' }}</span>
                    <span class="gis-check-text" style="white-space: nowrap;">Others:</span>
                    <span class="gis-others-underline">{{ !empty($otherCategory) ? $otherCategory : 'N/A' }}</span>
                </div>
            </td>
            <td class="gis-dswd-col-right">
                <div class="gis-dswd-title">Social worker's Assessment</div>
                <div class="gis-assessment-content">{{ $intake->social_worker_assessment ?? '' }}</div>
            </td>
        </tr>
    </table>

    <!-- 5. Section IV: Family Composition -->
    <div class="gis-banner gis-banner-dark">
        KOMPOSISYON NG PAMILYA (Family Composition)
    </div>
    
    <table class="gis-family-table">
        <thead>
            <tr>
                <th style="width: 32%;">Buong Pangalan<br><span class="gis-sublbl">(Complete Name)</span></th>
                <th style="width: 25%;">Relasyon sa Benepisyaryo<br><span class="gis-sublbl">(Relationship to the Beneficiary)</span></th>
                <th style="width: 8%;">Edad<br><span class="gis-sublbl">(Age)</span></th>
                <th style="width: 17%;">Trabaho<br><span class="gis-sublbl">(Occupation)</span></th>
                <th style="width: 18%;">Buwanang kita<br><span class="gis-sublbl">(Monthly Salary)</span></th>
            </tr>
        </thead>
        <tbody>
            @php
                $family = $intake->family_composition ?? [];
                $rowCount = max(count($family), 4);
            @endphp
            @for($i = 0; $i < $rowCount; $i++)
                @php
                    $mem = $family[$i] ?? null;
                @endphp
                <tr>
                    <td style="text-align: left; padding-left: 6px;">{{ $mem['name'] ?? '' }}</td>
                    <td>{{ $mem['relationship'] ?? '' }}</td>
                    <td>{{ $mem['age'] ?? '' }}</td>
                    <td>{{ $mem['occupation'] ?? '' }}</td>
                    <td>{{ !empty($mem['salary']) ? number_format($mem['salary'], 2) : '' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <!-- 6. Section V: Recommendation Line -->
    <div class="gis-rec-section">
        <div class="gis-rec-line">
            <span class="gis-rec-text">The Client is hereby recommended to receive</span>
            <span class="gis-rec-underline gis-underline-wide">{{ $intake->recommended_assistance_type ?? '' }}</span>
            <span class="gis-rec-text">assistance for</span>
        </div>
        <div class="gis-rec-line" style="margin-top: 4px;">
            <span class="gis-rec-underline gis-underline-med">{{ $intake->display_assistance_purpose !== 'N/A' ? $intake->display_assistance_purpose : '' }}</span>
            <span class="gis-rec-text">in the amount of</span>
            <span class="gis-rec-underline gis-underline-blank"></span>
            <span class="gis-rec-text">Php</span>
            <span class="gis-rec-underline gis-underline-amount">{{ $intake->recommended_amount ? number_format($intake->recommended_amount, 2) : '' }}</span>
            <span class="gis-rec-text">.</span>
        </div>
    </div>

    <!-- 7. Section VI: Signatures Section -->
    <table class="gis-sig-table">
        <tr>
            <!-- Left: Oath box with Thumbmark and Client/Representative Signature -->
            <td class="gis-sig-col-left">
                <div class="gis-oath-container">
                    <div class="gis-oath-text">
                        "I declare under oath that I personally accomplished the GIS Form and all the information provided herewith is TRUE, CORRECT, VALID, and COMPLETE pursuant to existing laws, rules, and regulations of the Republic of the Philippines. I authorized the Agency Head/Authorized Representatives to verify and validate the contents stated herein. I also AGREE that any MISINTERPRETATION and information/acts to DEFRAUD the government, including attached documents, shall cause the filing of appropriate case/s against me."
                    </div>
                    <div class="gis-thumbmark-box"></div>
                </div>
                <div class="gis-sig-signer">
                    <div class="gis-sig-name">{{ $intake->has_representative ? $intake->representative_full_name : $intake->beneficiary_full_name }}</div>
                    <div class="gis-sig-line"></div>
                    <div class="gis-sig-title">Buong Pangalan at Pirma</div>
                    <div class="gis-sig-subtitle">(Signature over Printed Name)</div>
                </div>
            </td>
            <!-- Center: Interviewed by -->
            <td class="gis-sig-col-center">
                <div class="gis-sig-header">Interviewed by:</div>
                <div class="gis-sig-spacer"></div>
                <div class="gis-sig-signer">
                    <div class="gis-sig-name">{{ $intake->interviewed_by ?? $intake->encoderUser?->name ?? '' }}</div>
                    <div class="gis-sig-line"></div>
                    <div class="gis-sig-title">MSWD Personnel / Social Worker</div>
                    <div class="gis-sig-subtitle">(Signature over Printed Name)</div>
                </div>
            </td>
            <!-- Right: Reviewed & Approved by -->
            <td class="gis-sig-col-right">
                <div class="gis-sig-header">Reviewed &amp; Approved by:</div>
                <div class="gis-sig-spacer"></div>
                <div class="gis-sig-signer">
                    <div class="gis-sig-name">{{ $intake->reviewed_by ?? '' }}</div>
                    <div class="gis-sig-line"></div>
                    <div class="gis-sig-title">Social Worker</div>
                    <div class="gis-sig-subtitle">(Signature over Printed Name)</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- 8. Section VII: Footer -->
    <div class="gis-footer">
        New Municipal Building, Barangay Biga I, Emilio Aguinaldo Highway, Silang, Cavite, Philippines<br>
        Mobile Nos.: 09770695194/09161512560 &nbsp;&nbsp; Email Address: socialwelfaresilang@gmail.com
    </div>

</div>
@endsection