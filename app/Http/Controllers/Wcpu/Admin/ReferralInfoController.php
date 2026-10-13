<?php

namespace App\Http\Controllers\Wcpu\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wcpu\CaseReferral;
use App\Models\Wcpu\ReportRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReferralInfoController extends Controller
{
    public function index()
    {
        $recipients = ReportRecipient::orderBy('created_at', 'desc')->get();
        $recipients->each(function (ReportRecipient $recipient): void {
            $recipient->setAttribute('has_active_referrals', CaseReferral::where('recipient_email', $recipient->email)->exists());
        });
        
        return view('admin-tailwind.referral-info', compact('recipients'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:report_recipients,email',
            'role' => auth()->user()->isDepartmentAdmin()
                ? ['required', 'string', Rule::in(['BCPC - Staff', 'SWD - Staff', 'SWD - Focal', 'BCPC - Focal'])]
                : 'nullable|string|max:255',
            'contact_number' => 'nullable|digits:11',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (ReportRecipient::whereRaw('LOWER(name) = ?', [mb_strtolower(trim($request->name))])->exists()) {
            return response()->json(['success' => false, 'errors' => ['name' => ['This user already exists.']]], 422);
        }

        $recipient = ReportRecipient::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'contact_number' => $request->contact_number ?? null,
            'active' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Referral recipient added successfully',
            'recipient' => $recipient
        ]);
    }

    public function update(Request $request, $id)
    {
        $recipient = ReportRecipient::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:report_recipients,email,' . $id,
            'role' => auth()->user()->isDepartmentAdmin()
                ? ['nullable', 'string', Rule::in(['BCPC - Staff', 'SWD - Staff', 'SWD - Focal', 'BCPC - Focal'])]
                : 'nullable|string|max:255',
            'contact_number' => 'nullable|digits:11',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (ReportRecipient::where('id', '!=', $id)->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($request->name))])->exists()) {
            return response()->json(['success' => false, 'errors' => ['name' => ['This user already exists.']]], 422);
        }

        $recipient->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'contact_number' => $request->contact_number ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Referral recipient updated successfully',
            'recipient' => $recipient
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $recipient = ReportRecipient::findOrFail($id);
        if (auth()->user()->isDepartmentAdmin()) {
            app(\App\Services\Wcpu\ReferralArchiveService::class)->archive($recipient, $request->boolean('confirm_linked_referrals'));
        } else {
            $recipient->forceDelete();
        }

        return response()->json([
            'success' => true,
            'message' => auth()->user()->isDepartmentAdmin() ? 'Referral info archived successfully' : 'Referral recipient deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $recipient = ReportRecipient::findOrFail($id);
        $recipient->update(['active' => !$recipient->active]);

        return response()->json([
            'success' => true,
            'message' => 'Recipient status updated successfully',
            'recipient' => $recipient
        ]);
    }
}
