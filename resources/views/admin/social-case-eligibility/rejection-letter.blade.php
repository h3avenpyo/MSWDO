<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Notice of Ineligibility · {{ $client->full_name }}</title>
    <style>
        @page { margin: 40px 50px; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .header h2 { font-size: 14px; margin: 3px 0; font-weight: bold; }
        .header p { margin: 2px 0; color: #555; }
        .rule { border-top: 2px solid #333; margin: 15px 0; }
        .title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 25px 0;
            text-decoration: underline;
            color: #b91c1c;
        }
        .date { margin-bottom: 20px; }
        .recipient { margin-bottom: 25px; line-height: 1.4; }
        .body-text { margin-bottom: 25px; text-align: justify; }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details-table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .details-table td.label {
            font-weight: bold;
            background-color: #f9fafb;
            width: 30%;
        }
        .signatures {
            width: 100%;
            margin-top: 50px;
        }
        .signatures td {
            width: 50%;
            vertical-align: top;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
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
        <h2><strong>MUNICIPALITY OF SILANG</strong></h2>
        <p>Municipal Social Welfare and Development Office (MSWDO)</p>
        <div class="rule"></div>
    </div>

    <p class="date right">Date: {{ now()->format('F d, Y') }}</p>

    <div class="recipient">
        <strong>TO: {{ $client->full_name }}</strong><br>
        Address: {{ $client->address }}<br>
        Contact: {{ $client->contact_number ?? 'N/A' }}
    </div>

    <div class="title">NOTICE OF INELIGIBILITY</div>

    <div class="body-text">
        Dear <strong>Mr./Ms. {{ $client->last_name }}</strong>,
    </div>

    <div class="body-text">
        Thank you for contacting the Municipal Social Welfare and Development Office (MSWDO). Following a review of your application and database records, we regret to inform you that you are currently <strong>NOT ELIGIBLE</strong> to receive new social case study assistance at this time.
    </div>

    <div class="body-text">
        Our municipal guidelines implement a strict <strong>6-Month Assistance Policy</strong>, requiring a minimum waiting period of six (6) months between approved releases. Below are the details regarding your status:
    </div>

    <table class="details-table">
        <tr>
            <td class="label">Last Assistance Type</td>
            <td>{{ data_get($result, 'latestAssistance.assistance_type', 'None') }}</td>
        </tr>
        <tr>
            <td class="label">Date of Last Release</td>
            <td>{{ data_get($result, 'lastAssistanceDate') ? data_get($result, 'lastAssistanceDate')->format('F d, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Validation Policy</td>
            <td>6-Month Waiting Period Policy</td>
        </tr>
        <tr>
            <td class="label">Next Eligible Date</td>
            <td><strong style="color: #15803d;">{{ data_get($result, 'eligibleAgainDate') ? data_get($result, 'eligibleAgainDate')->format('F d, Y') : 'N/A' }}</strong></td>
        </tr>
    </table>

    <div class="body-text">
        You may re-apply for assistance on or after your next eligible date. If you have any questions or require additional guidance regarding requirements, please feel free to visit our office or contact us.
    </div>

    <div class="body-text">
        Sincerely,
    </div>

    <table class="signatures">
        <tr>
            <td>
                <div class="signature-line">
                    <strong>{{ $officer_name }}</strong><br>
                    Social Worker Officer / Validator
                </div>
            </td>
            <td>
                <div class="signature-line">
                    <strong>MSWDO Supervisor</strong><br>
                    Approving Authority
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
