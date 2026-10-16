<?php

namespace App\Http\Controllers\Wcpu;

use App\Http\Controllers\Controller;
use App\Models\Wcpu\CaseReferral as Referral;
use App\Models\Wcpu\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReferralController extends Controller
{
    

    public function index()
    {
        $user = Auth::user();
        $barangay = Barangay::where('name', $user->barangay)->first();
        
        if (!$barangay) {
            return view('desk-officer.referrals.index', ['referrals' => collect()]);
        }

        // Show referrals for all cases in this barangay (shared among officers)
        $referrals = Referral::with(['referredBy'])
            ->whereHas('case', function($q) use ($barangay) {
                $q->where('case_designation', $barangay->id);
            })
            ->orderBy('date_of_referral', 'desc')
            ->get();
        return view('desk-officer.referrals.index', compact('referrals'));
    }

    public function details(Request $request, $id)
    {
        $user = Auth::user();
        $barangay = Barangay::where('name', $user->barangay)
            ->when($user->isMultiCaseDepartment(), fn ($q) => $q->orWhere('id', $user->barangay))->firstOrFail();
        
        // Allow viewing referral details for any case in the same barangay
        $referral = Referral::withTrashed()->with(['referredBy'])
            ->whereHas('case', function($q) use ($barangay, $user) {
                $q->where('case_designation', $barangay->id);
                if ($user->isMultiCaseDepartment()) {
                    $q->withTrashed()->whereIn('type', $user->caseDepartments());
                }
            })
            ->findOrFail($id);
        $isArchived = $request->boolean('archived');

        return view('desk-officer.partials.referral-details', compact('referral', 'isArchived'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $barangay = Barangay::where('name', $user->barangay)->first();

        // Allow editing referrals for any case in the same barangay.
        $referral = Referral::with(['case', 'referredBy'])
            ->whereHas('case', function($q) use ($barangay) {
                $q->where('case_designation', $barangay->id);
            })
            ->findOrFail($id);

        $usesRecipientDropdown = $user->position === 'Desk Officer'
            && ($user->isMultiCaseDepartment() || in_array($user->defaultCaseDepartment(), ['BCPC', 'VAWC'], true));
        if ($usesRecipientDropdown && $referral->caseHasDepartmentAdminReferral()) {
            abort(403, 'Editing is disabled after an Admin refers this case.');
        }
        if ($usesRecipientDropdown && filled($referral->feedback)) {
            abort(403, 'Referrals with feedback cannot be edited.');
        }
        $isMultiRoleDeskOfficer = $user->position === 'Desk Officer' && $user->isMultiCaseDepartment();
        $recipients = $usesRecipientDropdown ? \App\Models\Wcpu\ReportRecipient::active()->orderBy('name')->get() : collect();
        return view('desk-officer.partials.referral-edit', compact('referral', 'recipients', 'usesRecipientDropdown', 'isMultiRoleDeskOfficer'));
    }

    public function print($id)
    {
        $user = Auth::user();
        $barangay = Barangay::where('name', $user->barangay)->first();
        
        // Allow printing referrals for any case in the same barangay
        $referral = Referral::with(['referredBy'])
            ->whereHas('case', function($q) use ($barangay) {
                $q->where('case_designation', $barangay->id);
            })
            ->findOrFail($id);
        return view('desk-officer.referral-print', compact('referral'));
    }

    public function printAll(Request $request)
    {
        $user = Auth::user();
        $barangay = Barangay::where('name', $user->barangay)->orWhere('id', $user->barangay)->firstOrFail();
        $department = $request->input('type');
        if ($user->isMultiCaseDepartment()) {
            abort_unless(in_array($department, $user->caseDepartments(), true), 403);
        }
        
        // Print all referrals for cases in the same barangay
        $referrals = Referral::with(['case', 'referredBy'])
            ->whereHas('case', function($q) use ($barangay, $user, $department) {
                $q->where('case_designation', $barangay->id);
                if ($user->isMultiCaseDepartment()) $q->where('type', $department);
            })
            ->orderBy('date_of_referral', 'desc')
            ->get();
        
        return view('admin-tailwind.referrals.print-all', compact('referrals', 'department'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'client_contact' => ['nullable', new \App\Rules\ContactNumber(11)],
            'client_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'recipient_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'referred_by_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
        ], [
            '*.regex' => 'Names may contain only letters, spaces, hyphens, and apostrophes.',
        ]);

        try {
            Log::info('Referral update request received', [
                'id' => $id,
                'data' => $request->all()
            ]);
            
            $user = Auth::user();
            $barangay = Barangay::where('name', $user->barangay)->first();
            
            // Allow updating referrals for any case in the same barangay.
            $referral = Referral::whereHas('case', function($q) use ($barangay) {
                $q->where('case_designation', $barangay->id);
            })->findOrFail($id);

            if ($user->position === 'Desk Officer'
                && ($user->isMultiCaseDepartment() || in_array($user->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
                && $referral->caseHasDepartmentAdminReferral()) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Editing is disabled after an Admin refers this case.'], 403)
                    : back()->with('error', 'Editing is disabled after an Admin refers this case.');
            }

            if ($user->position === 'Desk Officer'
                && $user->defaultCaseDepartment() === 'BCPC'
                && $referral->trashed()) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Archived referrals cannot be edited.'], 403)
                    : back()->with('error', 'Archived referrals cannot be edited.');
            }

            if ($user->position === 'Desk Officer'
                && ($user->isMultiCaseDepartment() || in_array($user->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
                && filled($referral->feedback)) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Referrals with feedback cannot be edited.'], 403)
                    : back()->with('error', 'Referrals with feedback cannot be edited.');
            }

            if ($user->position === 'Desk Officer'
                && ($user->isMultiCaseDepartment() || in_array($user->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
                && !$request->filled('received_at')) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Date of feedback is required.'], 422)
                    : back()->with('error', 'Date of feedback is required.');
            }
            
            $referral->client_name = $request->input('client_name') ?: null;
            $referral->client_age = $request->input('client_age') ?: null;
            $referral->client_sex = $request->input('client_sex') ?: null;
            $referral->specific_services_requested = $request->input('specific_services_requested') ?: null;
            if ($user->position === 'Desk Officer'
                && ($user->isMultiCaseDepartment() || in_array($user->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))) {
                // An empty selection retains a previously saved, unavailable recipient.
                if ($request->filled('recipient_id')) {
                    $recipient = \App\Models\Wcpu\ReportRecipient::active()->findOrFail($request->input('recipient_id'));
                    $referral->recipient_name = $recipient->name;
                    $referral->recipient_email = $recipient->email;
                    $referral->recipient_contact = $recipient->contact_number;
                    $referral->recipient_address = 'N/A';
                }
            } else {
                $referral->recipient_name = $request->input('recipient_name') ?: null;
            }
            $referral->referred_by_name = $request->input('referred_by_name') ?: null;
            $referral->feedback_received_at = $request->input('received_at') ?: null;
            $referral->feedback = $request->input('action_taken') ?: null;
            $referral->additional_notes = $request->input('remarks') ?: null;
            $referral->status = $request->input('status') ?: null;
            
            $saved = $referral->save();
            
            Log::info('Referral save result', [
                'saved' => $saved,
                'referral' => $referral->toArray()
            ]);
            
            // Only return JSON if it's an AJAX request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true
                ]);
            }
            
            // Otherwise redirect
            return redirect()->route('desk-officer.referrals')->with('success', 'Referral updated successfully');
            
        } catch (\Exception $e) {
            Log::error('Referral update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
