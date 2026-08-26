@extends('layouts.financial')

@section('title', 'Create General Intake Sheet - MSWDO Silang')
@section('page-title', 'Create General Intake Sheet')

@section('page-styles')
<link href="{{ asset('css/beneficiary-intake/form.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">

    <!-- Header / Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1A237E;">GENERAL INTAKE SHEET</h4>
            <p class="text-muted small mb-0">Official MSWDO Financial Assistance Client Assessment Record.</p>
        </div>
        <a href="{{ route('admin.beneficiary-intake.index') }}"
            class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Intake List
        </a>
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

    <form action="{{ route('admin.beneficiary-intake.store') }}" method="POST" id="intakeForm">
        @csrf

        @if(isset($client) && $client)
        <input type="hidden" name="client_id" value="{{ $client->id }}">
        @endif

        <!-- HEADER CONTROL DETAILS -->
        <div class="form-card">
            <div class="form-header-bar d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fas fa-file-alt me-2"></i> GENERAL INTAKE SHEET DETAILS</span>
                <span class="badge bg-white text-dark fw-bold px-3 py-2 rounded-pill">{{ $controlNumber }}</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Control Number <span class="required-star">*</span></label>
                        <input type="text" name="control_number" class="form-control"
                            value="{{ old('control_number', $controlNumber) }}" readonly required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Client Status <span class="required-star">*</span></label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="client_type" id="type_new"
                                    value="New" {{ old('client_type', 'New' )=='New' ? 'checked' : '' }} required>
                                <label class="form-check-label fw-bold" for="type_new">New</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="client_type" id="type_returning"
                                    value="Returning" {{ old('client_type')=='Returning' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="type_returning">Returning</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date Processed</label>
                        <input type="text" class="form-control bg-light text-muted fw-bold"
                            value="{{ date('F d, Y') }}" readonly tabindex="-1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Time Start</label>
                        <input type="text" name="time_start" class="form-control"
                            value="{{ old('time_start', date('h:i A')) }}" placeholder="e.g. 09:00 AM">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Time End</label>
                        <input type="text" name="time_end" class="form-control" value="{{ old('time_end') }}"
                            placeholder="e.g. 09:30 AM">
                    </div>
                </div>
            </div>
        </div>

        <!-- 6-MONTH VALIDITY DUPLICATE ALERT CARD -->
        <div id="duplicateAlertCard" class="mb-4 {{ session('duplicate_check_error') ? '' : 'd-none' }}" style="background: #FFF5F5; border: 1.5px solid #FCA5A5; border-radius: 16px; box-shadow: 0 4px 16px rgba(220, 38, 38, 0.06); transition: all 180ms ease;">
            <div class="p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B;">
                        <i class="fas fa-user-lock fa-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <h5 class="fw-bold mb-0" style="color: #991B1B; font-size: 1.05rem; letter-spacing: -0.01em;">
                                <i class="fas fa-shield-halved me-2 text-danger"></i>6-Month Policy Restriction
                            </h5>
                            <span class="px-3 py-1 rounded-pill fw-semibold" style="background: #FEF2F2; border: 1px solid #F87171; color: #991B1B; font-size: 0.75rem;">
                                Restricted: Duplicate Beneficiary
                            </span>
                        </div>
                        <p class="mb-3" id="duplicateWarningText" style="color: #475569; font-size: 0.875rem; line-height: 1.5;">
                            {{ session('duplicate_check_error', 'Beneficiary has already received financial assistance within the last 6 months.') }}
                        </p>

                        <div class="table-responsive bg-white border rounded-3 overflow-hidden shadow-xs" style="border-color: #CBD5E1 !important;">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                <thead style="background: #F8FAFC; border-bottom: 1px solid #CBD5E1;">
                                    <tr class="text-secondary" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">
                                        <th class="ps-3 py-2">Control No.</th>
                                        <th class="py-2">App Date</th>
                                        <th class="py-2">Policy Status</th>
                                        <th class="py-2">Beneficiary Name</th>
                                        <th class="py-2">Representative</th>
                                        <th class="py-2">Assistance Type</th>
                                        <th class="pe-3 py-2 text-end">Eligible Again</th>
                                    </tr>
                                </thead>
                                <tbody id="duplicateMatchesTableBody">
                                    @if(session('duplicate_matches'))
                                        @foreach(session('duplicate_matches') as $match)
                                        <tr style="border-bottom: 1px solid #F1F5F9;">
                                            <td class="ps-3 fw-bold" style="color: #1A237E;">{{ $match['control_number'] }}</td>
                                            <td class="text-secondary">{{ $match['date_processed'] }}</td>
                                            <td>
                                                <span class="px-2.5 py-0.5 rounded-pill fw-semibold" style="background: #FEF2F2; border: 1px solid #F87171; color: #991B1B; font-size: 0.75rem;">
                                                    {{ $match['matched_role'] }}
                                                </span>
                                            </td>
                                            <td class="fw-semibold text-dark">{{ $match['beneficiary_name'] }}</td>
                                            <td class="text-secondary">{{ $match['representative_name'] }}</td>
                                            <td class="text-secondary">{{ $match['assistance_type'] }}</td>
                                            <td class="pe-3 text-end fw-bold" style="color: #DC2626;">{{ $match['eligible_again_date'] }}</td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
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
                        <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-user me-2 text-primary"></i> IMPORMASYON NG
                            BENEPISYARYO <span class="text-muted fs-6">(Beneficiary's Identifying Information)</span>
                        </h5>
                    </div>
                    <span class="text-muted small"><span class="required-star">*</span> Required fields</span>
                </div>

                <!-- Names -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Apelyido (Last Name) <span class="required-star">*</span></label>
                        <input type="text" name="beneficiary_last_name" id="beneficiary_last_name" class="form-control"
                            value="{{ old('beneficiary_last_name', $client->last_name ?? '') }}" placeholder="Apelyido"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unang Pangalan (First Name) <span
                                class="required-star">*</span></label>
                        <input type="text" name="beneficiary_first_name" id="beneficiary_first_name" class="form-control"
                            value="{{ old('beneficiary_first_name', $client->first_name ?? '') }}"
                            placeholder="Unang Pangalan" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gitnang Pangalan (Middle Name)</label>
                        <input type="text" name="beneficiary_middle_name" id="beneficiary_middle_name" class="form-control"
                            value="{{ old('beneficiary_middle_name', $client->middle_name ?? '') }}"
                            placeholder="Gitnang Pangalan">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ext. (Sr., Jr., III)</label>
                        <select name="beneficiary_extension_name" class="form-select">
                            <option value="">None</option>
                            <option value="Jr." {{ old('beneficiary_extension_name')=='Jr.' ? 'selected' : '' }}>Jr.
                            </option>
                            <option value="Sr." {{ old('beneficiary_extension_name')=='Sr.' ? 'selected' : '' }}>Sr.
                            </option>
                            <option value="III" {{ old('beneficiary_extension_name')=='III' ? 'selected' : '' }}>III
                            </option>
                            <option value="IV" {{ old('beneficiary_extension_name')=='IV' ? 'selected' : '' }}>IV
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Address -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">House No. / Street / Purok <span
                                class="required-star">*</span></label>
                        <input type="text" name="beneficiary_street_address" class="form-control"
                            value="{{ old('beneficiary_street_address', $client->street_address ?? '') }}"
                            placeholder="House No. / Street / Purok" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Barangay <span class="required-star">*</span></label>
                        <select name="beneficiary_barangay" class="form-select" required>
                            <option value="">Select Barangay</option>
                            @foreach($barangays as $brgy)
                            <option value="{{ $brgy }}" {{ old('beneficiary_barangay', $client->barangay ?? '') == $brgy
                                ? 'selected' : '' }}>{{ $brgy }}</option>
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
                                <input type="text" name="beneficiary_province" class="form-control" value="CAVITE"
                                    readonly>
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
                        <label class="form-label">Numero ng Telepono (Mobile No.) <span
                                class="required-star">*</span></label>
                        <input type="text" name="beneficiary_contact_number" class="form-control"
                            value="{{ old('beneficiary_contact_number', $client->contact_number ?? '') }}"
                            placeholder="09XXXXXXXXX" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kapanganakan (MM/DD/YYYY) <span class="required-star">*</span></label>
                        <input type="text" name="beneficiary_birthday" id="beneficiary_birthday" class="form-control"
                            value="{{ old('beneficiary_birthday', isset($client->birth_date) ? $client->birth_date->format('m/d/Y') : '') }}"
                            placeholder="MM/DD/YYYY" maxlength="10" required
                            oninput="formatAndCalculateAge(this, 'beneficiary_age')">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Edad (Age) <span class="required-star">*</span></label>
                        <input type="number" name="beneficiary_age" id="beneficiary_age" class="form-control"
                            value="{{ old('beneficiary_age', $client->age ?? '') }}" readonly required
                            placeholder="Edad">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kasarian (Gender) <span class="required-star">*</span></label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beneficiary_sex" id="sex_male"
                                    value="Male" {{ old('beneficiary_sex', $client->sex ?? '') == 'Male' ? 'checked' :
                                '' }} required>
                                <label class="form-check-label" for="sex_male">M</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="beneficiary_sex" id="sex_female"
                                    value="Female" {{ old('beneficiary_sex', $client->sex ?? '') == 'Female' ? 'checked'
                                : '' }}>
                                <label class="form-check-label" for="sex_female">F</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Civil Status (Katayuan) <span class="required-star">*</span></label>
                        <select name="beneficiary_civil_status" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Single" {{ old('beneficiary_civil_status')=='Single' ? 'selected' : '' }}>
                                Single</option>
                            <option value="Married" {{ old('beneficiary_civil_status')=='Married' ? 'selected' : '' }}>
                                Married</option>
                            <option value="Widowed" {{ old('beneficiary_civil_status')=='Widowed' ? 'selected' : '' }}>
                                Widowed</option>
                            <option value="Separated" {{ old('beneficiary_civil_status')=='Separated' ? 'selected' : ''
                                }}>Separated</option>
                            <option value="Cohabiting" {{ old('beneficiary_civil_status')=='Cohabiting' ? 'selected'
                                : '' }}>Cohabiting</option>
                        </select>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Trabaho (Occupation)</label>
                        <input type="text" name="beneficiary_occupation" class="form-control"
                            value="{{ old('beneficiary_occupation') }}"
                            placeholder="Trabaho (e.g. N/A, Vendor, Housekeeper)">
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Buwanang Kita (Monthly Salary)</label>
                        <input type="number" step="0.01" min="0" name="beneficiary_monthly_salary" class="form-control"
                            value="{{ old('beneficiary_monthly_salary') }}" placeholder="0.00">
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 2: IMPORMASYON NG KINATAWAN -->
        <div class="form-card">
            <div class="card-body p-4">

                <div class="toggle-card d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark"><i class="fas fa-user-friends me-2 text-primary"></i>
                            IMPORMASYON NG KINATAWAN <span class="text-muted fs-6">(Representative's Identifying
                                Information)</span></h6>
                        <p class="text-muted small mb-0">Check if representative is filing on behalf of the beneficiary.
                        </p>
                    </div>
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" role="switch" name="has_representative"
                            id="has_representative" value="1" {{ old('has_representative') ? 'checked' : '' }}
                            onchange="toggleRepresentativeSection()">
                        <label class="form-check-label fw-bold fs-6 ms-2" for="has_representative">Has
                            Representative</label>
                    </div>
                </div>

                <div id="representative_section" class="rep-card-disabled">

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Apelyido (Last Name) <span
                                    class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_last_name" id="rep_last_name" class="form-control rep-field"
                                value="{{ old('rep_last_name') }}" placeholder="Apelyido">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Unang Pangalan (First Name) <span
                                    class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_first_name" id="rep_first_name" class="form-control rep-field"
                                value="{{ old('rep_first_name') }}" placeholder="Unang Pangalan">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Gitnang Pangalan (Middle Name)</label>
                            <input type="text" name="rep_middle_name" id="rep_middle_name" class="form-control rep-field"
                                value="{{ old('rep_middle_name') }}" placeholder="Gitnang Pangalan">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ext. (Sr., Jr., III)</label>
                            <select name="rep_extension_name" class="form-select rep-field">
                                <option value="">None</option>
                                <option value="Jr." {{ old('rep_extension_name')=='Jr.' ? 'selected' : '' }}>Jr.
                                </option>
                                <option value="Sr." {{ old('rep_extension_name')=='Sr.' ? 'selected' : '' }}>Sr.
                                </option>
                                <option value="III" {{ old('rep_extension_name')=='III' ? 'selected' : '' }}>III
                                </option>
                                <option value="IV" {{ old('rep_extension_name')=='IV' ? 'selected' : '' }}>IV</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">House No. / Street / Purok <span
                                    class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_street_address" class="form-control rep-field"
                                value="{{ old('rep_street_address') }}" placeholder="House No., Street">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Barangay <span class="required-star rep-star">*</span></label>
                            <select name="rep_barangay" class="form-select rep-field">
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $brgy)
                                <option value="{{ $brgy }}" {{ old('rep_barangay')==$brgy ? 'selected' : '' }}>{{ $brgy
                                    }}</option>
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
                            <label class="form-label">Numero ng Telepono <span
                                    class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_contact_number" class="form-control rep-field"
                                value="{{ old('rep_contact_number') }}" placeholder="09XXXXXXXXX">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kapanganakan (MM/DD/YYYY) <span
                                    class="required-star rep-star">*</span></label>
                            <input type="text" name="rep_birthday" id="rep_birthday" class="form-control rep-field"
                                value="{{ old('rep_birthday') }}" placeholder="MM/DD/YYYY" maxlength="10"
                                oninput="formatAndCalculateAge(this, 'rep_age')">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Edad (Age) <span class="required-star rep-star">*</span></label>
                            <input type="number" name="rep_age" id="rep_age" class="form-control rep-field"
                                value="{{ old('rep_age') }}" readonly placeholder="Edad">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Kasarian (Gender) <span
                                    class="required-star rep-star">*</span></label>
                            <select name="rep_sex" class="form-select rep-field">
                                <option value="">Select</option>
                                <option value="Male" {{ old('rep_sex')=='Male' ? 'selected' : '' }}>M</option>
                                <option value="Female" {{ old('rep_sex')=='Female' ? 'selected' : '' }}>F</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Civil Status <span class="required-star rep-star">*</span></label>
                            <select name="rep_civil_status" class="form-select rep-field">
                                <option value="">Select</option>
                                <option value="Single" {{ old('rep_civil_status')=='Single' ? 'selected' : '' }}>Single
                                </option>
                                <option value="Married" {{ old('rep_civil_status')=='Married' ? 'selected' : '' }}>
                                    Married</option>
                                <option value="Widowed" {{ old('rep_civil_status')=='Widowed' ? 'selected' : '' }}>
                                    Widowed</option>
                                <option value="Separated" {{ old('rep_civil_status')=='Separated' ? 'selected' : '' }}>
                                    Separated</option>
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Trabaho (Occupation)</label>
                            <input type="text" name="rep_occupation" class="form-control rep-field"
                                value="{{ old('rep_occupation') }}" placeholder="Trabaho">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Buwanang Kita (Monthly Salary)</label>
                            <input type="number" step="0.01" min="0" name="rep_monthly_salary"
                                class="form-control rep-field" value="{{ old('rep_monthly_salary') }}"
                                placeholder="0.00">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Relasyon sa Benepisyaryo <span
                                    class="required-star rep-star">*</span></label>
                            <select name="rep_relationship" class="form-select rep-field">
                                <option value="">Select Relationship</option>
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

        <!-- DSWD OFFICIAL USE ONLY SECTION -->
        <div class="form-card">
            <div class="card-body p-4">

                <div class="row g-4">
                    <!-- Left Column: Beneficiary Category Checkboxes -->
                    <div class="col-md-5 border-end pe-md-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-list-check me-2 text-primary"></i>
                            Beneficiary Category</h6>
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
                            <label class="form-check-label small fw-semibold" for="cat_others">Others</label>
                        </div>
                        <input type="text" name="beneficiary_category_other" id="beneficiary_category_other_input"
                            class="form-control mt-2 {{ in_array('Others', $oldCats) ? '' : 'd-none' }}"
                            value="{{ old('beneficiary_category_other') }}" placeholder="Specify other category...">
                    </div>

                    <!-- Right Column: Social Worker's Assessment -->
                    <div class="col-md-7 ps-md-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-clipboard-check me-2 text-primary"></i>
                            Social Worker's Assessment</h6>
                        <textarea name="social_worker_assessment" rows="7" class="form-control"
                            placeholder="Write initial assessment, client situation, and social worker recommendations here...">{{ old('social_worker_assessment') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: KOMPOSISYON NG PAMILYA (Family Composition) -->
        <div class="form-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-users me-2 text-primary"></i> KOMPOSISYON NG
                        PAMILYA <span class="text-muted fs-6">(Family Composition)</span></h6>
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
                                        placeholder="e.g. Spouse, Son">
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
                                        onclick="removeFamilyRow(this)"><i class="fas fa-trash"></i></button>
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
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-hand-holding-heart me-2 text-primary"></i>
                    ASSISTANCE PURPOSE / MEDICAL CONDITION &amp; INTERVIEW DETAILS</h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Assistance Purpose / Medical Condition</label>
                        <select name="assistance_purpose" id="assistance_purpose_select" class="form-select"
                            onchange="togglePurposeOtherInput()">
                            <option value="">Select Medical Condition / Assistance Purpose</option>
                            @foreach($medicalConditions as $cond)
                            <option value="{{ $cond }}" {{ old('assistance_purpose')==$cond ? 'selected' : '' }}>{{
                                $cond }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="purpose_other" id="purpose_other_input"
                            class="form-control mt-2 {{ old('assistance_purpose') == 'Other Medical Conditions' ? '' : 'd-none' }}"
                            value="{{ old('purpose_other') }}" placeholder="Specify medical condition or details...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Interviewed by <span class="text-muted">(MSWD Personnel / Social
                                Worker)</span></label>
                        <input type="text" name="interviewed_by" class="form-control"
                            value="{{ old('interviewed_by', session('admin_user_name') ?? '') }}"
                            placeholder="Printed Name of Interviewer">
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Submit Bar -->
        <div class="d-flex justify-content-end gap-3 mb-5">
            <a href="{{ route('admin.beneficiary-intake.index') }}"
                class="btn btn-light border px-4 py-2 rounded-3">Cancel</a>
            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-3"
                style="background: #1A237E; border: none;">
                <i class="fas fa-save me-2"></i> Save General Intake Sheet
            </button>
        </div>

    </form>
</div>
@endsection

@section('page-scripts')
<script src="{{ asset('js/beneficiary-intake/form.js') }}"></script>
@endsection