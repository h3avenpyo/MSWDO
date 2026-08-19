<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PasswordResetManagementController extends Controller
{
    public function index()
    {
        $requests = PasswordResetRequest::with('processedBy')
            ->orderByDesc('requested_at')
            ->get();

        return view('admin.password-reset-management', compact('requests'));
    }

    public function approve(Request $request, $id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);

        if ($resetRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        if ($resetRequest->isExpired()) {
            $resetRequest->status = 'rejected';
            $resetRequest->processed_at = now();
            $resetRequest->processed_by = session('admin_user_id');
            $resetRequest->notes = 'Expired';
            $resetRequest->save();

            return back()->with('error', 'This request has expired.');
        }

        $resetRequest->status = 'approved';
        $resetRequest->processed_at = now();
        $resetRequest->processed_by = session('admin_user_id');
        $resetRequest->notes = $request->notes;
        $resetRequest->save();

        // Send email with reset link (base it on APP_URL so the link matches
        // the current deployment: localhost for local dev, LAN IP for phone testing)
        $resetLink = rtrim(config('app.url'), '/') . route('admin.password.reset', $resetRequest->token, false);
        try {
            Mail::to($resetRequest->email)->send(new PasswordResetMail($resetLink, $resetRequest->email));
            return back()->with('success', 'Password reset request approved. Email with reset link has been sent to the user.');
        } catch (\Exception $e) {
            return back()->with('success', 'Password reset request approved. Email could not be sent, but the reset link is available below for manual delivery.');
        }
    }

    public function reject(Request $request, $id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);

        if ($resetRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $resetRequest->status = 'rejected';
        $resetRequest->processed_at = now();
        $resetRequest->processed_by = session('admin_user_id');
        $resetRequest->notes = $request->notes;
        $resetRequest->save();

        return back()->with('success', 'Password reset request rejected.');
    }

    public function delete($id)
    {
        $resetRequest = PasswordResetRequest::findOrFail($id);
        $resetRequest->delete();

        return back()->with('success', 'Password reset request deleted.');
    }
}
