<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password - iSERVE</title>
</head>
<body style="font-family: 'Montserrat', Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f5f5f5;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(106, 27, 154, 0.1);">
        <!-- Header -->
        <tr>
            <td style="background: #C41E3A; padding: 32px 30px; text-align: center;">
                <h1 style="margin: 0; color: white; font-size: 24px; font-weight: 700;">Reset Your Password</h1>
                <p style="margin: 8px 0 0 0; color: rgba(255, 255, 255, 0.9); font-size: 14px;">Women and Child Protection Unit</p>
            </td>
        </tr>
        
        <!-- Body -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="margin: 0 0 20px 0; font-size: 16px; color: #333;">Hello!</p>
                
                <p style="margin: 0 0 20px 0; color: #555;">
                    You are receiving this email because we received a password reset request for your account.
                </p>
                
                <!-- Button -->
                <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                    <tr>
                        <td style="text-align: center;">
                            <a href="{{ $resetUrl }}" 
                               style="display: inline-block; padding: 14px 40px; background: #C41E3A; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(196, 30, 58, 0.3);">
                                Reset Password
                            </a>
                        </td>
                    </tr>
                </table>
                
                <p style="margin: 20px 0; color: #555; font-size: 14px;">
                    This password reset link will expire in <strong>60 minutes</strong>.
                </p>
                
                <p style="margin: 20px 0; color: #555;">
                    If you did not request a password reset, no further action is required.
                </p>
                
                <!-- Alternative Link -->
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                    <p style="margin: 0 0 10px 0; color: #666; font-size: 13px;">
                        If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
                    </p>
                    <p style="margin: 0; word-break: break-all;">
                        <a href="{{ $resetUrl }}" style="color: #6a1b9a; text-decoration: none; font-size: 12px;">{{ $resetUrl }}</a>
                    </p>
                </div>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style="background: #f9f9f9; padding: 25px 30px; border-top: 1px solid #eee;">
                <p style="margin: 0 0 8px 0; color: #666; font-size: 13px; text-align: center;">
                    <strong>Women and Child Protection Unit</strong>
                </p>
                <p style="margin: 0; color: #999; font-size: 12px; text-align: center;">
                    This is an automated message. Please do not reply to this email.
                </p>
                <p style="margin: 12px 0 0 0; color: #999; font-size: 11px; text-align: center;">
                    © {{ date('Y') }} WCPU. All rights reserved.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
