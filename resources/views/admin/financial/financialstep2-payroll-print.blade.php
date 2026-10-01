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
    <link
        href="{{ asset('css/financialstep2-payroll-print.css') }}?v={{ file_exists(public_path('css/financialstep2-payroll-print.css')) ? filemtime(public_path('css/financialstep2-payroll-print.css')) : time() }}"
        rel="stylesheet">
</head>

<body>

    @php
    $isFromRecords = request('from') === 'records' || request()->filled('payroll_id') || request()->has('records') ||
    !empty($selectedPayrollRecord);
    if ($isFromRecords) {
    $backUrl = isset($targetDate)
    ? route('admin.financial.financialstep2.payroll-records.date', array_filter(['date' => $targetDate->format('Y-m-d'),
    'barangay' => request('barangay')]))
    : route('admin.financial.financialstep2.payroll-records');
    $backLabel = 'Back to Payroll Records';
    } else {
    $backUrl = route('admin.financial.financialstep2.payroll', array_filter(['date' => request('date')]));
    $backLabel = 'Back to Payroll Generator';
    }

    // Program and Year Context
    $payrollYear = isset($targetDate) ? $targetDate->format('Y') : date('Y');

    // Multi-Page Legal Landscape Pagination Distribution (Engineered for 70px row heights)
    $totalCount = count($payrollRows);
    $page1Limit = 6;
    $pageNextLimit = 7;

    $pages = [];
    if ($totalCount <= $page1Limit) { $pages[]=[ 'pageNumber'=> 1,
        'isFirst' => true,
        'isLast' => true,
        'rows' => $payrollRows,
        ];
        } else {
        // Page 1
        $pages[] = [
        'pageNumber' => 1,
        'isFirst' => true,
        'isLast' => false,
        'rows' => $payrollRows->slice(0, $page1Limit),
        ];

        // Subsequent pages
        $remaining = $payrollRows->slice($page1Limit);
        $chunked = $remaining->chunk($pageNextLimit);
        $totalChunks = $chunked->count();

        foreach ($chunked as $index => $chunkRows) {
        $pageNum = $index + 2;
        $isLast = ($index + 1) === $totalChunks;
        $pages[] = [
        'pageNumber' => $pageNum,
        'isFirst' => false,
        'isLast' => $isLast,
        'rows' => $chunkRows,
        ];
        }
        }
        $totalPages = count($pages);
        @endphp

        <!-- Web Top Action Bar (Hidden when printing) -->
        <div class="no-print no-print-bar">
            <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ $backUrl }}" id="btnBackPayroll" class="btn btn-outline-light btn-sm rounded-pill px-3">
                        <i class="fas fa-arrow-left me-1"></i> {{ $backLabel }}
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-white-50 small me-2 d-none d-md-inline">
                        Document Format: <strong>Legal (8.5 &times; 14 in) Landscape</strong> &bull; {{ $totalPages }}
                        {{ \Illuminate\Support\Str::plural('Page', $totalPages) }}
                    </span>
                    <button type="button" id="btnPrintPayroll"
                        class="btn btn-warning fw-bold text-dark px-4 rounded-pill shadow-sm btn-print-payroll">
                        <i class="fas fa-print me-1"></i> Print Legal Landscape Payroll
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Printable Payroll Document (Preview & Print Container) -->
        <div class="payroll-preview-container">
            @foreach($pages as $page)
            <div class="payroll-page-wrapper">
                <!-- Preview Page Indicator Header (Hidden in Print) -->
                <div class="preview-page-header no-print">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-secondary-subtle text-dark border px-3 py-2 fw-semibold">
                            <i class="fas fa-file-invoice text-primary me-1"></i> Page {{ $page['pageNumber'] }} of {{
                            $totalPages }}
                        </span>
                        <span class="text-muted small">
                            Legal (8.5 &times; 14 in) Landscape &bull; {{ count($page['rows']) }} Beneficiaries on this
                            page
                        </span>
                    </div>
                </div>

                <!-- Legal Landscape Sheet (Exact 14 x 8.5 in Dimensions) -->
                <div class="payroll-page-sheet">
                    <!-- Background Seal Watermark -->
                    <div class="watermark-container" aria-hidden="true">
                        @if(file_exists(public_path('images/silangseal.png')))
                        <img src="{{ asset('images/silangseal.png') }}" alt="Watermark" class="sheet-watermark">
                        @elseif(file_exists(public_path('images/silang.png')))
                        <img src="{{ asset('images/silang.png') }}" alt="Watermark" class="sheet-watermark">
                        @endif
                    </div>

                    <!-- Page Body Content (Occupies Space Above the Fixed Footer) -->
                    <div class="sheet-body-content">
                        @if($page['isFirst'])
                        <!-- Official Header: Silang Seal Left, Titles Center, Bagong Pilipinas & DSWD Right -->
                        <div class="payroll-header">
                            @if(file_exists(public_path('images/silangseal.png')))
                            <img src="{{ asset('images/silangseal.png') }}" alt="Silang Seal" class="header-logo-left">
                            @elseif(file_exists(public_path('images/silang.png')))
                            <img src="{{ asset('images/silang.png') }}" alt="Silang Seal" class="header-logo-left">
                            @elseif(file_exists(public_path('iservesilang1.ico')))
                            <img src="{{ asset('iservesilang1.ico') }}" alt="Silang Seal" class="header-logo-left">
                            @endif

                            <div class="header-center-text">
                                <div class="header-province">Province of Cavite</div>
                                <div class="header-municipality">Municipality of Silang</div>
                                <div class="header-office">MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE</div>
                                <div class="header-doc-title">FINANCIAL ASSISTANCE PAYROLL</div>
                            </div>

                            <div class="header-logos-right">
                                @if(file_exists(public_path('images/bagong-pilipinas.svg')))
                                <img src="{{ asset('images/bagong-pilipinas.svg') }}" alt="Bagong Pilipinas"
                                    class="logo-bagong-pilipinas">
                                @endif

                                @if(file_exists(public_path('images/dswdlogo.png')))
                                <img src="{{ asset('images/dswdlogo.png') }}" alt="DSWD Logo" class="logo-dswd">
                                @elseif(file_exists(public_path('images/dswd.png')))
                                <img src="{{ asset('images/dswd.png') }}" alt="DSWD Logo" class="logo-dswd">
                                @endif
                            </div>
                        </div>

                        <!-- Solid Black Dividing Line -->
                        <div class="header-black-bar"></div>

                        <!-- Payroll Program Subtitle (Left-aligned) -->
                        <div class="payroll-program-header">
                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    <div class="program-title">Cash Assistance Payroll - Financial Assistance</div>
                                    <div class="program-subtitle">For payment / implementation of CASH INCENTIVES FOR
                                        THE YEAR {{ $payrollYear }}</div>
                                </div>
                                <div class="text-end text-muted small fw-semibold">
                                    Date: {{ $payrollDate }}
                                    @if($totalPages > 1)
                                    <span class="ms-2 badge bg-light text-dark border">Sheet 1 of {{ $totalPages
                                        }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @else
                        <!-- Continuation Header for Page 2 and beyond -->
                        <div class="payroll-continuation-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    @if(file_exists(public_path('images/silangseal.png')))
                                    <img src="{{ asset('images/silangseal.png') }}" alt="Seal" class="header-logo-mini">
                                    @elseif(file_exists(public_path('images/silang.png')))
                                    <img src="{{ asset('images/silang.png') }}" alt="Seal" class="header-logo-mini">
                                    @endif
                                    <div>
                                        <span class="continuation-lgu">MUNICIPALITY OF SILANG &bull; MSWDO</span>
                                        <span class="mx-2 text-muted">|</span>
                                        <span class="continuation-prog">Cash Assistance Payroll (Continuation)</span>
                                    </div>
                                </div>
                                <div class="continuation-meta">
                                    <span>Date: <strong>{{ $payrollDate }}</strong></span>
                                    <span class="mx-2 text-muted">&bull;</span>
                                    <span>Sheet <strong>{{ $page['pageNumber'] }}</strong> of <strong>{{ $totalPages
                                            }}</strong></span>
                                </div>
                            </div>
                            <div class="header-black-bar-thin"></div>
                        </div>
                        @endif

                        <!-- Masterlist Payroll Data Table -->
                        <table class="payroll-table">
                            <thead>
                                <tr>
                                    <th class="col-no">NO</th>
                                    <th class="col-date">DATE</th>
                                    <th class="col-client">CLIENT</th>
                                    <th class="col-ben">BENEFICIARY</th>
                                    <th class="col-brgy">BARANGAY</th>
                                    <th class="col-amount">AMOUNT</th>
                                    <th class="col-contact">CONTACT NUMBER</th>
                                    <th class="col-signature">SIGNATURE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($page['rows'] as $row)
                                @php
                                $rowDate = $row->raw_intake?->date_processed
                                ? \Carbon\Carbon::parse($row->raw_intake->date_processed)->format('m/d/Y')
                                : ($row->raw_intake?->created_at
                                ? $row->raw_intake->created_at->format('m/d/Y')
                                : (isset($targetDate) ? $targetDate->format('m/d/Y') : date('m/d/Y')));
                                @endphp
                                <tr>
                                    <td class="col-no">{{ $row->item_no }}</td>
                                    <td class="col-date">{{ $rowDate }}</td>
                                    <td class="col-client">
                                        <div class="fw-bold">{{ mb_strtoupper($row->representative_name) }}</div>
                                    </td>
                                    <td class="col-ben">
                                        <div>{{ mb_strtoupper($row->beneficiary_name) }}</div>
                                    </td>
                                    <td class="col-brgy">{{ $row->barangay }}</td>
                                    <td class="col-amount">
                                        {{ number_format($row->amount, 2) }}
                                    </td>
                                    <td class="col-contact">{{ $row->contact_number }}</td>
                                    <td class="col-signature">
                                        <!-- Space reserved for physical manual pen signing -->
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

                                @if($page['isFirst'] && $totalCount < 6) @php $targetRows=max(6 - $totalCount, 0);
                                    @endphp @for($i=0; $i < $targetRows; $i++) <tr class="empty-padding-row">
                                    <td class="col-no">&nbsp;</td>
                                    <td class="col-date">&nbsp;</td>
                                    <td class="col-client">&nbsp;</td>
                                    <td class="col-ben">&nbsp;</td>
                                    <td class="col-brgy">&nbsp;</td>
                                    <td class="col-amount">&nbsp;</td>
                                    <td class="col-contact">&nbsp;</td>
                                    <td class="col-signature">
                                        <div class="signature-box"></div>
                                    </td>
                                    </tr>
                                    @endfor
                                    @endif

                                    @if($page['isLast'])
                                    <!-- Grand Total Row: Placed on the final page after the last beneficiary item -->
                                    <tr class="payroll-total-row">
                                        <td colspan="5" class="text-end fw-bold px-3">
                                            GRAND TOTAL:
                                        </td>
                                        <td class="col-amount text-end fw-bold payroll-grand-total-amount">
                                            &#8369;{{ number_format($totalAmount, 2) }}
                                        </td>
                                        <td colspan="2" class="text-center text-dark small fw-bold">
                                            {{ $totalBeneficiaries }} Beneficiaries
                                        </td>
                                    </tr>
                                    @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Official Signatories & Certifications Section (Fixed Footer at Bottom of EVERY Sheet) -->
                    <div class="signatories-section">
                        <div class="certifications-row">
                            <div class="cert-left-text">
                                I hereby certify that each person whose name appears on this payroll are entitled to
                                cash assistance
                            </div>
                            <div class="cert-right-text">
                                I certify on my official oath that I have paid in cash this day ___ of ___________ to
                                each
                                individual whose name appears on the payroll, the amount set opposite the name, having
                                presented
                                oneself, established the identity and affixed signature on the space provided hereof
                            </div>
                        </div>

                        <div class="signatories-grid">
                            <!-- 1. Prepared by -->
                            <div class="signatory-col">
                                <div class="signatory-role">Prepared by:</div>
                                <div class="signatory-line"></div>
                                <div class="signatory-name">{{ (!empty($disbursingOfficer) &&
                                    !in_array($disbursingOfficer, ['MSWDO Disbursing Officer', 'Step 2 Officer', 'Step
                                    2', 'Admin'])) ? $disbursingOfficer : 'MENDY D. FAJARDO' }}</div>
                                <div class="signatory-title">MSWDO Staff</div>
                            </div>

                            <!-- 2. Reviewed and Certified by -->
                            <div class="signatory-col">
                                <div class="signatory-role">Reviewed and Certified by</div>
                                <div class="signatory-line"></div>
                                <div class="signatory-name">FREDDIE RICK O. CALOS,RSW</div>
                                <div class="signatory-title">MSWDO-HEAD</div>
                            </div>

                            <!-- 3. Approved for Payment -->
                            <div class="signatory-col">
                                <div class="signatory-role">Approved for Payment</div>
                                <div class="signatory-line"></div>
                                <div class="signatory-name">HON. PGEN EDWARD E. CARRANZA (RET.)</div>
                                <div class="signatory-title">Municipal Mayor</div>
                            </div>

                            <!-- 4. Municipal Treasurer -->
                            <div class="signatory-col">
                                <div class="signatory-role signatory-role-empty">&nbsp;</div>
                                <div class="signatory-line"></div>
                                <div class="signatory-name">CLARISSA M. TORRES</div>
                                <div class="signatory-title">Municipal Treasurer - OIC</div>
                            </div>

                            <!-- 5. Special Disbursing Officer / Paymaster -->
                            <div class="signatory-col">
                                <div class="signatory-role">Special Disbursing Officer</div>
                                <div class="signatory-line"></div>
                                <div class="signatory-name signatory-name-empty">&nbsp;</div>
                                <div class="signatory-title">Paymaster</div>
                            </div>
                        </div>

                        <div class="sheet-footer-page-meta">
                            <span class="footer-meta-left">MSWDO Silang, Cavite &bull; Financial Assistance Management
                                System</span>
                            <span class="footer-meta-right">Sheet {{ $page['pageNumber'] }} of {{ $totalPages }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- External Module Script -->
        <script
            src="{{ asset('js/financialstep2-payroll-print.js') }}?v={{ file_exists(public_path('js/financialstep2-payroll-print.js')) ? filemtime(public_path('js/financialstep2-payroll-print.js')) : time() }}">
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
            const backBtn = document.getElementById('btnBackPayroll');
            if (backBtn) {
                backBtn.addEventListener('click', function (e) {
                    e.preventDefault();

                    // 1. If opened from an existing opener tab (Payroll Records or Generator)
                    if (window.opener && !window.opener.closed) {
                        try {
                            window.opener.focus();
                        } catch (err) {}
                        window.close();

                        // Fallback if browser restricted window.close()
                        setTimeout(function () {
                            if (!window.closed) {
                                window.location.href = backBtn.getAttribute('href');
                            }
                        }, 250);
                        return;
                    }

                    // 2. Try closing this tab if it was opened as a separate window/tab
                    window.close();

                    // 3. If window is still open (e.g. opened directly or in same tab), return smoothly
                    setTimeout(function () {
                        if (!window.closed) {
                            if (window.history.length > 1) {
                                window.history.back();
                            } else {
                                window.location.href = backBtn.getAttribute('href');
                            }
                        }
                    }, 250);
                });
            }
        });
        </script>
</body>

</html>