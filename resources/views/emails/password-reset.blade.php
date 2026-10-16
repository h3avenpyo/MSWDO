<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - MSWDO Silang</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #1E3A8A; margin: 0; font-size: 24px;">MSWDO Silang</h1>
            <p style="color: #64748B; margin: 5px 0 0; font-size: 14px;">Municipal Social Welfare & Development Office</p>
        </div>

        <!-- Main Content -->
        <div style="margin-bottom: 30px;">
            <h2 style="color: #0F172A; margin: 0 0 15px; font-size: 20px;">Password Reset Request Approved</h2>
            <p style="color: #334155; line-height: 1.6; margin: 0 0 20px;">
                Your password reset request has been approved by an administrator. You can now reset your password by clicking the button below.
            </p>
            <p style="color: #334155; line-height: 1.6; margin: 0 0 20px;">
                This link will expire in 24 hours for security reasons.
            </p>
        </div>

        <!-- Reset Button -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetLink }}" style="display: inline-block; background-color: #1D4ED8; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                Reset My Password
            </a>
        </div>

        <!-- Alternative Link -->
        <div style="margin: 30px 0; padding: 15px; background-color: #F8FAFC; border-radius: 6px;">
            <p style="color: #64748B; font-size: 13px; margin: 0 0 8px;">
                If the button above doesn't work, you can copy and paste this link into your browser:
            </p>
            <p style="color: #1D4ED8; font-size: 12px; margin: 0; word-break: break-all;">
                {{ $resetLink }}
            </p>
        </div>

        <!-- Security Notice -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #E2E8F0;">
            <p style="color: #64748B; font-size: 13px; margin: 0; line-height: 1.5;">
                <strong>Security Notice:</strong> If you did not request a password reset, please ignore this email. Your account remains secure.
            </p>
        </div>

        <!-- Footer -->
        <div style="margin-top: 30px; text-align: center; color: #94A3B8; font-size: 12px;">
            <p style="margin: 0;">Municipality of Silang, Cavite</p>
            <p style="margin: 5px 0 0;">© {{ date('Y') }} MSWDO Silang. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
