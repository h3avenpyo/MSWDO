@extends('layouts.financial')

@section('title', 'General Intake Sheet - ' . $intake->control_number)
@section('page-title', 'General Intake Sheet Overview')

@section('page-styles')
<style>
    .detail-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        margin-bottom: 1.5rem;
    }
    .detail-header-bar {
        background: linear-gradient(135deg, #1A237E 0%, #283593 100%);
        color: #ffffff;
        padding: 1.25rem 1.5rem;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .info-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748B;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1E293B;
    }
    .badge-status {
        background-color: #EEF2FF;
        color: #1A237E;
        padding: 0.4rem 0.85rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.8rem;
    }

    /* Print Specific Styling matching Physical Hardcopy Form */
    @media print {
        body {
            background-color: #ffffff !important;
            font-family: 'Times New Roman', Times, serif, sans-serif !important;
            font-size: 11pt !important;
            color: #000000 !important;
        }
        .main-content, .p-4 {
            padding: 0 !important;
            margin: 0 !important;
        }
        .no-print, nav, .top-navbar, .sidebar, .sidebar-overlay, button, a {
            display: none !important;
        }
        .print-container {
            display: block !important;
            width: 100% !important;
            margin: 0 auto !important;
            padding: 10mm !important;
        }
        .print-table, .print-table th, .print-table td {
            border: 1px solid #000000 !important;
            border-collapse: collapse !important;
        }
    }
    .print-container {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid no-print">

    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1A237E;">GENERAL INTAKE SHEET: {{ $intake->control_number }}</h4>
            <p class="text-muted small mb-0">Processed on {{ $intake->date_processed ? $intake->date_processed->format('F d, Y') : 'N/A' }} • Client Status: <strong>{{ $intake->client_type ?? 'New' }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-print me-1"></i> Print Hardcopy Form
            </button>
            <a href="{{ route('admin.beneficiary-intake.edit', $intake) }}" class="btn btn-primary btn-sm rounded-pill px-3" style="background: #1A237E; border: none;">
                <i class="fas fa-edit me-1"></i> Edit Record
            </a>
            <a href="{{ route('admin.beneficiary-intake.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
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
                <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill mb-1">MSWDO Silang Intake Record</span>
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
                    <div class="info-value"><span class="badge bg-primary text-white px-3 py-1 rounded-pill">{{ $intake->client_type ?? 'New' }}</span></div>
                </div>
                <div class="col-md-3">
                    <div class="info-label">Date Processed</div>
                    <div class="info-value">{{ $intake->date_processed ? $intake->date_processed->format('F d, Y') : 'N/A' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="info-label">Time Start / End</div>
                    <div class="info-value">{{ $intake->time_start ?? '--:--' }} - {{ $intake->time_end ?? '--:--' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Encoder</div>
                    <div class="info-value">{{ $intake->encoderUser?->name ?? session('admin_user_name') ?? 'System Admin' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section I: Beneficiary Info Card -->
    <div class="detail-card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4 pb-2 border-bottom text-dark">
                <i class="fas fa-user me-2 text-primary"></i> IMPORMASYON NG BENEPISYARYO <span class="text-muted fs-6">(Beneficiary Info)</span>
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
                    <div class="info-value">{{ $intake->beneficiary_sex ?? 'N/A' }} • {{ $intake->beneficiary_civil_status ?? 'N/A' }}</div>
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
                <i class="fas fa-user-friends me-2 text-primary"></i> IMPORMASYON NG KINATAWAN <span class="text-muted fs-6">(Representative Info)</span>
            </h5>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="info-label">Representative Name</div>
                    <div class="info-value">{{ $intake->representative_full_name }}</div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Relasyon sa Benepisyaryo</div>
                    <div class="info-value"><span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill">{{ $intake->rep_relationship ?? 'N/A' }}</span></div>
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
                    <div class="p-3 bg-light rounded-3 border mt-1 font-monospace text-dark" style="white-space: pre-wrap; font-size: 0.9rem;">
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
                <i class="fas fa-users me-2 text-primary"></i> KOMPOSISYON NG PAMILYA <span class="text-muted fs-6">(Family Composition)</span>
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
                <i class="fas fa-hand-holding-heart me-2 text-primary"></i> ASSISTANCE PURPOSE / MEDICAL CONDITION &amp; INTERVIEW DETAILS
            </h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-label">Assistance Purpose / Medical Condition</div>
                    <div class="info-value">{{ $intake->display_assistance_purpose }}</div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Interviewed by</div>
                    <div class="info-value">{{ $intake->interviewed_by ?? $intake->encoderUser?->name ?? 'MSWD Staff' }}</div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- PRINTABLE CONTAINER FORMATTED LIKE PHYSICAL HARDCOPY FORM -->
<div class="print-container">
    
    <!-- Top Header -->
    <table style="width:100%; border:none; margin-bottom:10px;">
        <tr>
            <td style="width:15%; border:none; text-align:center;">
                <i class="fas fa-university fa-3x" style="color:#000;"></i>
            </td>
            <td style="width:70%; border:none; text-align:center;">
                <h3 style="margin:0; font-size:16pt; font-weight:bold;">GENERAL INTAKE SHEET</h3>
                <div style="font-size:9pt;">Municipal Social Welfare and Development Office - Silang, Cavite</div>
            </td>
            <td style="width:15%; border:none; text-align:right; font-size:9pt;">
                <div><strong>Control No:</strong></div>
                <div style="font-size:11pt; font-weight:bold;">{{ $intake->control_number }}</div>
            </td>
        </tr>
    </table>

    <table style="width:100%; margin-bottom:10px; font-size:9pt;" class="print-table">
        <tr>
            <td style="padding:4px;"><strong>Client Status:</strong> {{ $intake->client_type == 'Returning' ? '[X] Returning' : '[X] New' }}</td>
            <td style="padding:4px;"><strong>Time Start:</strong> {{ $intake->time_start ?? '____' }}</td>
            <td style="padding:4px;"><strong>Date:</strong> {{ $intake->date_processed ? $intake->date_processed->format('m/d/Y') : date('m/d/Y') }}</td>
        </tr>
    </table>

    <!-- Beneficiary Section -->
    <div style="background:#ddd; font-weight:bold; font-size:9pt; padding:3px; border:1px solid #000; margin-top:5px;">
        IMPORMASYON NG BENEPISYARYO (Beneficiary's Identifying Information)
    </div>
    <table style="width:100%; font-size:8.5pt;" class="print-table">
        <tr>
            <td style="width:30%; padding:3px;"><strong>Apelyido (Last Name):</strong><br>{{ $intake->beneficiary_last_name }}</td>
            <td style="width:30%; padding:3px;"><strong>Unang Pangalan (First Name):</strong><br>{{ $intake->beneficiary_first_name }}</td>
            <td style="width:30%; padding:3px;"><strong>Gitnang Pangalan (Middle Name):</strong><br>{{ $intake->beneficiary_middle_name ?? 'N/A' }}</td>
            <td style="width:10%; padding:3px;"><strong>Ext:</strong><br>{{ $intake->beneficiary_extension_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;"><strong>House No./Street:</strong><br>{{ $intake->beneficiary_street_address }}</td>
            <td style="padding:3px;"><strong>Barangay:</strong><br>{{ $intake->beneficiary_barangay }}</td>
            <td style="padding:3px;"><strong>City/Municipality:</strong><br>SILANG</td>
            <td style="padding:3px;"><strong>Province/Region:</strong><br>CAVITE / IV-A</td>
        </tr>
        <tr>
            <td style="padding:3px;"><strong>Mobile No:</strong><br>{{ $intake->beneficiary_contact_number }}</td>
            <td style="padding:3px;"><strong>Kapanganakan (Birthdate):</strong><br>{{ $intake->beneficiary_birthday ? $intake->beneficiary_birthday->format('m/d/Y') : '' }} (Edad: {{ $intake->beneficiary_age }})</td>
            <td style="padding:3px;"><strong>Kasarian / Katayuan:</strong><br>{{ $intake->beneficiary_sex }} / {{ $intake->beneficiary_civil_status }}</td>
            <td style="padding:3px;"><strong>Kita (Salary):</strong><br>{{ $intake->beneficiary_monthly_salary ? 'Php ' . number_format($intake->beneficiary_monthly_salary, 2) : 'N/A' }}</td>
        </tr>
    </table>

    <!-- Representative Section -->
    <div style="background:#ddd; font-weight:bold; font-size:9pt; padding:3px; border:1px solid #000; margin-top:5px;">
        IMPORMASYON NG KINATAWAN (Representative's Identifying Information)
    </div>
    @if($intake->has_representative)
    <table style="width:100%; font-size:8.5pt;" class="print-table">
        <tr>
            <td style="width:30%; padding:3px;"><strong>Apelyido (Last Name):</strong><br>{{ $intake->rep_last_name }}</td>
            <td style="width:30%; padding:3px;"><strong>Unang Pangalan (First Name):</strong><br>{{ $intake->rep_first_name }}</td>
            <td style="width:30%; padding:3px;"><strong>Gitnang Pangalan (Middle Name):</strong><br>{{ $intake->rep_middle_name ?? 'N/A' }}</td>
            <td style="width:10%; padding:3px;"><strong>Ext:</strong><br>{{ $intake->rep_extension_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;"><strong>House No./Street:</strong><br>{{ $intake->rep_street_address }}</td>
            <td style="padding:3px;"><strong>Barangay:</strong><br>{{ $intake->rep_barangay }}</td>
            <td style="padding:3px;"><strong>City/Province:</strong><br>SILANG / CAVITE</td>
            <td style="padding:3px;"><strong>Relasyon:</strong><br>{{ $intake->rep_relationship }}</td>
        </tr>
    </table>
    @else
    <div style="border:1px solid #000; padding:5px; font-size:8.5pt; text-align:center; color:#555;">N/A (Beneficiary filed directly without representative)</div>
    @endif

    <!-- DSWD Notice Bar -->
    <div style="background:#7F1D1D; color:#fff; font-weight:bold; font-size:8.5pt; text-align:center; padding:3px; margin-top:5px;">
        Huwag susulatan ang DSWD lamang ang pwede gumamit (Do not write below this part for DSWD's use only)
    </div>

    <table style="width:100%; font-size:8.5pt;" class="print-table">
        <tr>
            <td style="width:40%; vertical-align:top; padding:4px;">
                <strong>Beneficiary Category (Sub-Category):</strong><br>
                @php
                    $printCats = $intake->beneficiary_categories ?? [];
                @endphp
                <div>[{{ in_array('Solo Parents', $printCats) ? 'X' : ' ' }}] Solo Parents</div>
                <div>[{{ in_array('Indigenous People', $printCats) ? 'X' : ' ' }}] Indigenous People</div>
                <div>[{{ in_array('PWD', $printCats) ? 'X' : ' ' }}] PWD</div>
                <div>[{{ in_array('4PS DSWD Beneficiary', $printCats) ? 'X' : ' ' }}] 4PS DSWD Beneficiary</div>
                <div>[{{ in_array('LGBTQIA+', $printCats) ? 'X' : ' ' }}] LGBTQIA+</div>
                <div>[{{ in_array('Psychosocial/Mental/Learning Disability', $printCats) ? 'X' : ' ' }}] Psychosocial Disability</div>
                <div>[{{ in_array('Stateless Person/Asylum Seekers/Refugees', $printCats) ? 'X' : ' ' }}] Stateless / Refugees</div>
                <div>[{{ in_array('Others', $printCats) || $intake->beneficiary_category_other ? 'X' : ' ' }}] Others: {{ $intake->beneficiary_category_other ?? 'N/A' }}</div>
            </td>
            <td style="width:60%; vertical-align:top; padding:4px;">
                <strong>Social Worker's Assessment:</strong><br>
                <div style="min-height:80px; font-size:8pt; white-space:pre-wrap;">{{ $intake->social_worker_assessment ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <!-- Family Composition -->
    <div style="background:#ddd; font-weight:bold; font-size:9pt; padding:3px; border:1px solid #000; margin-top:5px;">
        KOMPOSISYON NG PAMILYA (Family Composition)
    </div>
    <table style="width:100%; font-size:8pt;" class="print-table">
        <thead>
            <tr style="background:#f5f5f5;">
                <th style="padding:2px;">Buong Pangalan</th>
                <th style="padding:2px;">Relasyon</th>
                <th style="padding:2px;">Edad</th>
                <th style="padding:2px;">Trabaho</th>
                <th style="padding:2px;">Buwanang Kita</th>
            </tr>
        </thead>
        <tbody>
            @forelse($intake->family_composition ?? [] as $fMember)
            <tr>
                <td style="padding:2px;">{{ $fMember['name'] ?? '' }}</td>
                <td style="padding:2px;">{{ $fMember['relationship'] ?? '' }}</td>
                <td style="padding:2px;">{{ $fMember['age'] ?? '' }}</td>
                <td style="padding:2px;">{{ $fMember['occupation'] ?? '' }}</td>
                <td style="padding:2px;">{{ !empty($fMember['salary']) ? 'Php ' . number_format($fMember['salary'], 2) : '' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:5px;">No family composition recorded.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Recommendation -->
    <div style="margin-top:10px; font-size:9pt; border:1px solid #000; padding:8px;">
        The Client is hereby recommended to receive <u>&nbsp;{{ $intake->recommended_assistance_type ?? '____________________' }}&nbsp;</u> assistance for <u>&nbsp;{{ $intake->display_assistance_purpose !== 'N/A' ? $intake->display_assistance_purpose : '____________________' }}&nbsp;</u> in the amount of <strong>Php <u>&nbsp;{{ $intake->recommended_amount ? number_format($intake->recommended_amount, 2) : '____________________' }}&nbsp;</u></strong>.
    </div>

    <!-- Signatures Grid -->
    <table style="width:100%; border:none; margin-top:15px; font-size:8.5pt;">
        <tr>
            <td style="width:33%; border:none; text-align:center; vertical-align:bottom;">
                <div style="border-bottom:1px solid #000; margin-bottom:3px; font-weight:bold;">
                    {{ $intake->has_representative ? $intake->representative_full_name : $intake->beneficiary_full_name }}
                </div>
                <div>Buong Pangalan at Pirma<br><span style="font-size:7.5pt;">(Signature over Printed Name)</span></div>
            </td>
            <td style="width:33%; border:none; text-align:center; vertical-align:bottom;">
                <div style="border-bottom:1px solid #000; margin-bottom:3px; font-weight:bold;">
                    {{ $intake->interviewed_by ?? $intake->encoderUser?->name ?? 'MSWD Personnel' }}
                </div>
                <div>Interviewed by:<br><span style="font-size:7.5pt;">(MSWD Personnel / Social Worker)</span></div>
            </td>
            <td style="width:33%; border:none; text-align:center; vertical-align:bottom;">
                <div style="border-bottom:1px solid #000; margin-bottom:3px; font-weight:bold;">
                    {{ $intake->reviewed_by ?? '____________________' }}
                </div>
                <div>Reviewed &amp; Approved by:<br><span style="font-size:7.5pt;">(Social Worker)</span></div>
            </td>
        </tr>
    </table>

    <!-- Footer address -->
    <div style="margin-top:20px; text-align:center; font-size:7.5pt; color:#555;">
        New Municipal Building, Barangay Biga I, Emilio Aguinaldo Highway, Silang, Cavite, Philippines<br>
        Mobile Nos.: 09770695194 / 09161512560 | Email Address: socialwelfaresilang@gmail.com
    </div>

</div>
@endsection
