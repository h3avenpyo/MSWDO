{{-- filepath: c:\Users\USER\WCPU_WEB\resources\views\emails\case-referral.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Case Referral Notification</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333333; background: #ffffff; margin: 0; padding: 0; }
        .header { background: #C41E3A; color: #ffffff; padding: 20px; text-align: center; }
        .header h2 { color: #ffffff; margin: 0 0 8px; }
        .header p { color: #ffffff; margin: 0; }
        .content { background: #ffffff; color: #333333; padding: 20px; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; color: #333333; }
        .footer { background: #ffffff; text-align: center; padding: 20px; color: #777777; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header" style="background-color: #C41E3A; color: #ffffff; padding: 20px; text-align: center;">
        <h2 style="color: #ffffff; margin: 0 0 8px;">Case Referral Notification</h2>
        <p style="color: #ffffff; margin: 0;">Municipal Social Welfare and Development Office</p>
    </div>
    <div class="content">
        <p>Dear {{ $referral->recipient_name }},</p>
        <p>A new case has been referred to you.</p>
        <div class="info-row"><span class="label">Referral Number:</span> {{ $referral->referral_number }}</div>
        <div class="info-row"><span class="label">Case Number:</span> {{ $referral->case->case_number ?? '' }}</div>
        <div class="info-row"><span class="label">Case Priority:</span> {{ $case->priority ?? 'Not specified' }}</div>
        <div class="info-row"><span class="label">Date of Referral:</span> {{ $referral->date_of_referral->format('F d, Y') }}</div>
        <div class="info-row"><span class="label">Client Name:</span> {{ $referral->client_name }}</div>
        <div class="info-row"><span class="label">Client Age:</span> {{ $referral->client_age }}</div>
        <div class="info-row"><span class="label">Reason for Referral:</span> {{ $referral->reason_for_referral }}</div>
        <div class="info-row"><span class="label">Services Requested:</span> {{ $referral->specific_services_requested }}</div>
        @if($referral->additional_notes)
        <div class="info-row"><span class="label">Additional Notes:</span> {{ $referral->additional_notes }}</div>
        @endif
        <div class="info-row"><span class="label">Referred By:</span> MSWDO SILANG (Administrator)</div>
    </div>
    <div class="footer">
        <p>This is an automated notification from the ISERVE SILANG Case Management System.</p>
        <p>Referral Number: {{ $referral->referral_number }}</p>
    </div>
</body>
</html>
