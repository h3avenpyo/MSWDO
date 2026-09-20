<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Report – {{ $serviceTitle }} ({{ $range['label'] }}) - MSWDO Silang</title>

    <!-- Google Fonts: Public Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* ==============================================================
           1. CORE RESETS & TYPOGRAPHY (Public Sans)
           ============================================================== */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            background-color: #F8FAFC;
            color: #0F172A;
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Web Preview Wrapper */
        .web-preview-container {
            max-width: 960px;
            margin: 0 auto;
            padding: 20px 20px 50px 20px;
        }

        /* Top Action Bar (Hidden on Print) */
        .no-print-bar {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 10px 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 18px;
            border-radius: 9999px;
            font-size: 8.5pt;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: #1A237E;
            color: #FFFFFF;
            border-color: #1A237E;
        }
        .btn-primary:hover {
            background: #121858;
            border-color: #121858;
            color: #FFFFFF;
        }

        .btn-secondary {
            background: #FFFFFF;
            color: #334155;
            border-color: #CBD5E1;
        }
        .btn-secondary:hover {
            background: #F1F5F9;
            color: #0F172A;
        }

        .btn-pdf {
            background: #EEF2FF;
            color: #1A237E;
            border-color: #C7D2FE;
        }
        .btn-pdf:hover {
            background: #E0E7FF;
            color: #121858;
        }

        /* Printable Document Sheet */
        .print-report-document {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 24px 28px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
            margin: 0 auto;
        }

        /* ==============================================================
           2. FINANCIAL ASSISTANCE SIGNATURE HEADER & LETTERHEAD
           ============================================================== */
        .report-header-banner {
            margin-bottom: 12px;
        }

        .letterhead-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 8px;
            margin-bottom: 8px;
            border-bottom: 2px solid #0F172A;
        }

        .header-seal-col {
            width: 65px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-seal-col.text-end {
            justify-content: flex-end;
        }

        .report-seal-img {
            width: 56px;
            height: 56px;
            object-fit: contain;
            display: block;
        }

        .header-text-col {
            flex-grow: 1;
            text-align: center;
            padding: 0 12px;
        }

        .govt-republic-text {
            font-size: 8pt;
            font-weight: 600;
            text-transform: uppercase;
            color: #475569;
            line-height: 1.2;
            letter-spacing: 0.04em;
        }

        .govt-province-text {
            font-size: 8.5pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #334155;
            line-height: 1.2;
            letter-spacing: 0.03em;
        }

        .govt-office-text {
            font-size: 10.5pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #1A237E;
            margin-top: 1px;
            line-height: 1.2;
            letter-spacing: 0.02em;
        }

        .report-main-title {
            font-size: 14pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #0F172A;
            margin: 4px 0 2px 0;
            line-height: 1.2;
        }

        .report-sub-title {
            font-size: 8.5pt;
            font-weight: 600;
            color: #64748B;
        }

        /* ==============================================================
           3. STANDARDIZED METADATA TABLE (4-Column)
           ============================================================== */
        .report-meta-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #94A3B8;
            background-color: #F8FAFC;
            margin-bottom: 12px;
        }

        .report-meta-cell {
            padding: 5px 8px;
            border: 1px solid #CBD5E1;
            vertical-align: middle;
        }

        .report-meta-title {
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748B;
            letter-spacing: 0.03em;
        }

        .report-meta-val {
            font-size: 9pt;
            font-weight: 700;
            color: #0F172A;
            margin-top: 1px;
        }

        /* ==============================================================
           4. EXECUTIVE SUMMARY INDICATORS (Financial Assistance 4-Tile Box)
           ============================================================== */
        .report-summary-box {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #94A3B8;
            margin-bottom: 14px;
        }

        .report-summary-cell {
            padding: 8px 10px;
            border: 1px solid #CBD5E1;
            text-align: center;
            vertical-align: middle;
            background-color: #FFFFFF;
            width: 25%;
        }

        .report-summary-title {
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
            letter-spacing: 0.03em;
        }

        .report-summary-val {
            font-size: 14pt;
            font-weight: 800;
            color: #1A237E;
            margin: 2px 0 1px 0;
            line-height: 1.2;
        }

        .report-summary-sub {
            font-size: 7pt;
            color: #64748B;
            font-weight: 600;
        }

        /* ==============================================================
           5. FULL-WIDTH REPORT SECTIONS & HEADINGS
           ============================================================== */
        .report-section {
            width: 100%;
            margin-bottom: 14px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .report-section-heading {
            font-size: 9.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #0F172A;
            margin-bottom: 4px;
            border-bottom: 2px solid #0F172A;
            padding-bottom: 3px;
        }

        .report-table-subtext {
            font-size: 7.5pt;
            color: #64748B;
            margin-top: -2px;
            margin-bottom: 6px;
        }

        /* ==============================================================
           6. REPORT DATA TABLES (Financial Assistance Style with #f2dbdb Headers)
           ============================================================== */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #94A3B8;
            font-size: 8.5pt;
            table-layout: fixed;
            margin-bottom: 4px;
        }

        .report-table th {
            background-color: #f2dbdb;
            color: #0F172A;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.03em;
            padding: 6px 8px;
            border: 1px solid #94A3B8;
            vertical-align: middle;
        }

        .report-table td {
            padding: 5px 8px;
            border: 1px solid #CBD5E1;
            color: #0F172A;
            font-size: 8.5pt;
            vertical-align: middle;
        }

        .report-table tr:nth-child(even) td {
            background-color: #F8FAFC;
        }

        .report-table-total-row td {
            background-color: #F8FAFC !important;
            font-weight: 800;
            border-top: 2px solid #0F172A;
            border-bottom: 2px solid #0F172A;
            color: #0F172A;
            font-size: 9pt;
            padding: 6px 8px;
        }

        /* ==============================================================
           7. ADMINISTRATIVE NOTES & DATA VERIFICATION
           ============================================================== */
        .report-notes-box {
            border: 1px solid #CBD5E1;
            background-color: #F8FAFC;
            padding: 7px 10px;
            border-radius: 4px;
            margin-bottom: 12px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* ==============================================================
           8. OFFICIAL SIGNATURES BLOCK
           ============================================================== */
        .report-signatures-section {
            margin-top: 14px;
            margin-bottom: 10px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .signatures-row {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }

        .signature-col {
            flex: 1;
            text-align: center;
        }

        .signature-space {
            height: 38px;
        }

        .signature-line {
            width: 220px;
            height: 1.5px;
            background-color: #0F172A;
            margin: 0 auto;
        }

        .signature-name {
            font-size: 9pt;
            font-weight: 800;
            color: #0F172A;
            margin-top: 3px;
        }

        .signature-title {
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #475569;
            font-weight: 600;
        }

        .signature-office {
            font-size: 7pt;
            color: #64748B;
        }

        /* ==============================================================
           9. OFFICIAL DOCUMENT FOOTER
           ============================================================== */
        .report-official-footer {
            page-break-inside: avoid;
            break-inside: avoid;
            font-size: 7.5pt;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px solid #CBD5E1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #64748B;
        }

        /* ==============================================================
           10. PRINT MEDIA STYLES (Strict A4 Layout)
           ============================================================== */
        @media print {
            html, body {
                background: #FFFFFF !important;
                background-color: #FFFFFF !important;
                color: #0F172A !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .web-preview-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .print-report-document {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            @page {
                size: A4 portrait;
                margin: 10mm 12mm 10mm 12mm;
            }

            .report-section {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .report-signatures-section {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .report-official-footer {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>

<body>
    @php
        // Resolve Municipality Seals
        $silangSealSrc = file_exists(public_path('images/silangseal.png')) 
            ? asset('images/silangseal.png') 
            : (file_exists(public_path('images/silang.png')) ? asset('images/silang.png') : '');
        $mswdoLogoSrc = file_exists(public_path('images/mswdo-logo.png')) 
            ? asset('images/mswdo-logo.png') 
            : (file_exists(public_path('images/dswdlogo.png')) ? asset('images/dswdlogo.png') : '');

        // Module Specific Metrics Setup
        $totalVol = (int) ($stats['totalVolume'] ?? 0);
        $totalAmt = (float) ($stats['totalAmount'] ?? 0);
        $serviceKey = $service ?? 'all';

        if ($serviceKey === 'social_case') {
            $metric1Title = 'Total Case Volume';
            $metric1Val = number_format($totalVol);
            $metric1Sub = 'Social Case Intakes';

            $metric2Title = 'Pending / Under Review';
            $metric2Val = number_format($stats['pendingVolume'] ?? 0);
            $metric2Color = '#B45309';
            $metric2Sub = 'Worker Evaluation';

            $metric3Title = 'Completed / Released';
            $metric3Val = number_format($stats['completedVolume'] ?? 0);
            $metric3Color = '#166534';
            $metric3Sub = 'Resolved Cases';

            $metric4Title = 'Assistance Disbursed';
            $metric4Val = '&#8369;' . number_format($totalAmt, 2);
            $metric4Color = '#15803D';
            $metric4Sub = 'Direct Welfare Grants';

            $section1Title = '1. Programmatic & Purpose Classification';
            $section1Sub = 'Distribution of social case study applications according to requested welfare purpose or assistance service.';
            $catColHeader = 'Welfare Purpose / Service Classification';

        } elseif ($serviceKey === 'senior') {
            $metric1Title = 'New Registrations';
            $metric1Val = number_format($totalVol);
            $metric1Sub = 'Enrolled in Period';

            $metric2Title = 'Active Senior Citizens';
            $metric2Val = number_format($stats['activeSeniors'] ?? 0);
            $metric2Color = '#0284C7';
            $metric2Sub = 'Verified Masterlist';

            $metric3Title = 'Payout Beneficiaries';
            $metric3Val = number_format($stats['payoutBeneficiaries'] ?? 0);
            $metric3Color = '#166534';
            $metric3Sub = 'Birthday Gifts Released';

            $metric4Title = 'Total Cash Subsidies';
            $metric4Val = '&#8369;' . number_format($totalAmt, 2);
            $metric4Color = '#15803D';
            $metric4Sub = 'Birthday Financial Aid';

            $section1Title = '1. Demographic Age Classification';
            $section1Sub = 'Age cohort breakdown of registered senior citizens residing within the Municipality of Silang.';
            $catColHeader = 'Age Bracket / Cohort';

        } elseif ($serviceKey === 'financial') {
            $metric1Title = 'Total Intake Volume';
            $metric1Val = number_format($totalVol);
            $metric1Sub = 'Financial Applications';

            $metric2Title = 'Pending Assessment';
            $metric2Val = number_format($stats['pendingVolume'] ?? 0);
            $metric2Color = '#B45309';
            $metric2Sub = 'Awaiting Recommendation';

            $metric3Title = 'Approved Assistance';
            $metric3Val = number_format($stats['completedVolume'] ?? 0);
            $metric3Color = '#166534';
            $metric3Sub = 'Step 1 & 2 Approved';

            $metric4Title = 'Assistance Granted';
            $metric4Val = '&#8369;' . number_format($totalAmt, 2);
            $metric4Color = '#15803D';
            $metric4Sub = 'Total Recommended Grants';

            $section1Title = '1. Financial Assistance Type Classification';
            $section1Sub = 'Categorical distribution of evaluated requests for financial, medical, burial, and educational aid.';
            $catColHeader = 'Assistance Category / Type';

        } else {
            // Consolidated (All Services)
            $metric1Title = 'Total Beneficiaries';
            $metric1Val = number_format($totalVol);
            $metric1Sub = 'Consolidated Clients';

            $metric2Title = 'Pending Casework';
            $metric2Val = number_format($stats['pendingVolume'] ?? 0);
            $metric2Color = '#B45309';
            $metric2Sub = 'Active In Review';

            $metric3Title = 'Completed Services';
            $metric3Val = number_format($stats['completedVolume'] ?? 0);
            $metric3Color = '#166534';
            $metric3Sub = 'Released & Approved';

            $metric4Title = 'Total Assistance Disbursed';
            $metric4Val = '&#8369;' . number_format($totalAmt, 2);
            $metric4Color = '#15803D';
            $metric4Sub = 'Combined Financial Grants';

            $section1Title = '1. Service Division Distribution';
            $section1Sub = 'Summary of beneficiary caseload across the major MSWDO welfare operating divisions.';
            $catColHeader = 'Welfare Program / Service Division';
        }

        // Section 1 Category Calculations
        $categories = $breakdowns['categories'] ?? [];
        $catSum = array_sum($categories);

        // Section 2 Barangay Calculations
        $barangays = $breakdowns['barangays'] ?? [];
        $brgySum = array_sum($barangays);

        // Section 3 Records Calculations
        $recordList = $records ?? [];
        $recordsCount = count($recordList);
        $recordsTotalAmount = 0;
        foreach ($recordList as $r) {
            $recordsTotalAmount += (float) ($r['amount'] ?? 0);
        }
    @endphp

    <div class="web-preview-container">
        <!-- Top Action Navigation Bar (Hidden when printed) -->
        <div class="no-print-bar">
            <div style="display: flex; align-items: center; gap: 8px;">
                <a href="{{ route('admin.dashboard') }}" id="btnBackToDashboard" class="btn-action btn-secondary" onclick="return handleBackToDashboard(event, this.href);" title="Return to MSWDO Executive Dashboard">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Dashboard</span>
                </a>
                <span style="font-size: 8pt; color: #94A3B8;">|</span>
                <span style="font-size: 8pt; color: #475569; font-weight: 600;">
                    <i class="fas fa-file-alt text-primary me-1"></i>{{ $serviceTitle }} &bull; {{ $range['label'] }}
                </span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <a href="{{ route('admin.dashboard.reports.pdf', request()->all()) }}" class="btn-action btn-pdf" title="Export report to PDF">
                    <i class="fas fa-file-pdf"></i>
                    <span>Download PDF</span>
                </a>
                <button type="button" onclick="window.print()" class="btn-action btn-primary" title="Print this official report">
                    <i class="fas fa-print"></i>
                    <span>Print Report</span>
                </button>
            </div>
        </div>

        <!-- Official Print Report Document -->
        <div class="print-report-document">

            <!-- 1. OFFICIAL GOVERNMENT LETTERHEAD & HEADER -->
            <div class="report-header-banner">
                <div class="letterhead-row">
                    <div class="header-seal-col">
                        @if($silangSealSrc)
                        <img src="{{ $silangSealSrc }}" alt="Silang Seal" class="report-seal-img">
                        @endif
                    </div>
                    <div class="header-text-col">
                        <div class="govt-republic-text">Republic of the Philippines</div>
                        <div class="govt-province-text">Province of Cavite &bull; Municipality of Silang</div>
                        <div class="govt-office-text">Municipal Social Welfare and Development Office</div>
                        <h2 class="report-main-title">OFFICIAL WELFARE &amp; CASEWORK REPORT</h2>
                        <div class="report-sub-title">
                            {{ $serviceTitle }} &bull; Official {{ ucfirst($range['period']) }} Report &bull; Coverage: {{ $range['label'] }}
                        </div>
                    </div>
                    <div class="header-seal-col text-end">
                        @if($mswdoLogoSrc)
                        <img src="{{ $mswdoLogoSrc }}" alt="MSWDO Logo" class="report-seal-img">
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. STANDARDIZED METADATA TABLE -->
            <table class="report-meta-table">
                <tr>
                    <td class="report-meta-cell" style="width: 25%;">
                        <div class="report-meta-title">Date &amp; Time Generated:</div>
                        <div class="report-meta-val">{{ $generatedAt ?? date('F d, Y • h:i A') }}</div>
                    </td>
                    <td class="report-meta-cell" style="width: 27%;">
                        <div class="report-meta-title">Period Covered:</div>
                        <div class="report-meta-val" style="color: #1A237E;">
                            {{ $range['label'] }} ({{ $range['formattedRange'] }})
                        </div>
                    </td>
                    <td class="report-meta-cell" style="width: 25%;">
                        <div class="report-meta-title">Service Division:</div>
                        <div class="report-meta-val" style="color: #1A237E;">
                            {{ $serviceTitle }}
                        </div>
                    </td>
                    <td class="report-meta-cell text-end" style="width: 23%;">
                        <div class="report-meta-title">{{ $totalAmt > 0 ? 'Total Grants Disbursed:' : 'Total Caseload Volume:' }}</div>
                        <div class="report-meta-val" style="color: {{ $totalAmt > 0 ? '#166534' : '#1A237E' }};">
                            @if($totalAmt > 0)
                                &#8369;{{ number_format($totalAmt, 2) }}
                            @else
                                {{ number_format($totalVol) }} Clients
                            @endif
                        </div>
                    </td>
                </tr>
            </table>

            <!-- 3. EXECUTIVE SUMMARY INDICATORS (Financial Assistance 4-Tile Box) -->
            <table class="report-summary-box">
                <tr>
                    <td class="report-summary-cell">
                        <div class="report-summary-title">{{ $metric1Title }}</div>
                        <div class="report-summary-val" style="color: #1A237E;">{{ $metric1Val }}</div>
                        <div class="report-summary-sub">{{ $metric1Sub }}</div>
                    </td>
                    <td class="report-summary-cell">
                        <div class="report-summary-title">{{ $metric2Title }}</div>
                        <div class="report-summary-val" style="color: {{ $metric2Color ?? '#B45309' }};">{{ $metric2Val }}</div>
                        <div class="report-summary-sub">{{ $metric2Sub }}</div>
                    </td>
                    <td class="report-summary-cell">
                        <div class="report-summary-title">{{ $metric3Title }}</div>
                        <div class="report-summary-val" style="color: {{ $metric3Color ?? '#166534' }};">{{ $metric3Val }}</div>
                        <div class="report-summary-sub">{{ $metric3Sub }}</div>
                    </td>
                    <td class="report-summary-cell">
                        <div class="report-summary-title">{{ $metric4Title }}</div>
                        <div class="report-summary-val" style="color: {{ $metric4Color ?? '#15803D' }};">{!! $metric4Val !!}</div>
                        <div class="report-summary-sub">{{ $metric4Sub }}</div>
                    </td>
                </tr>
            </table>

            <!-- 4. SECTION 1: PROGRAMMATIC & CATEGORICAL CLASSIFICATION -->
            <div class="report-section">
                <div class="report-section-heading">{{ $section1Title }} &bull; {{ $range['label'] }}</div>
                <p class="report-table-subtext">{{ $section1Sub }}</p>

                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 8%; text-align: center;">#</th>
                            <th style="width: 62%;">{{ $catColHeader }}</th>
                            <th style="width: 15%; text-align: right;">Count / Cases</th>
                            <th style="width: 15%; text-align: right;">Share (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $catIndex = 1; @endphp
                        @forelse($categories as $categoryName => $count)
                        @php
                            $pct = $catSum > 0 ? round(($count / $catSum) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td style="text-align: center; color: #64748B; font-weight: 600;">{{ $catIndex++ }}</td>
                            <td style="font-weight: 700; color: #0F172A;">{{ $categoryName }}</td>
                            <td style="text-align: right; font-weight: 700; color: #1A237E;">{{ number_format($count) }}</td>
                            <td style="text-align: right; color: #475569;">{{ $pct }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748B; padding: 10px;">
                                No categorical data recorded for this period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($categories) > 0)
                    <tfoot>
                        <tr class="report-table-total-row">
                            <td colspan="2" style="font-weight: 700;">Total Classified ({{ count($categories) }} Categories &bull; {{ $range['label'] }})</td>
                            <td style="text-align: right; font-weight: 800; color: #1A237E;">{{ number_format($catSum) }}</td>
                            <td style="text-align: right; font-weight: 800;">100%</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            <!-- 5. SECTION 2: GEOGRAPHIC DISTRIBUTION (PER BARANGAY) -->
            <div class="report-section">
                <div class="report-section-heading">2. Geographic Distribution of Beneficiaries (Per Barangay) &bull; {{ $range['label'] }}</div>
                <p class="report-table-subtext">Geographic distribution of served clients and beneficiaries across barangays in the Municipality of Silang.</p>

                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 8%; text-align: center;">Rank</th>
                            <th style="width: 62%;">Barangay Name</th>
                            <th style="width: 15%; text-align: right;">Beneficiaries</th>
                            <th style="width: 15%; text-align: right;">Share (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $bRank = 1; @endphp
                        @forelse($barangays as $bName => $bCount)
                        @php
                            $bPct = $brgySum > 0 ? round(($bCount / $brgySum) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td style="text-align: center; color: #64748B; font-weight: 600;">{{ $bRank++ }}</td>
                            <td style="font-weight: 700; color: #0F172A;">{{ $bName }}</td>
                            <td style="text-align: right; font-weight: 700; color: #1A237E;">{{ number_format($bCount) }}</td>
                            <td style="text-align: right; color: #475569;">{{ $bPct }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748B; padding: 10px;">
                                No barangay distribution records found for this period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($barangays) > 0)
                    <tfoot>
                        <tr class="report-table-total-row">
                            <td colspan="2" style="font-weight: 700;">Total Recorded ({{ count($barangays) }} Barangays &bull; {{ $range['label'] }})</td>
                            <td style="text-align: right; font-weight: 800; color: #1A237E;">{{ number_format($brgySum) }}</td>
                            <td style="text-align: right; font-weight: 800;">100%</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            <!-- 6. SECTION 3: CLIENT CASEWORK & ASSESSMENT REGISTRY -->
            <div class="report-section">
                <div class="report-section-heading">3. Client Casework &amp; Assessment Registry &bull; {{ $range['label'] }}</div>
                <p class="report-table-subtext">Individual client records evaluated, processed, or released during this reporting interval.</p>

                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 14%;">Ref / Case No.</th>
                            <th style="width: 24%;">Beneficiary / Client Name</th>
                            <th style="width: 18%;">Barangay</th>
                            <th style="width: 20%;">Program / Purpose</th>
                            <th style="width: 12%;">Status</th>
                            <th style="width: 12%; text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recordList as $rec)
                        <tr>
                            <td style="font-weight: 700; color: #1A237E; font-size: 8pt;">{{ $rec['ref'] }}</td>
                            <td style="font-weight: 600;">{{ $rec['name'] }}</td>
                            <td>{{ $rec['barangay'] }}</td>
                            <td>{{ $rec['category'] }}</td>
                            <td>
                                <span style="font-weight: 600; color: {{ in_array(strtolower($rec['status']), ['completed', 'released', 'approved', 'active']) ? '#166534' : '#B45309' }};">
                                    {{ $rec['status'] }}
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: {{ ($rec['amount'] ?? 0) > 0 ? '#166534' : '#64748B' }};">
                                {{ ($rec['amount'] ?? 0) > 0 ? '₱' . number_format($rec['amount'], 2) : '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748B; padding: 12px;">
                                No individual client records found for this period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($recordsCount > 0)
                    <tfoot>
                        <tr class="report-table-total-row">
                            <td colspan="5" style="font-weight: 700;">
                                Total Roster Records Evaluated ({{ number_format($recordsCount) }} Clients)
                            </td>
                            <td style="text-align: right; font-weight: 800; color: {{ $recordsTotalAmount > 0 ? '#166534' : '#0F172A' }};">
                                {{ $recordsTotalAmount > 0 ? '₱' . number_format($recordsTotalAmount, 2) : '—' }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            <!-- 7. ADMINISTRATIVE NOTES & DATA VERIFICATION -->
            <div class="report-notes-box">
                <div style="font-weight: 800; text-transform: uppercase; font-size: 7.5pt; color: #1E293B; margin-bottom: 2px;">
                    Administrative Notes &amp; Official Certification
                </div>
                <p style="margin: 0; font-size: 7pt; color: #475569; line-height: 1.4;">
                    This document represents the official electronic casework extract generated from the Municipal Social Welfare and Development Office (MSWDO) Central Information System. All records, program classifications, beneficiary counts, and financial disbursement entries have been validated against official case files and intake registries of the Municipality of Silang, Cavite.
                </p>
            </div>

            <!-- 8. OFFICIAL SIGNATURES BLOCK -->
            <div class="report-signatures-section">
                <div class="signatures-row">
                    <div class="signature-col">
                        <div class="signature-space"></div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $generatedBy ?: (session('admin_user_name') ?: 'Fred Calos') }}</div>
                        <div class="signature-title">Prepared by / Reporting Officer</div>
                        <div class="signature-office">Municipal Social Welfare and Development Office</div>
                    </div>
                    <div class="signature-col">
                        <div class="signature-space"></div>
                        <div class="signature-line"></div>
                        <div class="signature-name">MSWDO HEAD OFFICER</div>
                        <div class="signature-title">Noted &amp; Approved by</div>
                        <div class="signature-office">Municipal Social Welfare &amp; Development Officer</div>
                    </div>
                </div>
            </div>

            <!-- 9. OFFICIAL DOCUMENT FOOTER -->
            <div class="report-official-footer">
                <span>
                    MSWDO Silang, Cavite &bull; {{ $serviceTitle }} &bull; Confidential Official Report (Coverage: {{ $range['label'] }})
                </span>
                <span>
                    Document Printed on {{ date('F d, Y \a\t h:i A') }}
                </span>
            </div>

        </div><!-- /.print-report-document -->
    </div><!-- /.web-preview-container -->

    <!-- Navigation & Print Scripts -->
    <script>
        /**
         * Handle smooth navigation back to the original Dashboard tab
         * without spawning duplicate pages or extra browser tabs.
         */
        function handleBackToDashboard(event, fallbackUrl) {
            if (event) {
                event.preventDefault();
            }

            try {
                // If this report was opened in a new tab/window from the Dashboard,
                // bring the original Dashboard tab into focus and close this report tab.
                if (window.opener && !window.opener.closed) {
                    window.opener.focus();
                    window.close();

                    // Safety fallback if the browser restricts window.close():
                    setTimeout(function () {
                        if (!window.closed) {
                            window.location.href = fallbackUrl;
                        }
                    }, 350);
                    return false;
                }
            } catch (err) {
                console.warn('Opener focus attempt:', err);
            }

            // Fallback: If no opener tab is available, navigate in this same tab
            window.location.href = fallbackUrl;
            return false;
        }

        // Auto open print dialog when opened via print button
        window.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                window.print();
            }, 350);
        });
    </script>
</body>
</html>
