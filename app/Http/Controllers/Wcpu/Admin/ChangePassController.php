<?php

namespace App\Http\Controllers\Wcpu\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Wcpu\User;
use Carbon\Carbon;

class ChangePassController extends Controller
{
    public function sendResetLink(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $email = $request->input('email');
        $token = Str::random(64);

        // Save token to password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Send email with token (simple text, customize as needed)
        Mail::raw("Your password reset token: $token", function ($message) use ($email) {
            $message->to($email)
                ->subject('Password Reset Token');
        });

        return response()->json(['success' => true, 'message' => 'Password reset token sent to your email.']);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password'
        ]);

        $user = User::find($request->id);

        // Check if new password is the same as old password
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'New password can\'t be the same as old password.'
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Re-authenticate the user to maintain session
        if (Auth::check() && Auth::id() == $user->id) {
            Auth::login($user);
        }

        return response()->json(['success' => true, 'message' => 'Password changed successfully.']);
    }

    public function checkPassword(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'password' => 'required|string'
        ]);

        $user = User::find($request->id);
        $isSame = false;
        // Hash::check compares the plain password with the hashed password in DB
        if ($user && Hash::check($request->password, $user->password)) {
            $isSame = true;
        }
        return response()->json(['valid' => $isSame]);
    }
}
