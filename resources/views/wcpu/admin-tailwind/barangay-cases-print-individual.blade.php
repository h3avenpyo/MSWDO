@php $isBcpcReport = strtoupper((string) ($department ?? $cases->first()->type ?? auth()->user()?->defaultCaseDepartment() ?? (auth()->user()?->isBcpcAdmin() ? 'BCPC' : 'VAWC'))) === 'BCPC'; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $barangay->name }} Barangay Cases Report</title>
    <style>
        @page { size: A4 landscape; margin: 14mm 16mm 18mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #20252b; font-family: Arial, Helvetica, sans-serif; font-size: 9.5pt; line-height: 1.35; }

        .header-table, .summary-table, .cases-table, .signature-table { width: 100%; border-collapse: collapse; }
        .header-table { width: 78%; margin: 0 auto 10px; }
        .header-table td { border: 0; padding: 0; vertical-align: middle; }
        .logo-cell { width: 76px; text-align: center; }
        .logo { display: block; width: 62px; height: 62px; margin: 0 auto; object-fit: contain; }
        .header-copy { padding: 0 10px; text-align: center; }
        .republic { color: #1b1b1b; font-family: "Times New Roman", Times, serif; font-size: 10pt; font-weight: bold; letter-spacing: 1.7px; text-transform: uppercase; }
        .municipality { margin-top: 2px; color: #1b1b1b; font-family: "Times New Roman", Times, serif; font-size: 12pt; font-weight: bold; text-transform: uppercase; }
        .province { margin-top: 1px; color: #454545; font-family: "Times New Roman", Times, serif; font-size: 9pt; font-style: italic; }
        .office { margin-top: 6px; color: #20252b; font-family: "Times New Roman", Times, serif; font-size: 10pt; font-weight: bold; text-transform: uppercase; }
        .document-title { margin-top: 7px; padding: 5px 0; border-top: 1.5px solid #9b1b30; border-bottom: 1.5px solid #9b1b30; color: #8f172c; font-family: "Times New Roman", Times, serif; font-size: 13pt; font-weight: bold; letter-spacing: .4px; text-transform: uppercase; }

        .report-meta { margin: 0 0 12px; padding: 7px 10px; border: 1px solid #d7dce1; background: #f7f8fa; color: #4b5560; font-size: 8.5pt; text-align: center; }
        .summary-table { margin: 0 0 12px; border: 1px solid #cbd2d9; }
        .summary-table th { padding: 6px 9px; background: #9b1b30; color: #ffffff; font-size: 8.5pt; font-weight: bold; letter-spacing: .4px; text-align: left; text-transform: uppercase; }
        .summary-table td { width: 25%; padding: 9px 10px; border-right: 1px solid #d9dee3; background: #ffffff; text-align: center; }
        .summary-table td:last-child { border-right: 0; }
        .summary-label { color: #65707b; font-size: 8pt; font-weight: bold; text-transform: uppercase; }
        .summary-value { display: block; margin-top: 2px; color: #8f172c; font-size: 15pt; font-weight: bold; }

        .table-heading { margin: 0; padding: 7px 10px; border: 1px solid #9b1b30; border-bottom: 0; background: #9b1b30; color: #ffffff; font-size: 9pt; font-weight: bold; letter-spacing: .4px; text-transform: uppercase; }
        .cases-table { margin: 0; border: 1px solid #cbd2d9; table-layout: fixed; }
        .cases-table thead { display: table-header-group; }
        .cases-table tr { page-break-inside: avoid; }
        .cases-table th { padding: 7px 6px; border: 1px solid #cbd2d9; background: #efe3d2; color: #28231e; font-size: 8pt; font-weight: bold; letter-spacing: .25px; text-align: center; text-transform: uppercase; }
        .cases-table td { padding: 7px 6px; border: 1px solid #d7dce1; color: #303841; font-size: 8.8pt; overflow-wrap: break-word; text-align: center; vertical-align: middle; }
        .cases-table tbody tr:nth-child(even) td { background: #f8f9fa; }
        .cases-table td:nth-child(1), .cases-table td:nth-child(3), .cases-table td:nth-child(4) { text-align: left; }
        .case-number { color: #7f1528; font-weight: bold; }
        .no-cases { padding: 28px; border: 1px solid #d7dce1; color: #5f6974; font-size: 10pt; text-align: center; }
        .no-cases h3 { margin: 0 0 4px; color: #8f172c; font-size: 11pt; }

        .signature-section { margin-top: 28px; page-break-inside: avoid; }
        .signature-table td { width: 50%; padding: 0 36px; border: 0; vertical-align: top; }
        .signature-label { margin-bottom: 32px; color: #414952; font-size: 8.5pt; }
        .signature-line { width: 76%; margin: 0 auto 5px; border-top: 1px solid #303841; }
        .signature-role { color: #4d5660; font-size: 8.5pt; font-style: italic; text-align: center; }
        .footer { position: fixed; right: 0; bottom: -11mm; left: 0; border-top: 1px solid #d7dce1; color: #69737d; font-size: 7.5pt; text-align: center; }
    </style>
</head>
@php
    $mswdoLogoPath = public_path('images/mswdo_logo.jpg');
    $silangLogoPath = public_path('images/silang.png');
    $mswdoLogoBase64 = file_exists($mswdoLogoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($mswdoLogoPath)) : '';
    $silangLogoBase64 = file_exists($silangLogoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($silangLogoPath)) : '';
@endphp
<body>
    <div class="container">
        <table class="header-table" role="presentation">
            <tr>
                <td class="logo-cell">
                    @if($mswdoLogoBase64)
                        <img class="logo" src="{{ $mswdoLogoBase64 }}" alt="MSWDO Logo">
                    @endif
                </td>
                <td class="header-copy">
                    <div class="republic">Republic of the Philippines</div>
                    <div class="municipality">Municipality of Silang</div>
                    <div class="province">Province of Cavite</div>
                    <div class="office">{{ $isBcpcReport ? 'Barangay Council for the Protection of Children (BCPC)' : 'Social Welfare and Development (SWD)' }}</div>
                    <div class="document-title">{{ $barangay->name }} Barangay Cases Report</div>
                </td>
                <td class="logo-cell">
                    @if($silangLogoBase64)
                        <img class="logo" src="{{ $silangLogoBase64 }}" alt="Bayan ng Silang Logo">
                    @endif
                </td>
            </tr>
        </table>

        <div class="report-meta">
            Generated on {{ $printDate }}
            @if(!empty($dateRangeText))
                &nbsp; | &nbsp; Date Filter: {{ $dateRangeText }}
            @endif
            @if(!empty($statusFilter))
                &nbsp; | &nbsp; Status Filter: {{ $statusFilter }}
            @endif
        </div>

        <table class="summary-table" role="presentation">
            <tr><th colspan="4">{{ $barangay->name }} Case Summary</th></tr>
            <tr>
                <td data-print-locked><span class="summary-label">Cases Referred</span><span class="summary-value">{{ $caseReferred }}</span></td>
                <td data-print-locked><span class="summary-label">Cases Filed</span><span class="summary-value">{{ $caseFiled }}</span></td>
                <td data-print-locked><span class="summary-label">Completed Cases</span><span class="summary-value">{{ $caseClosed ?? 0 }}</span></td>
                <td data-print-locked><span class="summary-label">Total Cases</span><span class="summary-value">{{ $totalCases }}</span></td>
            </tr>
        </table>

        @if($cases->count() > 0)
            <div class="table-heading">Case Details</div>
            <table class="cases-table">
                <thead>
                    <tr>
                        <th style="width: 16%;">Case No.</th>
                        <th style="width: 14%;">Date Reported</th>
                        <th style="width: 24%;">Victim</th>
                        <th style="width: 20%;">Abuse Type</th>
                        <th style="width: 13%;">Status</th>
                        <th style="width: 13%;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cases as $case)
                        <tr>
                            <td class="case-number">{{ $case->case_number ?? 'N/A' }}</td>
                            <td>{{ $case->date_reported ? \Carbon\Carbon::parse($case->date_reported)->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $case->victim_name ?? 'N/A' }}</td>
                            <td>{{ $case->abuse_type ? ucfirst($case->abuse_type) : 'N/A' }}</td>
                            <td>{{ $case->status ?? 'Pending' }}</td>
                            <td>{{ $case->created_at ? $case->created_at->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-cases">
                <h3>No Cases Found</h3>
                <p>There are no cases recorded for {{ $barangay->name }} barangay at this time.</p>
            </div>
        @endif

        <div class="signature-section">
            <table class="signature-table" role="presentation">
                <tr>
                    <td data-print-locked>
                        <div class="signature-label">Prepared by:</div>
                        <div class="signature-line"></div>
                        <div class="signature-role">{{ ($isBcpcAdminPrint ?? false) ? 'BCPC Administrator' : (($isSwdAdminPrint ?? false) ? 'SWD Administrator' : ($isBcpcReport ? 'BCPC Desk Officer' : 'SWD Desk Officer')) }}</div>
                    </td>
                    <td data-print-locked>
                        <div class="signature-label">Reviewed and Approved by:</div>
                        <div class="signature-line"></div>
                        <div class="signature-role">Barangay Captain / Authorized Officer</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="footer">Barangay Cases Report | {{ $isBcpcReport ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System</div>
@include('print.edit-controls')
</body>
</html>
