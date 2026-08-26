@extends('layouts.financial')

@section('title', 'Financial Assistance Step 2 - Process: ' . $intake->beneficiary_full_name)
@section('page-title', 'Financial Assistance Step 2 Processing')

@section('page-styles')
<style>
.detail-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    margin-bottom: 24px;
    overflow: hidden;
}
.detail-header-bar {
    background: linear-gradient(135deg, #1A237E 0%, #283593 100%);
    padding: 16px 24px;
    color: #FFFFFF;
}
.section-tag {
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #1A237E;
    background: #EEF2FF;
    padding: 4px 10px;
    border-radius: 6px;
    display: inline-block;
    margin-bottom: 6px;
}
.info-label {
    font-size: 0.73rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748B;
    margin-bottom: 2px;
}
.info-value {
    font-size: 0.92rem;
    font-weight: 600;
    color: #1E293B;
}
.grant-hero-card {
    background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
    border: 2px solid #86EFAC;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 14px rgba(22, 163, 74, 0.06);
}
.step-banner-pill {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 50px;
    padding: 6px 16px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #475569;
}
.step-banner-pill.active {
    background: #1A237E;
    color: #FFFFFF;
    border-color: #1A237E;
}
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Top Navigation Breadcrumb & Back Action -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.financial.financialstep2') }}" class="text-decoration-none text-muted small">
                    <i class="fas fa-list me-1"></i> Financial Masterlist
                </a>
                <span class="text-muted small">/</span>
                <span class="text-primary fw-semibold small">Step 2: Client Processing</span>
            </div>
            <h4 class="fw-bold mb-0" style="color: #1A237E;">
                Financial Assistance Processing: {{ $intake->beneficiary_full_name }}
            </h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.financial.financialstep2') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Masterlist
            </a>
            <a href="{{ route('admin.beneficiary-intake.show', $intake) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-file-invoice me-1"></i> Full Intake Sheet
            </a>
            <a href="{{ route('admin.beneficiary-intake.transmittal', ['ids' => $intake->id]) }}" target="_blank" class="btn btn-sm btn-brand rounded-pill px-4">
                <i class="fas fa-print me-1"></i> Print Disbursement Voucher
            </a>
        </div>
    </div>

    <!-- Step Pipeline Progress Card -->
    <div class="card border-0 shadow-xs rounded-4 mb-4 p-3 bg-white">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="step-banner-pill">
                    <i class="fas fa-check-circle text-success"></i> Step 1: General Intake Completed
                </div>
                <i class="fas fa-chevron-right text-muted small"></i>
                <div class="step-banner-pill active">
                    <i class="fas fa-hand-holding-usd text-warning"></i> Step 2: Verification &amp; Cash Disbursement Active
                </div>
            </div>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.8rem;">
                <i class="fas fa-shield-check me-1"></i> Client Verified from Step 1
            </span>
        </div>
    </div>

    <!-- Grant & Assessment Hero Card (Step 2 Focus) -->
    <div class="grant-hero-card animate-fade-in">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom border-success-subtle">
            <div>
                <span class="badge bg-success text-white px-3 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
                    STEP 1 ASSESSED GRANT BASELINE
                </span>
                <h5 class="fw-bold text-dark mt-2 mb-0">Financial Assistance Grant Authorization</h5>
            </div>
            <div class="text-end">
                <div class="text-muted small">Intake Control Number</div>
                <div class="fs-5 fw-bold" style="color: #1A237E;">{{ $intake->control_number }}</div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="info-label">Assessed Grant Amount</div>
                <div class="fs-2 fw-bold text-success">
                    @if($intake->recommended_amount)
                        &#8369;{{ number_format($intake->recommended_amount, 2) }}
                    @else
                        &#8369;0.00 <span class="text-muted fs-6 fw-normal">(To be assessed)</span>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-label">Assistance Type</div>
                <div class="fs-5 fw-bold text-primary">{{ $intake->recommended_assistance_type ?? $intake->service_provided ?? 'Financial Assistance' }}</div>
            </div>
            <div class="col-md-6">
                <div class="info-label">Assistance Purpose / Medical Diagnosis</div>
                <div class="fs-6 fw-semibold text-dark">{{ $intake->display_assistance_purpose }}</div>
                @if(!empty($intake->medical_conditions) && is_array($intake->medical_conditions))
                <div class="mt-1">
                    @foreach($intake->medical_conditions as $cond)
                    <span class="badge bg-white text-danger border border-danger-subtle px-2 py-0.5 rounded-pill me-1" style="font-size: 0.72rem;">{{ $cond }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="col-12">
                <div class="info-label">Social Worker Assessment Findings (Step 1 Record)</div>
                <div class="p-3 bg-white rounded-3 border mt-1" style="font-size: 0.88rem; color: #334155; line-height: 1.5; white-space: pre-line;">{{ $intake->social_worker_assessment ?? 'Initial assessment completed during General Intake in Step 1.' }}</div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Section I: Beneficiary Identifying Information -->
        <div class="col-lg-8">
            <div class="detail-card animate-fade-in">
                <div class="detail-header-bar d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small">General Intake Section I</div>
                        <h5 class="fw-bold mb-0 text-white"><i class="fas fa-user me-2"></i> Beneficiary Identifying Information</h5>
                    </div>
                    <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill">{{ $intake->client_type ?? 'New' }} Client</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <div class="info-label">Buong Pangalan (Full Name)</div>
                            <div class="info-value fs-5 text-dark">{{ $intake->beneficiary_full_name }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Kasarian / Edad</div>
                            <div class="info-value">{{ $intake->beneficiary_sex ?? 'N/A' }} / {{ $intake->beneficiary_age ? $intake->beneficiary_age . ' yrs' : 'N/A' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Kapanganakan (Birthday)</div>
                            <div class="info-value">{{ $intake->beneficiary_birthday ? $intake->beneficiary_birthday->format('M d, Y') : 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Numero ng Telepono (Contact)</div>
                            <div class="info-value">{{ $intake->beneficiary_contact_number ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Civil Status</div>
                            <div class="info-value">{{ $intake->beneficiary_civil_status ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Trabaho / Buwanang Kita</div>
                            <div class="info-value">
                                {{ $intake->beneficiary_occupation ?? 'None / Unemployed' }}
                                @if($intake->beneficiary_monthly_salary)
                                    <span class="text-muted small">(&#8369;{{ number_format($intake->beneficiary_monthly_salary, 2) }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Tirahan (Complete Address)</div>
                            <div class="info-value">{{ $intake->beneficiary_address_formatted }}</div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Kategorya ng Benepisyaryo (Categories)</div>
                            <div class="mt-1">
                                @if(!empty($intake->beneficiary_categories) && is_array($intake->beneficiary_categories))
                                    @foreach($intake->beneficiary_categories as $cat)
                                    <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill me-1 fw-semibold">{{ $cat }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill fw-semibold">{{ $intake->display_category }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section II: Representative Information (If applicable) -->
            @if($intake->has_representative)
            <div class="detail-card animate-fade-in">
                <div class="detail-header-bar d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #2E7D32 0%, #388E3C 100%);">
                    <div>
                        <div class="text-white-50 small">General Intake Section II</div>
                        <h5 class="fw-bold mb-0 text-white"><i class="fas fa-user-friends me-2"></i> Authorized Representative Details</h5>
                    </div>
                    <span class="badge bg-white text-success fw-bold px-3 py-1 rounded-pill">{{ $intake->rep_relationship ?? 'Authorized Representative' }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-label">Pangalan ng Kinatawan (Rep Name)</div>
                            <div class="info-value fs-5">{{ $intake->representative_full_name }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Relasyon sa Benepisyaryo</div>
                            <div class="info-value text-success fw-bold">{{ $intake->rep_relationship ?? 'Representative' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Kasarian / Edad</div>
                            <div class="info-value">{{ $intake->rep_sex ?? 'N/A' }} / {{ $intake->rep_age ? $intake->rep_age . ' yrs' : 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Contact Number</div>
                            <div class="info-value">{{ $intake->rep_contact_number ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-label">Tirahan ng Kinatawan</div>
                            <div class="info-value">{{ $intake->representative_address_formatted ?? 'Same as Beneficiary' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Section III: Family Composition -->
            <div class="detail-card animate-fade-in">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="fas fa-users me-2 text-primary"></i> Family Composition (From Step 1)</h6>
                        <span class="text-muted small">
                            {{ is_array($intake->family_composition) ? count($intake->family_composition) : 0 }} Member(s)
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.84rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Pangalan (Name)</th>
                                    <th>Relasyon</th>
                                    <th>Edad</th>
                                    <th>Civil Status</th>
                                    <th>Trabaho</th>
                                    <th>Kita</th>
                                    <th>Edukasyon</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($intake->family_composition) && is_array($intake->family_composition))
                                    @foreach($intake->family_composition as $member)
                                    <tr>
                                        <td class="fw-semibold">{{ $member['name'] ?? '--' }}</td>
                                        <td>{{ $member['relationship'] ?? '--' }}</td>
                                        <td>{{ $member['age'] ?? '--' }}</td>
                                        <td>{{ $member['civil_status'] ?? '--' }}</td>
                                        <td>{{ $member['occupation'] ?? '--' }}</td>
                                        <td>{{ !empty($member['monthly_income']) ? '₱' . number_format((float)$member['monthly_income'], 2) : '--' }}</td>
                                        <td>{{ $member['educational_attainment'] ?? $member['education'] ?? '--' }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="7" class="text-center text-muted py-3">No family members recorded in Step 1.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions & Officer Verification Panel -->
        <div class="col-lg-4">
            <!-- Step 2 Processing Action Card -->
            <div class="detail-card animate-fade-in mb-4">
                <div class="detail-header-bar">
                    <h6 class="fw-bold mb-0 text-white"><i class="fas fa-tasks me-2"></i> Step 2 Processing Actions</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2 mb-3">
                        <a href="{{ route('admin.beneficiary-intake.transmittal', ['ids' => $intake->id]) }}" target="_blank" class="btn btn-primary py-2.5 rounded-3 fw-semibold" style="background: #1A237E; border-color: #1A237E;">
                            <i class="fas fa-print me-2"></i> Print Disbursement Voucher
                        </a>
                        <button type="button" class="btn btn-success py-2.5 rounded-3 fw-semibold" onclick="certifyDisbursement('{{ $intake->beneficiary_full_name }}', '{{ number_format($intake->recommended_amount ?? 0, 2) }}')">
                            <i class="fas fa-check-circle me-2"></i> Certify Payout Release
                        </button>
                        <a href="{{ route('admin.beneficiary-intake.show', $intake) }}" class="btn btn-outline-secondary py-2 rounded-3">
                            <i class="fas fa-file-alt me-2"></i> View General Intake Sheet
                        </a>
                    </div>
                    <hr class="my-3">
                    <div class="text-muted small">
                        <i class="fas fa-info-circle text-primary me-1"></i> All voucher printouts and records will use the verified data collected from this client's Step 1 Intake Sheet.
                    </div>
                </div>
            </div>

            <!-- Case Officers & Audit Card -->
            <div class="detail-card animate-fade-in">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-user-shield me-2 text-primary"></i> Case Officers &amp; History</h6>
                    <div class="mb-3">
                        <div class="info-label">Date Processed</div>
                        <div class="info-value">{{ $intake->date_processed ? $intake->date_processed->format('F d, Y') : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Intake Encoder</div>
                        <div class="info-value">{{ $intake->encoderUser?->name ?? session('admin_user_name') ?? 'MSWDO Admin' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Interviewer (Step 1)</div>
                        <div class="info-value">{{ $intake->interviewed_by ?? 'MSWDO Social Worker' }}</div>
                    </div>
                    <div class="mb-0">
                        <div class="info-label">Reviewer (Step 1)</div>
                        <div class="info-value">{{ $intake->reviewed_by ?? 'MSWDO Department Head' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script>
function certifyDisbursement(clientName, amount) {
    Swal.fire({
        title: 'Certify Cash Disbursement',
        html: `<p class="mb-2">Are you sure you want to certify and authorize payout release for <strong>${clientName}</strong>?</p><p class="fs-5 fw-bold text-success mb-0">&#8369;${amount}</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1A237E',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check me-1"></i> Yes, Certify Release',
        cancelButtonText: 'Cancel',
        background: '#ffffff',
        customClass: {
            popup: 'rounded-4 shadow-lg'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Disbursement Certified!',
                text: `Cash assistance voucher for ${clientName} is ready for release.`,
                icon: 'success',
                confirmButtonColor: '#1A237E'
            });
        }
    });
}
</script>
@endsection
