<!doctype html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title>Social Case Study Report · {{ $report->case_number }}</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        body {
            font-family: 'Calibri', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000000;
        }
        .center {
            text-align: center;
        }
        .header {
            margin-bottom: 24pt;
        }
        .header h1 {
            font-size: 16pt;
            margin-top: 12pt;
            margin-bottom: 6pt;
            font-weight: bold;
        }
        .header p {
            margin: 2pt 0;
            font-size: 10pt;
        }
        .rule {
            border-bottom: 3px double #000;
            margin: 12pt 0;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 18pt;
            margin-bottom: 6pt;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 2pt;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12pt;
        }
        .info-table td {
            padding: 6pt;
            border: 1px solid #000;
            vertical-align: top;
        }
        .info-table td.label {
            font-weight: bold;
            width: 25%;
            background-color: #f3f4f6;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12pt;
        }
        .grid-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            border: 1px solid #000;
            padding: 6pt;
            text-align: left;
        }
        .grid-table td {
            border: 1px solid #000;
            padding: 6pt;
            vertical-align: top;
        }
        .text-content {
            margin-bottom: 12pt;
            text-align: justify;
            white-space: pre-wrap;
        }
        .signatures {
            width: 100%;
            margin-top: 40pt;
        }
        .signatures td {
            width: 50%;
            vertical-align: top;
            padding: 12pt;
        }
        .sig-line {
            border-top: 1px solid #000;
            margin-top: 36pt;
            padding-top: 4pt;
            text-align: center;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    <div class="header center">
        <p>Republic of the Philippines</p>
        <p><strong>MUNICIPALITY OF SILANG</strong></p>
        <p>Municipal Social Welfare and Development Office (MSWDO)</p>
        <div class="rule"></div>
        <h1>SOCIAL CASE STUDY REPORT</h1>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Case Number</td>
            <td>{{ $report->case_number }}</td>
            <td class="label">Date Processed</td>
            <td>{{ data_get($snapshot, 'case.date_processed', '') }}</td>
        </tr>
        <tr>
            <td class="label">Client Name</td>
            <td colspan="3">{{ collect($snapshot['client'] ?? [])->filter()->implode(' ') }}</td>
        </tr>
        <tr>
            <td class="label">Beneficiary Name</td>
            <td colspan="3">{{ collect($snapshot['beneficiary'] ?? [])->only(['beneficiary_first_name','beneficiary_middle_name','beneficiary_last_name'])->filter()->implode(' ') }}</td>
        </tr>
        <tr>
            <td class="label">Purpose</td>
            <td colspan="3">{{ data_get($snapshot, 'case.purpose', '') }}</td>
        </tr>
    </table>

    <div class="section-title">I. Family Composition</div>
    <table class="grid-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Relationship</th>
                <th>Age</th>
                <th>Sex</th>
                <th>Occupation</th>
                <th>Monthly Income</th>
            </tr>
        </thead>
        <tbody>
            @forelse($snapshot['family'] ?? [] as $member)
                <tr>
                    <td>{{ $member['full_name'] ?? '' }}</td>
                    <td>{{ $member['relationship'] ?? '' }}</td>
                    <td>{{ $member['age'] ?? '' }}</td>
                    <td>{{ $member['sex'] ?? '' }}</td>
                    <td>{{ $member['occupation'] ?? '' }}</td>
                    <td>{{ $member['monthly_income'] ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">No family members recorded.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">II. Presenting Problem / Interview</div>
    <div class="text-content">
        {{ data_get($snapshot, 'interview.interview_reason', '') }}
        
        {{ data_get($snapshot, 'interview.interview_situation', '') }}
    </div>

    <div class="section-title">III. Social Worker's Assessment</div>
    <div class="text-content">
        {{ data_get($snapshot, 'assessment.social_worker_assessment', '') }}
    </div>

    <div class="section-title">IV. Recommendation</div>
    <div class="text-content">
        <strong>Recommendation Status:</strong> {{ data_get($snapshot, 'assessment.recommendation', '') }}
        @if(data_get($snapshot, 'assessment.recommended_amount'))
            <br><strong>Recommended Amount:</strong> PHP {{ number_format((float) data_get($snapshot, 'assessment.recommended_amount'), 2) }}
        @endif
    </div>

    <table class="signatures">
        <tr>
            <td>
                <div class="sig-line">
                    <strong>Social Case Study Officer</strong><br>
                    Prepared By
                </div>
            </td>
            <td>
                <div class="sig-line">
                    <strong>MSWDO Officer-In-Charge</strong><br>
                    Noted / Approved By
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
