@extends('layouts.admin')

@section('title', 'Create Social Case Study')
@section('navbar-title', 'Create Social Case Study')

@section('page-styles')
<style>
    .case-study-step-indicator { gap: .75rem; }
    .case-study-step-indicator .step-indicator { position: relative; z-index: 1; flex: 1 1 0; min-width: 0; }
    .case-study-step-indicator small { display: block; line-height: 1.2; }

    @media (max-width: 575.98px) {
        .case-study-step-indicator { align-items: flex-start !important; overflow-x: auto; padding-bottom: .5rem; }
        .case-study-step-indicator .progress { display: none; }
        .case-study-step-indicator .step-indicator { flex: 0 0 72px; }
        .case-study-step-indicator .step-circle { width: 34px !important; height: 34px !important; margin-bottom: .35rem !important; }
        .case-study-step-indicator small { font-size: .7rem; white-space: normal; }
    }
</style>
@endsection

@section('content')
<div class="card">
                <div class="card-body">
                    <!-- Step Progress -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center position-relative case-study-step-indicator">
                            <div class="progress position-absolute w-100" style="height: 4px; top: 50%; transform: translateY(-50%); z-index: 0;">
                                <div class="progress-bar" id="progressBar" style="width: 20%;"></div>
                            </div>
                            <div class="step-indicator text-center" data-step="1">
                                <div class="step-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px; z-index: 1;">1</div>
                                <small class="fw-bold">Requirements</small>
                            </div>
                            <div class="step-indicator text-center" data-step="2">
                                <div class="step-circle bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px; z-index: 1;">2</div>
                                <small class="text-muted">Interview</small>
                            </div>
                            <div class="step-indicator text-center" data-step="3">
                                <div class="step-circle bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px; z-index: 1;">3</div>
                                <small class="text-muted">Evaluation</small>
                            </div>
                            <div class="step-indicator text-center" data-step="4">
                                <div class="step-circle bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px; z-index: 1;">4</div>
                                <small class="text-muted">Report</small>
                            </div>
                            <div class="step-indicator text-center" data-step="5">
                                <div class="step-circle bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px; z-index: 1;">5</div>
                                <small class="text-muted">Assistance</small>
                            </div>
                        </div>
                    </div>

            <form method="POST" action="{{ route('admin.social-case-studies.store', $client) }}" id="socialCaseForm">
                @csrf
                @php($caseBeneficiary = $intake && ! $intake->is_client_beneficiary ? $intake : null)
                <input type="hidden" name="current_step" id="currentStep" value="1">
                <input type="hidden" name="beneficiary_intake_id" value="{{ $intake?->id }}">
                
                <!-- Step 1: Requirements Verification -->
                <div class="step-content" id="step1">
                    <h6 class="mb-3">Step 1: Requirements Verification</h6>
                    <p class="text-muted mb-4">Check and verify client requirements based on the type of assistance requested.</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Date Processed *</label>
                        <input type="date" name="date_processed" class="form-control" value="{{ old('date_processed', $intake?->date_processed?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                    </div>

                    <h6 class="mt-4 mb-3">CLIENT INFORMATION</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Last Name *</label>
                            <input type="text" name="client_last_name" class="form-control" value="{{ old('client_last_name', $intake?->client_last_name ?? $client->last_name) }}" required readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">First Name *</label>
                            <input type="text" name="client_first_name" class="form-control" value="{{ old('client_first_name', $intake?->client_first_name ?? $client->first_name) }}" required readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="client_middle_name" class="form-control" value="{{ old('client_middle_name', $intake?->client_middle_name ?? $client->middle_name ?? '') }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Age *</label>
                            <input type="number" name="client_age" class="form-control" value="{{ old('client_age', $intake?->client_age ?? $client->birthdate?->age) }}" required readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sex *</label>
                            <select name="client_sex" class="form-select" required>
                                <option value="">Select Sex</option>
                                <option value="Male" @selected(old('client_sex', $intake?->client_sex ?? $client->gender) === 'Male')>Male</option>
                                <option value="Female" @selected(old('client_sex', $intake?->client_sex ?? $client->gender) === 'Female')>Female</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Barangay *</label>
                            <select name="client_barangay" class="form-select" required>
                                <option value="">Select Barangay</option>
                                <option value="ACACIA">ACACIA</option>
                                <option value="ADLAS">ADLAS</option>
                                <option value="ANAHAW I">ANAHAW I</option>
                                <option value="ANAHAW II">ANAHAW II</option>
                                <option value="BALITE I">BALITE I</option>
                                <option value="BALITE II">BALITE II</option>
                                <option value="BALUBAD">BALUBAD</option>
                                <option value="BANABA">BANABA</option>
                                <option value="BATAS">BATAS</option>
                                <option value="BIGA I">BIGA I</option>
                                <option value="BIGA II">BIGA II</option>
                                <option value="BILUSO">BILUSO</option>
                                <option value="BUCAL">BUCAL</option>
                                <option value="BUHO">BUHO</option>
                                <option value="BULIHAN">BULIHAN</option>
                                <option value="CABANGAAN">CABANGAAN</option>
                                <option value="CARMEN">CARMEN</option>
                                <option value="HOYO">HOYO</option>
                                <option value="HUKAY">HUKAY</option>
                                <option value="IBA">IBA</option>
                                <option value="INCHICAN">INCHICAN</option>
                                <option value="IPIL I">IPIL I</option>
                                <option value="IPIL II">IPIL II</option>
                                <option value="KALUBKOB">KALUBKOB</option>
                                <option value="KAONG">KAONG</option>
                                <option value="LALAAN I">LALAAN I</option>
                                <option value="LALAAN II">LALAAN II</option>
                                <option value="LITLIT">LITLIT</option>
                                <option value="LUCSUHIN">LUCSUHIN</option>
                                <option value="LUMIL">LUMIL</option>
                                <option value="MAGUYAM">MAGUYAM</option>
                                <option value="MALABAG">MALABAG</option>
                                <option value="MALAKING TATIAO">MALAKING TATIAO</option>
                                <option value="MATAAS NA BUROL">MATAAS NA BUROL</option>
                                <option value="MUNTING ILOG">MUNTING ILOG</option>
                                <option value="NARRA I">NARRA I</option>
                                <option value="NARRA II">NARRA II</option>
                                <option value="NARRA III">NARRA III</option>
                                <option value="PALIGAWAN">PALIGAWAN</option>
                                <option value="PASONG LANGKA">PASONG LANGKA</option>
                                <option value="POBLACION I">POBLACION I</option>
                                <option value="POBLACION II">POBLACION II</option>
                                <option value="POBLACION III">POBLACION III</option>
                                <option value="POBLACION IV">POBLACION IV</option>
                                <option value="POBLACION V">POBLACION V</option>
                                <option value="POOC I">POOC I</option>
                                <option value="POOC II">POOC II</option>
                                <option value="PULONG BUNGA">PULONG BUNGA</option>
                                <option value="PULONG SAGING">PULONG SAGING</option>
                                <option value="PUTING KAHOY">PUTING KAHOY</option>
                                <option value="SABUTAN">SABUTAN</option>
                                <option value="SAN MIGUEL I">SAN MIGUEL I</option>
                                <option value="SAN MIGUEL II">SAN MIGUEL II</option>
                                <option value="SAN VICENTE I">SAN VICENTE I</option>
                                <option value="SAN VICENTE II">SAN VICENTE II</option>
                                <option value="SANTOL">SANTOL</option>
                                <option value="TARTARIA">TARTARIA</option>
                                <option value="TIBIG">TIBIG</option>
                                <option value="TOLEDO">TOLEDO</option>
                                <option value="TUBUAN I">TUBUAN I</option>
                                <option value="TUBUAN II">TUBUAN II</option>
                                <option value="TUBUAN III">TUBUAN III</option>
                                <option value="ULAT">ULAT</option>
                                <option value="YAKAL">YAKAL</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3">BENEFICIARY INFORMATION</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="beneficiary_last_name" class="form-control" value="{{ old('beneficiary_last_name', $caseBeneficiary?->beneficiary_last_name ?? ($intake?->is_client_beneficiary ? $intake->client_last_name : '')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <input type="text" name="beneficiary_first_name" class="form-control" value="{{ old('beneficiary_first_name', $caseBeneficiary?->beneficiary_first_name ?? ($intake?->is_client_beneficiary ? $intake->client_first_name : '')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="beneficiary_middle_name" class="form-control" value="{{ old('beneficiary_middle_name', $caseBeneficiary?->beneficiary_middle_name ?? ($intake?->is_client_beneficiary ? $intake->client_middle_name : '')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Age</label>
                            <input type="number" name="beneficiary_age" class="form-control" value="{{ old('beneficiary_age', $caseBeneficiary?->beneficiary_age ?? ($intake?->is_client_beneficiary ? $intake->client_age : '')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Birthday</label>
                            <input type="date" name="beneficiary_birthday" class="form-control" value="{{ old('beneficiary_birthday', $caseBeneficiary?->beneficiary_birthday?->format('Y-m-d') ?? ($intake?->is_client_beneficiary ? $intake->client_birthday?->format('Y-m-d') : '')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sex</label>
                            <select name="beneficiary_sex" class="form-select">
                                <option value="">Select Sex</option>
                                <option value="Male" @selected(old('beneficiary_sex', $caseBeneficiary?->beneficiary_sex ?? ($intake?->is_client_beneficiary ? $intake->client_sex : null)) === 'Male')>Male</option>
                                <option value="Female" @selected(old('beneficiary_sex', $caseBeneficiary?->beneficiary_sex ?? ($intake?->is_client_beneficiary ? $intake->client_sex : null)) === 'Female')>Female</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Barangay</label>
                            <select name="beneficiary_barangay" class="form-select">
                                <option value="">Select Barangay</option>
                                <option value="ACACIA">ACACIA</option>
                                <option value="ADLAS">ADLAS</option>
                                <option value="ANAHAW I">ANAHAW I</option>
                                <option value="ANAHAW II">ANAHAW II</option>
                                <option value="BALITE I">BALITE I</option>
                                <option value="BALITE II">BALITE II</option>
                                <option value="BALUBAD">BALUBAD</option>
                                <option value="BANABA">BANABA</option>
                                <option value="BATAS">BATAS</option>
                                <option value="BIGA I">BIGA I</option>
                                <option value="BIGA II">BIGA II</option>
                                <option value="BILUSO">BILUSO</option>
                                <option value="BUCAL">BUCAL</option>
                                <option value="BUHO">BUHO</option>
                                <option value="BULIHAN">BULIHAN</option>
                                <option value="CABANGAAN">CABANGAAN</option>
                                <option value="CARMEN">CARMEN</option>
                                <option value="HOYO">HOYO</option>
                                <option value="HUKAY">HUKAY</option>
                                <option value="IBA">IBA</option>
                                <option value="INCHICAN">INCHICAN</option>
                                <option value="IPIL I">IPIL I</option>
                                <option value="IPIL II">IPIL II</option>
                                <option value="KALUBKOB">KALUBKOB</option>
                                <option value="KAONG">KAONG</option>
                                <option value="LALAAN I">LALAAN I</option>
                                <option value="LALAAN II">LALAAN II</option>
                                <option value="LITLIT">LITLIT</option>
                                <option value="LUCSUHIN">LUCSUHIN</option>
                                <option value="LUMIL">LUMIL</option>
                                <option value="MAGUYAM">MAGUYAM</option>
                                <option value="MALABAG">MALABAG</option>
                                <option value="MALAKING TATIAO">MALAKING TATIAO</option>
                                <option value="MATAAS NA BUROL">MATAAS NA BUROL</option>
                                <option value="MUNTING ILOG">MUNTING ILOG</option>
                                <option value="NARRA I">NARRA I</option>
                                <option value="NARRA II">NARRA II</option>
                                <option value="NARRA III">NARRA III</option>
                                <option value="PALIGAWAN">PALIGAWAN</option>
                                <option value="PASONG LANGKA">PASONG LANGKA</option>
                                <option value="POBLACION I">POBLACION I</option>
                                <option value="POBLACION II">POBLACION II</option>
                                <option value="POBLACION III">POBLACION III</option>
                                <option value="POBLACION IV">POBLACION IV</option>
                                <option value="POBLACION V">POBLACION V</option>
                                <option value="POOC I">POOC I</option>
                                <option value="POOC II">POOC II</option>
                                <option value="PULONG BUNGA">PULONG BUNGA</option>
                                <option value="PULONG SAGING">PULONG SAGING</option>
                                <option value="PUTING KAHOY">PUTING KAHOY</option>
                                <option value="SABUTAN">SABUTAN</option>
                                <option value="SAN MIGUEL I">SAN MIGUEL I</option>
                                <option value="SAN MIGUEL II">SAN MIGUEL II</option>
                                <option value="SAN VICENTE I">SAN VICENTE I</option>
                                <option value="SAN VICENTE II">SAN VICENTE II</option>
                                <option value="SANTOL">SANTOL</option>
                                <option value="TARTARIA">TARTARIA</option>
                                <option value="TIBIG">TIBIG</option>
                                <option value="TOLEDO">TOLEDO</option>
                                <option value="TUBUAN I">TUBUAN I</option>
                                <option value="TUBUAN II">TUBUAN II</option>
                                <option value="TUBUAN III">TUBUAN III</option>
                                <option value="ULAT">ULAT</option>
                                <option value="YAKAL">YAKAL</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3">MEDICAL CONDITION (if applicable)</h6>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="medical_conditions[]" value="Cancer" id="med_cancer">
                                <label class="form-check-label" for="med_cancer">Cancer</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="medical_conditions[]" value="Cardiovascular" id="med_cardio">
                                <label class="form-check-label" for="med_cardio">Cardiovascular</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="medical_conditions[]" value="Kidney Diseases" id="med_kidney">
                                <label class="form-check-label" for="med_kidney">Kidney Diseases</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="medical_conditions[]" value="Neurological Disorders" id="med_neuro">
                                <label class="form-check-label" for="med_neuro">Neurological Disorders</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="medical_conditions[]" value="Respiratory Diseases" id="med_resp">
                                <label class="form-check-label" for="med_resp">Respiratory Diseases</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="medical_conditions[]" value="Infectious Disease" id="med_infectious">
                                <label class="form-check-label" for="med_infectious">Infectious Disease</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="medical_conditions[]" value="Diabetes" id="med_diabetes">
                                <label class="form-check-label" for="med_diabetes">Diabetes</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="medical_conditions[]" value="Surgical" id="med_surgical">
                                <label class="form-check-label" for="med_surgical">Surgical</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="medical_conditions[]" value="Trauma and Injury" id="med_trauma">
                                <label class="form-check-label" for="med_trauma">Trauma and Injury</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="medical_conditions[]" value="Other Medical Conditions" id="med_other">
                                <label class="form-check-label" for="med_other">Other Medical Conditions</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="medical_conditions[]" value="Special Welfare Cases" id="med_welfare">
                                <label class="form-check-label" for="med_welfare">Special Welfare Cases</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="medical_conditions[]" value="Not Applicable" id="med_na">
                                <label class="form-check-label" for="med_na">Not Applicable</label>
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3">REQUIREMENTS CHECKLIST</h6>
                    <div class="alert alert-info mb-3">
                        <strong>General Requirements (for all types):</strong>
                        <ul class="mb-0 mt-2">
                            <li>Personal Letter to the Mayor</li>
                            <li>Valid ID</li>
                            <li>Barangay Indigency</li>
                            <li>Purpose of Assistance</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional Requirements (based on assistance type)</label>
                        <textarea name="additional_requirements" rows="3" class="form-control" placeholder="List additional documents required based on assistance type (Medical Certificate, Clinical Abstract, Statement of Account, Death Certificate, etc.)"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="requirements_complete" id="req_complete">
                            <label class="form-check-label fw-bold" for="req_complete">All requirements verified and complete</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" onclick="nextStep(2)">Next: Assessment Interview <i class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- Step 2: Assessment Interview -->
                <div class="step-content d-none" id="step2">
                    <h6 class="mb-3">Step 2: Assessment Interview</h6>
                    <p class="text-muted mb-4">Conduct interview with client to assess their situation and needs.</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Interview Date *</label>
                        <input type="date" name="interview_date" class="form-control" required>
                    </div>

                    <h6 class="mt-4 mb-3">INTERVIEW QUESTIONS</h6>
                    <div class="mb-3">
                        <label class="form-label">Why are you requesting assistance?</label>
                        <textarea name="interview_reason" rows="3" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">What happened? (Describe the situation)</label>
                        <textarea name="interview_situation" rows="3" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Who lives in your household?</label>
                        <textarea name="interview_household" rows="3" class="form-control" required></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Monthly Income</label>
                            <input type="number" name="monthly_income" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Monthly Expenses</label>
                            <input type="number" name="monthly_expenses" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Are there family members with illnesses?</label>
                        <textarea name="family_illnesses" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Have you received assistance before?</label>
                        <select name="previous_assistance" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Interview Notes</label>
                        <textarea name="interview_notes" rows="4" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interview_complete" id="int_complete">
                            <label class="form-check-label fw-bold" for="int_complete">Interview completed and documented</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)"><i class="fas fa-arrow-left me-2"></i> Back</button>
                        <button type="button" class="btn btn-primary" onclick="nextStep(3)">Next: Evaluation <i class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- Step 3: Social Worker Assessment -->
                <div class="step-content d-none" id="step3">
                    <h6 class="mb-3">Step 3: Social Worker Assessment</h6>
                    <p class="text-muted mb-4">Evaluate the case and submit a recommendation for the report.</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Social Worker Assessment</label>
                        <textarea name="social_worker_assessment" rows="4" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recommendation</label>
                        <select name="recommendation" class="form-select" required>
                            <option value="">Select Recommendation</option>
                            <option value="Approved">Approved</option>
                            <option value="Needs Additional Info">Needs Additional Info</option>
                            <option value="Not Qualified">Not Qualified</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assistance Amount Recommended</label>
                        <input type="number" name="recommended_amount" class="form-control" step="0.01">
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)"><i class="fas fa-arrow-left me-2"></i> Back</button>
                        <button type="button" class="btn btn-primary" onclick="nextStep(4)">Next: Report Generation <i class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- Step 4: Report Generation -->
                <div class="step-content d-none" id="step4">
                    <h6 class="mb-3">Step 4: Report Generation</h6>
                    <p class="text-muted mb-4">Generate and prepare the Social Case Study Report.</p>
                    
                    <h6 class="mt-4 mb-3">SERVICE PROVIDED *</h6>
                    <div class="mb-3">
                        <select name="service_provided" class="form-select" required>
                            <option value="">Select Service</option>
                            <option value="SOCIAL CASE STUDY REPORT">SOCIAL CASE STUDY REPORT</option>
                            <option value="GENERAL INTAKE">GENERAL INTAKE</option>
                            <option value="CERTIFICATION">CERTIFICATION</option>
                        </select>
                    </div>

                    <h6 class="mt-4 mb-3">PURPOSE *</h6>
                    <div class="mb-3">
                        <select name="purpose" class="form-select" required>
                            <option value="">Select Purpose</option>
                            <option value="FINANCIAL ASSISTANCE">FINANCIAL ASSISTANCE</option>
                            <option value="MEDICAL ASSISTANCE">MEDICAL ASSISTANCE</option>
                            <option value="BURIAL ASSISTANCE">BURIAL ASSISTANCE</option>
                            <option value="BIRTH CORRECTION">BIRTH CORRECTION</option>
                            <option value="PHILHEALTH INDIGENCY">PHILHEALTH INDIGENCY</option>
                            <option value="MERALCO INDIGENCY">MERALCO INDIGENCY</option>
                            <option value="PUBLIC ATTORNEY'S OFFICE CERTIFICATION">PUBLIC ATTORNEY'S OFFICE CERTIFICATION</option>
                            <option value="BALIK PROBINSYA">BALIK PROBINSYA</option>
                            <option value="FIRE INCIDENT">FIRE INCIDENT</option>
                            <option value="CHED SCHOLARSHIP">CHED SCHOLARSHIP</option>
                            <option value="NATURAL DISASTER">NATURAL DISASTER</option>
                            <option value="DRUG REHABILITATION">DRUG REHABILITATION</option>
                        </select>
                    </div>

                    <h6 class="mt-4 mb-3">SUBMITTED TO *</h6>
                    <div class="mb-3">
                        <select name="submitted_to" class="form-select" required>
                            <option value="">Select Office</option>
                            <option value="OFFICE OF THE PRESIDENT">OFFICE OF THE PRESIDENT</option>
                            <option value="OFFICE OF THE VICE PRESIDENT">OFFICE OF THE VICE PRESIDENT</option>
                            <option value="DSWD - REGIONAL OFFICE">DSWD - REGIONAL OFFICE</option>
                            <option value="DSWD - CENTRAL OFFICE">DSWD - CENTRAL OFFICE</option>
                            <option value="DOH">DOH</option>
                            <option value="PCSO">PCSO</option>
                            <option value="PROVINCIAL DEPARTMENT OF HEALTH OFFICE">PROVINCIAL DEPARTMENT OF HEALTH OFFICE</option>
                            <option value="OFFICE OF THE SENATE">OFFICE OF THE SENATE</option>
                            <option value="PARTYLIST">PARTYLIST</option>
                            <option value="OFFICE OF THE CONGRESSMAN">OFFICE OF THE CONGRESSMAN</option>
                            <option value="SANGUNIANG BAYAN COUNCILOR">SANGUNIANG BAYAN COUNCILOR</option>
                            <option value="OFFICE OF THE VICE MAYOR">OFFICE OF THE VICE MAYOR</option>
                            <option value="PHILHEALTH">PHILHEALTH</option>
                            <option value="NOT APPLICABLE">NOT APPLICABLE</option>
                            <option value="SATELLITE OFFICE">SATELLITE OFFICE</option>
                            <option value="INSTITUTION">INSTITUTION</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Summary / Case Details</label>
                        <textarea name="summary" rows="6" class="form-control"></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep(3)"><i class="fas fa-arrow-left me-2"></i> Back</button>
                        <button type="button" class="btn btn-primary" onclick="nextStep(5)">Next: Assistance Release <i class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>

                <!-- Step 5: Assistance Release & Case Closure -->
                <div class="step-content d-none" id="step5">
                    <h6 class="mb-3">Step 5: Assistance Release & Case Closure</h6>
                    <p class="text-muted mb-4">Record assistance release and close the case.</p>
                    
                    <h6 class="mt-4 mb-3">ENCODED BY *</h6>
                    <div class="mb-3">
                        <input type="text" name="encoded_by" class="form-control" value="{{ session('admin_user_name') ?? 'Admin User' }}" required>
                    </div>

                    <h6 class="mt-4 mb-3">ASSISTANCE RELEASE</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Assistance Amount</label>
                            <input type="number" name="assistance_amount" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assistance Date</label>
                            <input type="date" name="assistance_date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="assistance_released" id="assist_release">
                            <label class="form-check-label fw-bold" for="assist_release">Assistance released to client</label>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3">CASE STATUS</h6>
                    <div class="mb-3">
                        <select name="status" class="form-select" required>
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep(4)"><i class="fas fa-arrow-left me-2"></i> Back</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check me-2"></i> Complete & Save Case</button>
                    </div>
                </div>
            </form>
                </div>
</div>
@endsection

@section('page-scripts')
<script>
function nextStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(el => el.classList.add('d-none'));
            // Show target step
            document.getElementById('step' + step).classList.remove('d-none');
            // Update progress bar
            document.getElementById('progressBar').style.width = (step * 20) + '%';
            // Update current step input
            document.getElementById('currentStep').value = step;
            // Update step indicators
            updateStepIndicators(step);
        }

        function prevStep(step) {
            nextStep(step);
        }

        function updateStepIndicators(currentStep) {
            document.querySelectorAll('.step-indicator').forEach(el => {
                const stepNum = parseInt(el.dataset.step);
                const circle = el.querySelector('.step-circle');
                const label = el.querySelector('small');
                
                if (stepNum < currentStep) {
                    circle.classList.remove('bg-primary', 'bg-secondary');
                    circle.classList.add('bg-success');
                    label.classList.remove('fw-bold', 'text-muted');
                    label.classList.add('text-success');
                } else if (stepNum === currentStep) {
                    circle.classList.remove('bg-secondary', 'bg-success');
                    circle.classList.add('bg-primary');
                    label.classList.remove('text-muted', 'text-success');
                    label.classList.add('fw-bold');
                } else {
                    circle.classList.remove('bg-primary', 'bg-success');
                    circle.classList.add('bg-secondary');
                    label.classList.remove('fw-bold', 'text-success');
                    label.classList.add('text-muted');
                }
            });
        }
</script>
@endsection
