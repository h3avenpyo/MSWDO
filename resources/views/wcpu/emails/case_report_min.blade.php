<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Case Report {{ $number }}</title>
</head>
<body style="font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.45;color:#222;margin:0;padding:18px;background:#f5f7fa;">
    <div style="max-width:620px;margin:0 auto;background:#ffffff;border:1px solid #e2e6ea;border-radius:6px;padding:20px;">
        <h2 style="margin:0 0 10px;font-size:18px;font-weight:600;color:#2c3e50;">
            Case Report {{ $number }}
        </h2>
        <p style="margin:0 0 12px;">
            Please find attached the official PDF case report.
        </p>
        <table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:13px;margin:0 0 14px;">
            <tr>
                <td style="width:36%;padding:6px 8px;background:#f3f5f7;font-weight:600;border:1px solid #dde2e6;">Case Number</td>
                <td style="padding:6px 8px;border:1px solid #dde2e6;">{{ $number }}</td>
            </tr>
            <tr>
                <td style="padding:6px 8px;background:#f3f5f7;font-weight:600;border:1px solid #dde2e6;">Status</td>
                <td style="padding:6px 8px;border:1px solid #dde2e6;">{{ $status }}</td>
            </tr>
            <tr>
                <td style="padding:6px 8px;background:#f3f5f7;font-weight:600;border:1px solid #dde2e6;">Barangay</td>
                <td style="padding:6px 8px;border:1px solid #dde2e6;">{{ $barangayName ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding:6px 8px;background:#f3f5f7;font-weight:600;border:1px solid #dde2e6;">Generated</td>
                <td style="padding:6px 8px;border:1px solid #dde2e6;">{{ now()->format('F j, Y g:i A T') }}</td>
            </tr>
        </table>
        <p style="margin:0 0 10px;">
            If you have trouble opening the attachment, contact the system administrator.
        </p>
        <p style="font-size:11px;color:#666;margin:18px 0 0;">
            This is an automated message. Do not reply.
        </p>
    </div>
</body>
</html>
