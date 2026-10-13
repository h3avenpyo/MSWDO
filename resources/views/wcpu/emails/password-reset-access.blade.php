<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset Access - iSERVE</title>
</head>
<body style="font-family: 'Montserrat', Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f5f5f5;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(196, 30, 58, 0.1);">
        <tr>
            <td style="background: #C41E3A; padding: 40px 30px; text-align: center;">
                <img src="{{ $message->embed(public_path('images/iserve_logo.jpg')) }}" alt="iServe Logo" width="80" height="80" style="display: block; margin: 0 auto 16px; border-radius: 50%; object-fit: cover;">
                <h1 style="margin: 0; color: white; font-size: 24px; font-weight: 700;">Password Reset Ready</h1>
                <p style="margin: 8px 0 0 0; color: #ffffff; font-size: 14px;">Requested by: <span style="color: #ffffff; text-decoration: none;">{{ str_replace(['@', '.'], ["\u{200B}@\u{200B}", ".\u{200B}"], $requester) }}</span></p>
            </td>
        </tr>
        <tr>
            <td style="padding: 40px 30px;">
                <p style="margin: 0 0 20px 0; font-size: 16px; color: #333;">Hello {{ $user->first_name ?? $user->email }}!</p>
                <p style="margin: 0 0 20px 0; color: #555;">Your password reset request has been approved. A one-time access link is ready for you to open your account and view the reset notice.</p>
                <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                    <tr>
                        <td style="text-align: center;">
                            <a href="{{ $accessUrl }}" style="display: inline-block; padding: 14px 40px; background: #C41E3A; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(196, 30, 58, 0.3);">Open Access Link</a>
                        </td>
                    </tr>
                </table>
                <p style="margin: 20px 0; color: #555; font-size: 14px;">This link will expire in <strong>24 hours</strong>.</p>
                <p style="margin: 20px 0; color: #555;">If you did not expect this message, please contact the administrator.</p>
            </td>
        </tr>
        <tr>
            <td style="background: #f9f9f9; padding: 25px 30px; border-top: 1px solid #eee;">
                <p style="margin: 0 0 8px 0; color: #666; font-size: 13px; text-align: center;"><strong>iSERVE SILANG</strong></p>
                <p style="margin: 0; color: #999; font-size: 12px; text-align: center;">This is an automated message. Please do not reply to this email.</p>
            </td>
        </tr>
    </table>
</body>
</html>
