<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Case Report</title>
</head>
<body style="font-family:Arial,Helvetica,sans-serif; font-size:14px; line-height:1.4; color:#222; margin:0; padding:20px; background:#f7f7f7;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e2e2e2;border-collapse:collapse;">
        <tr>
            <td style="padding:18px 22px; border-bottom:1px solid #ddd;">
                <h2 style="margin:0;font-size:18px;letter-spacing:.5px;">Barangay SWD Case Report</h2>
                <p style="margin:6px 0 0 0;font-size:12px;color:#666;">Generated {{ now()->format('F j, Y \\a\\t g:i A') }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:18px 22px;">
                <p style="margin:0 0 12px 0;">
                    Below is a summary of the case. The full printable version is attached as an HTML file.
                </p>

                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:13px;">
                    <tr>
                        <td style="padding:6px 8px;font-weight:bold;width:34%;background:#f3f4f6;border:1px solid #ddd;">Case Number</td>
                        <td style="padding:6px 8px;border:1px solid #ddd;">{{ $case->case_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 8px;font-weight:bold;background:#f3f4f6;border:1px solid #ddd;">Barangay</td>
                        <td style="padding:6px 8px;border:1px solid #ddd;">{{ $barangayName }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 8px;font-weight:bold;background:#f3f4f6;border:1px solid #ddd;">Type of Abuse</td>
                        <td style="padding:6px 8px;border:1px solid #ddd;">{{ $case->abuse_type ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 8px;font-weight:bold;background:#f3f4f6;border:1px solid #ddd;">Status</td>
                        <td style="padding:6px 8px;border:1px solid #ddd;">{{ $case->status ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 8px;font-weight:bold;background:#f3f4f6;border:1px solid #ddd;">Victim</td>
                        <td style="padding:6px 8px;border:1px solid #ddd;">{{ $case->victim_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 8px;font-weight:bold;background:#f3f4f6;border:1px solid #ddd;">Incident Date</td>
                        <td style="padding:6px 8px;border:1px solid #ddd;">
                            {{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('F j, Y') : 'N/A' }}
                        </td>
                    </tr>
                </table>

                <p style="margin:18px 0 6px 0;font-weight:bold;">Incident Details</p>
                <p style="margin:0 0 18px 0; white-space:pre-wrap;">
                    {{ $case->incident_details ?? 'No details provided.' }}
                </p>

                <p style="margin:0;font-size:12px;color:#666;">
                    Attachment: Full printable case report (HTML).
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 22px;background:#f3f4f6;font-size:11px;color:#555;text-align:center;">
                This is an automated case report email.
            </td>
        </tr>
    </table>
</body>
</html>
