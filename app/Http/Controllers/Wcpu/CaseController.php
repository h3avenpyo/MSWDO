<?php

namespace App\Http\Controllers\Wcpu;

use App\Http\Controllers\Controller;
use App\Models\Wcpu\CaseModel;
use App\Models\Wcpu\Barangay;
use App\Models\Wcpu\CaseReferral;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\Wcpu\CaseReferralNotification;
use Illuminate\Support\Facades\Mail;

/**
 * Class CaseController
 * @package App\Http\Controllers
 */
class CaseController extends Controller
{
    /**
     * Display a listing of cases for the dashboard
     */
    public function index()
    {
        $cases = CaseModel::with('barangay')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Force display in Asia/Manila (PST)
        $cases->getCollection()->transform(function ($case) {
            if ($case->created_at) {
                $case->created_at = $case->created_at->timezone('Asia/Manila');
            }
            if ($case->updated_at) {
                $case->updated_at = $case->updated_at->timezone('Asia/Manila');
            }
            return $case;
        });
            
        return view('admin.admin_dashboard', compact('cases'));
    }

    /**
     * Show the form for creating a new case
     */
    public function create()
    {
        // Debug: Log that we're entering the create method
        \Log::info('Entering CaseController@create');
        
        // Get and log the barangays
        $barangays = Barangay::orderBy('name')->get();
        $caseTypes = \App\Models\Wcpu\CaseType::orderBy('name')->get();
        $caseDepartment = Auth::user()?->defaultCaseDepartment() ?? 'VAWC';
        
        \Log::info('Barangays count: ' . $barangays->count());
        \Log::info('Case types count: ' . $caseTypes->count());
        
        // Debug: Check if view exists
        if (!\View::exists('admin-tailwind.add-new-case')) {
            \Log::error('View not found: admin-tailwind.add-new-case');
            abort(500, 'View file not found');
        }
        
        return view('admin-tailwind.add-new-case', compact('barangays', 'caseTypes', 'caseDepartment'));
    }

    /**
     * Store a newly created case in storage
     */
    public function store(Request $request)
    {
        \Log::info('Form submission received', $request->all());
        
        DB::connection('wcpu')->beginTransaction();

        try {
            // Log available case types
            if (Schema::connection('wcpu')->hasTable('case_types')) {
                $caseTypes = DB::connection('wcpu')->table('case_types')->get();
                \Log::debug('Available case types:', [
                    'count' => $caseTypes->count(),
                    'types' => $caseTypes->toArray()
                ]);
            }
            
            // Log the incoming request data
            \Log::debug('Incoming case creation request', [
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
                'ip' => $request->ip()
            ]);
            $caseDepartment = Auth::user()?->defaultCaseDepartment() ?? 'VAWC';
            
            $victimAgeRule = $caseDepartment === 'BCPC'
                ? 'required|integer|min:0|max:17'
                : 'required|integer|min:1|max:120';
            $abuserAgeRule = $caseDepartment === 'BCPC'
                ? 'required|integer|min:1|max:120'
                : 'nullable|integer|min:1|max:120';
            
            // Validate the request data (added abuse_relationship; relationship_to_victim kept nullable)
            $validated = $request->validate([
                'case_designation' => 'required|exists:wcpu.barangays,id',
                'incident_date' => 'required|date',
                'incident_time' => 'required',
                'date_reported' => 'required|date',
                'victim_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                'victim_age' => $victimAgeRule,
                'victim_gender' => 'required|string|in:Female,Male,LGBTQIA+,Prefer not to say',
                'victim_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
                'victim_address' => 'required|string',
                'guardian' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                'guardian_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
                'guardian_address' => 'nullable|string',
                'relationship_to_victim' => 'nullable|string|max:255', // guardian relation
                'perpetrator_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                'abuser_address' => 'required|string|max:2000',
                'abuser_age' => $abuserAgeRule,
                'abuse_relationship' => 'required|string|max:255', // abuser relation (required)
                // Guardian of Abuser fields
                'guardian_abuser_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                'guardian_abuser_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
                'guardian_abuser_address' => 'nullable|string|max:255',
                'relationship_to_abuser' => 'nullable|string|max:255',
                'abuse_type' => 'required|string|max:255',
                'other_abuse_details' => 'required_if:abuse_type,Others|nullable|string|max:1000',
                'incident_location' => 'required|string',
                'incident_details' => 'required|string',
                'witness_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                'witness_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
                'priority' => 'required|in:Urgent,Emergency,Non-urgent',
                'status' => 'required|in:Case Filed,Case closed,Case Closed,Case Referred',
                // documents validation removed
            ]);

            // Generate case number using the account's case department.
            $caseData = $this->generateCaseNumberData($validated['case_designation'], $caseDepartment);
            
            if (!$caseData['success']) {
                throw new \Exception('Failed to generate case number: ' . ($caseData['message'] ?? 'Unknown error'));
            }
            
            $caseNumber = $caseData['case_number'];

            // Create the case (include both abuse_relationship and relationship_to_victim)
            $case = CaseModel::create([
                'type' => $caseDepartment,
                'case_number' => $caseNumber,
                'case_designation' => $validated['case_designation'],
                'incident_date' => $validated['incident_date'],
                'incident_time' => $validated['incident_time'],
                'date_reported' => $validated['date_reported'],
                'abuse_type' => $validated['abuse_type'],
                'victim_name' => $validated['victim_name'],
                'victim_age' => $validated['victim_age'],
                'victim_gender' => $validated['victim_gender'],
                'victim_contact' => $validated['victim_contact'] ?? null,
                'victim_address' => $validated['victim_address'],
                // Guardian of Victim fields
                'guardian' => $validated['guardian'] ?? null,
                'guardian_contact' => $validated['guardian_contact'] ?? null,
                'guardian_address' => $validated['guardian_address'] ?? null,
                'relationship_to_victim' => $validated['relationship_to_victim'] ?? null,
                // Abuser fields
                'abuser_name' => $validated['perpetrator_name'],
                'abuser_address' => $validated['abuser_address'] ?? null,
                'abuser_age' => $validated['abuser_age'] ?? null,
                'abuse_relationship' => $validated['abuse_relationship'] ?? '',
                // Guardian of Abuser fields
                'guardian_abuser_name' => $validated['guardian_abuser_name'] ?? null,
                'guardian_abuser_contact' => $validated['guardian_abuser_contact'] ?? null,
                'guardian_abuser_address' => $validated['guardian_abuser_address'] ?? null,
                'relationship_to_abuser' => $validated['relationship_to_abuser'] ?? null,
                // Incident fields
                'incident_location' => $validated['incident_location'],
                'incident_details' => $validated['abuse_type'] === 'Others'
                    ? "Specific Type of Abuse (Others): " . trim($validated['other_abuse_details']) . "\n\n" . $validated['incident_details']
                    : $validated['incident_details'],
                'witness_name' => $validated['witness_name'] ?? null,
                'witness_contact' => $validated['witness_contact'] ?? null,
                'status' => $validated['status'],
                'priority' => $validated['priority'],
                'assigned_to' => auth()->id() ?? 1,
                'created_by' => auth()->id() ?? 1,
                'case_details' => $validated['abuse_type'] === 'Others'
                    ? "Specific Type of Abuse (Others): " . trim($validated['other_abuse_details']) . "\n\n" . $validated['incident_details']
                    : $validated['incident_details'],
                // Force PST timestamps
                'created_at' => Carbon::now('Asia/Manila'),
                'updated_at' => Carbon::now('Asia/Manila'),
            ]);

            DB::connection('wcpu')->commit();
            
            // Log the successful case creation (removed documents from log)
            \Log::info('Case created successfully', [
                'case_id' => $case->id,
                'case_number' => $case->case_number,
                'user_id' => auth()->id(),
            ]);
            
            // Log the activity (removed documents property)
            activity()
                ->causedBy(auth()->user())
                ->performedOn($case)
                ->withProperties([
                    'case_number' => $case->case_number, 
                    'victim_name' => $case->victim_name,
                ])
                ->log('created case ' . $case->case_number);

            return response()->json([
                'success' => true,
                'message' => 'Case created successfully. Case Number: ' . $case->case_number,
                'case_id' => $case->id,
                'barangay_id' => $case->case_designation,
                'redirect' => route('cases.show', ['id' => $case->id], false),
                'case_url' => route('cases.show', ['id' => $case->id], false),
            ]);

        } catch (\Exception $e) {
            DB::connection('wcpu')->rollBack();
            
            $errorContext = [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token']),
                'user_id' => auth()->id(),
                // removed documents_input / other_documents from context
            ];
            
            \Log::error('Error creating case', $errorContext);
            
            $errorMessage = config('app.debug') 
                ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()
                : 'An error occurred while creating the case. Please try again.';
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'debug' => config('app.debug') ? $errorContext : null
            ], 500);
        }
    }

    /**
     * Display the specified case
     *
     * @param  int  $id
    * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $case = CaseModel::withTrashed()->with(['barangay', 'caseType', 'createdBy', 'assignedTo'])->findOrFail($id);
        // Normalize to PST for display
        if ($case->created_at) $case->created_at = $case->created_at->timezone('Asia/Manila');
        if ($case->updated_at) $case->updated_at = $case->updated_at->timezone('Asia/Manila');
        // If request is AJAX, return the modal partial HTML (for inline modals)
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $html = view('admin-tailwind._case-details-modal', compact('case'))->render();
            return response($html, 200)->header('Content-Type', 'text/html');
        }
        
        // Otherwise render the full case view page
        return view('admin-tailwind.view-case', compact('case'));
    }

    /**
     * Show the form for editing the specified case
     */
    public function edit(Request $request, $id)
    {
        $case = CaseModel::with('barangay')->findOrFail($id);
        $isSwdDeskOfficer = auth()->user()?->position === 'Desk Officer'
            && auth()->user()?->defaultCaseDepartment() === 'VAWC';
        if ($isSwdDeskOfficer && $case->status !== 'Case Filed') {
            abort(403, 'Only cases marked Case Filed can be edited.');
        }
        $isClosed = preg_match('/closed|complet/i', trim((string) $case->status));
        $isReferred = $case->hasBeenReferred();
        if ($isClosed) {
            abort(403, 'This case is already closed and can no longer be edited.');
        }
        if (auth()->user()?->position !== 'Desk Officer' && $isReferred) {
            abort(403, 'This case cannot be edited after it has been referred.');
        }
        if ($case->created_at) $case->created_at = $case->created_at->timezone('Asia/Manila');
        if ($case->updated_at) $case->updated_at = $case->updated_at->timezone('Asia/Manila');
        $barangays = Barangay::orderBy('name')->get();
        $caseTypes = \App\Models\Wcpu\CaseType::orderBy('name')->get();

        // Load active report recipients so the edit modal can populate recipient select
        $recipients = \App\Models\Wcpu\ReportRecipient::active()
            ->select('id','name','email')
            ->orderBy('name')
            ->get();
        
        // If AJAX request (for modal), return only the form content
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $html = view('admin-tailwind._edit-form', compact('case', 'barangays', 'caseTypes', 'recipients'))->render();
            return response($html, 200)->header('Content-Type', 'text/html');
        }
        
        return view('admin-tailwind.view-case', compact('case', 'barangays', 'caseTypes', 'recipients'));
    }

    /**
     * Update the specified case in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Load case early
            $case = \App\Models\Wcpu\CaseModel::findOrFail($id);

            $isSwdDeskOfficer = auth()->user()?->position === 'Desk Officer'
                && auth()->user()?->defaultCaseDepartment() === 'VAWC';
            if ($isSwdDeskOfficer && $case->status !== 'Case Filed') {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Only cases marked Case Filed can be edited.'], 403);
                }
                return redirect()->back()->with('error', 'Only cases marked Case Filed can be edited.');
            }

            // Prevent editing closed cases (for all) or referred cases (for non-desk officers)
            $isClosed = preg_match('/closed|complet/i', trim((string) $case->status));
            $isReferred = $case->hasBeenReferred();
            if ($isClosed) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'This case is already closed and can no longer be edited.'], 403);
                }
                return redirect()->back()->with('error', 'This case is already closed and can no longer be edited.');
            }
            if (auth()->user()?->position !== 'Desk Officer' && $isReferred) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'This case cannot be edited.'], 403);
                }
                return redirect()->back()->with('error', 'This case cannot be edited.');
            }

            $previousStatus = $case->status ?? null;

            // Validation (align with DeskOfficerController rules)
            $validated = $request->validate([
                'victim_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                'victim_age' => 'required|integer|min:1|max:120',
                'victim_gender' => 'required|string|in:Female,Male,LGBTQIA+,Prefer not to say',
                'victim_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
                'victim_address' => 'required|string',
                'guardian' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                'guardian_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
                'guardian_address' => 'nullable|string',
                'relationship_to_victim' => 'nullable|string|max:255',
                'perpetrator_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                'abuser_address' => 'required|string|max:2000',
                'abuser_age' => 'nullable|integer|min:1|max:120',
                'abuse_relationship' => 'nullable|string|max:255',
                'guardian_abuser_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                'abuse_type' => 'required|string|max:255',
                'guardian_abuser_contact' => ['nullable', new \App\Rules\ContactNumber(11)],
                'client_contact' => ['nullable', new \App\Rules\ContactNumber(11)],
                'other_abuse_details' => 'required_if:abuse_type,Others|nullable|string|max:1000',
                'incident_date' => 'required|date',
                'incident_time' => 'required',
                'date_reported' => 'required|date',
                'incident_location' => 'required|string',
                'incident_details' => 'required|string',
                'witness_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                'witness_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
                'priority' => 'required|string|in:Urgent,Emergency,Non-urgent',
                'status' => 'required|string|in:Case Filed,Case closed,Case Closed,Case Referred,Complete case,Complete Case',
            ]);

            // Explicit assignments to avoid mass-assignment issues
            $case->victim_name = $validated['victim_name'];
            $case->victim_age = $validated['victim_age'];
            $case->victim_gender = $validated['victim_gender'];
            $case->victim_contact = $validated['victim_contact'] ?? null;
            $case->victim_address = $validated['victim_address'];

            $case->abuser_name = $validated['perpetrator_name'];
            $case->abuser_address = $validated['abuser_address'] ?? $case->abuser_address;
            $case->abuser_age = $validated['abuser_age'] ?? null;
            $case->abuse_relationship = $validated['abuse_relationship'] ?? $case->abuse_relationship;

            if (isset($validated['relationship_to_victim'])) {
                $case->relationship_to_victim = $validated['relationship_to_victim'];
            }

            $case->abuse_type = $validated['abuse_type'];
            $case->incident_date = $validated['incident_date'];
            $case->incident_time = $validated['incident_time'];
            $case->date_reported = $validated['date_reported'];
            $case->incident_location = $validated['incident_location'];
            $case->incident_details = $validated['abuse_type'] === 'Others'
                ? "Specific Type of Abuse (Others): " . trim($validated['other_abuse_details']) . "\n\n" . $validated['incident_details']
                : $validated['incident_details'];
            $case->witness_name = $validated['witness_name'] ?? null;
            $case->witness_contact = $validated['witness_contact'] ?? null;
            $case->priority = $validated['priority'];
            $case->status = $validated['status'];

            // Keep the View Cases "Last Updated" value current for every Admin or Desk Officer update.
            if (auth()->user()?->isDepartmentAdmin() || auth()->user()?->position === 'Desk Officer') {
                $case->updated_at = Carbon::now('Asia/Manila');
            }

            $case->save();

            // If status changed to "Case Referred", insert referral record
            if (($case->status ?? '') === 'Case Referred' && $previousStatus !== 'Case Referred') {
                try {
                    $referralNumber = \App\Models\Wcpu\CaseReferral::generateReferralNumber();

                    $user = auth()->user();
                    $referredByName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'Unknown';
                    $referredByDesignation = $user->position ?? 'Admin';
                    $referredByAddress = $user->barangay ? "WCPU Office, {$user->barangay}" : ($user->address ?? 'N/A');
                    $referredByContact = $user->phone_number ?? null;
                    $referredByEmail = $user->email ?? null;

                    // resolve selected recipient (if posted)
                    $recipientName = 'Not specified';
                    $recipientEmail = null;
                    $recipientContact = null;
                    $recipientAddress = 'N/A';
                    $recipientId = $request->input('recipient_id');
                    if ($recipientId) {
                        $recipient = \App\Models\Wcpu\ReportRecipient::find($recipientId);
                        if ($recipient) {
                            $recipientName = $recipient->name;
                            $recipientEmail = $recipient->email ?? null;
                            $recipientContact = $recipient->getAttribute('contact') ?? $recipient->getAttribute('phone') ?? null;
                            $recipientAddress = $recipient->getAttribute('address') ?? $recipient->getAttribute('office_address') ?? 'N/A';
                        }
                    }

                    // prefer client_* from request (frontend may post them), otherwise fall back to case victim_* fields
                    $clientName = $request->input('client_name') ?? $case->victim_name ?? null;
                    $clientAge = $request->input('client_age') ?? $case->victim_age ?? null;
                    $clientSex = $request->input('client_sex') ?? $case->victim_gender ?? null;
                    $clientAddress = $request->input('client_address') ?? $case->victim_address ?? null;
                    $clientContact = $request->input('client_contact') ?? $case->victim_contact ?? null;

                    \DB::connection('wcpu')->table('case_referrals')->insert([
                        'referral_number' => $referralNumber,
                        'case_id' => $case->id,
                        'referred_by_user_id' => $user->id ?? null,
                        'created_by_department_admin' => $user?->isDepartmentAdmin() ?? false,
                        'referred_by_name' => $referredByName,
                        'referred_by_designation' => $referredByDesignation,
                        'referred_by_address' => $referredByAddress,
                        'referred_by_contact' => $referredByContact,
                        'referred_by_email' => $referredByEmail,
                        'recipient_name' => $recipientName,
                        'recipient_email' => $recipientEmail,
                        'recipient_contact' => $recipientContact,
                        'recipient_address' => $recipientAddress,
                        'client_name' => $clientName,
                        'client_age' => $clientAge,
                        'client_sex' => $clientSex,
                        'client_address' => $clientAddress ?: 'N/A',
                        'client_contact' => $clientContact,
                        'reason_for_referral' => $request->input('reason_for_referral') ?: ($case->incident_details ?: 'Case referred for assistance.'),
                        'specific_services_requested' => $request->input('specific_services_requested') ?: 'Not specified',
                        'status' => 'Pending',
                        'date_of_referral' => Carbon::now()->toDateString(),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    \Log::info('Admin inserted case_referrals record', ['case_id' => $case->id, 'referral_number' => $referralNumber]);
                } catch (\Exception $e) {
                    \Log::error('Admin failed to insert into case_referrals: ' . $e->getMessage(), [
                        'case_id' => $case->id,
                        'referral_number' => $referralNumber ?? null,
                    ]);
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                $isDeskOfficer = auth()->user()?->position === 'Desk Officer';
                $selectedDepartment = strtolower((string) $request->input('type'));
                $returnUrl = ($isDeskOfficer || auth()->user()->isVawcAdmin())
                    ? route('admin.barangay-cases.show', array_filter([
                        'barangay' => $case->case_designation,
                        'type' => in_array($selectedDepartment, ['bcpc', 'vawc'], true) ? $selectedDepartment : null,
                    ]))
                    : null;
                if ($isDeskOfficer && auth()->user()->isMultiCaseDepartment()) {
                    $returnUrl = route('desk-officer.cases.index', ['type' => strtolower($case->type)]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Case updated successfully.',
                    'redirect_url' => $returnUrl,
                ]);
            }
            if (auth()->user()->isVawcAdmin()) {
                return redirect()->route('admin.barangay-cases.show', $case->case_designation)
                    ->with('success', 'Case updated successfully.');
            }
            return redirect()->back()->with('success', 'Case updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $ve) {
            throw $ve;
        } catch (\Exception $e) {
            \Log::error('Admin CaseController@update error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update case: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified case from storage (soft delete)
     */
    public function destroy($id)
    {
        $case = CaseModel::findOrFail($id);
        $user = Auth::user();
        abort_unless($user->canAccessCaseDepartment($case->type), 403);
        if (!$user->isDepartmentAdmin() && strtolower($user->position) !== 'administrator') {
            $barangay = Barangay::where('name', $user->barangay)->orWhere('id', $user->barangay)->first();
            abort_unless($barangay && $case->case_designation == $barangay->id, 403);
        }
        try {
            $case->delete();

            if (!request()->expectsJson() && !request()->ajax() && !request()->wantsJson()) {
                return back()->with('success', 'Case archived successfully.');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Case archived successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting case: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restoreArchivedCase($id)
    {
        $case = CaseModel::onlyTrashed()->findOrFail($id);
        abort_unless(Auth::user()->canAccessCaseDepartment($case->type), 403);
        $case->restore();

        if (Auth::user()->isDepartmentAdmin() && request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Barangay case restored successfully.',
            ]);
        }

        if (Auth::user()->isVawcAdmin()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Barangay case restored successfully.',
                ]);
            }

            return redirect()->route('admin.quarterly-reports.archived')
                ->with('success', 'Barangay case restored successfully.');
        }

        return redirect()->route('admin.quarterly-reports.archived', ['section' => 'barangay-cases'])
            ->with('success', 'Barangay case restored successfully.');
    }

    public function forceDelete($id)
    {
        $case = CaseModel::onlyTrashed()->findOrFail($id);
        abort_unless(Auth::user()->canAccessCaseDepartment($case->type), 403);
        $case->forceDelete();

        return back()->with('success', 'Archived barangay case deleted successfully.');
    }

    /**
     * Generate a case number for the specified barangay (internal use)
     * 
     * @param int $barangayId The ID of the barangay
     * @param string $caseType The type of case (BCPC or VAWC)
     * @return array
     */
    protected function generateCaseNumberData($barangayId, $caseType = 'VAWC')
    {
        $isSwdCase = strtoupper($caseType) === 'VAWC'
            && (auth()->user()?->isVawcAdmin()
                || (auth()->user()?->position === 'Desk Officer'
                    && (auth()->user()?->isMultiCaseDepartment() || auth()->user()?->defaultCaseDepartment() === 'VAWC')));
        if ($isSwdCase) {
            $caseType = 'SWD';
        }
        try {
            \Log::info('Generating case number for barangay ID: ' . $barangayId);
            
            // Get the barangay
            $barangay = Barangay::find($barangayId);
            \Log::info('Barangay found: ' . ($barangay ? 'Yes' : 'No'));
            
            if (!$barangay) {
                throw new \Exception('Barangay not found with ID: ' . $barangayId);
            }
            
            // Get the latest case number for this barangay and type
            $latestCase = CaseModel::where('case_number', 'LIKE', strtoupper($caseType) . '-' . $barangay->code . '-%')
                ->orderBy('created_at', 'desc')
                ->first();
                
            $sequenceNumber = 1;
            
            if ($latestCase) {
                // Extract the sequence number from the latest case number
                $parts = explode('-', $latestCase->case_number);
                if (count($parts) >= 4) {
                    $lastSequence = (int)end($parts);
                    $sequenceNumber = $lastSequence + 1;
                }
            }
            
            // Format the date as ddmmyyyy
            $date = now()->format('mdy');
            
            // Generate the case number in format: {TYPE}-{BARANGAY_CODE}-{DATE}-{SEQUENCE}
            $caseNumber = strtoupper($caseType) . '-' . $barangay->code . '-' . $date . '-' . str_pad($sequenceNumber, 3, '0', STR_PAD_LEFT);
            while (CaseModel::withTrashed()->where('case_number', $caseNumber)->exists()) {
                $sequenceNumber++;
                $caseNumber = strtoupper($caseType) . '-' . $barangay->code . '-' . $date . '-' . str_pad($sequenceNumber, 3, '0', STR_PAD_LEFT);
            }
            \Log::info('Generated case number: ' . $caseNumber);
            
            return [
                'success' => true,
                'case_number' => $caseNumber,
                'case_count' => $sequenceNumber
            ];
            
        } catch (\Exception $e) {
            $errorMsg = 'Error generating case number: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
            \Log::error($errorMsg);
            \Log::error($e->getTraceAsString());
            
            return [
                'success' => false,
                'message' => $errorMsg
            ];
        }
    }
    
    /**
     * API endpoint to generate a case number for the specified barangay
     * 
     * @param int $barangayId The ID of the barangay
     * @param string $caseType The type of case (BCPC or VAWC)
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateCaseNumber($barangayId, $caseType = 'VAWC')
    {
        $result = $this->generateCaseNumberData($barangayId, $caseType);
        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Display the case report as an editable print preview using the desk officer layout.
     */
    public function print(Request $request, $id)
    {
        $caseQuery = CaseModel::with(['barangay','assignedTo','createdBy']);
        if (auth()->user()->isVawcAdmin()) {
            $caseQuery->withTrashed();
        }
        $case = $caseQuery->findOrFail($id);

        // Keep the print view in HTML so temporary edits can be printed from the page.
        $printView = auth()->user()?->isBcpcAdmin()
            ? 'desk-officer.case-print-bcpc-admin'
            : (auth()->user()?->isVawcAdmin()
                ? 'desk-officer.case-print-swd-admin'
                : 'desk-officer.case-print');

        return view($printView, compact('case'));
    }

    /**
     * Refer case to selected recipients via email with PDF attachment.
     */
    public function referCase(Request $request, $id)
    {
        $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'email'
        ]);

        $case = CaseModel::with(['barangay'])->findOrFail($id);
        if ($case->hasBeenReferred()) {
            return response()->json([
                'success' => false,
                'message' => 'This case has already been referred.',
            ], 409);
        }
        $recipients = $request->input('recipients');

        // Resolve barangay name
        $barangayName = 'N/A';
        if ($case->barangay && is_object($case->barangay) && isset($case->barangay->name)) {
            $barangayName = $case->barangay->name;
        } elseif (!empty($case->case_designation)) {
            $raw = trim((string)$case->case_designation);
            if (ctype_digit($raw)) {
                $b = \App\Models\Wcpu\Barangay::select('id','name')->find((int)$raw);
                if ($b && $b->name !== '') $barangayName = $b->name;
            } else {
                $barangayName = $raw;
            }
        }

        try {
            $createdAtPst = $case->created_at?->timezone('Asia/Manila');

            // Generate PDF from the same Blade used by print (desk-officer)
            $pdfBytes = Pdf::loadView('desk-officer.case-print', ['case' => $case])
                           ->setPaper('a4', 'portrait')
                           ->output();

            $pdfFilename = 'Case-' . ($case->case_number ?? 'report') . '.pdf';

            // Send email to each recipient
            foreach ($recipients as $email) {
                Mail::send([], [], function ($message) use ($email, $case, $createdAtPst, $barangayName, $pdfBytes, $pdfFilename) {
                    $body = "You have been referred to a case.\n\n"
                        . "Case Number: {$case->case_number}\n"
                        . "Status: {$case->status}\n"
                        . "Date Filed: " . ($createdAtPst?->format('F j, Y g:i A') ?? 'N/A') . "\n"
                        . "Type of Abuse: {$case->abuse_type}\n"
                        . "Barangay: {$barangayName}\n\n"
                        . "A PDF copy is attached for your review.\n\n"
                        . "This is an automated message. Do not reply.";

                    $htmlBody = nl2br(e($body));

                    $message->to($email)
                        ->subject('Case Referral: ' . $case->case_number)
                        ->html($htmlBody)
                        ->text($body);

                    if ($pdfBytes) {
                        $message->attachData($pdfBytes, $pdfFilename, [
                            'mime' => 'application/pdf'
                        ]);
                    }
                });
            }

            // Update case status to 'Case Referred'
            $oldStatus = $case->status;
            $case->status = 'Case Referred';
            $case->updated_at = Carbon::now('Asia/Manila');
            $case->save();

            // Log activity (wrap in try-catch to not fail the whole operation)
            try {
                if (function_exists('activity')) {
                    activity()
                        ->causedBy(auth()->user())
                        ->performedOn($case)
                        ->withProperties([
                            'case_number' => $case->case_number,
                            'recipients' => $recipients,
                            'pdf_attached' => (bool)$pdfBytes,
                            'old_status' => $oldStatus,
                            'new_status' => 'Case Referred',
                        ])
                        ->log('referred case to ' . count($recipients) . ' recipient(s)');
                }
            } catch (\Exception $logError) {
                \Log::warning('Activity logging failed for case referral', [
                    'case_id' => $case->id,
                    'error' => $logError->getMessage()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Case referred successfully to ' . count($recipients) . ' recipient(s). Status updated to Case Referred.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error referring case', [
                'case_id' => $case->id,
                'recipients' => $recipients,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to refer case. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Show referral form for a case
     */
    public function showReferralForm($id)
    {
        $case = CaseModel::findOrFail($id);
        $caseStatus = trim((string) $case->status);
        $alreadyReferred = Auth::user()?->isDepartmentAdmin()
            ? CaseReferral::withTrashed()
                ->where('case_id', $case->id)
                ->where('created_by_department_admin', true)
                ->exists()
            : $case->hasBeenReferred();
        $referralUnavailable = preg_match('/closed|complet/i', $caseStatus)
            || $alreadyReferred;

        if ($referralUnavailable) {
            $user = Auth::user();
            $isBcpcDeskOfficer = $user?->position === 'Desk Officer'
                && $user?->defaultCaseDepartment() === 'BCPC'
                && !$user?->isMultiCaseDepartment();

            if (session('referral_success') || request()->boolean('referral_success')) {
                if ($isBcpcDeskOfficer) {
                    return redirect()->route('admin.barangay-cases.show', [
                        'barangay' => $case->case_designation,
                        'referral_success' => 1,
                    ]);
                }
                return redirect()->route('admin-barangay-cases', ['referral_success' => 1]);
            }

            if ($isBcpcDeskOfficer) {
                return redirect()->route('admin.barangay-cases.show', [
                    'barangay' => $case->case_designation,
                    'referral_unavailable' => 1,
                ]);
            }

            return redirect()->route('admin-barangay-cases', ['referral_unavailable' => 1]);
        }
        
        // Only recipients who are currently active can be selected for a referral.
        $recipients = \App\Models\Wcpu\ReportRecipient::active()
            ->select('id', 'name', 'email', 'role')
            ->orderBy('name')
            ->get();

        return view('admin-tailwind.referral-form', compact('case', 'recipients'));
    }

    /**
     * Store case referral
     */
    public function storeReferral(Request $request, $id)
    {
        try {
            $multiroleReferral = Auth::user()?->position === 'Desk Officer' && Auth::user()?->isMultiCaseDepartment();
            $isDepartmentAdminReferral = Auth::user()?->isDepartmentAdmin() ?? false;
            if ($multiroleReferral) {
                $lockedCase = CaseModel::findOrFail($id);
                $lockedFields = \App\Services\Wcpu\MultiroleReferralFields::forCase(Auth::user(), $lockedCase);
                unset($lockedFields['referred_by_contact']);
                $request->merge($lockedFields);
            }
            $rules = [
                'date_of_referral' => 'required|date',
                'referred_by_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                'referred_by_designation' => 'required|string|max:255',
                'referred_by_address' => 'required|string',
                'referred_by_contact' => ["required", "digits:11", new \App\Rules\ContactNumber(11)],
                'referred_by_email' => 'nullable|email|max:255',
                'guardian_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                'guardian_contact' => ["nullable", "string", "max:50", new \App\Rules\ContactNumber(11)],
                'guardian_address' => 'nullable|string',
                'relationship_to_victim' => 'nullable|string|max:255',
                'reason_for_referral' => 'required|string',
                'specific_services_requested' => 'required|string',
                'additional_notes' => 'nullable|string',
            ];

            if ($isDepartmentAdminReferral) {
                $rules += [
                    'recipient_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
                    'recipient_contact' => ["required", "digits:11", new \App\Rules\ContactNumber(11)],
                    'recipient_address' => 'required|string|max:255',
                    'recipient_email' => 'required|email:rfc|max:255',
                ];
            } else {
                $rules['recipient_id'] = 'required|exists:wcpu.report_recipients,id';
            }

            $validated = $request->validate($rules);

            $case = CaseModel::findOrFail($id);
            $caseStatus = trim((string) $case->status);
            $alreadyReferred = $isDepartmentAdminReferral
                ? CaseReferral::withTrashed()
                    ->where('case_id', $case->id)
                    ->where('created_by_department_admin', true)
                    ->exists()
                : $case->hasBeenReferred();
            $referralUnavailable = preg_match('/closed|complet/i', $caseStatus)
                || $alreadyReferred;

            if ($referralUnavailable) {
                return response()->json([
                    'success' => false,
                    'message' => 'This case has already been referred or completed.'
                ], 409);
            }
            $recipient = null;
            if ($isDepartmentAdminReferral) {
                $recipientName = $validated['recipient_name'];
                $recipientContact = $validated['recipient_contact'];
                $recipientAddress = $validated['recipient_address'];
                $recipientEmail = $validated['recipient_email'];
            } else {
                $recipient = \App\Models\Wcpu\ReportRecipient::active()
                    ->findOrFail($validated['recipient_id']);
                $recipientName = $recipient->name;
                $recipientContact = null;
                $recipientAddress = 'N/A';
                $recipientEmail = $recipient->email;
            }

            // Server-side validation: require guardian contact if guardian name is provided or victim is minor
            if (!$multiroleReferral && (($validated['guardian_name'] ?? '') !== '' || ($case->victim_age ?? 18) < 18) && empty($validated['guardian_contact'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guardian contact number is required for minors or when guardian is specified.',
                    'errors' => ['guardian_contact' => ['Guardian contact number is required for minors or when guardian is specified.']]
                ], 422);
            }

            \Log::info('Creating referral', [
                'case_id' => $case->id,
                'recipient_email' => $recipientEmail,
                'user_id' => Auth::id()
            ]);

            // Create referral
            $referral = CaseReferral::create([
                'case_id' => $case->id,
                'referral_number' => CaseReferral::generateReferralNumber(),
                'date_of_referral' => $validated['date_of_referral'],
                'referred_by_name' => $validated['referred_by_name'],
                'referred_by_designation' => $validated['referred_by_designation'],
                'referred_by_address' => $validated['referred_by_address'],
                'referred_by_contact' => $validated['referred_by_contact'],
                'referred_by_email' => $validated['referred_by_email'],
                'recipient_name' => $recipientName,
                'recipient_address' => $recipientAddress,
                'recipient_contact' => $recipientContact,
                'recipient_email' => $recipientEmail,
                'client_name' => $case->victim_name,
                'client_age' => $case->victim_age,
                'client_sex' => $case->victim_gender,
                'client_address' => $case->victim_address,
                'client_contact' => $case->victim_contact,
                'guardian_name' => $validated['guardian_name'] ?? null,
                'guardian_contact' => $validated['guardian_contact'] ?? null,
                'guardian_address' => $validated['guardian_address'] ?? null,
                'relationship_to_victim' => $validated['relationship_to_victim'] ?? null,
                'reason_for_referral' => $validated['reason_for_referral'],
                'specific_services_requested' => $validated['specific_services_requested'],
                'additional_notes' => $validated['additional_notes'],
                'referred_by_user_id' => Auth::id(),
                // Snapshot the creator's role at submission time so later viewers
                // or account changes cannot alter this referral's table mapping.
                'created_by_department_admin' => $isDepartmentAdminReferral,
                'status' => 'Pending',
                ...($multiroleReferral ? [
                    'family_guardian_name' => $case->guardian,
                    'family_guardian_contact' => $case->guardian_contact,
                    'family_guardian_address' => $case->guardian_address,
                ] : []),
            ]);

            \Log::info('Referral created successfully', ['referral_id' => $referral->id]);

            // Update case status
            $case->update(['status' => 'Case Referred']);
            session()->forget('referral_restriction');
            session()->flash('referral_success', true);

            try {
                activity('case')
                    ->performedOn($case)
                    ->causedBy(Auth::user())
                    ->withProperties(['referral_number' => $referral->referral_number])
                    ->log('Case referred');
            } catch (\Exception $logError) {
                \Log::warning('Activity logging failed for case referral', [
                    'case_id' => $case->id,
                    'error' => $logError->getMessage(),
                ]);
            }

            // Send email notification
            $emailSent = false;
            $emailError = null;
            
            try {
                \Log::info('Generating PDF for referral email');
                
                // Check if view exists
                if (!\View::exists('admin-tailwind.referrals.print')) {
                    throw new \Exception('View admin-tailwind.referrals.print not found');
                }
                
                $pdf = Pdf::loadView('admin-tailwind.referrals.print', ['referral' => $referral])
                    ->setPaper('a4', 'portrait')
                    ->output();

                \Log::info('PDF generated successfully', ['size' => strlen($pdf)]);

                // Check if mailable exists
                if (!class_exists(CaseReferralNotification::class)) {
                    throw new \Exception('Mailable class CaseReferralNotification not found');
                }

                $mail = (new CaseReferralNotification($referral))
                    ->attachData($pdf, 'Referral-' . $referral->referral_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);

                \Log::info('Sending email', [
                    'to' => $referral->recipient_email,
                    'referral_number' => $referral->referral_number
                ]);

                if ($isDepartmentAdminReferral) {
                    Mail::mailer('wcpu')->to($referral->recipient_email)->send($mail);
                } else {
                    Mail::to($referral->recipient_email)->send($mail);
                }
                
                $emailSent = true;
                \Log::info('Email sent successfully to ' . $referral->recipient_email);
                
            } catch (\Exception $e) {
                $emailError = $e->getMessage();
                \Log::error('Failed to send referral email', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'recipient_email' => $referral->recipient_email,
                    'referral_number' => $referral->referral_number
                ]);
            }

            // Send SMS notification to victim or guardian
            $smsSent = false;
            $smsError = null;
            
            try {
                $smsService = new \App\Services\Wcpu\PhilSmsService();
                // Pass the recipient role to the referral object temporarily for SMS
                $referral->recipient_role = $recipient->role ?? null;
                $smsResult = $smsService->sendVictimReferralNotification($referral);
                
                $smsSent = $smsResult['success'];
                if (!$smsSent) {
                    $smsError = $smsResult['message'];
                }
                
                \Log::info('SMS notification attempt', [
                    'sent_to' => $smsResult['sent_to'] ?? 'unknown',
                    'sent_to_name' => $smsResult['sent_to_name'] ?? 'unknown',
                    'sent_to_number' => $smsResult['sent_to_number'] ?? 'N/A',
                    'success' => $smsSent,
                    'error' => $smsError
                ]);
            } catch (\Exception $e) {
                $smsError = $e->getMessage();
                \Log::error('Failed to send referral SMS', [
                    'error' => $e->getMessage(),
                    'referral_number' => $referral->referral_number
                ]);
            }

            $responseMessage = 'Case referral created successfully';
            if (!$emailSent) {
                $responseMessage .= ' (Note: Email notification failed to send - ' . $emailError . ')';
            }
            if (!$smsSent) {
                $responseMessage .= ' (Note: SMS notification failed - ' . $smsError . ')';
            }

            return response()->json([
                'success' => true,
                'message' => $responseMessage,
                'referral_number' => $referral->referral_number,
                'email_sent' => $emailSent,
                'email_error' => $emailError,
                'sms_sent' => $smsSent,
                'sms_error' => $smsError
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating referral: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all referrals (admin view)
     */
    public function referrals(Request $request)
    {
        $query = CaseReferral::with(['case', 'referredBy'])->latest();

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $referrals = $query->paginate(15);

        // Calculate stats
        $baseQuery = CaseReferral::query();
        $stats = [
            'total' => $baseQuery->count(),
            'pending' => (clone $baseQuery)->where('status', 'Pending')->count(),
            'accepted' => (clone $baseQuery)->where('status', 'Accepted')->count(),
            'completed' => (clone $baseQuery)->where('status', 'Completed')->count(),
        ];

        return view('admin.referrals', compact('referrals', 'stats'));
    }

    /**
     * Show referral details
     */
    public function showReferral($id)
    {
        $referral = CaseReferral::with(['case', 'referredBy'])->findOrFail($id);
        return view('admin.referral-details', compact('referral'));
    }

    /**
     * Print referral
     */
    public function printReferral($id)
    {
        $referral = CaseReferral::with(['case', 'referredBy'])->findOrFail($id);

        return view('admin-tailwind.referrals.print', compact('referral'));
    }

    /**
     * Print all referrals
     */
    public function printAllReferrals()
    {
        $referrals = CaseReferral::with(['case', 'referredBy'])->orderBy('date_of_referral', 'desc')->get();

        return view('admin-tailwind.referrals.print-all', compact('referrals'));
    }
}
