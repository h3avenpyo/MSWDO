@php $isBcpcReport = strtoupper((string) ($department ?? auth()->user()?->defaultCaseDepartment() ?? (auth()->user()?->isBcpcAdmin() ? 'BCPC' : 'VAWC'))) === 'BCPC'; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Barangay Cases Report - {{ $isBcpcReport ? 'BCPC' : 'iSERVE SILANG' }}</title>
    <style>
        /* PDF print setup */
        @page { 
            size: A4 landscape; 
            margin: 15mm 20mm;
        }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 11pt; 
            line-height: 1.5; 
            color: #000; 
            background: #fff; 
        }
        
        /* Official Government Header */
        .header { 
            text-align: center; 
            margin-bottom: 12px; 
            padding-bottom: 8px; 
        }
        
        .header .republic { 
            font-size: 10pt; 
            font-weight: normal; 
            margin-bottom: 1px; 
        }
        
        .header .office { 
            font-size: 12pt; 
            font-weight: bold; 
            margin-bottom: 1px;
            text-transform: uppercase;
        }
        
        .header .municipality {
            font-size: 10pt;
            font-weight: normal;
            margin-bottom: 8px;
        }
        
        .header .document-title { 
            font-size: 13pt; 
            font-weight: bold; 
            margin-top: 8px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-decoration: underline;
        }
        
        .header .print-info {
            font-size: 9pt;
            color: #333;
            margin-top: 4px;
        }
        
        .print-button { 
            display: none !important;
        }
         
        /* Summary Box */
        .summary-section { 
            margin-bottom: 12px; 
            background: #fff; 
            padding: 0;
            border: 2px solid #000; 
        }

        .summary-title {
            font-size: 11pt; 
            font-weight: 700; 
            color: #000; 
            background: #fff;
            margin-bottom: 0; 
            padding: 6px 10px;
            text-transform: uppercase;
            text-align: center;
            letter-spacing: 1.5px;
            border-bottom: 2px solid #000;
        }

        .summary-stats {
            display: flex;
            /* justify-content: center; */
            gap: 36px;
            flex-wrap: nowrap;
            background: #fff;
            padding: 8px 10px;
            align-items: center;
            text-align: center;
        }

        /* Each stat is a compact block with number and label stacked vertically */
        .summary-stat {
            text-align: center;
            display: inline-flex;
            flex-direction: row;
            gap: 4px;
            padding: 0 12px;
            white-space: nowrap;
        }

        .summary-stat .number {
            font-size: 12pt;
            font-weight: 800;
            color: #000;
            display: inline-block;
        }

        .summary-stat .label {
            font-size: 12pt;
            color: #222;
            /* text-transform: uppercase; */
            font-weight: 800;
            display: inline-block;
            opacity: 0.9;
        }
         
        /* Table Styling */
        .barangay-summary-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .continued-section-title {
            margin-top: 80px !important;
        }
        
        .data-table-wrapper {
            page-break-inside: auto;
        }
        
        .data-table-wrapper table tbody tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        .page-break-before {
            page-break-before: always;
        }
        
        .section-title {
            font-size: 11pt; 
            font-weight: bold; 
            color: #000;
            background: #fff;
            margin-bottom: 0; 
            padding: 6px;
            text-transform: uppercase;
            text-align: center;
            letter-spacing: 1px;
            border-bottom: 2px solid #000;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 12px; 
            font-size: 10pt;
            border: 2px solid #000;
        }
        
        table th, table td { 
            border: 1px solid #000; 
            padding: 4px 6px; 
            vertical-align: middle; 
        }
        
        table th { 
            background: #fff; 
            color: #000; 
            text-align: center; 
            font-weight: bold; 
            font-size: 9.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #000;
        }
        
        table tbody tr:nth-child(even) { 
            background: #f8f9fa; 
        }
        
        table tbody tr:nth-child(odd) { 
            background: #fff; 
        }
        
        table tbody td {
            text-align: center;
        }
        
        table tbody td:first-child {
            text-align: left;
            font-weight: 600;
        }
        
        /* Grand Total Row */
        .grand-total-row {
            background: #fff !important;
            color: #000 !important;
            font-weight: bold;
            border-top: 2px solid #000 !important;
        }
        
        .grand-total-row td {
            padding: 8px;
            font-size: 10.5pt;
            border: 1px solid #000;
            font-weight: bold;
        }
         
        .no-cases {
            text-align: center;
            padding: 40px;
            color: #555;
            font-style: italic;
            font-size: 11pt;
        }
         
        /* Signature Section */
        .signature-section { 
            width: 100%; 
            margin-top: auto; 
            page-break-inside: avoid;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            padding: 20px 15mm;
        }
        
        .signature-table { 
            width: 100%; 
            border-collapse: collapse; 
            border: none; 
        }
        
        .signature-table td { 
            width: 50%; 
            vertical-align: top; 
            padding: 0 50px; 
            border: none; 
        }
        
        .signature-cell .sig-label { 
            font-size: 10pt; 
            margin-bottom: 45px;
            font-weight: normal;
            text-align: left;
        }
        
        .signature-cell .sig-line { 
            border-bottom: 2px solid #000; 
            margin: 0;
            width: 70%;
            margin-bottom: 3px;
        }
        
        .signature-cell .sig-name {
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
        }
        
        .signature-cell .sig-role { 
            text-align: center; 
            font-size: 9pt;
            margin-top: 0;
            margin-bottom: 30px;
            width: 70%;
        }
         
        .footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 8pt;
            color: #555;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            padding: 10px 15mm;
            width: 100%;
        }
         
        @media screen {
            .container { 
                max-width: 100%; 
                margin: 0 auto; 
                padding: 30px; 
                background: #fff;
            }
        }
         
        @media print {
            .container { 
                max-width: none; 
                margin: 0; 
                padding: 0; 
            }
        }
    </style>
</head>
@php
    $mswdoLogoPath = public_path('images/mswdo_logo.jpg');
    $mswdoLogoBase64 = file_exists($mswdoLogoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($mswdoLogoPath)) : '';
@endphp
<body>
    <div class="container">
        <!-- Header -->
        <table style="width: 100%; border: none; margin-bottom: 12px; border-collapse: collapse;">
            <tr>
                <td style="width: 65px; vertical-align: middle; text-align: left; border: none; padding: 0;">
                    @if($mswdoLogoBase64)
                        <img src="{{ $mswdoLogoBase64 }}" style="width: 55px; height: 55px;" alt="MSWDO Logo">
                    @endif
                </td>
                <td style="vertical-align: middle; text-align: center; border: none; padding: 0;">
                    <div class="republic">Republic of the Philippines</div>
                    <div class="office">{{ $isBcpcReport ? 'Barangay Council for the Protection of Children (BCPC)' : 'Women and Children Protection Unit (WCPU)' }}</div>
                    <div class="municipality">Municipality of Silang, Province of Cavite</div>
                    <div class="document-title">Consolidated Barangay Cases Report</div>
                    <div class="print-info">
                        Document Generated: {{ now()->format('F j, Y \a\t g:i A') }}<br>
                        @if(!empty($dateRangeText))
                            <strong>Date Filter: {{ $dateRangeText }}</strong><br>
                        @endif
                        Total Barangays Covered: {{ $barangayData->count() }}
                    </div>
                </td>
                <td style="width: 65px; border: none; padding: 0;"></td>
            </tr>
        </table>
        
        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-title">Overall Summary</div>
            <div class="summary-stats" role="group" aria-label="Overall summary statistics">
                <div class="summary-stat">
                    <span class="label">Case Referred: </span>
                    <span class="number">{{ $grandTotals['case_referred'] ?? 0 }}</span>
                </div>
                <div class="summary-stat">
                    <span class="label">Case Filed: </span>
                    <span class="number">{{ $grandTotals['case_filed'] }}</span>
                </div>
                <div class="summary-stat">
                    <span class="label">Case Closed: </span>
                    <span class="number">{{ $grandTotals['case_closed'] }}</span>
                </div>
                <div class="summary-stat">
                    <span class="label">Total Cases: </span>
                    <span class="number">{{ $grandTotals['total_cases'] }}</span>
                </div>
            </div>
        </div>
        
        <!-- Barangay Summary Table -->
        <div class="barangay-summary-section">
            <div class="section-title">Summary of Cases per Barangay</div>
            
            @if($barangayData->count() > 0)
                @php
                    $chunks = $barangayData->chunk(10);
                @endphp
                
                @foreach($chunks as $chunkIndex => $chunk)
                    @if($chunkIndex > 0)
                        <div class="page-break-before"></div>
                        <div class="section-title continued-section-title">Summary of Cases per Barangay (continued)</div>
                    @endif
                    
                    <div class="data-table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 36%;">Barangay</th>
                                    <th style="width: 16%;">Referred</th>
                                    <th style="width: 16%;">Filed</th>
                                    <th style="width: 16%;">Complete Cases</th>
                                    <th style="width: 16%;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chunk as $data)
                                    <tr>
                                        <td style="text-align: left; font-weight: 600;">
                                            {{ $data['barangay']->name }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $data['case_referred'] ?? 0 }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $data['case_filed'] }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $data['case_closed'] }}
                                        </td>
                                        <td style="text-align: center; font-weight: 700;">
                                            {{ $data['total_cases'] }}
                                        </td>
                                    </tr>
                                @endforeach
                                
                                @if($chunkIndex == $chunks->count() - 1)
                                    <!-- Grand Total Row only on last page -->
                                    <tr class="grand-total-row">
                                        <td style="text-align: left;">
                                            <strong>GRAND TOTAL</strong>
                                        </td>
                                        <td style="text-align: center;">
                                            <strong>{{ $grandTotals['case_referred'] }}</strong>
                                        </td>
                                        <td style="text-align: center;">
                                            <strong>{{ $grandTotals['case_filed'] }}</strong>
                                        </td>
                                        <td style="text-align: center;">
                                            <strong>{{ $grandTotals['case_closed'] }}</strong>
                                        </td>
                                        <td style="text-align: center;">
                                            <strong>{{ $grandTotals['total_cases'] }}</strong>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @else
                <div class="no-cases">
                    <strong>No Cases Found</strong><br>
                    There are no cases recorded in any barangay at this time.
                </div>
            @endif
        </div>
        
        <!-- Signature -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td class="signature-cell">
                        <div class="sig-label">Prepared by:</div>
                        <div class="sig-line"></div>
                        <div class="sig-role">{{ ($isBcpcAdminPrint ?? false) ? 'BCPC Administrator' : (($isSwdAdminPrint ?? false) ? 'SWD Administrator' : ($isBcpcReport ? 'BCPC Desk Officer' : 'WCPU Desk Officer')) }}</div>
                    </td>
                    <td class="signature-cell">
                        <div class="sig-label">Reviewed and Approved by:</div>
                        <div class="sig-line"></div>
                        <div class="sig-role">Barangay Captain / Authorized Officer</div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            Barangay Cases Report | {{ $isBcpcReport ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System
        </div>
    </div>
    
    <script>
        // Auto-focus print dialog when page loads (optional)
        // window.addEventListener('load', function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // });
    </script>
@include('print.edit-controls')
</body>
</html>
