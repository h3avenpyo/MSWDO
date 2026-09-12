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
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.financial.financialstep2.liquidation') }}"
                    class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Back to Liquidation Dashboard
                </a>
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
            @if(file_exists(public_path('images/silangseal.png')))
            <img src="{{ asset('images/silangseal.png') }}" alt="Silang Seal" class="header-logo-left">
            @elseif(file_exists(public_path('iservesilang1.ico')))
            <img src="{{ asset('iservesilang1.ico') }}" alt="Silang Seal" class="header-logo-left">
            @elseif(file_exists(public_path('iservesilang.ico')))
            <img src="{{ asset('iservesilang.ico') }}" alt="Silang Seal" class="header-logo-left">
            @endif

            @if(file_exists(public_path('images/dswd.png')))
            <img src="{{ asset('images/dswd.png') }}" alt="DSWD / MSWD Logo" class="header-logo-right">
            @endif

            <div class="govt-titles">
                <div>Republic of the Philippines</div>
                <div>Province of Cavite</div>
                <div>Municipality of Silang</div>
            </div>
            <div class="office-title">
                MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE (MSWDO)
            </div>
            <div class="document-title">
                @if(!empty($isMonthlyReport))
                OFFICIAL MONTHLY LIQUIDATION REPORT
                @else
                OFFICIAL PAYROLL LIQUIDATION REPORT
                @endif
            </div>
            <div class="document-subtitle">
                Assistance to Individuals in Crisis Situation (AICS) / Emergency Financial Assistance Program
                @if(!empty($isMonthlyReport))
                &bull; <strong>Month of {{ $monthLabel }}</strong>
                @elseif(!empty($payroll->payroll_number))
                &bull; <strong>Ref: {{ $payroll->payroll_number }}</strong>
                @endif
            </div>
        </div>

        <!-- Official Liquidation Particulars & Financial Accountability Summary Table -->
        <table class="report-particulars-table">
            <tr>
                <td class="particulars-col">
                    <div class="particulars-title">REPORT DETAILS &amp; PARTICULARS</div>
                    <table class="particulars-inner-table">
                        <tr>
                            <td class="p-lbl">Implementing Office:</td>
                            <td class="p-val">MSWDO - Municipality of Silang, Cavite</td>
                        </tr>
                        <tr>
                            <td class="p-lbl">Program / Project:</td>
                            <td class="p-val">Assistance to Individuals in Crisis Situation (AICS)</td>
                        </tr>
                        <tr>
                            <td class="p-lbl">Payroll / Transaction Date:</td>
                            <td class="p-val fw-bold">{{ !empty($isMonthlyReport) ? $monthLabel : ($payrollDate ?? 'N/A') }}</td>
                        </tr>
                        <tr>
                            <td class="p-lbl">Payroll Reference No.:</td>
                            <td class="p-val font-monospace">{{ !empty($isMonthlyReport) ? ($payrollsCount . ' Generated Batches') : ($payroll->payroll_number ?? 'N/A') }}</td>
                        </tr>
                        <tr>
                            <td class="p-lbl">Disbursing Officer:</td>
                            <td class="p-val">{{ $disbursingOfficer }}</td>
                        </tr>
                    </table>
                </td>
                <td class="particulars-col particulars-financial">
                    <div class="particulars-title">FINANCIAL LIQUIDATION SUMMARY</div>
                    <table class="particulars-inner-table">
                        <tr>
                            <td class="p-lbl">Total Amount Released (Fund):</td>
                            <td class="p-val text-end fw-bold">₱{{ number_format($totalAllocated, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="p-lbl">Total Amount Claimed (Disbursed):</td>
                            <td class="p-val text-end text-success fw-bold">₱{{ number_format($totalClaimed, 2) }} <span class="p-subnote">({{ $claimedCount }} Beneficiaries)</span></td>
                        </tr>
                        <tr>
                            <td class="p-lbl">Total Amount Unclaimed:</td>
                            <td class="p-val text-end text-danger-emphasis fw-bold">₱{{ number_format($totalUnclaimed, 2) }} <span class="p-subnote">({{ $unclaimedCount }} Beneficiaries)</span></td>
                        </tr>
                        <tr class="fin-balance-row">
                            <td class="p-lbl fw-bold">Remaining Balance (For Return/Refund):</td>
                            <td class="p-val text-end fw-bold">₱{{ number_format($remainingBalance, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        @if(!empty($isMonthlyReport) && isset($payrollBatches) && count($payrollBatches) > 0)
        <!-- Payroll Batches Summary in this Month -->
        <div class="section-container mb-3">
            <div class="section-heading">
                <i class="fas fa-layer-group me-1"></i> Summary of Payroll Batches in {{ $monthLabel }}
            </div>
            <table class="liquidation-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 45px;" class="text-center">BATCH</th>
                        <th style="width: 170px;">PAYROLL NUMBER</th>
                        <th style="width: 120px;">DATE</th>
                        <th>DISBURSING OFFICER</th>
                        <th style="width: 85px;" class="text-center">CLIENTS</th>
                        <th style="width: 125px;" class="text-end">AMOUNT RELEASED</th>
                        <th style="width: 125px;" class="text-end">AMOUNT CLAIMED</th>
                        <th style="width: 125px;" class="text-end">AMOUNT UNCLAIMED</th>
                        <th style="width: 125px;" class="text-end">REMAINING BALANCE</th>
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
                        <td class="text-end fw-semibold text-success">₱{{ number_format($pBatch->claimed, 2) }}</td>
                        <td class="text-end fw-semibold text-danger-emphasis">₱{{ number_format($pBatch->unclaimed, 2) }}</td>
                        <td class="text-end fw-bold">₱{{ number_format($pBatch->remaining, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Detailed Beneficiary Liquidation Masterlist Table -->
        <div class="section-container mb-2">
            <div class="section-heading">
                <i class="fas fa-users me-1"></i>
                @if(!empty($isMonthlyReport))
                Monthly Beneficiary Liquidation Masterlist ({{ $totalBeneficiaries }} Records)
                @else
                Beneficiary Liquidation Masterlist ({{ $totalBeneficiaries }} Records)
                @endif
            </div>
            <table class="liquidation-table">
                <thead>
                    <tr>
                        <th style="width: 32px;" class="text-center">NO.</th>
                        <th style="width: 120px;">CONTROL NO.</th>
                        @if(!empty($isMonthlyReport))
                        <th style="width: 90px;">PAYROLL DATE</th>
                        @endif
                        <th>NAME OF REPRESENTATIVE</th>
                        <th>NAME OF BENEFICIARY</th>
                        <th style="width: 130px;">BARANGAY</th>
                        <th style="width: 100px;">CONTACT NO.</th>
                        <th style="width: 110px;" class="text-end">ASSISTANCE AMOUNT</th>
                        <th style="width: 85px;" class="text-center">CLAIM STATUS</th>
                        <th style="width: 125px;">DATE &amp; TIME CLAIMED</th>
                        <th style="width: 120px;">SIGNATURE / REMARKS</th>
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
                        <td>{{ mb_strtoupper($ben->representative_name) }}</td>
                        <td class="fw-semibold">{{ mb_strtoupper($ben->beneficiary_name) }}</td>
                        <td>{{ $ben->barangay }}</td>
                        <td class="font-monospace">{{ $ben->contact_number }}</td>
                        <td class="text-end fw-bold">₱{{ number_format($ben->amount, 2) }}</td>
                        <td class="text-center">
                            @if($ben->claim_status === 'Claimed')
                            <span class="status-claimed-text">CLAIMED</span>
                            @else
                            <span class="status-unclaimed-text">UNCLAIMED</span>
                            @endif
                        </td>
                        <td>{{ $ben->claimed_at }}</td>
                        <td class="signature-line-cell"></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ !empty($isMonthlyReport) ? '11' : '10' }}" class="text-center py-3 text-muted">
                            No beneficiary records found for this liquidation report.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="liquidation-tfoot-row">
                        <th colspan="{{ !empty($isMonthlyReport) ? '7' : '6' }}" class="text-end text-uppercase fw-bold pe-3">
                            GRAND TOTAL ({{ $totalBeneficiaries }} BENEFICIARIES):
                        </th>
                        <th class="text-end fw-bold">₱{{ number_format($totalAllocated, 2) }}</th>
                        <th colspan="3" class="text-center">
                            <span class="text-success fw-bold">{{ $claimedCount }} Claimed (₱{{ number_format($totalClaimed, 2) }})</span> &bull;
                            <span class="text-danger-emphasis fw-bold">{{ $unclaimedCount }} Unclaimed (₱{{ number_format($totalUnclaimed, 2) }})</span>
                            @if($remainingBalance > 0)
                            &bull; <span class="fw-bold text-dark">Remaining Balance: ₱{{ number_format($remainingBalance, 2) }}</span>
                            @endif
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Official Certification Statement -->
        <div class="liquidation-cert-box">
            <p class="cert-statement">
                <strong>CERTIFICATION:</strong> I hereby certify under oath that the amounts listed above were disbursed to the entitled beneficiaries in the amounts indicated opposite their respective names in accordance with established government accounting and auditing laws, rules, and regulations.
            </p>
        </div>

        <!-- Official Signatories Section -->
        <div class="signatories-wrapper">
            <!-- Signatory 1: Disbursing Officer -->
            <div class="signatory-box">
                <div class="signatory-role">Prepared &amp; Liquidated By:</div>
                <div class="signatory-spacer"></div>
                <div class="signatory-line"></div>
                <div class="signatory-name">{{ mb_strtoupper($disbursingOfficer) }}</div>
                <div class="signatory-title">Disbursing Officer / Special Disbursing Officer</div>
            </div>

            <!-- Signatory 2: MSWDO Head -->
            <div class="signatory-box">
                <div class="signatory-role">Certified Correct:</div>
                <div class="signatory-spacer"></div>
                <div class="signatory-line"></div>
                <div class="signatory-name">{{ mb_strtoupper($payroll->certified_by ?? 'MSWDO DEPARTMENT HEAD') }}</div>
                <div class="signatory-title">Municipal Social Welfare &amp; Development Officer</div>
            </div>

            <!-- Signatory 3: Local Chief Executive / Accountant -->
            <div class="signatory-box">
                <div class="signatory-role">Approved By:</div>
                <div class="signatory-spacer"></div>
                <div class="signatory-line"></div>
                <div class="signatory-name">{{ mb_strtoupper($payroll->approved_by ?? 'HON. MUNICIPAL MAYOR') }}</div>
                <div class="signatory-title">Municipal Mayor / Municipal Accountant</div>
            </div>
        </div>

        <!-- Official Print Footer Note -->
        <div class="print-footer-note">
            <div>
                <span>MSWDO Silang &bull; AICS Financial Assistance Liquidation System</span>
                @if(!empty($payroll->payroll_number))
                <span class="mx-2">&bull;</span>
                <span>Ref No: {{ $payroll->payroll_number }}</span>
                @endif
            </div>
            <div>
                <span>Official Liquidation Report &bull; Generated: {{ $reportDate }}</span>
            </div>
        </div>

    </div>

</body>

</html>
