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
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $perPage = $request->get('per_page', 5);

        $query = PasswordResetRequest::with('processedBy', 'user')
            ->orderByDesc('requested_at');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status && $status !== 'All Status') {
            $query->where('status', $status);
        }

        $requests = $query->paginate($perPage);

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

        // Send email with reset link. Prefer the URL the user requested from
        // (e.g. the phone's LAN host http://192.168.1.5:8000) so the link is
        // reachable from their device; fall back to APP_URL.
        $baseUrl = $resetRequest->requested_from_url ?: rtrim(config('app.url'), '/');
        $resetLink = $baseUrl . route('admin.password.reset', $resetRequest->token, false);
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
