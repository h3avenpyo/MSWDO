<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset Request Update - iSERVE SILANG</title>
</head>
<body style="font-family: 'Montserrat', Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f5f5f5;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);">
        <tr>
            <td style="background: {{ $brandColor ?? '#b91c1c' }}; padding: 40px 30px; text-align: center;">
                <img src="{{ $message->embed(public_path('images/iserve_logo.jpg')) }}" alt="iServe Logo" width="80" height="80" style="display: block; margin: 0 auto 16px; border-radius: 50%; object-fit: cover;">
                <h1 style="margin: 0; color: white; font-size: 22px; font-weight: 700;">Password Reset Request Update</h1>
                <p style="margin: 8px 0 0 0; color: #ffffff; font-size: 14px;">Account: <span style="color: #ffffff; text-decoration: none;">{{ str_replace(['@', '.'], ["\u{200B}@\u{200B}", ".\u{200B}"], $requester) }}</span></p>
            </td>
        </tr>
        <tr>
            <td style="padding: 40px 30px;">
                <p style="margin: 0 0 20px 0; font-size: 16px; color: #333;">Hello {{ $user->first_name ?? $user->email }}!</p>
                <p style="margin: 0 0 20px 0; color: #555;">Your request to reset the password for your account has been reviewed by the administrator and has been <strong>rejected</strong>.</p>
                
                <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 16px 20px; border-radius: 6px; margin: 24px 0;">
                    <p style="margin: 0 0 6px 0; font-size: 12px; font-weight: 700; text-transform: uppercase; color: #991b1b; letter-spacing: 0.5px;">Reason for Rejection:</p>
                    <p style="margin: 0; color: #7f1d1d; font-size: 14px; white-space: pre-wrap;">{{ $reason }}</p>
                </div>

                <p style="margin: 20px 0; color: #555; font-size: 14px;">If you believe this was in error, please contact your department administrator or submit a new password reset request with valid details.</p>
            </td>
        </tr>
        <tr>
            <td style="background: #f9f9f9; padding: 25px 30px; border-top: 1px solid #eee;">
                <p style="margin: 0 0 8px 0; color: #666; font-size: 13px; text-align: center;"><strong>{{ $senderName ?? 'Women and Child Protection Unit' }}</strong></p>
                <p style="margin: 0; color: #999; font-size: 12px; text-align: center;">This is an automated message. Please do not reply directly to this email.</p>
            </td>
        </tr>
    </table>
</body>
</html>
