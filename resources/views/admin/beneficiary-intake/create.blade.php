@extends('layouts.admin')

@section('title', 'Beneficiary Intake Form')

@section('navbar-title', 'Beneficiary Intake Form')

@section('page-styles')
    <style>
        /* Step Wizard Styles */
        .step-wizard {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem 0;
            border-bottom: 2px solid var(--border);
            position: relative;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
            position: relative;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .step-item.active {
            opacity: 1;
        }

        .step-item.completed {
            opacity: 0.7;
        }

        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #E2E8F0;
            color: #64748B;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: 2px solid #E2E8F0;
        }

        .step-item.active .step-circle {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(26, 35, 126, 0.1);
        }

        .step-item.completed .step-circle {
            background-color: var(--success);
            color: white;
            border-color: var(--success);
        }

        .step-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-muted);
            text-align: center;
        }

        .step-item.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }

        .step-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .step-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-success {
            background-color: var(--success);
            border-color: var(--success);
        }

        .btn-success:hover {
            background-color: #059669;
            border-color: #059669;
        }

        .btn-step {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
        }
    </style>
@endsection

@section('content')
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Please correct the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <!-- Step Wizard -->
                    <div class="step-wizard">
                        <div class="step-item active" data-step="1">
                            <div class="step-circle">1</div>
                            <div class="step-label">Processing</div>
                        </div>
                        <div class="step-item" data-step="2">
                            <div class="step-circle">2</div>
                            <div class="step-label">Client</div>
                        </div>
                        <div class="step-item" data-step="3">
                            <div class="step-circle">3</div>
                            <div class="step-label">Beneficiary</div>
                        </div>
                        <div class="step-item" data-step="4">
                            <div class="step-circle">4</div>
                            <div class="step-label">Medical</div>
                        </div>
                        <div class="step-item" data-step="5">
                            <div class="step-circle">5</div>
                            <div class="step-label">Service</div>
                        </div>
                        <div class="step-item" data-step="6">
                            <div class="step-circle">6</div>
                            <div class="step-label">Purpose</div>
                        </div>
                        <div class="step-item" data-step="7">
                            <div class="step-circle">7</div>
                            <div class="step-label">Submitted To</div>
                        </div>
                        <div class="step-item" data-step="8">
                            <div class="step-circle">8</div>
                            <div class="step-label">Review</div>
                        </div>
                    </div>

                    <form id="intakeForm" action="{{ route('admin.beneficiary-intake.store') }}" method="POST" novalidate>
                        @csrf
                        <input type="hidden" name="client_id" value="{{ $client?->id }}">

                        <!-- Step 1: Processing Information -->
                        <div class="step-content active" data-step="1">
                            <h5 class="mb-4" style="font-weight: 600; color: var(--text);">Processing Information</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Control Number</label>
                                    <input type="text" name="control_number" class="form-control" value="{{ $controlNumber }}" readonly style="background-color: #F8FAFC;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date Processed</label>
                                    <input type="date" name="date_processed" class="form-control" value="{{ old('date_processed', now()->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Encoder</label>
                                    <input type="text" name="encoder" class="form-control" value="{{ $encoder }}" readonly style="background-color: #F8FAFC;">
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Client Information -->
                        <div class="step-content" data-step="2">
                            <h5 class="mb-4" style="font-weight: 600; color: var(--text);">Client Information</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="client_last_name" class="form-control" value="{{ old('client_last_name', $client?->last_name) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="client_first_name" class="form-control" value="{{ old('client_first_name', $client?->first_name) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="client_middle_name" class="form-control" value="{{ old('client_middle_name', $client?->middle_name) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Birthday</label>
                                    <input type="date" name="client_birthday" class="form-control" value="{{ old('client_birthday', $client?->birthdate?->format('Y-m-d')) }}" required onchange="calculateClientAge()">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Age</label>
                                    <input type="number" name="client_age" class="form-control" value="{{ old('client_age', $client?->birthdate?->age) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Sex</label>
                                    <select name="client_sex" class="form-select" required>
                                        <option value="">Select Sex</option>
                                        <option value="Male" @selected(old('client_sex', $client?->gender) === 'Male')>Male</option>
                                        <option value="Female" @selected(old('client_sex', $client?->gender) === 'Female')>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Civil Status</label>
                                    <select name="client_civil_status" class="form-select" required>
                                        <option value="">Select Civil Status</option>
                                        <option value="Single" @selected(old('client_civil_status') === 'Single')>Single</option>
                                        <option value="Married" @selected(old('client_civil_status') === 'Married')>Married</option>
                                        <option value="Widowed" @selected(old('client_civil_status') === 'Widowed')>Widowed</option>
                                        <option value="Separated" @selected(old('client_civil_status') === 'Separated')>Separated</option>
                                        <option value="Divorced" @selected(old('client_civil_status') === 'Divorced')>Divorced</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Address</label>
                                    <textarea name="client_address" class="form-control" rows="2" required>{{ old('client_address', $client?->address) }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Barangay</label>
                                    <input type="text" name="client_barangay" class="form-control" value="{{ old('client_barangay') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" name="client_contact_number" class="form-control" value="{{ old('client_contact_number', $client?->contact_number) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Occupation</label>
                                    <input type="text" name="client_occupation" class="form-control" value="{{ old('client_occupation') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Monthly Income</label>
                                    <input type="number" name="client_monthly_income" class="form-control" step="0.01" value="{{ old('client_monthly_income') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Beneficiary Information -->
                        <div class="step-content" data-step="3">
                            <h5 class="mb-4" style="font-weight: 600; color: var(--text);">Beneficiary Information</h5>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="is_client_beneficiary" id="isClientBeneficiary" value="1" @checked(old('is_client_beneficiary', true)) onchange="toggleBeneficiaryFields()">
                                <label class="form-check-label" for="isClientBeneficiary">
                                    Client is also the Beneficiary
                                </label>
                            </div>
                            
                            <div id="beneficiaryFields" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" name="beneficiary_last_name" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <input type="text" name="beneficiary_first_name" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" name="beneficiary_middle_name" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Birthday</label>
                                        <input type="date" name="beneficiary_birthday" class="form-control" onchange="calculateBeneficiaryAge()">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Age</label>
                                        <input type="number" name="beneficiary_age" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Sex</label>
                                        <select name="beneficiary_sex" class="form-select">
                                            <option value="">Select Sex</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Barangay</label>
                                        <input type="text" name="beneficiary_barangay" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Relationship to Client</label>
                                        <input type="text" name="beneficiary_relationship" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Medical Condition -->
                        <div class="step-content" data-step="4">
                            <h5 class="mb-4" style="font-weight: 600; color: var(--text);">Medical Condition</h5>
                            <div class="checkbox-group mb-4">
                                <div class="checkbox-item">
                                    <input type="checkbox" name="medical_conditions[]" value="Cancer" id="med_cancer">
                                    <label for="med_cancer">Cancer</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="medical_conditions[]" value="Cardiovascular" id="med_cardio">
                                    <label for="med_cardio">Cardiovascular</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="medical_conditions[]" value="Kidney Disease" id="med_kidney">
                                    <label for="med_kidney">Kidney Disease</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="medical_conditions[]" value="Neurological" id="med_neuro">
                                    <label for="med_neuro">Neurological</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="medical_conditions[]" value="Respiratory" id="med_resp">
                                    <label for="med_resp">Respiratory</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="medical_conditions[]" value="Diabetes" id="med_diabetes">
                                    <label for="med_diabetes">Diabetes</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="medical_conditions[]" value="Surgical" id="med_surgical">
                                    <label for="med_surgical">Surgical</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="medical_conditions[]" value="Trauma" id="med_trauma">
                                    <label for="med_trauma">Trauma</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="medical_conditions[]" value="Infectious Disease" id="med_infectious">
                                    <label for="med_infectious">Infectious Disease</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="medical_conditions[]" value="Special Welfare Case" id="med_welfare">
                                    <label for="med_welfare">Special Welfare Case</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="medical_conditions[]" value="Other" id="med_other" onchange="toggleMedicalOther()">
                                    <label for="med_other">Other</label>
                                </div>
                            </div>
                            <div id="medicalOtherField" style="display: none;">
                                <label class="form-label">Please specify</label>
                                <input type="text" name="medical_condition_other" class="form-control">
                            </div>
                        </div>

                        <!-- Step 5: Service Provided -->
                        <div class="step-content" data-step="5">
                            <h5 class="mb-4" style="font-weight: 600; color: var(--text);">Service Provided</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="service_provided" id="service_social" value="Social Case Study" @checked(old('service_provided', 'Social Case Study') === 'Social Case Study') required>
                                <label class="form-check-label" for="service_social">Social Case Study</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="service_provided" id="service_general" value="General Intake" @checked(old('service_provided') === 'General Intake')>
                                <label class="form-check-label" for="service_general">General Intake</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="service_provided" id="service_certification" value="Certification" @checked(old('service_provided') === 'Certification')>
                                <label class="form-check-label" for="service_certification">Certification</label>
                            </div>
                        </div>

                        <!-- Step 6: Purpose -->
                        <div class="step-content" data-step="6">
                            <h5 class="mb-4" style="font-weight: 600; color: var(--text);">Purpose</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <select name="purpose" class="form-select" required onchange="togglePurposeOther()">
                                        <option value="">Select Purpose</option>
                                        <option value="Financial Assistance">Financial Assistance</option>
                                        <option value="Medical Assistance">Medical Assistance</option>
                                        <option value="Burial Assistance">Burial Assistance</option>
                                        <option value="Birth Correction">Birth Correction</option>
                                        <option value="PhilHealth">PhilHealth</option>
                                        <option value="Meralco">Meralco</option>
                                        <option value="CHED">CHED</option>
                                        <option value="Fire Incident">Fire Incident</option>
                                        <option value="Natural Disaster">Natural Disaster</option>
                                        <option value="Drug Rehabilitation">Drug Rehabilitation</option>
                                        <option value="Balik Probinsya">Balik Probinsya</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <div id="purposeOtherField" style="display: none;">
                                        <label class="form-label">Please specify</label>
                                        <input type="text" name="purpose_other" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 7: Submitted To -->
                        <div class="step-content" data-step="7">
                            <h5 class="mb-4" style="font-weight: 600; color: var(--text);">Submitted To</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <select name="submitted_to" class="form-select" required>
                                        <option value="">Select Office</option>
                                        <option value="Office of the President">Office of the President</option>
                                        <option value="Office of the Vice President">Office of the Vice President</option>
                                        <option value="DSWD Regional">DSWD Regional</option>
                                        <option value="DSWD Central">DSWD Central</option>
                                        <option value="DOH">DOH</option>
                                        <option value="PCSO">PCSO</option>
                                        <option value="Congressman">Congressman</option>
                                        <option value="Vice Mayor">Vice Mayor</option>
                                        <option value="PhilHealth">PhilHealth</option>
                                        <option value="Institution">Institution</option>
                                        <option value="Satellite Office">Satellite Office</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Step 8: Review -->
                        <div class="step-content" data-step="8">
                            <h5 class="mb-4" style="font-weight: 600; color: var(--text);">Review Intake Information</h5>
                            <div id="reviewContent" class="alert alert-info">
                                Please review all information before submitting.
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-primary btn-step" id="prevBtn" onclick="prevStep()" style="display: none;">Previous</button>
                            <button type="button" class="btn btn-primary btn-step" id="nextBtn" onclick="nextStep()">Next</button>
                            <button type="submit" class="btn btn-success btn-step" id="submitBtn" style="display: none;">Save Intake</button>
                        </div>
                    </form>
                </div>
            </div>
@endsection

@section('page-scripts')
    <script>
        let currentStep = 1;
        const totalSteps = 8;

        function getStepFields(step) {
            const stepEl = document.querySelector(`.step-content[data-step="${step}"]`);
            return [...stepEl.querySelectorAll('input, select, textarea')].filter(el => {
                if (el.type === 'hidden' || el.disabled) {
                    return false;
                }

                if (step === 3 && document.getElementById('isClientBeneficiary').checked && el.closest('#beneficiaryFields')) {
                    return false;
                }

                return true;
            });
        }

        function validateStep(step) {
            const fields = getStepFields(step);
            let valid = true;
            let firstInvalid = null;

            if (step === 5) {
                const serviceSelected = document.querySelector('input[name="service_provided"]:checked');
                if (!serviceSelected) {
                    valid = false;
                }
            }

            fields.forEach(field => {
                field.classList.remove('is-invalid');

                if (field.type === 'radio') {
                    return;
                }

                if (!field.checkValidity()) {
                    valid = false;
                    field.classList.add('is-invalid');
                    if (!firstInvalid) {
                        firstInvalid = field;
                    }
                }
            });

            if (step === 4 && document.getElementById('med_other').checked) {
                const otherField = document.querySelector('input[name="medical_condition_other"]');
                otherField.required = true;
                if (!otherField.value.trim()) {
                    valid = false;
                    otherField.classList.add('is-invalid');
                    if (!firstInvalid) {
                        firstInvalid = otherField;
                    }
                }
            }

            if (step === 6 && document.querySelector('select[name="purpose"]').value === 'Others') {
                const otherField = document.querySelector('input[name="purpose_other"]');
                otherField.required = true;
                if (!otherField.value.trim()) {
                    valid = false;
                    otherField.classList.add('is-invalid');
                    if (!firstInvalid) {
                        firstInvalid = otherField;
                    }
                }
            }

            if (!valid) {
                if (firstInvalid) {
                    firstInvalid.focus();
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Information',
                    text: 'Please fill in all required fields before continuing.',
                    confirmButtonColor: '#1A237E'
                });
            }

            return valid;
        }

        function showStep(step) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.step-item').forEach(el => {
                el.classList.remove('active');
                el.classList.remove('completed');
            });

            document.querySelector(`.step-content[data-step="${step}"]`).classList.add('active');
            document.querySelector(`.step-item[data-step="${step}"]`).classList.add('active');

            for (let i = 1; i < step; i++) {
                document.querySelector(`.step-item[data-step="${i}"]`).classList.add('completed');
            }

            document.getElementById('prevBtn').style.display = step > 1 ? 'inline-block' : 'none';
            document.getElementById('nextBtn').style.display = step < totalSteps ? 'inline-block' : 'none';
            document.getElementById('submitBtn').style.display = step === totalSteps ? 'inline-block' : 'none';

            if (step === totalSteps) {
                populateReview();
            }

            currentStep = step;
        }

        function nextStep() {
            if (!validateStep(currentStep)) {
                return;
            }

            if (currentStep < totalSteps) {
                showStep(currentStep + 1);
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                showStep(currentStep - 1);
            }
        }

        function toggleBeneficiaryFields() {
            const isClientBeneficiary = document.getElementById('isClientBeneficiary').checked;
            document.getElementById('beneficiaryFields').style.display = isClientBeneficiary ? 'none' : 'block';
        }

        function toggleMedicalOther() {
            const isOther = document.getElementById('med_other').checked;
            document.getElementById('medicalOtherField').style.display = isOther ? 'block' : 'none';
        }

        function togglePurposeOther() {
            const purpose = document.querySelector('select[name="purpose"]').value;
            document.getElementById('purposeOtherField').style.display = purpose === 'Others' ? 'block' : 'none';
        }

        function calculateClientAge() {
            const birthday = document.querySelector('input[name="client_birthday"]').value;
            if (birthday) {
                const age = new Date().getFullYear() - new Date(birthday).getFullYear();
                document.querySelector('input[name="client_age"]').value = age;
            }
        }

        function calculateBeneficiaryAge() {
            const birthday = document.querySelector('input[name="beneficiary_birthday"]').value;
            if (birthday) {
                const age = new Date().getFullYear() - new Date(birthday).getFullYear();
                document.querySelector('input[name="beneficiary_age"]').value = age;
            }
        }

        function populateReview() {
            const form = document.getElementById('intakeForm');
            const formData = new FormData(form);
            let html = '<h6>Processing Information</h6>';
            html += `<p><strong>Control Number:</strong> ${formData.get('control_number')}</p>`;
            html += `<p><strong>Date Processed:</strong> ${formData.get('date_processed')}</p>`;
            html += `<p><strong>Encoder:</strong> ${formData.get('encoder')}</p>`;

            html += '<hr><h6>Client Information</h6>';
            html += `<p><strong>Name:</strong> ${formData.get('client_first_name')} ${formData.get('client_middle_name')} ${formData.get('client_last_name')}</p>`;
            html += `<p><strong>Birthday:</strong> ${formData.get('client_birthday')} (Age: ${formData.get('client_age')})</p>`;
            html += `<p><strong>Sex:</strong> ${formData.get('client_sex')}</p>`;
            html += `<p><strong>Civil Status:</strong> ${formData.get('client_civil_status')}</p>`;
            html += `<p><strong>Address:</strong> ${formData.get('client_address')}</p>`;
            html += `<p><strong>Barangay:</strong> ${formData.get('client_barangay')}</p>`;
            html += `<p><strong>Contact:</strong> ${formData.get('client_contact_number')}</p>`;

            const isClientBeneficiary = formData.get('is_client_beneficiary');
            html += '<hr><h6>Beneficiary Information</h6>';
            html += `<p><strong>Client is Beneficiary:</strong> ${isClientBeneficiary ? 'Yes' : 'No'}</p>`;
            if (!isClientBeneficiary) {
                html += `<p><strong>Beneficiary Name:</strong> ${formData.get('beneficiary_first_name')} ${formData.get('beneficiary_middle_name')} ${formData.get('beneficiary_last_name')}</p>`;
                html += `<p><strong>Relationship:</strong> ${formData.get('beneficiary_relationship')}</p>`;
            }

            html += '<hr><h6>Medical Conditions</h6>';
            const conditions = formData.getAll('medical_conditions[]');
            html += `<p>${conditions.length > 0 ? conditions.join(', ') : 'None'}</p>`;
            if (conditions.includes('Other')) {
                html += `<p><strong>Other:</strong> ${formData.get('medical_condition_other')}</p>`;
            }

            html += '<hr><h6>Service Provided</h6>';
            html += `<p>${formData.get('service_provided')}</p>`;

            html += '<hr><h6>Purpose</h6>';
            html += `<p>${formData.get('purpose')}</p>`;
            if (formData.get('purpose') === 'Others') {
                html += `<p><strong>Other:</strong> ${formData.get('purpose_other')}</p>`;
            }

            html += '<hr><h6>Submitted To</h6>';
            html += `<p>${formData.get('submitted_to')}</p>`;

            document.getElementById('reviewContent').innerHTML = html;
        }

        document.getElementById('intakeForm').addEventListener('submit', function (event) {
            for (let step = 1; step <= 7; step++) {
                if (!validateStep(step)) {
                    event.preventDefault();
                    showStep(step);
                    return;
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            toggleBeneficiaryFields();
            toggleMedicalOther();
            togglePurposeOther();

            @if($errors->any())
                showStep(2);
            @endif
        });
    </script>
@endsection
