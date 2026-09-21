@extends('admin.layout')

@section('title', 'Historical Financial Intake - MSWDO Admin')
@section('page_title', 'Historical Financial Intake')

@push('head')
<!-- Bootstrap 5 CSS for Form & Grid Compatibility -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="{{ asset('css/beneficiary-intake/form.css') }}" rel="stylesheet">
<style>
    :is(input[type="text"], input:not([type]), textarea):not([readonly]) {
        text-transform: uppercase !important;
    }

    :is(input, textarea)::placeholder {
        text-transform: none !important;
    }

    .historical-badge {
        background: #FEF3C7;
        color: #92400E;
        border: 1px solid #FCD34D;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .form-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .form-header-bar {
        background: linear-gradient(135deg, #1A237E 0%, #283593 100%);
        color: #ffffff;
        padding: 1rem 1.5rem;
    }

    .historical-header-bar {
        background: linear-gradient(135deg, #B45309 0%, #78350F 100%);
        color: #ffffff;
        padding: 1rem 1.5rem;
    }

    .required-star {
        color: #DC2626;
        font-weight: bold;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #334155;
        margin-bottom: 0.35rem;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        padding: 0.55rem 0.85rem;
        font-size: 0.9rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #1A237E;
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.15);
    }

    .form-control[readonly] {
        background-color: #F8FAFC;
        color: #64748B;
    }

    .rep-card-disabled {
        opacity: 0.55;
        pointer-events: none;
    }

    .toggle-card {
        background-color: #F8FAFC;
        border: 1px dashed #CBD5E1;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
    }

    .section-tag {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #1A237E;
        background-color: #EEF2FF;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        display: inline-block;
        margin-bottom: 1rem;
    }

    .highlight-input {
        background-color: #FFFBEB !important;
        border-color: #F59E0B !important;
        font-weight: 600 !important;
    }

    .highlight-input:focus {
        border-color: #B45309 !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25) !important;
    }

    @media (max-width: 575.98px) {
        .row.g-3>[class*="col-"] {
            width: 100%;
            max-width: 100%;
            flex: 0 0 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>

            <h4 class="fw-bold mb-1" style="color: #1A237E;">Historical Financial Intake Encoding</h4>
            <p class="text-muted small mb-0">
                Encode historical client records created before system implementation. The original intake date and
                actual assistance amount will be preserved and reflected in existing masterlists, statistics, and
                reports.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.beneficiary-intake.index') }}"
                class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="View All Intakes">
                <i class="fas fa-list me-1"></i> All Intakes Masterlist
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3"
                title="Back to Dashboard">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Error Summary Alert -->
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
        <div class="d-flex align-items-start">
            <i class="fas fa-exclamation-triangle fa-lg me-3 mt-1 text-danger"></i>
            <div>
                <strong>Please check the following form errors:</strong>
                <ul class="mb-0 mt-1 small">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ route('admin.historical-data.financial-intake.store') }}" method="POST" id="historicalIntakeForm"
        novalidate>
        @csrf

        <!-- 1. HISTORICAL RECORD CONTROL & INTAKE DATE DETAILS -->
        <div class="form-card">
            <div class="form-header-bar d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="fw-bold"><i class="fas fa-calendar-alt me-2"></i> HISTORICAL RECORD &amp; ORIGINAL INTAKE
                    DATE</span>
                <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill"><i
                        class="fas fa-clock-rotate-left me-1"></i> Pre-System Record</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label" for="control_number">Control Number <span
                                class="required-star">*</span></label>
                        <input type="text" name="control_number" id="control_number" class="form-control fw-bold"
                            value="{{ old('control_number', $controlNumber) }}" placeholder="e.g. MSWDO-2023-00001"
                            required>
                        <small class="text-muted" style="font-size: 0.75rem;">Original or generated reference
                            number.</small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Client Status <span class="required-star">*</span></label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="client_type" id="type_new"
                                    value="New" {{ old('client_type', 'New' )=='New' ? 'checked' : '' }} required>
                                <label class="form-check-label fw-bold" for="type_new">New Client</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="client_type" id="type_returning"
                                    value="Returning" {{ old('client_type')=='Returning' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="type_returning">Returning Client</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="date_processed">
                            <i class="fas fa-calendar-day text-primary me-1"></i> Original Intake Date <span
                                class="required-star">*</span>
                        </label>
                        <input type="date" name="date_processed" id="date_processed"
                            class="form-control highlight-input" value="{{ old('date_processed') }}"
                            max="{{ date('Y-m-d') }}" required>
                        <small class="text-muted" style="font-size: 0.75rem;">Actual historical date this intake was
                            conducted.</small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="encoder_display">Encoder / Officer</label>
                        <input type="text" id="encoder_display" class="form-control bg-light text-muted"
                            value="{{ $encoder }}" readonly tabindex="-1">
                        <small class="text-muted" style="font-size: 0.75rem;">Account encoding this record.</small>
                    </div>
                </div>

                <div class="row g-3 mt-2 pt-3 border-top">
                    <div class="col-md-3">
                        <label class="form-label" for="time_start">Time Start <span class="text-muted fw-normal">(Optional)</span></label>
                        <input type="text" name="time_start" id="time_start" class="form-control"
                            value="{{ old('time_start') }}" placeholder="e.g. 09:00 AM">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="time_end">Time End <span class="text-muted fw-normal">(Optional)</span></label>
                        <input type="text" name="time_end" id="time_end" class="form-control"
                            value="{{ old('time_end') }}" placeholder="e.g. 09:30 AM">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. FINANCIAL ASSISTANCE AMOUNT & DISBURSEMENT STATUS -->
        <div class="form-card" style="border-left: 4px solid #F59E0B;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 36px; height: 36px; background: #FEF3C7; color: #B45309;">
                        <i class="fas fa-hand-holding-dollar fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">ACTUAL FINANCIAL ASSISTANCE AMOUNT &amp; DISBURSEMENT STATUS
                        </h6>
                        <small class="text-muted">Enter the actual assistance amount that was granted to this historical
                            beneficiary.</small>
                    </div>
                </div>

                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" for="recommended_amount">
                            Actual Financial Assistance Amount Provided (₱) <span class="required-star">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold bg-white text-dark">₱</span>
                            <input type="number" step="0.01" min="0" max="9999999.99" name="recommended_amount"
                                id="recommended_amount" class="form-control fw-bold fs-5 highlight-input"
                                value="{{ old('recommended_amount') }}" placeholder="0.00" required>
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Actual financial aid amount
                            disbursed.</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Disbursement Status in Step 2</label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="claim_status" id="status_claimed"
                                    value="Claimed" {{ old('claim_status', 'Claimed' )=='Claimed' ? 'checked' : '' }}
                                    onchange="toggleClaimDate(this.value)">
                                <label class="form-check-label fw-bold text-success" for="status_claimed">
                                    <i class="fas fa-check-circle me-1"></i> Claimed / Disbursed (Default)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="claim_status" id="status_pending"
                                    value="Pending" {{ old('claim_status')=='Pending' ? 'checked' : '' }}
                                    onchange="toggleClaimDate(this.value)">
                                <label class="form-check-label fw-bold text-warning" for="status_pending">
                                    <i class="fas fa-clock me-1"></i> Amount Assigned / Pending
                                </label>
                            </div>
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Reflects status in Step 2 Masterlist and
                            statistics.</small>
                    </div>

                    <div class="col-md-4" id="claimingDateWrapper">
                        <label class="form-label" for="claiming_date">Claim / Release Date</label>
                        <input type="date" name="claiming_date" id="claiming_date" class="form-control"
                            value="{{ old('claiming_date') }}" max="{{ date('Y-m-d') }}">
                        <small class="text-muted" style="font-size: 0.75rem;">Leave blank to use Original Intake
                            Date.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6-MONTH VALIDITY RESTRICTION ALERT CARD (AJAX & Server-side) -->
        <div id="duplicateAlertCard" class="mb-4 {{ session('duplicate_check_error') ? '' : 'd-none' }}"
            style="background: #FFF5F5; border: 1.5px solid #FCA5A5; border-radius: 14px; box-shadow: 0 4px 16px rgba(220, 38, 38, 0.06); transition: all 180ms ease;">
            <div class="p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B;">
                        <i class="fas fa-user-lock fa-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <h5 class="fw-bold mb-0"
                                style="color: #991B1B; font-size: 1.05rem; letter-spacing: -0.01em;">
                                <i class="fas fa-shield-halved me-2 text-danger"></i>6-Month Policy Restriction Alert
                            </h5>
                        </div>
                        <p class="text-muted mb-2 small" id="duplicateWarningText">
                            {{ session('duplicate_check_error', 'A duplicate beneficiary within the 6-month validity
                            window was detected.') }}
                        </p>
                        <div id="duplicateMatchesList" class="mt-2">
                            @if(session('duplicate_matches'))
                            @foreach(session('duplicate_matches') as $m)
                            <div class="p-2 mb-1 rounded border bg-white small">
                                <strong>Control No:</strong> {{ $m['control_number'] }} |
                                <strong>Beneficiary:</strong> {{ $m['beneficiary_name'] }} |
                                <strong>Date:</strong> {{ $m['date_processed'] }}
                            </div>
                            @endforeach
                            @endif
                        </div>
                        <div class="form-check mt-3 pt-2 border-top">
                            <input class="form-check-input" type="checkbox" name="confirm_duplicate_override"
                                id="confirm_duplicate_override" value="1">
                            <label class="form-check-label small fw-bold text-danger" for="confirm_duplicate_override">
                                I confirm this historical record has been verified against physical MSWDO archive files
                                and authorize saving it despite the 6-month alert.
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 1: IMPORMASYON NG BENEPISYARYO (Beneficiary Information) -->
        <div class="form-card">
            <div class="card-body p-4">
                <span class="section-tag">Seksyon I</span>
                <h5 class="fw-bold text-dark mb-4">IMPORMASYON NG BENEPISYARYO (Beneficiary Information)</h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label" for="beneficiary_last_name">Apelyido (Last Name) <span
                                class="required-star">*</span></label>
                        <input type="text" name="beneficiary_last_name" id="beneficiary_last_name" class="form-control"
                            value="{{ old('beneficiary_last_name') }}" placeholder="Dela Cruz" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="beneficiary_first_name">Unang Pangalan (First Name) <span
                                class="required-star">*</span></label>
                        <input type="text" name="beneficiary_first_name" id="beneficiary_first_name"
                            class="form-control" value="{{ old('beneficiary_first_name') }}" placeholder="Juan"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="beneficiary_middle_name">Gitnang Pangalan (Middle Name)</label>
                        <input type="text" name="beneficiary_middle_name" id="beneficiary_middle_name"
                            class="form-control" value="{{ old('beneficiary_middle_name') }}" placeholder="Reyes">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="beneficiary_extension_name">Extension Name</label>
                        <input type="text" name="beneficiary_extension_name" id="beneficiary_extension_name"
                            class="form-control" value="{{ old('beneficiary_extension_name') }}"
                            placeholder="Jr., Sr., III">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="beneficiary_street_address">Tirahan (House No. / Street / Purok)
                            <span class="required-star">*</span></label>
                        <input type="text" name="beneficiary_street_address" id="beneficiary_street_address"
                            class="form-control" value="{{ old('beneficiary_street_address') }}"
                            placeholder="e.g. 123 Purok 2" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="beneficiary_barangay">Barangay <span
                                class="required-star">*</span></label>
                        <select name="beneficiary_barangay" id="beneficiary_barangay" class="form-select" required>
                            <option value="">Pumili ng Barangay</option>
                            @foreach($barangays as $brgy)
                            <option value="{{ $brgy }}" {{ old('beneficiary_barangay')==$brgy ? 'selected' : '' }}>{{
                                $brgy }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label" for="beneficiary_city">Bayan / Lungsod (City/Municipality) <span
                                class="required-star">*</span></label>
                        <input type="text" name="beneficiary_city" id="beneficiary_city" class="form-control bg-light"
                            value="{{ old('beneficiary_city', 'Silang') }}" readonly required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="beneficiary_province">Lalawigan (Province) <span
                                class="required-star">*</span></label>
                        <input type="text" name="beneficiary_province" id="beneficiary_province"
                            class="form-control bg-light" value="{{ old('beneficiary_province', 'Cavite') }}" readonly
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="beneficiary_region">Rehiyon (Region) <span
                                class="required-star">*</span></label>
                        <input type="text" name="beneficiary_region" id="beneficiary_region"
                            class="form-control bg-light" value="{{ old('beneficiary_region', 'Region IV-A') }}"
                            readonly required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label" for="beneficiary_contact_number">
                            Numero ng Telepono <span class="required-star">*</span>
                        </label>
                        <input type="text" name="beneficiary_contact_number" id="beneficiary_contact_number"
                            class="form-control" value="{{ old('beneficiary_contact_number') }}"
                            placeholder="09XXXXXXXXX (11 digits)" maxlength="11" pattern="[0-9]{11}" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Exact 11 numeric digits (e.g.
                            09123456789).</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="beneficiary_birthday">
                            Araw ng Kapanganakan (Birthday) <span class="required-star">*</span>
                        </label>
                        <input type="date" name="beneficiary_birthday" id="beneficiary_birthday" class="form-control"
                            value="{{ old('beneficiary_birthday') }}" max="{{ date('Y-m-d') }}" required
                            onchange="calculateBeneficiaryAge()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="beneficiary_age">Edad (Age) <span
                                class="required-star">*</span></label>
                        <input type="number" name="beneficiary_age" id="beneficiary_age" class="form-control"
                            value="{{ old('beneficiary_age') }}" min="0" max="150" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="beneficiary_sex">Kasarian (Sex) <span
                                class="required-star">*</span></label>
                        <select name="beneficiary_sex" id="beneficiary_sex" class="form-select" required>
                            <option value="">Piliin</option>
                            <option value="Male" {{ old('beneficiary_sex')=='Male' ? 'selected' : '' }}>Lalaki (Male)
                            </option>
                            <option value="Female" {{ old('beneficiary_sex')=='Female' ? 'selected' : '' }}>Babae
                                (Female)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="beneficiary_civil_status">Katayuang Sibil <span
                                class="required-star">*</span></label>
                        <select name="beneficiary_civil_status" id="beneficiary_civil_status" class="form-select"
                            required>
                            <option value="">Piliin</option>
                            <option value="Single" {{ old('beneficiary_civil_status')=='Single' ? 'selected' : '' }}>
                                Walang Asawa (Single)</option>
                            <option value="Married" {{ old('beneficiary_civil_status')=='Married' ? 'selected' : '' }}>
                                May Asawa (Married)</option>
                            <option value="Widowed" {{ old('beneficiary_civil_status')=='Widowed' ? 'selected' : '' }}>
                                Balo (Widowed)</option>
                            <option value="Separated" {{ old('beneficiary_civil_status')=='Separated' ? 'selected' : ''
                                }}>Hiwalay (Separated)</option>
                            <option value="Live-in" {{ old('beneficiary_civil_status')=='Live-in' ? 'selected' : '' }}>
                                Live-in</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label" for="beneficiary_occupation">Trabaho (Occupation)</label>
                        <input type="text" name="beneficiary_occupation" id="beneficiary_occupation"
                            class="form-control" value="{{ old('beneficiary_occupation') }}"
                            placeholder="e.g. Magsasaka, Vendor, None">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="beneficiary_monthly_salary">Buwanang Kita (Monthly
                            Salary)</label>
                        <input type="number" step="0.01" min="0" name="beneficiary_monthly_salary"
                            id="beneficiary_monthly_salary" class="form-control"
                            value="{{ old('beneficiary_monthly_salary') }}" placeholder="0.00">
                    </div>
                </div>

                <!-- REPRESENTATIVE TOGGLE -->
                <div class="toggle-card">
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" name="has_representative"
                            id="has_representative" value="1" {{ old('has_representative') ? 'checked' : '' }}
                            onchange="toggleRepresentativeSection(this.checked)">
                        <label class="form-check-label fw-bold text-dark ms-2" for="has_representative">
                            May Kinatawan ba ang Benepisyaryo? (Has Representative?)
                        </label>
                        <div class="text-muted small ms-2">I-tsek ito kung ibang tao ang lumapit para sa benepisyaryo.
                        </div>
                    </div>
                </div>

                <!-- REPRESENTATIVE DETAILS (CONDITIONAL) -->
                <div id="representativeSection"
                    class="{{ old('has_representative') ? '' : 'rep-card-disabled d-none' }}">
                    <h6 class="fw-bold text-secondary mb-3"><i class="fas fa-user-friends me-2"></i> Impormasyon ng
                        Kinatawan (Representative)</h6>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label" for="rep_last_name">Apelyido <span
                                    class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_last_name" id="rep_last_name" class="form-control rep-input"
                                value="{{ old('rep_last_name') }}" placeholder="Apelyido">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="rep_first_name">Unang Pangalan <span
                                    class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_first_name" id="rep_first_name" class="form-control rep-input"
                                value="{{ old('rep_first_name') }}" placeholder="Pangalan">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="rep_middle_name">Gitnang Pangalan</label>
                            <input type="text" name="rep_middle_name" id="rep_middle_name"
                                class="form-control rep-input" value="{{ old('rep_middle_name') }}"
                                placeholder="Gitnang Pangalan">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="rep_extension_name">Extension Name</label>
                            <input type="text" name="rep_extension_name" id="rep_extension_name"
                                class="form-control rep-input" value="{{ old('rep_extension_name') }}"
                                placeholder="Jr., Sr.">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="rep_street_address">Tirahan <span
                                    class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_street_address" id="rep_street_address"
                                class="form-control rep-input" value="{{ old('rep_street_address') }}"
                                placeholder="House No., Street">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="rep_barangay">Barangay <span
                                    class="required-star rep-star">*</span></label>
                            <select name="rep_barangay" id="rep_barangay" class="form-select rep-input">
                                <option value="">Pumili ng Barangay</option>
                                @foreach($barangays as $brgy)
                                <option value="{{ $brgy }}" {{ old('rep_barangay')==$brgy ? 'selected' : '' }}>{{ $brgy
                                    }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label" for="rep_contact_number">Telepono <span
                                    class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_contact_number" id="rep_contact_number"
                                class="form-control rep-input" value="{{ old('rep_contact_number') }}"
                                placeholder="09XXXXXXXXX" maxlength="11">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="rep_birthday">Araw ng Kapanganakan <span
                                    class="required-star rep-star">*</span></label>
                            <input type="date" name="rep_birthday" id="rep_birthday" class="form-control rep-input"
                                value="{{ old('rep_birthday') }}" max="{{ date('Y-m-d') }}"
                                onchange="calculateRepAge()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="rep_age">Edad <span
                                    class="required-star rep-star">*</span></label>
                            <input type="number" name="rep_age" id="rep_age" class="form-control rep-input"
                                value="{{ old('rep_age') }}" min="0" max="150">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="rep_sex">Kasarian <span
                                    class="required-star rep-star">*</span></label>
                            <select name="rep_sex" id="rep_sex" class="form-select rep-input">
                                <option value="">Piliin</option>
                                <option value="Male" {{ old('rep_sex')=='Male' ? 'selected' : '' }}>Lalaki</option>
                                <option value="Female" {{ old('rep_sex')=='Female' ? 'selected' : '' }}>Babae</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="rep_relationship">Relasyon sa Benepisyaryo <span
                                    class="required-star rep-star">*</span></label>
                            <select name="rep_relationship" id="rep_relationship" class="form-select rep-input">
                                <option value="">Piliin</option>
                                @foreach($relationships as $rel)
                                <option value="{{ $rel }}" {{ old('rep_relationship')==$rel ? 'selected' : '' }}>{{ $rel
                                    }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 2: KATEGORYA NG BENEPISYARYO & ASSESSMENT -->
        <div class="form-card">
            <div class="card-body p-4">
                <span class="section-tag">Seksyon II</span>
                <h5 class="fw-bold text-dark mb-4">KATEGORYA NG BENEPISYARYO &amp; ASSESSMENT</h5>

                <div class="row g-4">
                    <!-- Left: Categories -->
                    <div class="col-md-5 border-end pe-md-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-tags me-2 text-primary"></i> Kategorya
                            (Beneficiary Category)</h6>
                        @php
                        $catOptions = [
                        'Solo Parents',
                        'Indigenous People',
                        'PWD',
                        '4PS DSWD Beneficiary',
                        'LGBTQIA+',
                        'Psychosocial/Mental/Learning Disability',
                        'Stateless Person/Asylum Seekers/Refugees',
                        'Senior Citizen',
                        'Indigent Resident'
                        ];
                        $oldCats = old('beneficiary_categories', []);
                        @endphp

                        @foreach($catOptions as $cOpt)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="beneficiary_categories[]"
                                value="{{ $cOpt }}" id="cat_{{ Str::slug($cOpt) }}" {{ in_array($cOpt, $oldCats)
                                ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="cat_{{ Str::slug($cOpt) }}">{{ $cOpt
                                }}</label>
                        </div>
                        @endforeach

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="beneficiary_categories[]"
                                value="Others" id="cat_others" {{ in_array('Others', $oldCats) ? 'checked' : '' }}
                                onchange="toggleCategoryOtherText()">
                            <label class="form-check-label small fw-semibold" for="cat_others">Iba pa (Others)</label>
                        </div>
                        <input type="text" name="beneficiary_category_other" id="beneficiary_category_other_input"
                            class="form-control mt-2 {{ in_array('Others', $oldCats) ? '' : 'd-none' }}"
                            value="{{ old('beneficiary_category_other') }}"
                            placeholder="Tukuyin ang ibang kategorya...">
                    </div>

                    <!-- Right: Social Worker's Assessment -->
                    <div class="col-md-7 ps-md-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-clipboard-check me-2 text-primary"></i>
                            Social Worker's Assessment (Pagsusuri)</h6>
                        <textarea name="social_worker_assessment" id="social_worker_assessment" rows="8"
                            class="form-control"
                            placeholder="Isulat ang orihinal na pagsusuri, sitwasyon ng kliyente, at rekomendasyon mula sa historical intake...">{{ old('social_worker_assessment') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: KOMPOSISYON NG PAMILYA -->
        <div class="form-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="section-tag mb-1">Seksyon III</span>
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-users me-2 text-primary"></i> KOMPOSISYON NG
                            PAMILYA (Family Composition)</h5>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold"
                        onclick="addFamilyRow()">
                        <i class="fas fa-plus me-1"></i> Add Family Member
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle fam-table mb-0" id="familyTable">
                        <thead>
                            <tr>
                                <th>Buong Pangalan <span class="text-muted">(Complete Name)</span></th>
                                <th style="width: 22%;">Relasyon sa Benepisyaryo</th>
                                <th style="width: 12%;">Edad <span class="text-muted">(Age)</span></th>
                                <th style="width: 20%;">Trabaho <span class="text-muted">(Occupation)</span></th>
                                <th style="width: 18%;">Buwanang Kita</th>
                                <th style="width: 50px;" class="text-center"><i class="fas fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody id="familyTableBody">
                            @php
                            $oldFam = old('family_composition', [ ['name' => '', 'relationship' => '', 'age' => '',
                            'occupation' => '', 'salary' => ''] ]);
                            @endphp
                            @foreach($oldFam as $index => $fam)
                            <tr>
                                <td>
                                    <input type="text" name="family_composition[{{ $index }}][name]"
                                        class="form-control form-control-sm" value="{{ $fam['name'] ?? '' }}"
                                        placeholder="Full Name">
                                </td>
                                <td>
                                    <input type="text" name="family_composition[{{ $index }}][relationship]"
                                        class="form-control form-control-sm" value="{{ $fam['relationship'] ?? '' }}"
                                        placeholder="e.g. Asawa, Anak">
                                </td>
                                <td>
                                    <input type="number" min="0" name="family_composition[{{ $index }}][age]"
                                        class="form-control form-control-sm" value="{{ $fam['age'] ?? '' }}"
                                        placeholder="Edad">
                                </td>
                                <td>
                                    <input type="text" name="family_composition[{{ $index }}][occupation]"
                                        class="form-control form-control-sm" value="{{ $fam['occupation'] ?? '' }}"
                                        placeholder="Trabaho">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0"
                                        name="family_composition[{{ $index }}][salary]"
                                        class="form-control form-control-sm" value="{{ $fam['salary'] ?? '' }}"
                                        placeholder="0.00">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0"
                                        onclick="removeFamilyRow(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SECTION 4: ASSISTANCE PURPOSE & INTERVIEW DETAILS -->
        <div class="form-card">
            <div class="card-body p-4">
                <span class="section-tag">Seksyon IV</span>
                <h5 class="fw-bold text-dark mb-4"><i class="fas fa-hand-holding-heart me-2 text-primary"></i>
                    ASSISTANCE PURPOSE &amp; SIGNATORIES</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="assistance_purpose_select">Medical Condition / Assistance
                            Purpose</label>
                        <select name="assistance_purpose" id="assistance_purpose_select" class="form-select"
                            onchange="togglePurposeOtherInput()">
                            <option value="">Pumili ng Kondisyon / Layunin ng Tulong</option>
                            @foreach($medicalConditions as $cond)
                            <option value="{{ $cond }}" {{ old('assistance_purpose')==$cond ? 'selected' : '' }}>{{
                                $cond }}</option>
                            @endforeach
                            <option value="Other Medical Conditions" {{
                                old('assistance_purpose')=='Other Medical Conditions' ? 'selected' : '' }}>Other Medical
                                Conditions / Iba pa</option>
                        </select>
                        <input type="text" name="purpose_other" id="purpose_other_input"
                            class="form-control mt-2 {{ old('assistance_purpose') == 'Other Medical Conditions' ? '' : 'd-none' }}"
                            value="{{ old('purpose_other') }}" placeholder="Tukuyin ang medical condition o detalye...">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="interviewed_by">Interviewed by <span
                                class="text-muted">(Interviewer)</span></label>
                        <select name="interviewed_by" id="interviewed_by" class="form-select">
                            <option value="">Pumili ng Step 1 Officer</option>
                            @foreach($step1Officers ?? [] as $officer)
                            <option value="{{ $officer->name }}" {{ old('interviewed_by') == $officer->name ? 'selected' : '' }}>
                                {{ $officer->name }}
                            </option>
                            @endforeach
                            @if(old('interviewed_by') && (!isset($step1Officers) || !$step1Officers->contains('name', old('interviewed_by'))))
                            <option value="{{ old('interviewed_by') }}" selected>{{ old('interviewed_by') }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="reviewed_by">Reviewed by <span
                                class="text-muted">(Supervisor/Head)</span></label>
                        <input type="text" name="reviewed_by" id="reviewed_by" class="form-control"
                            value="{{ old('reviewed_by') }}" placeholder="Pangalan ng Tagasuri">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit & Actions Bar -->
        <div class="d-flex justify-content-end align-items-center gap-3 mb-5 flex-wrap">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-light border px-4 py-2 rounded-3 fw-semibold">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary px-5 py-2.5 fw-bold rounded-3 shadow"
                style="background: #1A237E; border: none;">
                <i class="fas fa-save me-2"></i> Save Historical Record
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    // Uppercase text transformation
    document.addEventListener('input', function (e) {
        const el = e.target;
        if (el.matches?.(':is(input[type="text"], input:not([type]), textarea):not([readonly])')) {
            const { selectionStart: s, selectionEnd: end, value } = el;
            if (value !== value.toUpperCase()) {
                el.value = value.toUpperCase();
                el.setSelectionRange?.(s, end);
            }
        }
    });

    // Dynamic Beneficiary Age Calculation based on Original Intake Date
    function calculateBeneficiaryAge() {
        const bdayInput = document.getElementById('beneficiary_birthday').value;
        const intakeDateInput = document.getElementById('date_processed').value;
        const ageInput = document.getElementById('beneficiary_age');

        if (!bdayInput) return;

        const birthDate = new Date(bdayInput);
        const refDate = intakeDateInput ? new Date(intakeDateInput) : new Date();

        let age = refDate.getFullYear() - birthDate.getFullYear();
        const m = refDate.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && refDate.getDate() < birthDate.getDate())) {
            age--;
        }
        if (age >= 0 && !isNaN(age)) {
            ageInput.value = age;
        }

        // Trigger duplicate check when relevant fields are updated
        triggerDuplicateCheck();
    }

    // Dynamic Representative Age Calculation based on Original Intake Date
    function calculateRepAge() {
        const bdayInput = document.getElementById('rep_birthday').value;
        const intakeDateInput = document.getElementById('date_processed').value;
        const ageInput = document.getElementById('rep_age');

        if (!bdayInput) return;

        const birthDate = new Date(bdayInput);
        const refDate = intakeDateInput ? new Date(intakeDateInput) : new Date();

        let age = refDate.getFullYear() - birthDate.getFullYear();
        const m = refDate.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && refDate.getDate() < birthDate.getDate())) {
            age--;
        }
        if (age >= 0 && !isNaN(age)) {
            ageInput.value = age;
        }
    }

    // Toggle Representative Section
    function toggleRepresentativeSection(isChecked) {
        const repSec = document.getElementById('representativeSection');
        if (!repSec) return;
        if (isChecked) {
            repSec.classList.remove('rep-card-disabled', 'd-none');
            repSec.querySelectorAll('.rep-input').forEach(el => el.removeAttribute('disabled'));
        } else {
            repSec.classList.add('rep-card-disabled', 'd-none');
            repSec.querySelectorAll('.rep-input').forEach(el => {
                el.setAttribute('disabled', 'disabled');
            });
        }
    }

    // Toggle Claiming Date
    function toggleClaimDate(status) {
        const wrapper = document.getElementById('claimingDateWrapper');
        if (!wrapper) return;
        if (status === 'Claimed') {
            wrapper.classList.remove('opacity-50');
        } else {
            wrapper.classList.add('opacity-50');
        }
    }

    // Toggle Category Others Text
    function toggleCategoryOtherText() {
        const check = document.getElementById('cat_others');
        const text = document.getElementById('beneficiary_category_other_input');
        if (check && text) {
            if (check.checked) {
                text.classList.remove('d-none');
                text.focus();
            } else {
                text.classList.add('d-none');
                text.value = '';
            }
        }
    }

    // Toggle Purpose Others Text
    function togglePurposeOtherInput() {
        const sel = document.getElementById('assistance_purpose_select');
        const text = document.getElementById('purpose_other_input');
        if (sel && text) {
            if (sel.value === 'Other Medical Conditions') {
                text.classList.remove('d-none');
                text.focus();
            } else {
                text.classList.add('d-none');
                text.value = '';
            }
        }
    }

    // Add Family Composition Row
    let famRowIdx = {{ count($oldFam) }};
    function addFamilyRow() {
        const tbody = document.getElementById('familyTableBody');
        if (!tbody) return;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="family_composition[${famRowIdx}][name]" class="form-control form-control-sm" placeholder="Full Name"></td>
            <td><input type="text" name="family_composition[${famRowIdx}][relationship]" class="form-control form-control-sm" placeholder="e.g. Asawa, Anak"></td>
            <td><input type="number" min="0" name="family_composition[${famRowIdx}][age]" class="form-control form-control-sm" placeholder="Edad"></td>
            <td><input type="text" name="family_composition[${famRowIdx}][occupation]" class="form-control form-control-sm" placeholder="Trabaho"></td>
            <td><input type="number" step="0.01" min="0" name="family_composition[${famRowIdx}][salary]" class="form-control form-control-sm" placeholder="0.00"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeFamilyRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        famRowIdx++;
    }

    function removeFamilyRow(btn) {
        const row = btn.closest('tr');
        const tbody = document.getElementById('familyTableBody');
        if (tbody.querySelectorAll('tr').length > 1) {
            row.remove();
        } else {
            row.querySelectorAll('input').forEach(i => i.value = '');
        }
    }

    // Real-time 6-Month Duplicate Check Relative to Historical Intake Date
    let checkTimeout = null;
    function triggerDuplicateCheck() {
        clearTimeout(checkTimeout);
        checkTimeout = setTimeout(performDuplicateCheck, 400);
    }

    function performDuplicateCheck() {
        const fName = document.getElementById('beneficiary_first_name')?.value?.trim();
        const lName = document.getElementById('beneficiary_last_name')?.value?.trim();
        const mName = document.getElementById('beneficiary_middle_name')?.value?.trim();
        const bday = document.getElementById('beneficiary_birthday')?.value;
        const intakeDate = document.getElementById('date_processed')?.value;

        if (!fName || fName.length < 2 || !lName || lName.length < 2) {
            const card = document.getElementById('duplicateAlertCard');
            if (card && !card.dataset.serverAlert) card.classList.add('d-none');
            return;
        }

        const formData = new FormData();
        formData.append('beneficiary_first_name', fName);
        formData.append('beneficiary_last_name', lName);
        formData.append('beneficiary_middle_name', mName || '');
        formData.append('beneficiary_birthday', bday || '');
        formData.append('date_processed', intakeDate || '');

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch("{{ route('admin.historical-data.financial-intake.check-duplicate') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const card = document.getElementById('duplicateAlertCard');
            const warnText = document.getElementById('duplicateWarningText');
            const matchList = document.getElementById('duplicateMatchesList');

            if (data.is_duplicate) {
                card.classList.remove('d-none');
                warnText.innerText = data.warning_message || '6-Month policy restriction violated.';
                if (data.matches && data.matches.length > 0) {
                    let html = '';
                    data.matches.forEach(m => {
                        html += `<div class="p-2 mb-1 rounded border bg-white small">
                            <strong>Control No:</strong> ${m.control_number} | 
                            <strong>Beneficiary:</strong> ${m.beneficiary_name} | 
                            <strong>Intake Date:</strong> ${m.date_processed}
                        </div>`;
                    });
                    matchList.innerHTML = html;
                }
            } else {
                card.classList.add('d-none');
            }
        })
        .catch(() => {});
    }

    document.getElementById('beneficiary_first_name')?.addEventListener('input', triggerDuplicateCheck);
    document.getElementById('beneficiary_last_name')?.addEventListener('input', triggerDuplicateCheck);
    document.getElementById('beneficiary_middle_name')?.addEventListener('input', triggerDuplicateCheck);
    document.getElementById('date_processed')?.addEventListener('change', () => {
        calculateBeneficiaryAge();
        calculateRepAge();
        triggerDuplicateCheck();
    });
</script>
@endpush