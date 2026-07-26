@extends('layouts.financial')

@section('title', 'Edit General Intake Sheet - MSWDO Silang')
@section('page-title', 'Edit General Intake Sheet')

@section('page-styles')
<style>
    .form-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        margin-bottom: 1.5rem;
    }
    .form-header-bar {
        background: linear-gradient(135deg, #1A237E 0%, #283593 100%);
        color: #ffffff;
        padding: 1.25rem 1.5rem;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
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
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        padding: 0.6rem 0.85rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #1A237E;
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.15);
    }
    .form-control[readonly] {
        background-color: #F8FAFC;
        color: #64748B;
    }
    .rep-card-disabled {
        opacity: 0.6;
        pointer-events: none;
    }
    .toggle-card {
        background-color: #F8FAFC;
        border: 1px dashed #CBD5E1;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
    }
    .dswd-notice-bar {
        background-color: #7F1D1D;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.85rem;
        text-align: center;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .fam-table th {
        background-color: #F1F5F9;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #475569;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Header / Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1A237E;">Edit Intake Sheet: {{ $intake->control_number }}</h4>
            <p class="text-muted small mb-0">Update intake sheet details for {{ $intake->beneficiary_full_name }}.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.beneficiary-intake.show', $intake) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-eye me-1"></i> View Record
            </a>
            <a href="{{ route('admin.beneficiary-intake.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-lg me-3"></i>
                <div>
                    <strong>Please check the form errors below:</strong>
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

    <form action="{{ route('admin.beneficiary-intake.update', $intake) }}" method="POST" id="editIntakeForm">
        @csrf
        @method('PUT')

        <!-- HEADER CONTROL DETAILS -->
        <div class="form-card">
            <div class="form-header-bar d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fas fa-file-alt me-2"></i> GENERAL INTAKE SHEET DETAILS</span>
                <span class="badge bg-white text-dark fw-bold px-3 py-2 rounded-pill">{{ $intake->control_number }}</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Control Number <span class="required-star">*</span></label>
                        <input type="text" name="control_number" class="form-control" value="{{ old('control_number', $intake->control_number) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Client Status <span class="required-star">*</span></label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="client_type" id="type_new" value="New" {{ old('client_type', $intake->client_type ?? 'New') == 'New' ? 'checked' : '' }} required>
                                <label class="form-check-label fw-bold" for="type_new">New</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="client_type" id="type_returning" value="Returning" {{ old('client_type', $intake->client_type) == 'Returning' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="type_returning">Returning</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date <span class="required-star">*</span></label>
                        <input type="date" name="date_processed" class="form-control" value="{{ old('date_processed', $intake->date_processed ? $intake->date_processed->format('Y-m-d') : date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Time Start</label>
                        <input type="text" name="time_start" class="form-control" value="{{ old('time_start', $intake->time_start) }}" placeholder="e.g. 09:00 AM">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Time End</label>
                        <input type="text" name="time_end" class="form-control" value="{{ old('time_end', $intake->time_end) }}" placeholder="e.g. 09:30 AM">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 1: IMPORMASYON NG BENEPISYARYO -->
        <div class="form-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                    <div>
                        <span class="section-tag">Section I</span>
                        <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-user me-2 text-primary"></i> IMPORMASYON NG BENEPISYARYO <span class="text-muted fs-6">(Beneficiary's Identifying Information)</span></h5>
                    </div>
                    <span class="text-muted small"><span class="required-star">*</span> Required fields</span>
                </div>

                <!-- Names -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Apelyido (Last Name) <span class="required-star">*</span></label>
                        <input type="text" name="beneficiary_last_name" class="form-control" value="{{ old('beneficiary_last_name', $intake->beneficiary_last_name) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unang Pangalan (First Name) <span class="required-star">*</span></label>
                        <input type="text" name="beneficiary_first_name" class="form-control" value="{{ old('beneficiary_first_name', $intake->beneficiary_first_name) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gitnang Pangalan (Middle Name)</label>
                        <input type="text" name="beneficiary_middle_name" class="form-control" value="{{ old('beneficiary_middle_name', $intake->beneficiary_middle_name) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ext. (Sr., Jr., III)</label>
                        <select name="beneficiary_extension_name" class="form-select">
                            <option value="">None</option>
                            <option value="Jr." {{ old('beneficiary_extension_name', $intake->beneficiary_extension_name) == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                            <option value="Sr." {{ old('beneficiary_extension_name', $intake->beneficiary_extension_name) == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                            <option value="III" {{ old('beneficiary_extension_name', $intake->beneficiary_extension_name) == 'III' ? 'selected' : '' }}>III</option>
                            <option value="IV" {{ old('beneficiary_extension_name', $intake->beneficiary_extension_name) == 'IV' ? 'selected' : '' }}>IV</option>
                        </select>
                    </div>
                </div>

                <!-- Address -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">House No. / Street / Purok <span class="required-star">*</span></label>
                        <input type="text" name="beneficiary_street_address" class="form-control" value="{{ old('beneficiary_street_address', $intake->beneficiary_street_address) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Barangay <span class="required-star">*</span></label>
                        <select name="beneficiary_barangay" class="form-select" required>
                            <option value="">Select Barangay</option>
                            @foreach($barangays as $brgy)
                                <option value="{{ $brgy }}" {{ old('beneficiary_barangay', $intake->beneficiary_barangay) == $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">City/Municipality</label>
                        <input type="text" name="beneficiary_city" class="form-control" value="SILANG" readonly>
                    </div>
                    <div class="col-md-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label">Province/District</label>
                                <input type="text" name="beneficiary_province" class="form-control" value="CAVITE" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Region</label>
                                <input type="text" name="beneficiary_region" class="form-control" value="IV-A" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Demographics -->
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Numero ng Telepono (Mobile No.) <span class="required-star">*</span></label>
                        <input type="text" name="beneficiary_contact_number" class="form-control" value="{{ old('beneficiary_contact_number', $intake->beneficiary_contact_number) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kapanganakan (MM/DD/YYYY) <span class="required-star">*</span></label>
                        <input type="text" name="beneficiary_birthday" id="beneficiary_birthday" class="form-control" value="{{ old('beneficiary_birthday', $intake->beneficiary_birthday ? $intake->beneficiary_birthday->format('m/d/Y') : '') }}" placeholder="MM/DD/YYYY" maxlength="10" required oninput="formatAndCalculateAge(this, 'beneficiary_age')">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Edad (Age) <span class="required-star">*</span></label>
                        <input type="number" name="beneficiary_age" id="beneficiary_age" class="form-control" value="{{ old('beneficiary_age', $intake->beneficiary_age) }}" readonly required placeholder="Edad">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kasarian (Gender) <span class="required-star">*</span></label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beneficiary_sex" id="sex_male" value="Male" {{ old('beneficiary_sex', $intake->beneficiary_sex) == 'Male' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="sex_male">M</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beneficiary_sex" id="sex_female" value="Female" {{ old('beneficiary_sex', $intake->beneficiary_sex) == 'Female' ? 'checked' : '' }}>
                                <label class="form-check-label" for="sex_female">F</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Civil Status (Katayuan) <span class="required-star">*</span></label>
                        <select name="beneficiary_civil_status" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Single" {{ old('beneficiary_civil_status', $intake->beneficiary_civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('beneficiary_civil_status', $intake->beneficiary_civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Widowed" {{ old('beneficiary_civil_status', $intake->beneficiary_civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Separated" {{ old('beneficiary_civil_status', $intake->beneficiary_civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                            <option value="Cohabiting" {{ old('beneficiary_civil_status', $intake->beneficiary_civil_status) == 'Cohabiting' ? 'selected' : '' }}>Cohabiting</option>
                        </select>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Trabaho (Occupation)</label>
                        <input type="text" name="beneficiary_occupation" class="form-control" value="{{ old('beneficiary_occupation', $intake->beneficiary_occupation) }}" placeholder="Trabaho">
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Buwanang Kita (Monthly Salary)</label>
                        <input type="number" step="0.01" min="0" name="beneficiary_monthly_salary" class="form-control" value="{{ old('beneficiary_monthly_salary', $intake->beneficiary_monthly_salary) }}" placeholder="0.00">
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 2: IMPORMASYON NG KINATAWAN -->
        <div class="form-card">
            <div class="card-body p-4">
                
                <div class="toggle-card d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark"><i class="fas fa-user-friends me-2 text-primary"></i> IMPORMASYON NG KINATAWAN <span class="text-muted fs-6">(Representative's Identifying Information)</span></h6>
                        <p class="text-muted small mb-0">Check if representative is filing on behalf of the beneficiary.</p>
                    </div>
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" role="switch" name="has_representative" id="has_representative" value="1" {{ old('has_representative', $intake->has_representative) ? 'checked' : '' }} onchange="toggleRepresentativeSection()">
                        <label class="form-check-label fw-bold fs-6 ms-2" for="has_representative">Has Representative</label>
                    </div>
                </div>

                <div id="representative_section" class="{{ old('has_representative', $intake->has_representative) ? '' : 'rep-card-disabled' }}">

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Apelyido (Last Name) <span class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_last_name" class="form-control rep-field" value="{{ old('rep_last_name', $intake->rep_last_name) }}" placeholder="Apelyido">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Unang Pangalan (First Name) <span class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_first_name" class="form-control rep-field" value="{{ old('rep_first_name', $intake->rep_first_name) }}" placeholder="Unang Pangalan">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Gitnang Pangalan (Middle Name)</label>
                            <input type="text" name="rep_middle_name" class="form-control rep-field" value="{{ old('rep_middle_name', $intake->rep_middle_name) }}" placeholder="Gitnang Pangalan">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ext. (Sr., Jr., III)</label>
                            <select name="rep_extension_name" class="form-select rep-field">
                                <option value="">None</option>
                                <option value="Jr." {{ old('rep_extension_name', $intake->rep_extension_name) == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                <option value="Sr." {{ old('rep_extension_name', $intake->rep_extension_name) == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                <option value="III" {{ old('rep_extension_name', $intake->rep_extension_name) == 'III' ? 'selected' : '' }}>III</option>
                                <option value="IV" {{ old('rep_extension_name', $intake->rep_extension_name) == 'IV' ? 'selected' : '' }}>IV</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">House No. / Street / Purok <span class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_street_address" class="form-control rep-field" value="{{ old('rep_street_address', $intake->rep_street_address) }}" placeholder="House No., Street">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Barangay <span class="required-star rep-star">*</span></label>
                            <select name="rep_barangay" class="form-select rep-field">
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $brgy)
                                    <option value="{{ $brgy }}" {{ old('rep_barangay', $intake->rep_barangay) == $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">City/Municipality</label>
                            <input type="text" class="form-control" value="SILANG" readonly>
                        </div>
                        <div class="col-md-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Province</label>
                                    <input type="text" class="form-control" value="CAVITE" readonly>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Region</label>
                                    <input type="text" class="form-control" value="IV-A" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Numero ng Telepono <span class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_contact_number" class="form-control rep-field" value="{{ old('rep_contact_number', $intake->rep_contact_number) }}" placeholder="09XXXXXXXXX">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kapanganakan (MM/DD/YYYY) <span class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_birthday" id="rep_birthday" class="form-control rep-field" value="{{ old('rep_birthday', $intake->rep_birthday ? $intake->rep_birthday->format('m/d/Y') : '') }}" placeholder="MM/DD/YYYY" maxlength="10" oninput="formatAndCalculateAge(this, 'rep_age')">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Edad (Age) <span class="required-star rep-star">*</span></label>
                            <input type="number" name="rep_age" id="rep_age" class="form-control rep-field" value="{{ old('rep_age', $intake->rep_age) }}" readonly placeholder="Edad">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Kasarian (Gender) <span class="required-star rep-star">*</span></label>
                            <select name="rep_sex" class="form-select rep-field">
                                <option value="">Select</option>
                                <option value="Male" {{ old('rep_sex', $intake->rep_sex) == 'Male' ? 'selected' : '' }}>M</option>
                                <option value="Female" {{ old('rep_sex', $intake->rep_sex) == 'Female' ? 'selected' : '' }}>F</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Civil Status <span class="required-star rep-star">*</span></label>
                            <select name="rep_civil_status" class="form-select rep-field">
                                <option value="">Select</option>
                                <option value="Single" {{ old('rep_civil_status', $intake->rep_civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('rep_civil_status', $intake->rep_civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Widowed" {{ old('rep_civil_status', $intake->rep_civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="Separated" {{ old('rep_civil_status', $intake->rep_civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Trabaho (Occupation)</label>
                            <input type="text" name="rep_occupation" class="form-control rep-field" value="{{ old('rep_occupation', $intake->rep_occupation) }}" placeholder="Trabaho">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Buwanang Kita (Monthly Salary)</label>
                            <input type="number" step="0.01" min="0" name="rep_monthly_salary" class="form-control rep-field" value="{{ old('rep_monthly_salary', $intake->rep_monthly_salary) }}" placeholder="0.00">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Relasyon sa Benepisyaryo <span class="required-star rep-star">*</span></label>
                            <select name="rep_relationship" class="form-select rep-field">
                                <option value="">Select Relationship</option>
                                @foreach($relationships as $rel)
                                    <option value="{{ $rel }}" {{ old('rep_relationship', $intake->rep_relationship) == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- DSWD OFFICIAL USE ONLY SECTION -->
        <div class="form-card">
            <div class="card-body p-4">
                <div class="dswd-notice-bar">
                    Huwag susulatan ang DSWD lamang ang pwede gumamit <span class="fw-normal">(Do not write below this part for DSWD's use only)</span>
                </div>

                <div class="row g-4">
                    <!-- Left Column: Beneficiary Category Checkboxes -->
                    <div class="col-md-5 border-end pe-md-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-list-check me-2 text-primary"></i> Beneficiary Category</h6>
                        <p class="text-muted small mb-3">Specify Sub-Category (Check all applicable):</p>
                        
                        @php
                            $catOptions = [
                                'Solo Parents',
                                'Indigenous People',
                                'PWD',
                                '4PS DSWD Beneficiary',
                                'LGBTQIA+',
                                'Psychosocial/Mental/Learning Disability',
                                'Stateless Person/Asylum Seekers/Refugees',
                            ];
                            $savedCats = old('beneficiary_categories', $intake->beneficiary_categories ?? []);
                        @endphp

                        @foreach($catOptions as $cOpt)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="beneficiary_categories[]" value="{{ $cOpt }}" id="cat_{{ Str::slug($cOpt) }}" {{ in_array($cOpt, $savedCats) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold" for="cat_{{ Str::slug($cOpt) }}">{{ $cOpt }}</label>
                            </div>
                        @endforeach

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="beneficiary_categories[]" value="Others" id="cat_others" {{ in_array('Others', $savedCats) ? 'checked' : '' }} onchange="toggleCategoryOtherText()">
                            <label class="form-check-label small fw-semibold" for="cat_others">Others</label>
                        </div>
                        <input type="text" name="beneficiary_category_other" id="beneficiary_category_other_input" class="form-control mt-2 {{ in_array('Others', $savedCats) ? '' : 'd-none' }}" value="{{ old('beneficiary_category_other', $intake->beneficiary_category_other) }}" placeholder="Specify other category...">
                    </div>

                    <!-- Right Column: Social Worker's Assessment -->
                    <div class="col-md-7 ps-md-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-clipboard-check me-2 text-primary"></i> Social Worker's Assessment</h6>
                        <textarea name="social_worker_assessment" rows="7" class="form-control" placeholder="Write initial assessment, client situation, and social worker recommendations here...">{{ old('social_worker_assessment', $intake->social_worker_assessment) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: KOMPOSISYON NG PAMILYA (Family Composition) -->
        <div class="form-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-users me-2 text-primary"></i> KOMPOSISYON NG PAMILYA <span class="text-muted fs-6">(Family Composition)</span></h6>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold" onclick="addFamilyRow()">
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
                                $famData = old('family_composition', $intake->family_composition ?? [ ['name' => '', 'relationship' => '', 'age' => '', 'occupation' => '', 'salary' => ''] ]);
                                if(empty($famData)) {
                                    $famData = [ ['name' => '', 'relationship' => '', 'age' => '', 'occupation' => '', 'salary' => ''] ];
                                }
                            @endphp
                            @foreach($famData as $index => $fam)
                            <tr>
                                <td>
                                    <input type="text" name="family_composition[{{ $index }}][name]" class="form-control form-control-sm" value="{{ $fam['name'] ?? '' }}" placeholder="Full Name">
                                </td>
                                <td>
                                    <input type="text" name="family_composition[{{ $index }}][relationship]" class="form-control form-control-sm" value="{{ $fam['relationship'] ?? '' }}" placeholder="e.g. Spouse, Son">
                                </td>
                                <td>
                                    <input type="number" min="0" name="family_composition[{{ $index }}][age]" class="form-control form-control-sm" value="{{ $fam['age'] ?? '' }}" placeholder="Edad">
                                </td>
                                <td>
                                    <input type="text" name="family_composition[{{ $index }}][occupation]" class="form-control form-control-sm" value="{{ $fam['occupation'] ?? '' }}" placeholder="Trabaho">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="family_composition[{{ $index }}][salary]" class="form-control form-control-sm" value="{{ $fam['salary'] ?? '' }}" placeholder="0.00">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeFamilyRow(this)"><i class="fas fa-trash"></i></button>
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
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-hand-holding-heart me-2 text-primary"></i> ASSISTANCE PURPOSE / MEDICAL CONDITION &amp; INTERVIEW DETAILS</h6>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Assistance Purpose / Medical Condition</label>
                        @php
                            $savedPurpose = old('assistance_purpose', $intake->assistance_purpose);
                            $isCustom = $savedPurpose && !in_array($savedPurpose, $medicalConditions);
                        @endphp
                        <select name="assistance_purpose" id="assistance_purpose_select" class="form-select" onchange="togglePurposeOtherInput()">
                            <option value="">Select Medical Condition / Assistance Purpose</option>
                            @foreach($medicalConditions as $cond)
                                <option value="{{ $cond }}" {{ ($savedPurpose == $cond || ($isCustom && $cond == 'Other Medical Conditions')) ? 'selected' : '' }}>{{ $cond }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="purpose_other" id="purpose_other_input" class="form-control mt-2 {{ ($savedPurpose == 'Other Medical Conditions' || $isCustom) ? '' : 'd-none' }}" value="{{ old('purpose_other', $isCustom ? $savedPurpose : $intake->purpose_other) }}" placeholder="Specify medical condition or details...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Interviewed by <span class="text-muted">(MSWD Personnel / Social Worker)</span></label>
                        <input type="text" name="interviewed_by" class="form-control" value="{{ old('interviewed_by', $intake->interviewed_by ?? $intake->encoderUser?->name ?? session('admin_user_name')) }}" placeholder="Printed Name of Interviewer">
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Submit Bar -->
        <div class="d-flex justify-content-end gap-3 mb-5">
            <a href="{{ route('admin.beneficiary-intake.show', $intake) }}" class="btn btn-light border px-4 py-2 rounded-3">Cancel</a>
            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-3" style="background: #1A237E; border: none;">
                <i class="fas fa-save me-2"></i> Update General Intake Sheet
            </button>
        </div>

    </form>
</div>
@endsection

@section('page-scripts')
<script>
    function formatAndCalculateAge(inputEl, ageInputId) {
        let val = inputEl.value.replace(/\D/g, '');
        if (val.length >= 2 && val.length < 4) {
            val = val.slice(0, 2) + '/' + val.slice(2);
        } else if (val.length >= 4) {
            val = val.slice(0, 2) + '/' + val.slice(2, 4) + '/' + val.slice(4, 8);
        }
        inputEl.value = val;

        const ageEl = document.getElementById(ageInputId);
        if (val.length === 10) {
            const parts = val.split('/');
            const month = parseInt(parts[0], 10) - 1;
            const day = parseInt(parts[1], 10);
            const year = parseInt(parts[2], 10);

            if (!isNaN(month) && !isNaN(day) && !isNaN(year) && year > 1900 && month >= 0 && month <= 11 && day >= 1 && day <= 31) {
                const birthDate = new Date(year, month, day);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                ageEl.value = age >= 0 ? age : '';
                return;
            }
        }
        ageEl.value = '';
    }

    function toggleCategoryOtherText() {
        const catOthers = document.getElementById('cat_others');
        const otherInput = document.getElementById('beneficiary_category_other_input');
        if (catOthers && catOthers.checked) {
            otherInput.classList.remove('d-none');
        } else {
            otherInput.classList.add('d-none');
            otherInput.value = '';
        }
    }

    function togglePurposeOtherInput() {
        const select = document.getElementById('assistance_purpose_select');
        const otherInput = document.getElementById('purpose_other_input');
        if (select && (select.value === 'Other Medical Conditions' || select.value === 'Others')) {
            otherInput.classList.remove('d-none');
        } else if (otherInput) {
            otherInput.classList.add('d-none');
            otherInput.value = '';
        }
    }

    function toggleRepresentativeSection() {
        const hasRep = document.getElementById('has_representative').checked;
        const repSection = document.getElementById('representative_section');
        const repFields = repSection.querySelectorAll('.rep-field');

        if (hasRep) {
            repSection.classList.remove('rep-card-disabled');
            repFields.forEach(el => el.disabled = false);
        } else {
            repSection.classList.add('rep-card-disabled');
            repFields.forEach(el => {
                el.disabled = true;
                if (el.tagName === 'INPUT' && el.type !== 'hidden') el.value = '';
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
            });
        }
    }

    let familyIndex = {{ count(old('family_composition', $intake->family_composition ?? [1])) }};
    function addFamilyRow() {
        const tbody = document.getElementById('familyTableBody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="family_composition[${familyIndex}][name]" class="form-control form-control-sm" placeholder="Full Name"></td>
            <td><input type="text" name="family_composition[${familyIndex}][relationship]" class="form-control form-control-sm" placeholder="e.g. Spouse, Son"></td>
            <td><input type="number" min="0" name="family_composition[${familyIndex}][age]" class="form-control form-control-sm" placeholder="Edad"></td>
            <td><input type="text" name="family_composition[${familyIndex}][occupation]" class="form-control form-control-sm" placeholder="Trabaho"></td>
            <td><input type="number" step="0.01" min="0" name="family_composition[${familyIndex}][salary]" class="form-control form-control-sm" placeholder="0.00"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeFamilyRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        familyIndex++;
    }

    function removeFamilyRow(btn) {
        const row = btn.closest('tr');
        const tbody = document.getElementById('familyTableBody');
        if (tbody.children.length > 1) {
            row.remove();
        } else {
            row.querySelectorAll('input').forEach(i => i.value = '');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const benBirth = document.getElementById('beneficiary_birthday');
        if (benBirth && benBirth.value) {
            formatAndCalculateAge(benBirth, 'beneficiary_age');
        }
        const repBirth = document.getElementById('rep_birthday');
        if (repBirth && repBirth.value) {
            formatAndCalculateAge(repBirth, 'rep_age');
        }
        toggleRepresentativeSection();
        togglePurposeOtherInput();
    });
</script>
@endsection
