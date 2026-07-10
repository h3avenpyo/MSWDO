<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beneficiary Intake Form | MSWDO Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #1A237E;
            --primary-dark: #121858;
            --secondary: #374151;
            --accent: #FBC02D;
            --danger: #D32F2F;
            --background: #F1F5F9;
            --cards: #FFFFFF;
            --text: #111827;
            --text-muted: #4B5563;
            --sidebar-bg: #1A237E;
            --border: #D1D5DB;
        }

        body {
            background-color: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            margin: 0;
            padding: 0;
        }

        /* Sidebar */
        .sidebar {
            background: var(--sidebar-bg);
            width: 260px;
            min-height: 100vh;
            position: fixed;
            left: 0; top: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform .3s ease;
        }
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: .65rem;
        }
        .sidebar-brand i { font-size: 1.3rem; color: var(--accent); }
        .sidebar-menu {
            list-style: none;
            margin: 0;
            padding: 1rem 0;
            flex: 1;
        }
        .sidebar-menu li { margin-bottom: .2rem; }
        .sidebar-menu a {
            color: rgba(255,255,255,.75);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
            font-size: .9rem;
            border-left: 3px solid transparent;
            transition: all .2s ease;
        }
        .sidebar-menu a:hover {
            background: rgba(255,255,255,.1);
            color: var(--accent);
        }
        .sidebar-menu a.active {
            background: rgba(255,255,255,.1);
            color: var(--accent);
            border-left-color: var(--accent);
        }
        .sidebar-menu a i { width: 20px; text-align: center; }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 260px);
        }

        .top-navbar {
            background-color: var(--cards);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            flex-shrink: 0;
        }

        /* Cards */
        .card {
            background-color: var(--cards);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.12);
        }

        .card-body { padding: 1.5rem; }

        .form-control, .form-select {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: .75rem 1rem;
            color: var(--text);
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,35,126,.08); }
        .form-label { font-weight: 600; color: #475569; margin-bottom: .55rem; }

        /* Step Wizard */
        .step-wizard {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        .step-wizard::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--border);
            z-index: 0;
            transform: translateY(-50%);
        }
        .step-item {
            position: relative;
            z-index: 1;
            text-align: center;
            flex: 1;
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--cards);
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: all .3s ease;
        }
        .step-item.active .step-circle {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        .step-item.completed .step-circle {
            background: #22C55E;
            border-color: #22C55E;
            color: white;
        }
        .step-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
        }
        .step-item.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }
        .step-item.completed .step-label {
            color: #22C55E;
        }

        .step-content {
            display: none;
        }
        .step-content.active {
            display: block;
        }

        .btn-step {
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
        }

        .page-title { font-size: 1.15rem; font-weight: 700; margin: 0; }
        .btn-icon {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 8px;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: all .2s ease;
        }
        .btn-icon:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .step-wizard {
                flex-wrap: wrap;
            }
            .step-wizard::before {
                display: none;
            }
            .step-item {
                flex: 0 0 33.333%;
                margin-bottom: 1rem;
            }
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.75rem;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand"><i class="fas fa-building"></i> MSWDO Admin</div>
        <ul class="sidebar-menu">
            <li><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="/admin/senior"><i class="fas fa-user"></i> Senior Citizen</a></li>
            <li><a href="/admin/social-case/dashboard"><i class="fas fa-home"></i> Social Case Dashboard</a></li>
            <li><a href="/admin/beneficiary-intake" class="active"><i class="fas fa-clipboard-list"></i> Beneficiary Intake</a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <!-- Top Navigation -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-md-none me-3" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 me-4">Beneficiary Intake Form</h5>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-4 text-muted small" id="currentDateTime"></div>
                    <div class="activity-avatar" style="width: 35px; height: 35px; font-size: 0.875rem; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 50%;">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="p-4" style="flex: 1; overflow: hidden; display: flex; flex-direction: column;">
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

                    <form id="intakeForm" action="{{ route('admin.beneficiary-intake.store') }}" method="POST">
                        @csrf

                        <!-- Step 1: Processing Information -->
                        <div class="step-content active" data-step="1">
                            <h5 class="mb-4">Processing Information</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Control Number</label>
                                    <input type="text" name="control_number" class="form-control" value="{{ $controlNumber }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date Processed</label>
                                    <input type="date" name="date_processed" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Encoder</label>
                                    <input type="text" name="encoder" class="form-control" value="{{ $encoder }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Client Information -->
                        <div class="step-content" data-step="2">
                            <h5 class="mb-4">Client Information</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="client_last_name" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="client_first_name" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="client_middle_name" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Birthday</label>
                                    <input type="date" name="client_birthday" class="form-control" required onchange="calculateClientAge()">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Age</label>
                                    <input type="number" name="client_age" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Sex</label>
                                    <select name="client_sex" class="form-select" required>
                                        <option value="">Select Sex</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Civil Status</label>
                                    <select name="client_civil_status" class="form-select" required>
                                        <option value="">Select Civil Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                        <option value="Divorced">Divorced</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Address</label>
                                    <textarea name="client_address" class="form-control" rows="2" required></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Barangay</label>
                                    <input type="text" name="client_barangay" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" name="client_contact_number" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Occupation</label>
                                    <input type="text" name="client_occupation" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Monthly Income</label>
                                    <input type="number" name="client_monthly_income" class="form-control" step="0.01">
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Beneficiary Information -->
                        <div class="step-content" data-step="3">
                            <h5 class="mb-4">Beneficiary Information</h5>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="is_client_beneficiary" id="isClientBeneficiary" value="1" checked onchange="toggleBeneficiaryFields()">
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
                            <h5 class="mb-4">Medical Condition</h5>
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
                            <h5 class="mb-4">Service Provided</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="service_provided" id="service_social" value="Social Case Study" required>
                                <label class="form-check-label" for="service_social">Social Case Study</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="service_provided" id="service_general" value="General Intake">
                                <label class="form-check-label" for="service_general">General Intake</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="service_provided" id="service_certification" value="Certification">
                                <label class="form-check-label" for="service_certification">Certification</label>
                            </div>
                        </div>

                        <!-- Step 6: Purpose -->
                        <div class="step-content" data-step="6">
                            <h5 class="mb-4">Purpose</h5>
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
                            <h5 class="mb-4">Submitted To</h5>
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
                            <h5 class="mb-4">Review Intake Information</h5>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 8;

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.step-item').forEach(el => el.classList.remove('active'));
            
            // Show current step
            document.querySelector(`.step-content[data-step="${step}"]`).classList.add('active');
            document.querySelector(`.step-item[data-step="${step}"]`).classList.add('active');
            
            // Mark previous steps as completed
            for (let i = 1; i < step; i++) {
                document.querySelector(`.step-item[data-step="${i}"]`).classList.add('completed');
            }
            
            // Update buttons
            document.getElementById('prevBtn').style.display = step > 1 ? 'inline-block' : 'none';
            document.getElementById('nextBtn').style.display = step < totalSteps ? 'inline-block' : 'none';
            document.getElementById('submitBtn').style.display = step === totalSteps ? 'inline-block' : 'none';
            
            // If on review step, populate review content
            if (step === totalSteps) {
                populateReview();
            }
            
            currentStep = step;
        }

        function nextStep() {
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

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        function confirmLogout(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to log out?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1A237E',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, log out',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);
    </script>

    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>
</html>
