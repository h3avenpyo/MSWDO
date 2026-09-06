<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if(!empty($isMonthlyReport))
        Monthly Payroll Liquidation Report - {{ $monthLabel }} - MSWDO Silang
        @else
        Payroll Liquidation Report - {{ $payroll->payroll_number ?? 'Report' }} - MSWDO Silang
        @endif
    </title>

    <!-- Google Fonts: Public Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- External Liquidation Report Stylesheet -->
    <link href="{{ asset('css/financialstep2-liquidation-report.css') }}" rel="stylesheet">
</head>

<body>

    <!-- Screen Action Bar (Hidden when printing) -->
    <div class="no-print-bar no-print">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <a href="{{ route('admin.financial.financialstep2.liquidation') }}"
                    class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Back to Liquidation Dashboard
                </a>
                <span class="text-white-50">|</span>
                <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1">Paper: Legal Landscape</span>
                @if(!empty($isMonthlyReport))
                <span class="fw-semibold">Coverage: <span class="text-warning fw-bold">{{ $monthLabel }}</span></span>
                <span class="badge bg-info text-dark rounded-pill px-3">{{ $payrollsCount ?? 1 }} Batches</span>
                @else
                <span class="fw-semibold">Ref: <span class="text-warning font-monospace">{{ $payroll->payroll_number ?? 'N/A' }}</span></span>
                @endif
                <span class="badge bg-primary rounded-pill px-3">{{ $totalBeneficiaries }} Beneficiaries</span>
                <span class="badge bg-success rounded-pill px-3">Claimed: ₱{{ number_format($totalClaimed, 2) }}</span>
                <span class="badge bg-warning text-dark rounded-pill px-3">Remaining: ₱{{ number_format($remainingBalance, 2) }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" onclick="window.print()" class="btn btn-warning fw-bold text-dark px-4 rounded-pill shadow-sm">
                    <i class="fas fa-print me-1"></i> Print Liquidation Report
                </button>
            </div>
        </div>
    </div>

    <!-- Printable Report Sheet (Legal Landscape Container) -->
    <div class="report-sheet-wrapper">

        <!-- Official Seal & Government Heading -->
        <div class="report-header">
            @if(file_exists(public_path('iservesilang1.ico')))
            <img src="{{ asset('iservesilang1.ico') }}" alt="Silang Seal" class="header-logo-left">
            @elseif(file_exists(public_path('iservesilang.ico')))
            <img src="{{ asset('iservesilang.ico') }}" alt="Silang Seal" class="header-logo-left">
            @endif

            <div class="govt-titles">
                <div>Republic of the Philippines</div>
                <div>Province of Cavite</div>
                <div>Municipality of Silang</div>
            </div>
            <div class="office-title">
                Municipal Social Welfare and Development Office (MSWDO)
            </div>
            <div class="document-title">
                @if(!empty($isMonthlyReport))
                OFFICIAL MONTHLY LIQUIDATION REPORT
                @else
                OFFICIAL PAYROLL LIQUIDATION REPORT
                @endif
            </div>
            <div class="document-subtitle">
                Assistance to Individuals in Crisis Situation (AICS) &bull; Financial Assistance Program
                @if(!empty($isMonthlyReport))
                &bull; <strong>Month of {{ $monthLabel }}</strong>
                @endif
            </div>
        </div>

        <!-- Report Metadata Grid -->
        <div class="metadata-grid">
            @if(!empty($isMonthlyReport))
            <div class="metadata-item">
                <span class="metadata-label">Report Period</span>
                <span class="metadata-value fw-bold text-primary">{{ $monthLabel }}</span>
            </div>
            <div class="metadata-item">
                <span class="metadata-label">Payroll Batches</span>
                <span class="metadata-value">{{ $payrollsCount ?? 1 }} Generated Batches</span>
            </div>
            @else
            <div class="metadata-item">
                <span class="metadata-label">Payroll Reference No.</span>
                <span class="metadata-value font-monospace">{{ $payroll->payroll_number ?? 'N/A' }}</span>
            </div>
            <div class="metadata-item">
                <span class="metadata-label">Payroll Date</span>
                <span class="metadata-value">{{ $payrollDate ?? 'N/A' }}</span>
            </div>
            @endif
            <div class="metadata-item">
                <span class="metadata-label">Disbursing Officer</span>
                <span class="metadata-value">{{ $disbursingOfficer }}</span>
            </div>
            <div class="metadata-item">
                <span class="metadata-label">Report Generated</span>
                <span class="metadata-value">{{ $reportDate }}</span>
            </div>
            <div class="metadata-item">
                <span class="metadata-label">Liquidation Status</span>
                <span class="metadata-value">
                    @if($liquidationStatus === 'Fully Liquidated')
                    <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Fully Liquidated</span>
                    @elseif($liquidationStatus === 'Partially Liquidated')
                    <span class="text-warning fw-bold"><i class="fas fa-clock me-1"></i> Partially Liquidated</span>
                    @else
                    <span class="text-secondary fw-bold"><i class="fas fa-hourglass-start me-1"></i> Unreleased</span>
                    @endif
                </span>
            </div>
        </div>

        <!-- Executive Financial Summary Box (4 Core Metrics + Rate) -->
        <div class="financial-summary-box">
            <div class="summary-card card-allocated">
                <div class="summary-card-label">Total Allocated Amount</div>
                <div class="summary-card-value text-dark">₱{{ number_format($totalAllocated, 2) }}</div>
                <div class="summary-card-sub">{{ $totalBeneficiaries }} total beneficiaries</div>
            </div>
            <div class="summary-card card-claimed">
                <div class="summary-card-label">Total Claimed Amount</div>
                <div class="summary-card-value text-success">₱{{ number_format($totalClaimed, 2) }}</div>
                <div class="summary-card-sub">{{ $claimedCount }} disbursed to clients</div>
            </div>
            <div class="summary-card card-unclaimed">
                <div class="summary-card-label">Total Unclaimed Amount</div>
                <div class="summary-card-value text-warning-emphasis">₱{{ number_format($totalUnclaimed, 2) }}</div>
                <div class="summary-card-sub">{{ $unclaimedCount }} pending beneficiary release</div>
            </div>
            <div class="summary-card card-remaining">
                <div class="summary-card-label">Remaining Balance</div>
                <div class="summary-card-value text-dark">₱{{ number_format($remainingBalance, 2) }}</div>
                <div class="summary-card-sub">Unreleased month balance</div>
            </div>
            <div class="summary-card card-rate">
                <div class="summary-card-label">Liquidation Rate</div>
                <div class="summary-card-value text-primary">{{ $liquidationRate }}%</div>
                <div class="summary-card-sub">Disbursement efficiency</div>
            </div>
        </div>

        @if(!empty($isMonthlyReport) && isset($payrollBatches) && count($payrollBatches) > 0)
        <!-- Payroll Batches Summary in this Month -->
        <div class="mb-3">
            <div class="fw-bold text-uppercase small text-dark mb-1" style="font-size: 0.78rem; letter-spacing: 0.04em;">
                <i class="fas fa-layer-group me-1 text-primary"></i> Summary of Payroll Batches in {{ $monthLabel }}
            </div>
            <table class="liquidation-table mb-0" style="font-size: 0.73rem;">
                <thead>
                    <tr>
                        <th style="width: 35px;" class="text-center">BATCH</th>
                        <th style="width: 155px;">PAYROLL NUMBER</th>
                        <th style="width: 110px;">DATE</th>
                        <th>DISBURSING OFFICER</th>
                        <th style="width: 80px;" class="text-center">CLIENTS</th>
                        <th style="width: 105px;" class="text-end">ALLOCATED</th>
                        <th style="width: 105px;" class="text-end">CLAIMED</th>
                        <th style="width: 105px;" class="text-end">UNCLAIMED</th>
                        <th style="width: 105px;" class="text-end">REMAINING</th>
                        <th style="width: 95px;" class="text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrollBatches as $pBatch)
                    <tr>
                        <td class="text-center fw-bold">{{ $pBatch->batch_no }}</td>
                        <td class="font-monospace fw-semibold">{{ $pBatch->payroll_number }}</td>
                        <td>{{ $pBatch->payroll_date }}</td>
                        <td>{{ $pBatch->disbursing_officer }}</td>
                        <td class="text-center fw-bold">{{ $pBatch->total_beneficiaries }}</td>
                        <td class="text-end fw-semibold">₱{{ number_format($pBatch->allocated, 2) }}</td>
                        <td class="text-end text-success fw-semibold">₱{{ number_format($pBatch->claimed, 2) }}</td>
                        <td class="text-end text-warning-emphasis fw-semibold">₱{{ number_format($pBatch->unclaimed, 2) }}</td>
                        <td class="text-end fw-bold">₱{{ number_format($pBatch->remaining, 2) }}</td>
                        <td class="text-center">
                            @if($pBatch->status === 'Fully Liquidated')
                            <span class="badge-status-claimed">FULL (100%)</span>
                            @elseif($pBatch->status === 'Partially Liquidated')
                            <span class="badge-status-unclaimed">PARTIAL</span>
                            @else
                            <span class="badge-status-unclaimed">UNRELEASED</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Detailed Beneficiary Liquidation Masterlist Table -->
        <div class="mb-2">
            <div class="fw-bold text-uppercase small text-dark mb-1" style="font-size: 0.78rem; letter-spacing: 0.04em;">
                <i class="fas fa-users me-1 text-primary"></i>
                @if(!empty($isMonthlyReport))
                Monthly Beneficiary Liquidation Masterlist ({{ $totalBeneficiaries }} Records)
                @else
                Beneficiary Liquidation Masterlist ({{ $totalBeneficiaries }} Records)
                @endif
            </div>
            <table class="liquidation-table">
                <thead>
                    <tr>
                        <th style="width: 35px;" class="text-center">NO.</th>
                        <th style="width: 125px;">CONTROL NO.</th>
                        @if(!empty($isMonthlyReport))
                        <th style="width: 95px;">PAYROLL DATE</th>
                        @endif
                        <th>NAME OF REPRESENTATIVE</th>
                        <th>NAME OF BENEFICIARY</th>
                        <th>BARANGAY</th>
                        <th>CONTACT NO.</th>
                        <th style="width: 105px;" class="text-end">AMOUNT (PHP)</th>
                        <th style="width: 90px;" class="text-center">STATUS</th>
                        <th style="width: 120px;">DATE &amp; TIME CLAIMED</th>
                        <th style="width: 110px;">SIGNATURE / REMARKS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($beneficiaries as $ben)
                    <tr>
                        <td class="text-center fw-bold">{{ $ben->item_no }}</td>
                        <td class="font-monospace fw-bold">{{ $ben->control_number }}</td>
                        @if(!empty($isMonthlyReport))
                        <td class="text-muted">{{ $ben->payroll_date ?? '--' }}</td>
                        @endif
                        <td>{{ $ben->representative_name }}</td>
                        <td class="fw-semibold">{{ $ben->beneficiary_name }}</td>
                        <td>{{ $ben->barangay }}</td>
                        <td class="font-monospace">{{ $ben->contact_number }}</td>
                        <td class="text-end fw-bold">₱{{ number_format($ben->amount, 2) }}</td>
                        <td class="text-center">
                            @if($ben->claim_status === 'Claimed')
                            <span class="badge-status-claimed">CLAIMED</span>
                            @else
                            <span class="badge-status-unclaimed">UNCLAIMED</span>
                            @endif
                        </td>
                        <td>{{ $ben->claimed_at }}</td>
                        <td class="text-muted" style="border-bottom: 1px dotted #9CA3AF;"></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ !empty($isMonthlyReport) ? '11' : '10' }}" class="text-center py-3 text-muted">
                            No beneficiary records found for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="{{ !empty($isMonthlyReport) ? '7' : '6' }}" class="text-end text-uppercase fw-bold pe-3">
                            Grand Total ({{ $totalBeneficiaries }} Beneficiaries):
                        </th>
                        <th class="text-end fw-bold">₱{{ number_format($totalAllocated, 2) }}</th>
                        <th colspan="3" class="text-center">
                            <span class="text-success fw-bold">{{ $claimedCount }} Claimed (₱{{ number_format($totalClaimed, 2) }})</span> &bull;
                            <span class="text-warning-emphasis fw-bold">{{ $unclaimedCount }} Unclaimed (₱{{ number_format($totalUnclaimed, 2) }})</span>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Certification & Signatories Block -->
        <div class="signatories-wrapper">
            <!-- Signatory 1: Disbursing Officer -->
            <div class="signatory-box">
                <div class="signatory-role">Prepared &amp; Liquidated By:</div>
                <div class="signatory-name">{{ $disbursingOfficer }}</div>
                <div class="signatory-title">Disbursing Officer / Special Disbursing Officer</div>
            </div>

            <!-- Signatory 2: MSWDO Head -->
            <div class="signatory-box">
                <div class="signatory-role">Certified Correct:</div>
                <div class="signatory-name">{{ $payroll->certified_by ?? 'MSWDO Department Head' }}</div>
                <div class="signatory-title">Municipal Social Welfare and Development Officer</div>
            </div>

            <!-- Signatory 3: Local Chief Executive / Accountant -->
            <div class="signatory-box">
                <div class="signatory-role">Approved By:</div>
                <div class="signatory-name">{{ $payroll->approved_by ?? 'Municipal Mayor / Municipal Accountant' }}</div>
                <div class="signatory-title">Local Chief Executive / Municipal Accountant</div>
            </div>
        </div>

    </div>

</body>

</html>
