<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OfficerVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OfficerEmailVerificationController extends Controller
{
    public const REQUIRED_MESSAGE = 'Please verify your email address before creating an account.';

    public function send(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $previous = $request->session()->get('officer_email_verification');
        if ($previous && ($previous['sent_at'] ?? 0) > now()->timestamp - 60) {
            return response()->json(['message' => 'Please wait 60 seconds before requesting another code.'], 429);
        }

        $code = (string) random_int(100000, 999999);
        // A resend invalidates the previous code, including when delivery fails.
        $request->session()->forget('officer_email_verification');
        try {
            Mail::to($data['email'])->send(new OfficerVerificationMail($code));
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'Unable to send the verification email. Please try again.'], 503);
        }

        $request->session()->put('officer_email_verification', [
            'email' => strtolower($data['email']),
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(10)->timestamp,
            'sent_at' => now()->timestamp,
            'attempts' => 0,
        ]);

        return response()->json([
            'message' => 'Verification code sent. Ask the officer for the code from their email. It expires in 10 minutes.',
            'expires_at' => $request->session()->get('officer_email_verification.expires_at'),
        ]);
    }

    public static function validateCode(Request $request): void
    {
        $record = $request->session()->get('officer_email_verification');
        if (! $record || $record['email'] !== strtolower($request->email)
            || $record['expires_at'] <= now()->timestamp || $record['attempts'] >= 5) {
            throw ValidationException::withMessages([
                'email_verification_code' => self::REQUIRED_MESSAGE,
            ]);
        }

        $record['attempts']++;
        $request->session()->put('officer_email_verification', $record);
        if (! Hash::check((string) $request->email_verification_code, $record['code'])) {
            throw ValidationException::withMessages([
                'email_verification_code' => self::REQUIRED_MESSAGE,
            ]);
        }
    }
}
