<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $barangayName }} Cases Report - {{ strtoupper((string) ($cases->first()->type ?? auth()->user()?->defaultCaseDepartment())) === 'BCPC' ? 'BCPC' : 'iSERVE SILANG' }}</title>
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
        .header .republic { font-size: 10pt; font-weight: normal; margin-bottom: 1px; }
        .header .office { font-size: 12pt; font-weight: bold; margin-bottom: 1px; text-transform: uppercase; }
        .header .municipality { font-size: 10pt; font-weight: normal; margin-bottom: 8px; }
        .header .document-title { font-size: 13pt; font-weight: bold; margin-top: 8px; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 1.5px; text-decoration: underline; }
        .header .print-info { font-size: 9pt; color: #333; margin-top: 4px; }

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
            justify-content: center;
            gap: 36px;
            flex-wrap: nowrap;
            background: #fff;
            padding: 8px 10px;
            align-items: center;
            text-align: center;
        }
        .summary-stat {
            text-align: center;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 0 12px;
            white-space: nowrap;
        }
        .summary-stat .number {
            font-size: 12pt;
            font-weight: 800;
            color: #000;
            display: inline-block;
            text-align: center;
        }
        .summary-stat .label {
            font-size: 12pt;
            color: #222;
            font-weight: 800;
            display: inline-block;
            opacity: 0.9;
            text-align: center;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 12px; 
            font-size: 10pt;
            border: 2px solid #000;
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
            padding: 4px 6px;
        }
        table th, table td { 
            border: 1px solid #000; 
            padding: 4px 6px; 
            vertical-align: middle; 
        }
        table tbody tr:nth-child(even) { background: #f8f9fa; }
        table tbody tr:nth-child(odd) { background: #fff; }
        table tbody td { text-align: center; }
        .case-number { font-weight: 600; color: #000; }
        
        .no-cases {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-style: italic;
        }
        
        /* Footer and signature */
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
            .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        }
        @media print {
            .container { max-width: none; margin: 0; padding: 0; }
        }
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
        .signature-cell .sig-name { text-align: center; font-weight: bold; font-size: 10pt; text-transform: uppercase; }
        .signature-cell .sig-role { text-align: center; font-size: 9pt; margin-top: 0; margin-bottom: 30px; width: 70%; }
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
                    <div class="office">{{ strtoupper((string) ($cases->first()->type ?? auth()->user()?->defaultCaseDepartment())) === 'BCPC' ? 'Barangay Council for the Protection of Children (BCPC)' : 'Women and Children Protection Unit (WCPU)' }}</div>
                    <div class="municipality">Municipality of Silang, Province of Cavite</div>
                    <div class="document-title">{{ $barangayName }} Barangay Cases Report</div>
                    <div class="print-info">
                        Document Generated: {{ $generatedAt ?? now()->format('F j, Y \a\t g:i A') }}<br>
                        Total Cases: {{ $stats['total_cases'] ?? 0 }}
                    </div>
                </td>
                <td style="width: 65px; border: none; padding: 0;"></td>
            </tr>
        </table>
        
        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-title">{{ $barangayName }} Cases Summary</div>
            <div class="summary-stats" role="group" aria-label="Barangay summary statistics">
                <div class="summary-stat">
                    <span class="label">Case Filed: </span>
                    <span class="number">{{ $stats['case_reported'] ?? 0 }}</span>
                </div>
                <div class="summary-stat">
                    <span class="label">Case Referred: </span>
                    <span class="number">{{ $stats['case_referred'] ?? 0 }}</span>
                </div>
                <div class="summary-stat">
                    <span class="label">Case Filed: </span>
                    <span class="number">{{ $stats['case_filed'] ?? 0 }}</span>
                </div>
                <div class="summary-stat">
                    <span class="label">Case Closed: </span>
                    <span class="number">{{ $stats['case_closed'] ?? 0 }}</span>
                </div>
                <div class="summary-stat">
                    <span class="label">Total Cases: </span>
                    <span class="number">{{ $stats['total_cases'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Cases Table -->
        @if(($cases ?? collect())->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 16%;">Case No.</th>
                        <th style="width: 16%;">Reported</th>
                        <th style="width: 24%;">Victim</th>
                        <th style="width: 20%;">Abuse Type</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 12%;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cases as $case)
                        <tr>
                            <td class="case-number">{{ $case->case_number ?? 'N/A' }}</td>
                            <td>{{ $case->date_reported ? \Carbon\Carbon::parse($case->date_reported)->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ censorLastName($case->victim_name) }}</td>
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
                <p>There are no cases recorded for {{ $barangayName }} barangay at this time.</p>
            </div>
        @endif

        <!-- Signature -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td class="signature-cell">
                        <div class="sig-label">Prepared by:</div>
                        <div class="sig-line"></div>
                        <div class="sig-role">{{ strtoupper((string) ($cases->first()->type ?? auth()->user()?->defaultCaseDepartment())) === 'BCPC' ? 'BCPC Desk Officer' : 'WCPU Desk Officer' }}</div>
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
            Barangay Cases Report | {{ strtoupper((string) ($cases->first()->type ?? auth()->user()?->defaultCaseDepartment())) === 'BCPC' ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System
        </div>
    </div>
@include('print.edit-controls')
</body>
</html>
