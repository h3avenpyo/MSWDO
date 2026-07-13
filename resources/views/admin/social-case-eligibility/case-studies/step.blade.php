@php
    $flowSteps = [
        'Client Visit',
        'Search Client',
        'Eligibility Check',
        'Beneficiary Intake',
        'Requirements Verification',
        'Social Worker Interview',
        'Family Composition',
        'Social Case Assessment',
        'Generate Report',
        'Print / Export',
        'Release Report',
        'Record Assistance',
        'Close Case'
    ];
    $stepMapping = [
        'requirements_verification' => 4,
        'assessment_interview' => 5,
        'family_composition' => 6,
        'social_case_assessment' => 7,
        'report_generation' => 8,
        'print_export' => 9,
        'release_report' => 10,
        'assistance_release' => 11,
        'case_closed' => 12,
    ];
    $currentFlowIndex = $stepMapping[$step] ?? 4;
    $positionLabel = $currentFlowIndex + 1;
    $case = $socialCaseStudy;
    $value = fn ($field, $default = '') => old($field, $case->$field ?? $default);
    $familyMembers = old('family_members', $case->familyMembers->map(fn ($member) => $member->only(['full_name', 'relationship', 'age', 'sex', 'occupation', 'monthly_income', 'is_dependent', 'notes']))->all());
    $formAction = match ($step) {
        'report_generation' => route('admin.social-case-studies.reports.generate', $case),
        'release_report' => route('admin.social-case-studies.reports.release', $case),
        default => route('admin.social-case-studies.step.save', [$case, $step]),
    };
    $formConfirmation = $step === 'release_report' ? "return confirm('Confirm release of this report to the selected recipient? This action will be recorded in the audit log.');" : null;
@endphp

@extends('layouts.admin')

@section('title', $flowSteps[$currentFlowIndex] . ' · Case ' . $case->case_number)
@section('navbar-title', 'Social Case Study Process')

@section('page-styles')
<style>
    .stepper-container {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .timeline-steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        margin-bottom: 2rem;
    }
    .timeline-steps::before {
        content: '';
        position: absolute;
        top: 24px;
        left: 0;
        right: 0;
        height: 4px;
        background: #e5e7eb;
        z-index: 1;
    }
    .timeline-progress {
        position: absolute;
        top: 24px;
        left: 0;
        height: 4px;
        background: var(--primary, #4f46e5);
        z-index: 2;
        transition: width 0.4s ease;
    }
    .timeline-step-item {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 60px;
    }
    .step-badge {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #f3f4f6;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        border: 3px solid #fff;
        box-shadow: 0 0 0 1px #e5e7eb;
        transition: all 0.3s ease;
    }
    .timeline-step-item.completed .step-badge {
        background: #10b981;
        color: #fff;
        box-shadow: 0 0 0 1px #10b981;
    }
    .timeline-step-item.active .step-badge {
        background: var(--primary, #4f46e5);
        color: #fff;
        box-shadow: 0 0 0 1px var(--primary, #4f46e5);
    }
    .step-text {
        font-size: 0.7rem;
        font-weight: 500;
        margin-top: 8px;
        text-align: center;
        white-space: nowrap;
        color: #6b7280;
        max-width: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .timeline-step-item.active .step-text {
        color: var(--primary, #4f46e5);
        font-weight: 700;
    }
    .timeline-step-item.completed .step-text {
        color: #10b981;
    }
    .card-workflow {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .card-workflow-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
        padding: 20px;
    }
    .family-member {
        background-color: #f9fafb;
        transition: all 0.3s ease;
    }
    .family-member:hover {
        background-color: #f3f4f6;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Back to Active Cases -->
    <div class="mb-4">
        <a class="btn btn-link text-decoration-none p-0 text-secondary" href="{{ route('admin.social-case-studies.index') }}">
            <i class="fas fa-chevron-left me-1"></i> Back to Active Cases
        </a>
    </div>

    <!-- Stepper Progress -->
    <div class="stepper-container mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Case Progression Workflow</h5>
                <p class="text-muted small mb-0">Track progress through the 13-step MSWDO flow</p>
            </div>
            <span class="badge bg-primary px-3 py-2 rounded-pill">Step {{ $positionLabel }} of 13</span>
        </div>

        <div class="timeline-steps d-none d-md-flex">
            <!-- Progress Line -->
            <div class="timeline-progress" style="width: {{ ($currentFlowIndex / 12) * 100 }}%"></div>

            @foreach($flowSteps as $idx => $label)
                @php
                    $statusClass = '';
                    if ($idx < $currentFlowIndex) {
                        $statusClass = 'completed';
                    } elseif ($idx === $currentFlowIndex) {
                        $statusClass = 'active';
                    }
                @endphp
                <div class="timeline-step-item {{ $statusClass }}" title="{{ $label }}">
                    <div class="step-badge">
                        @if($idx < $currentFlowIndex)
                            <i class="fas fa-check"></i>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>
                    <div class="step-text">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        <!-- Mobile progress bar -->
        <div class="d-block d-md-none">
            <div class="progress" style="height: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: {{ (($currentFlowIndex + 1) / 13) * 100 }}%"></div>
            </div>
            <p class="text-center text-muted small mt-2 mb-0">Current step: <strong>{{ $flowSteps[$currentFlowIndex] }}</strong></p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-start border-danger border-4">
            <div class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Unable to save this step:</div>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Step Form -->
    <form method="POST" action="{{ $formAction }}" class="card card-workflow shadow-sm" id="stepForm" @if($formConfirmation) onsubmit="{{ $formConfirmation }}" @endif>
        @csrf
        
        <div class="card-workflow-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="h5 mb-0 fw-bold"><i class="fas fa-edit me-2"></i>{{ $flowSteps[$currentFlowIndex] }}</h4>
                <p class="small mb-0 text-white-50">Case: {{ $case->case_number }} | Client: {{ $case->client->full_name }}</p>
            </div>
            @if($case->status === 'Waiting for Requirements')
                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Waiting for Requirements</span>
            @endif
        </div>

        <div class="card-body p-4">
            @if($step === 'requirements_verification')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Date Processed</label>
                        <input required type="date" name="date_processed" value="{{ $value('date_processed', now()->format('Y-m-d')) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Last Name</label>
                        <input required name="client_last_name" value="{{ $value('client_last_name', $case->client->last_name) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">First Name</label>
                        <input required name="client_first_name" value="{{ $value('client_first_name', $case->client->first_name) }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Age</label>
                        <input required type="number" name="client_age" value="{{ $value('client_age', $case->client->birthdate?->age) }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Sex</label>
                        <select required name="client_sex" class="form-select">
                            <option value="">Select</option>
                            <option @selected($value('client_sex',$case->client->gender)==='Male')>Male</option>
                            <option @selected($value('client_sex',$case->client->gender)==='Female')>Female</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Barangay</label>
                        <input required name="client_barangay" value="{{ $value('client_barangay') }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Additional Requirements Listed</label>
                        <textarea name="additional_requirements" class="form-control" placeholder="List any missing or extra documents requested" rows="3">{{ $value('additional_requirements') }}</textarea>
                    </div>
                </div>

                <hr class="my-4">

                <div class="form-check p-3 border rounded border-primary bg-light-subtle d-flex align-items-center">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="requirements_complete" value="1" id="complete" @checked($value('requirements_complete')) style="width: 24px; height: 24px;">
                    <label class="form-check-label fw-bold" for="complete">
                        Mark Requirements as Complete & Verified
                        <span class="d-block text-muted small fw-normal">Check this when the client has submitted all required documents to proceed to the Social Worker Interview.</span>
                    </label>
                </div>

            @elseif($step === 'assessment_interview')
                <div class="mb-3">
                    <label class="form-label fw-bold">Interview Date</label>
                    <input required type="date" name="interview_date" value="{{ $value('interview_date', now()->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Reason for Interview</label>
                    <textarea required name="interview_reason" class="form-control" rows="3" placeholder="State client's presenting request/problem">{{ $value('interview_reason') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Current Situation & Findings</label>
                    <textarea required name="interview_situation" class="form-control" rows="4" placeholder="Detail interview findings, home situation, and observations">{{ $value('interview_situation') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Social Worker Interview Notes <span class="text-muted">(optional)</span></label>
                    <textarea name="interview_notes" class="form-control" rows="3" placeholder="Internal notes or observations">{{ $value('interview_notes') }}</textarea>
                </div>
                <div class="form-check p-3 border rounded border-success bg-light-subtle d-flex align-items-center">
                    <input required class="form-check-input ms-0 me-3" type="checkbox" name="interview_complete" value="1" id="complete" style="width: 24px; height: 24px;">
                    <label class="form-check-label fw-bold" for="complete">
                        Confirm Interview Documentation Complete
                        <span class="d-block text-muted small fw-normal">Verify that the client situation has been interviewed and recorded properly.</span>
                    </label>
                </div>

            @elseif($step === 'family_composition')
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="fas fa-users me-2"></i>Family Composition</h5>
                        <p class="text-muted small mb-0">Add every household member. At least one is required to continue.</p>
                    </div>
                    <button class="btn btn-outline-primary btn-sm fw-bold" type="button" id="addFamilyMember">
                        <i class="fas fa-user-plus me-1"></i>Add Family Member
                    </button>
                </div>

                <div id="familyMembers" class="vstack gap-3">
                    @foreach($familyMembers as $index => $member)
                        <div class="border rounded p-3 family-member">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <strong class="text-secondary"><i class="fas fa-user me-1"></i>Family Member #{{ $index + 1 }}</strong>
                                <button type="button" class="btn btn-outline-danger btn-sm remove-family-member">Remove</button>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Full Name</label>
                                    <input required name="family_members[{{ $index }}][full_name]" value="{{ $member['full_name'] ?? '' }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Relationship</label>
                                    <input required name="family_members[{{ $index }}][relationship]" value="{{ $member['relationship'] ?? '' }}" placeholder="e.g. Spouse, Child" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Age</label>
                                    <input type="number" min="0" name="family_members[{{ $index }}][age]" value="{{ $member['age'] ?? '' }}" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Sex</label>
                                    <select name="family_members[{{ $index }}][sex]" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Male" @selected(($member['sex'] ?? '') === 'Male')>Male</option>
                                        <option value="Female" @selected(($member['sex'] ?? '') === 'Female')>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Occupation</label>
                                    <input name="family_members[{{ $index }}][occupation]" value="{{ $member['occupation'] ?? '' }}" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Monthly Income</label>
                                    <input type="number" min="0" step=".01" name="family_members[{{ $index }}][monthly_income]" value="{{ $member['monthly_income'] ?? '' }}" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Notes</label>
                                    <input name="family_members[{{ $index }}][notes]" value="{{ $member['notes'] ?? '' }}" class="form-control">
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="family_members[{{ $index }}][is_dependent]" value="1" id="dependent{{ $index }}" @checked(!empty($member['is_dependent']))>
                                        <label class="form-check-label" for="dependent{{ $index }}">Dependent household member</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mb-3 mt-4">
                    <label class="form-label fw-bold">Household situation summary <span class="text-muted">(optional)</span></label>
                    <textarea name="interview_household" class="form-control" rows="3" placeholder="Brief household environment or summary details">{{ $value('interview_household') }}</textarea>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Monthly Household Income</label>
                        <div class="input-group">
                            <span class="input-group-text">PHP</span>
                            <input type="number" step=".01" name="monthly_income" value="{{ $value('monthly_income') }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Monthly Expenses</label>
                        <div class="input-group">
                            <span class="input-group-text">PHP</span>
                            <input type="number" step=".01" name="monthly_expenses" value="{{ $value('monthly_expenses') }}" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-bold">Has client received previous assistance?</label>
                    <select required name="previous_assistance" class="form-select">
                        <option value="">Select</option>
                        <option @selected($value('previous_assistance')==='Yes')>Yes</option>
                        <option @selected($value('previous_assistance')==='No')>No</option>
                    </select>
                </div>

            @elseif($step === 'social_case_assessment')
                <div class="mb-3">
                    <label class="form-label fw-bold">Social Worker Assessment / Evaluation findings</label>
                    <textarea required name="social_worker_assessment" class="form-control" rows="5" placeholder="State critical observations and reasons for approving or denying the request">{{ $value('social_worker_assessment') }}</textarea>
                </div>
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Recommendation</label>
                        <select required name="recommendation" class="form-select">
                            <option value="">Select</option>
                            @foreach(['Approved','Needs Additional Info','Not Qualified'] as $option)
                                <option @selected($value('recommendation')===$option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Recommended Amount (PHP)</label>
                        <input type="number" step=".01" name="recommended_amount" value="{{ $value('recommended_amount') }}" class="form-control">
                    </div>
                </div>

            @elseif($step === 'report_generation')
                <div class="alert alert-info d-flex align-items-center shadow-sm">
                    <i class="fas fa-info-circle fa-lg me-3"></i>
                    <div>
                        <strong>Generate Draft Report:</strong> This compiles the recorded intake, interview notes, family composition list, and social case assessment into a point-in-time snapshot.
                    </div>
                </div>
                <dl class="row my-4 bg-light p-3 rounded">
                    <dt class="col-sm-4 text-secondary">Assigned Case number</dt>
                    <dd class="col-sm-8 fw-bold">{{ $case->case_number }}</dd>
                    <dt class="col-sm-4 text-secondary">Client Full Name</dt>
                    <dd class="col-sm-8">{{ $case->client->full_name }}</dd>
                </dl>
                <div class="text-center py-3">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                        <i class="fas fa-file-invoice me-2"></i>Generate official report
                    </button>
                </div>

            @elseif($step === 'print_export')
                <div class="alert alert-success d-flex align-items-center shadow-sm">
                    <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                    <div>
                        <strong>Report Compiled successfully!</strong> You can now preview and export or print the document in multiple formats.
                    </div>
                </div>
                <div class="text-center py-4 bg-light rounded border mb-4">
                    <h6 class="fw-bold mb-3 text-dark">Official Report Documents</h6>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a class="btn btn-outline-primary fw-bold" target="_blank" href="{{ route('admin.social-case-studies.reports.preview', $case) }}">
                            <i class="fas fa-eye me-2"></i>Preview Report
                        </a>
                        <a class="btn btn-primary fw-bold" href="{{ route('admin.social-case-studies.reports.download', $case) }}">
                            <i class="fas fa-file-pdf me-2"></i>Download PDF Format
                        </a>
                        <a class="btn btn-outline-success fw-bold" href="{{ route('admin.social-case-studies.reports.word', $case) }}">
                            <i class="fas fa-file-word me-2"></i>Download Word (.DOC)
                        </a>
                    </div>
                </div>
                <p class="text-muted text-center small mb-0">After printing or downloading the documents, click the button below to proceed to the next step.</p>

            @elseif($step === 'release_report')
                <div class="alert alert-warning d-flex align-items-center shadow-sm">
                    <i class="fas fa-exclamation-triangle fa-lg me-3 text-warning"></i>
                    <div>
                        <strong>Release Notice:</strong> This action records the dispatch of the printed case study report to the client or the requesting office. It does not release financial assistance.
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Release Report To</label>
                    <input required name="released_to" value="{{ old('released_to', $case->submitted_to) }}" class="form-control" placeholder="Client, requesting office, or representative">
                </div>
                <dl class="row mb-4 bg-light p-3 rounded">
                    <dt class="col-sm-4 text-secondary">Report Case Number</dt>
                    <dd class="col-sm-8">{{ $case->case_number }}</dd>
                    <dt class="col-sm-4 text-secondary">Logged Dispatched By</dt>
                    <dd class="col-sm-8">{{ session('admin_user_name') }}</dd>
                    <dt class="col-sm-4 text-secondary">Release Timestamp</dt>
                    <dd class="col-sm-8">{{ now()->format('M d, Y h:i A') }}</dd>
                </dl>
                <div class="text-center py-2">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                        <i class="fas fa-paper-plane me-2"></i>Release Report
                    </button>
                </div>

            @elseif($step === 'assistance_release')
                <div class="alert alert-info d-flex align-items-center shadow-sm">
                    <i class="fas fa-hand-holding-usd fa-lg me-3"></i>
                    <div>
                        <strong>Assistance Release Form:</strong> Fill out the final assistance release details. This will record an official transaction in the client ledger.
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Assistance Amount released</label>
                        <div class="input-group">
                            <span class="input-group-text">PHP</span>
                            <input required type="number" step=".01" name="assistance_amount" value="{{ $value('assistance_amount', $case->recommended_amount) }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Release Date</label>
                        <input required type="date" name="assistance_date" value="{{ $value('assistance_date', now()->format('Y-m-d')) }}" class="form-control">
                    </div>
                </div>
                <div class="form-check p-3 border rounded border-success bg-light-subtle d-flex align-items-center mt-3">
                    <input required class="form-check-input ms-0 me-3" type="checkbox" name="assistance_released" value="1" id="complete" style="width: 24px; height: 24px;">
                    <label class="form-check-label fw-bold" for="complete">
                        Confirm Assistance Funds Received by Client
                        <span class="d-block text-muted small fw-normal">Verify that the funds have been handed over to the client.</span>
                    </label>
                </div>

            @else
                <div class="text-center py-4">
                    <i class="fas fa-lock fa-3x text-secondary mb-3"></i>
                    <h5 class="fw-bold text-dark">Close Case File</h5>
                    <p class="text-muted">Closing this case marks it as complete, archiving all notes, reports, and release logs. It cannot be altered after closure.</p>
                    <input type="hidden" name="status" value="Closed">
                </div>
            @endif
        </div>

        <div class="card-footer bg-white d-flex justify-content-between p-3">
            <a href="{{ route('admin.social-case-studies.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-save me-1"></i>Save Draft & Exit
            </a>
            @if($step !== 'report_generation' && $step !== 'release_report')
                <button class="btn btn-primary" id="submitBtn">
                    {{ $step === 'case_closed' ? 'Close Case File' : 'Save and continue' }} <i class="fas fa-arrow-right ms-1"></i>
                </button>
            @endif
        </div>
    </form>
</div>
@endsection

@section('page-scripts')
<script>
    document.querySelector('form').addEventListener('submit', e => {
        if (!e.target.checkValidity()) {
            e.preventDefault();
            e.target.reportValidity();
        }
    });

    @if($step === 'requirements_verification')
        (() => {
            const reqCheck = document.getElementById('complete');
            const submitBtn = document.getElementById('submitBtn');
            if (reqCheck && submitBtn) {
                const updateButtonText = () => {
                    if (reqCheck.checked) {
                        submitBtn.innerHTML = 'Save & Proceed <i class="fas fa-arrow-right ms-1"></i>';
                        submitBtn.className = 'btn btn-primary';
                    } else {
                        submitBtn.innerHTML = '<i class="fas fa-clock me-1"></i>Save as Incomplete & Exit';
                        submitBtn.className = 'btn btn-warning text-dark fw-bold';
                    }
                };
                reqCheck.addEventListener('change', updateButtonText);
                updateButtonText();
            }
        })();
    @endif

    @if($step === 'family_composition')
        (() => {
            const list = document.getElementById('familyMembers');
            const add = document.getElementById('addFamilyMember');
            let index = {{ count($familyMembers) }};
            
            const row = i => `
                <div class="border rounded p-3 family-member">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <strong class="text-secondary"><i class="fas fa-user me-1"></i>Family Member #${i + 1}</strong>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-family-member">Remove</button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Name</label>
                            <input required name="family_members[${i}][full_name]" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Relationship</label>
                            <input required name="family_members[${i}][relationship]" placeholder="e.g. Spouse, Child" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Age</label>
                            <input type="number" min="0" name="family_members[${i}][age]" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Sex</label>
                            <select name="family_members[${i}][sex]" class="form-select">
                                <option value="">Select</option>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Occupation</label>
                            <input name="family_members[${i}][occupation]" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Monthly Income</label>
                            <input type="number" min="0" step=".01" name="family_members[${i}][monthly_income]" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Notes</label>
                            <input name="family_members[${i}][notes]" class="form-control">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="family_members[${i}][is_dependent]" value="1" id="dependent${i}">
                                <label class="form-check-label" for="dependent${i}">Dependent household member</label>
                            </div>
                        </div>
                    </div>
                </div>`;
            
            add.addEventListener('click', () => {
                list.insertAdjacentHTML('beforeend', row(index++));
            });
            
            list.addEventListener('click', e => {
                if (e.target.classList.contains('remove-family-member')) {
                    e.target.closest('.family-member').remove();
                    // Optional: recalculate member numbering
                    const members = list.querySelectorAll('.family-member');
                    members.forEach((m, idx) => {
                        const title = m.querySelector('strong.text-secondary');
                        if (title) title.innerHTML = `<i class="fas fa-user me-1"></i>Family Member #${idx + 1}`;
                    });
                }
            });
        })();
    @endif
</script>
@endsection
