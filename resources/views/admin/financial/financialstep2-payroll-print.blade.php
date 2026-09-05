<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Assistance Payroll (Legal Landscape) - MSWDO Silang, Cavite</title>

    <!-- Google Fonts: Public Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- External Module Stylesheet (Legal Landscape Print & Screen) -->
    <link href="{{ asset('css/financialstep2-payroll-print.css') }}" rel="stylesheet">
</head>

<body>

    <!-- Web Top Action Bar (Hidden when printing) -->
    <div class="no-print no-print-bar">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.financial.financialstep2.payroll') }}"
                    class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Back to Payroll Generator
                </a>
                <span class="text-white-50">|</span>
                <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1">Paper: Legal (8.5 × 14 in)
                    Landscape</span>
                <span class="fw-semibold">Date: <span class="text-warning">{{ $payrollDate }}</span></span>
                @if(!empty($generatedTime))
                <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1"><i class="fas fa-clock me-1"></i>{{ $generatedTime }}</span>
                @endif
                <span class="badge bg-primary rounded-pill px-3">{{ $totalBeneficiaries }} Beneficiaries</span>
                <span class="badge bg-success rounded-pill px-3">Total: {{ $formattedTotalAmount }}</span>
                @if($missingAmountCount > 0)
                <span class="badge bg-warning text-dark rounded-pill px-3"><i
                        class="fas fa-exclamation-triangle me-1"></i> {{ $missingAmountCount }} Pending Amount</span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btnPrintPayroll" class="btn btn-warning fw-bold text-dark px-4 rounded-pill shadow-sm btn-print-payroll">
                    <i class="fas fa-print me-1"></i> Print Legal Landscape Payroll
                </button>
            </div>
        </div>
    </div>

    <!-- Main Printable Payroll Document Sheet (Legal Landscape Container) -->
    <div class="payroll-sheet-wrapper">

        <!-- Official Seal & Government Heading -->
        <div class="payroll-header">
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
                PAYROLL FOR FINANCIAL ASSISTANCE
            </div>
            <div class="document-subtitle">
                Assistance to Individuals in Crisis Situation (AICS) / Emergency Financial Assistance Program
                @if(!empty($payrollRefNo))
                &bull; <span class="font-monospace">Ref: {{ $payrollRefNo }}</span>
                @endif
            </div>
        </div>

        <!-- Masterlist Payroll Data Table -->
        <table class="payroll-table">
            <thead>
                <tr>
                    <th class="col-no">NO.</th>
                    <th class="col-control">CONTROL NO.</th>
                    <th class="col-rep">NAME OF REPRESENTATIVE</th>
                    <th class="col-ben">NAME OF BENEFICIARY/BENEFICIARIES</th>
                    <th class="col-brgy">BARANGAY</th>
                    <th class="col-contact">CONTACT NO.</th>
                    <th class="col-amount">AMOUNT OF FINANCIAL ASSISTANCE</th>
                    <th class="col-signature">SIGNATURE</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrollRows as $row)
                <tr>
                    <td class="col-no">{{ $row->item_no }}</td>
                    <td class="col-control">{{ $row->control_number }}</td>
                    <td class="col-rep">
                        <div class="fw-bold">{{ mb_strtoupper($row->representative_name) }}</div>
                    </td>
                    <td class="col-ben">
                        <div>{{ mb_strtoupper($row->beneficiary_name) }}</div>
                    </td>
                    <td class="col-brgy">{{ $row->barangay }}</td>
                    <td class="col-contact">{{ $row->contact_number }}</td>
                    <td class="col-amount">
                        &#8369;{{ number_format($row->amount, 2) }}
                    </td>
                    <td class="col-signature">
                        <!-- Signature field completely blank for manual physical signing -->
                        <div class="signature-box"></div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        No intake records recorded for payroll on this date ({{ $payrollDate }}).
                    </td>
                </tr>
                @endforelse

                <!-- Minimum row padding when intake count is small -->
                @php
                $rowCount = count($payrollRows);
                $targetRows = max(4 - $rowCount, 0);
                @endphp
                @for($i = 0; $i < $targetRows; $i++) <tr>
                    <td class="col-no text-muted">&nbsp;</td>
                    <td class="col-control">&nbsp;</td>
                    <td class="col-rep">&nbsp;</td>
                    <td class="col-ben">&nbsp;</td>
                    <td class="col-brgy">&nbsp;</td>
                    <td class="col-contact">&nbsp;</td>
                    <td class="col-amount">&nbsp;</td>
                    <td class="col-signature">
                        <div class="signature-box"></div>
                    </td>
                    </tr>
                    @endfor
            </tbody>
            <tfoot>
                <tr class="payroll-total-row">
                    <td colspan="6" class="text-end fw-bold px-3">
                        GRAND TOTAL FINANCIAL ASSISTANCE:
                    </td>
                    <td class="col-amount text-end fw-bold payroll-grand-total-amount">
                        &#8369;{{ number_format($totalAmount, 2) }}
                    </td>
                    <td class="col-signature text-center text-muted small fw-semibold">
                        {{ $totalBeneficiaries }} Beneficiaries
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Official Signatories Section -->
        <div class="signatories-container">
            <div class="signatory-card">
                <div class="signatory-role">Prepared &amp; Disbursed by:</div>
                <div class="signatory-line"></div>
                <div class="signatory-name">{{ $disbursingOfficer }}</div>
                <div class="signatory-title">Step 2 Disbursing Officer / MSWDO Staff</div>
            </div>

            <div class="signatory-card">
                <div class="signatory-role">Verified &amp; Certified by:</div>
                <div class="signatory-line"></div>
                <div class="signatory-name">MSWDO HEAD / OFFICER-IN-CHARGE</div>
                <div class="signatory-title">Municipal Social Welfare &amp; Development Officer</div>
            </div>

            <div class="signatory-card">
                <div class="signatory-role">Approved by:</div>
                <div class="signatory-line"></div>
                <div class="signatory-name">HON. MUNICIPAL MAYOR</div>
                <div class="signatory-title">Municipality of Silang, Cavite</div>
            </div>
        </div>

        <!-- Official Print Footer Note -->
        <div class="print-footer-note">
            <div>
                <span>MSWDO Financial Assistance Payroll System</span>
                <span class="mx-2">&bull;</span>
                <span>Ref: {{ $payrollRefNo }}</span>
            </div>
            <div>
                {{-- <span>Printed on: {{ date('F d, Y h:i A') }}</span>
                <span class="mx-2">&bull;</span>
                <span>Document Format: Legal (8.5 × 14 in) Landscape</span> --}}
            </div>
        </div>

    </div>

    <!-- External Module Script -->
    <script src="{{ asset('js/financialstep2-payroll-print.js') }}"></script>
</body>

</html>