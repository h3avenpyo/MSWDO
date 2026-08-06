<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSWDO Login Code</title>
</head>
<body style="margin:0;padding:0;background-color:#F8FAFC;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F8FAFC;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background-color:#FFFFFF;border:1px solid #E2E8F0;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#1A237E;padding:24px 32px;text-align:center;">
                            <h1 style="margin:0;color:#FFFFFF;font-size:20px;">MSWDO Silang Portal</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;text-align:center;">
                            <p style="margin:0 0 16px;color:#475569;font-size:15px;line-height:1.6;">Use the code below to sign in to your account. This code expires in <strong>10 minutes</strong>.</p>
                            <div style="display:inline-block;background-color:#EEF2FF;border:2px dashed #1A237E;border-radius:12px;padding:16px 40px;margin:16px 0;">
                                <span style="font-size:34px;font-weight:800;letter-spacing:8px;color:#1A237E;">{{ $code }}</span>
                            </div>
                            <p style="margin:0;color:#94A3B8;font-size:13px;">If you did not request this code, you can safely ignore this email.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
