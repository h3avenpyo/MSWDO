<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Record {{ $payrollRefNo }} (Legal Landscape) - MSWDO Silang, Cavite</title>

    <!-- Google Fonts: Public Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* ============================================================
           PRINT & SCREEN SPECIFICATIONS: LEGAL LANDSCAPE (8.5 x 14 in)
           ============================================================ */
        :root {
            --print-width: 14in;
            --print-height: 8.5in;
            --color-primary: #1A237E;
            --color-dark: #0F172A;
            --color-border-table: #1E293B;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #E2E8F0;
            color: #0F172A;
            margin: 0;
            padding: 0;
            font-size: 9pt;
            line-height: 1.35;
        }

        /* Top Action Bar (Screen Only) */
        .no-print-bar {
            background: #1E293B;
            color: #FFFFFF;
            padding: 10px 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Paper Simulation on Screen (Legal Landscape) */
        .payroll-sheet-wrapper {
            width: 13.6in;
            max-width: 98%;
            margin: 20px auto 40px auto;
            background: #FFFFFF;
            padding: 0.4in 0.45in;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            border: 1px solid #CBD5E1;
            border-radius: 4px;
        }

        /* Government Header Layout */
        .payroll-header {
            position: relative;
            text-align: center;
            padding-bottom: 12px;
            margin-bottom: 16px;
            border-bottom: 2px solid #0F172A;
        }

        .header-logo-left {
            position: absolute;
            left: 10px;
            top: 2px;
            width: 68px;
            height: 68px;
            object-fit: contain;
        }

        .govt-titles {
            font-size: 8.5pt;
            font-weight: 600;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            line-height: 1.25;
        }

        .office-title {
            font-size: 10pt;
            font-weight: 800;
            color: #0F172A;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .document-title {
            font-size: 13pt;
            font-weight: 800;
            color: #1A237E;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .document-subtitle {
            font-size: 8.5pt;
            font-weight: 600;
            color: #475569;
            margin-top: 1px;
        }

        /* Master Payroll Table - Legal Landscape Proportions */
        .payroll-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            page-break-inside: auto;
        }

        .payroll-table thead {
            display: table-header-group;
        }

        .payroll-table tbody tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .payroll-table tfoot {
            display: table-footer-group;
        }

        .payroll-table th,
        .payroll-table td {
            border: 1px solid #0F172A;
            padding: 5px 7px;
            font-size: 8pt;
            vertical-align: middle;
        }

        .payroll-table th {
            background-color: #F1F5F9 !important;
            color: #0F172A;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-size: 7.5pt;
            white-space: nowrap;
        }

        /* Column Width Distribution for Legal Landscape */
        .col-no {
            width: 3.5%;
            text-align: center;
            font-weight: 700;
        }

        .col-control {
            width: 10%;
            text-align: center;
            font-weight: 700;
            font-family: monospace;
            font-size: 8pt;
        }

        .col-rep {
            width: 21%;
            text-align: left;
        }

        .col-ben {
            width: 21%;
            text-align: left;
        }

        .col-brgy {
            width: 12%;
            text-align: left;
        }

        .col-contact {
            width: 10%;
            text-align: center;
            font-size: 7.5pt;
        }

        .col-amount {
            width: 11%;
            text-align: right;
            font-weight: 700;
            font-size: 8.5pt;
            white-space: nowrap;
        }

        .col-signature {
            width: 11.5%;
            text-align: center;
            padding: 3px 6px;
        }

        /* Blank space specifically reserved for physical manual pen signing */
        .signature-box {
            min-height: 38px;
            width: 100%;
            display: block;
            border-bottom: 1px solid #000000;
            margin: 18px 0 2px 0;
        }

        .payroll-total-row th,
        .payroll-total-row td {
            background-color: #F8FAFC !important;
            font-weight: 800;
            border-top: 2px solid #0F172A;
            border-bottom: 2px solid #0F172A;
            font-size: 8.5pt;
        }

        /* Signatories Block */
        .signatories-container {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .signatory-card {
            flex: 1;
            text-align: center;
        }

        .signatory-role {
            font-size: 7.5pt;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 40px;
        }

        .signatory-line {
            border-bottom: 1.2px solid #0F172A;
            margin: 0 auto 4px auto;
            width: 85%;
        }

        .signatory-name {
            font-size: 8.5pt;
            font-weight: 800;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .signatory-title {
            font-size: 7pt;
            color: #475569;
        }

        /* Print Page Footer Notes */
        .print-footer-note {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid #CBD5E1;
            font-size: 6.8pt;
            color: #64748B;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* ============================================================
           PRINT MEDIA RULES (Legal Landscape Strict Mode)
           ============================================================ */
        @media print {
            @page {
                size: legal landscape;
                margin: 8mm 10mm 10mm 10mm;
            }

            html,
            body {
                background: #FFFFFF !important;
                color: #000000 !important;
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 8pt !important;
            }

            .no-print {
                display: none !important;
            }

            .payroll-sheet-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .payroll-header {
                padding-bottom: 8px;
                margin-bottom: 12px;
                border-bottom: 2px solid #000000 !important;
            }

            .header-logo-left {
                width: 58px;
                height: 58px;
                left: 0;
                top: 0;
            }

            .govt-titles {
                font-size: 7.5pt;
                color: #000000 !important;
            }

            .office-title {
                font-size: 9pt;
                color: #000000 !important;
            }

            .document-title {
                font-size: 11.5pt;
                color: #000000 !important;
            }

            .document-subtitle {
                font-size: 7.5pt;
                color: #000000 !important;
            }

            .payroll-table th,
            .payroll-table td {
                border: 1px solid #000000 !important;
                color: #000000 !important;
                padding: 4px 6px;
                font-size: 7.5pt;
            }

            .payroll-table th {
                background-color: #E2E8F0 !important;
                font-size: 7pt;
            }

            .signature-box {
                min-height: 32px;
                border-bottom: 1px solid #000000 !important;
                margin: 14px 0 2px 0;
            }

            .signatories-container {
                margin-top: 20px;
            }

            .signatory-line {
                border-bottom: 1px solid #000000 !important;
            }

            .signatory-role {
                color: #000000 !important;
                margin-bottom: 34px;
            }

            .signatory-name {
                color: #000000 !important;
            }

            .signatory-title {
                color: #000000 !important;
            }

            .print-footer-note {
                border-top: 1px solid #000000 !important;
                color: #000000 !important;
            }
        }
    </style>
</head>

<body>

    <!-- Web Top Action Bar (Hidden when printing) -->
    <div class="no-print no-print-bar">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.financial.financialstep2.payroll-records') }}"
                    class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Back to Payroll Records
                </a>
                <a href="{{ route('admin.financial.financialstep2.payroll') }}"
                    class="btn btn-outline-secondary btn-sm rounded-pill px-3 text-light">
                    <i class="fas fa-file-invoice-dollar me-1"></i> Payroll Generator
                </a>
                <span class="text-white-50">|</span>
                <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1">Paper: Legal (8.5 × 14 in) Landscape</span>
                <span class="badge bg-dark border border-secondary text-warning font-monospace px-3 py-1">{{ $payrollRefNo }}</span>
                <span class="fw-semibold text-white">Date: <span class="text-warning">{{ $payrollDate }}</span></span>
                <span class="badge bg-primary rounded-pill px-3">{{ $totalBeneficiaries }} Beneficiaries</span>
                <span class="badge bg-success rounded-pill px-3">Total: {{ $formattedTotalAmount }}</span>
                @if($missingAmountCount > 0)
                <span class="badge bg-warning text-dark rounded-pill px-3"><i class="fas fa-exclamation-triangle me-1"></i> {{ $missingAmountCount }} Pending Amount</span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.financial.financialstep2.payroll-records.export-csv', $record->id) }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="fas fa-file-csv me-1"></i> Export CSV
                </a>
                <button onclick="window.print()" class="btn btn-warning fw-bold text-dark px-4 rounded-pill shadow-sm">
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
                    <td class="col-no">{{ $row->item_no ?? $loop->iteration }}</td>
                    <td class="col-control">{{ $row->control_number }}</td>
                    <td class="col-rep">
                        <div class="fw-bold">{{ mb_strtoupper($row->representative_name ?? '') }}</div>
                    </td>
                    <td class="col-ben">
                        <div>{{ mb_strtoupper($row->beneficiary_name ?? '') }}</div>
                    </td>
                    <td class="col-brgy">{{ $row->barangay ?? 'Silang, Cavite' }}</td>
                    <td class="col-contact">{{ $row->contact_number ?? 'N/A' }}</td>
                    <td class="col-amount">
                        &#8369;{{ number_format((float) ($row->amount ?? 0), 2) }}
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
                @for($i = 0; $i < $targetRows; $i++)
                <tr>
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
                    <td class="col-amount text-end fw-bold" style="color: #1A237E;">
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
                <div class="signatory-name">{{ $certifiedBy }}</div>
                <div class="signatory-title">Municipal Social Welfare &amp; Development Officer</div>
            </div>

            <div class="signatory-card">
                <div class="signatory-role">Approved by:</div>
                <div class="signatory-line"></div>
                <div class="signatory-name">{{ $approvedBy }}</div>
                <div class="signatory-title">Municipality of Silang, Cavite</div>
            </div>
        </div>

        <!-- Official Print Footer Note -->
        <div class="print-footer-note">
            <div>
                <span>MSWDO Financial Assistance Payroll System</span>
                <span class="mx-2">&bull;</span>
                <span>Ref: {{ $payrollRefNo }}</span>
                <span class="mx-2">&bull;</span>
                <span>Generated by: {{ $generatedBy }} ({{ $generatedAt }})</span>
            </div>
            <div>
                <span>Official Legal Landscape Record</span>
            </div>
        </div>

    </div>

    @if(!empty($autoprint))
    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
    @endif

</body>

</html>
