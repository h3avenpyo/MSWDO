<?php

namespace App\Http\Controllers\Admin\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\OnlineFinancialIntake;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Services\Financial\FinancialDuplicateChecker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OnlineFinancialIntakeController extends Controller
{
    /**
     * Silang Barangays list
     */
    protected array $barangays = [
        'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
        'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Acacia', 'Anabu',
        'Balite I', 'Balite II', 'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho',
        'Cabangaan', 'Carmen', 'Hukay', 'Iba', 'Kalubkob', 'Kaong', 'Lalaan I',
        'Lalaan II', 'Litlit', 'Lucsuhin', 'Lumil', 'Maguyam', 'Malabag', 'Malaking Tatyao',
        'Mataas na Burol', 'Munting Ilog', 'Narra I', 'Narra II', 'Narra III',
        'Paligawan', 'Pasong Langka', 'Pooc I', 'Pooc II', 'Pulong Bunga', 'Pulong Saging',
        'Puting Kahoy', 'Sabutan', 'San Miguel I', 'San Miguel II', 'San Vicente I',
        'San Vicente II', 'Santol', 'Tartaria', 'Tibig', 'Toledo', 'Tubuan I', 'Tubuan II',
        'Tubuan III', 'Ulat', 'Yakal'
    ];

    /**
     * Display a listing of online financial assistance intake applications with compound filters.
     */
    public function index(Request $request)
    {
        $query = OnlineFinancialIntake::with(['reviewer', 'beneficiaryIntake']);

        // 1. Filter by Status (For Review, Accepted, Rejected, All)
        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        // 2. Filter by Barangay
        if ($request->filled('barangay') && $request->barangay !== 'All') {
            $query->where('beneficiary_barangay', $request->barangay);
        }

        // 3. Filter by Specific Date or Date Range
        if ($request->filled('date')) {
            $query->whereDate('date_submitted', $request->date);
        } else {
            if ($request->filled('date_from')) {
                $query->whereDate('date_submitted', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('date_submitted', '<=', $request->date_to);
            }
        }

        // 4. Search by applicant / beneficiary name, control number, or online reference ID
        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanId = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $cleanId) {
                $q->where('control_number', 'like', "%{$search}%")
                  ->orWhere('beneficiary_first_name', 'like', "%{$search}%")
                  ->orWhere('beneficiary_last_name', 'like', "%{$search}%")
                  ->orWhere('beneficiary_middle_name', 'like', "%{$search}%")
                  ->orWhere('rep_first_name', 'like', "%{$search}%")
                  ->orWhere('rep_last_name', 'like', "%{$search}%")
                  ->orWhere('beneficiary_barangay', 'like', "%{$search}%");

                if (!empty($cleanId)) {
                    $q->orWhere('id', (int) $cleanId);
                }
            });
        }

        // Summary Metric Counts
        $totalCount = OnlineFinancialIntake::count();
        $pendingCount = OnlineFinancialIntake::where('status', 'For Review')->count();
        $acceptedCount = OnlineFinancialIntake::where('status', 'Accepted')->count();
        $rejectedCount = OnlineFinancialIntake::where('status', 'Rejected')->count();

        // Paginate results (newest first)
        $applications = $query->latest('id')->paginate(15)->withQueryString();

        $barangays = $this->barangays;

        return view('admin.financial.online-intakes.index', compact(
            'applications',
            'barangays',
            'totalCount',
            'pendingCount',
            'acceptedCount',
            'rejectedCount'
        ));
    }

    /**
     * Show detailed JSON information of an online intake application for the review modal.
     */
    public function show($id)
    {
        $application = OnlineFinancialIntake::with(['reviewer', 'beneficiaryIntake'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $application->id,
                'reference_number' => $application->reference_number,
                'status' => $application->status,
                'status_badge' => $application->status_badge_class,
                'control_number' => $application->control_number,
                'client_type' => $application->client_type,
                'date_submitted' => $application->date_submitted ? $application->date_submitted->format('F d, Y h:i A') : 'N/A',
                'beneficiary' => [
                    'full_name' => $application->beneficiary_full_name,
                    'first_name' => $application->beneficiary_first_name,
                    'middle_name' => $application->beneficiary_middle_name,
                    'last_name' => $application->beneficiary_last_name,
                    'extension_name' => $application->beneficiary_extension_name,
                    'birthday' => $application->beneficiary_birthday ? $application->beneficiary_birthday->format('F d, Y') : 'N/A',
                    'age' => $application->beneficiary_age,
                    'sex' => $application->beneficiary_sex,
                    'civil_status' => $application->beneficiary_civil_status,
                    'contact_number' => $application->beneficiary_contact_number,
                    'occupation' => $application->beneficiary_occupation ?: 'None / Unemployed',
                    'monthly_salary' => $application->beneficiary_monthly_salary ? number_format($application->beneficiary_monthly_salary, 2) : '0.00',
                    'address' => $application->beneficiary_street_address,
                    'barangay' => $application->beneficiary_barangay,
                    'city' => $application->beneficiary_city,
                    'province' => $application->beneficiary_province,
                    'category' => $application->beneficiary_category,
                    'category_other' => $application->beneficiary_category_other,
                    'categories' => $application->beneficiary_categories ?: [],
                ],
                'has_representative' => (bool) $application->has_representative,
                'representative' => $application->has_representative ? [
                    'full_name' => $application->rep_full_name,
                    'first_name' => $application->rep_first_name,
                    'middle_name' => $application->rep_middle_name,
                    'last_name' => $application->rep_last_name,
                    'extension_name' => $application->rep_extension_name,
                    'relationship' => $application->rep_relationship ?: 'Representative',
                    'birthday' => $application->rep_birthday ? $application->rep_birthday->format('F d, Y') : 'N/A',
                    'age' => $application->rep_age,
                    'sex' => $application->rep_sex,
                    'civil_status' => $application->rep_civil_status,
                    'contact_number' => $application->rep_contact_number,
                    'occupation' => $application->rep_occupation ?: 'None',
                    'monthly_salary' => $application->rep_monthly_salary ? number_format($application->rep_monthly_salary, 2) : '0.00',
                    'address' => $application->rep_street_address,
                    'barangay' => $application->rep_barangay,
                ] : null,
                'family_composition' => $application->family_composition ?: [],
                'medical_conditions' => $application->medical_conditions ?: [],
                'medical_condition_other' => $application->medical_condition_other,
                'assistance_purpose' => $application->assistance_purpose ?: 'General Assistance Request',
                'service_provided' => $application->service_provided ?: 'Financial Assistance Intake',
                'purpose' => $application->purpose ?: 'General Assistance Request',
                'review_info' => [
                    'reviewed_by' => $application->reviewer ? $application->reviewer->name : null,
                    'reviewed_at' => $application->reviewed_at ? $application->reviewed_at->format('F d, Y h:i A') : null,
                    'review_notes' => $application->review_notes,
                    'rejection_reason' => $application->rejection_reason,
                    'beneficiary_intake_id' => $application->beneficiary_intake_id,
                ]
            ]
        ]);
    }

    /**
     * Accept the online application, generate official Control Number, run duplicate check, and transfer into regular Step 1 records.
     */
    public function accept(Request $request, $id, FinancialDuplicateChecker $duplicateChecker)
    {
        $onlineIntake = OnlineFinancialIntake::findOrFail($id);

        if ($onlineIntake->status === 'Accepted') {
            return response()->json([
                'success' => false,
                'message' => 'Ang aplikasyong ito ay na-accept na dati. Record ID: #' . $onlineIntake->beneficiary_intake_id . ' (Control No: ' . $onlineIntake->control_number . ')',
            ], 422);
        }

        // Prepare duplicate check payload
        $checkData = [
            'beneficiary_first_name' => $onlineIntake->beneficiary_first_name,
            'beneficiary_last_name' => $onlineIntake->beneficiary_last_name,
            'beneficiary_middle_name' => $onlineIntake->beneficiary_middle_name,
            'beneficiary_birthday' => $onlineIntake->beneficiary_birthday ? $onlineIntake->beneficiary_birthday->toDateString() : null,
            'beneficiary_barangay' => $onlineIntake->beneficiary_barangay,
            'date_processed' => Carbon::today()->toDateString(),
            'has_representative' => $onlineIntake->has_representative,
            'rep_first_name' => $onlineIntake->rep_first_name,
            'rep_last_name' => $onlineIntake->rep_last_name,
            'rep_birthday' => $onlineIntake->rep_birthday ? $onlineIntake->rep_birthday->toDateString() : null,
        ];

        $duplicateCheck = $duplicateChecker->checkDuplicate($checkData);

        // If duplicate detected and not explicitly confirmed by staff
        if ($duplicateCheck['is_duplicate'] && !$request->boolean('confirm_duplicate_override')) {
            return response()->json([
                'success' => false,
                'is_duplicate' => true,
                'warning_message' => $duplicateCheck['warning_message'],
                'matches' => $duplicateCheck['matches'],
                'message' => 'Babala sa Duplikasyon: Ang kliyente o kinatawan ay may tala sa nakalipas na 6 na buwan.'
            ], 409);
        }

        // Prepare data to transfer into regular BeneficiaryIntake
        $encoderId = auth()->id() ?? session('admin_user_id');

        // Control Number Flow:
        // Generate and assign Control Number ONLY upon acceptance if not already assigned.
        // Uses the same sequence used by the regular Step 1 Intake to prevent duplicates or conflicts.
        $controlNumber = $onlineIntake->control_number;
        if (empty($controlNumber)) {
            $year = date('Y');
            $nextSeq = BeneficiaryIntake::count() + 1;
            $controlNumber = 'MSWDO-' . $year . '-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
            while (BeneficiaryIntake::where('control_number', $controlNumber)->exists() || OnlineFinancialIntake::where('control_number', $controlNumber)->exists()) {
                $nextSeq++;
                $controlNumber = 'MSWDO-' . $year . '-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
            }
        }

        $intakeData = [
            'control_number' => $controlNumber,
            'client_type' => $onlineIntake->client_type ?: 'New',
            'date_processed' => Carbon::today()->toDateString(),
            'encoder' => $encoderId,
            'is_client_beneficiary' => $onlineIntake->is_client_beneficiary,
            'beneficiary_last_name' => $onlineIntake->beneficiary_last_name,
            'beneficiary_first_name' => $onlineIntake->beneficiary_first_name,
            'beneficiary_middle_name' => $onlineIntake->beneficiary_middle_name,
            'beneficiary_extension_name' => $onlineIntake->beneficiary_extension_name,
            'beneficiary_street_address' => $onlineIntake->beneficiary_street_address,
            'beneficiary_barangay' => $onlineIntake->beneficiary_barangay,
            'beneficiary_city' => $onlineIntake->beneficiary_city ?: 'Silang',
            'beneficiary_province' => $onlineIntake->beneficiary_province ?: 'Cavite',
            'beneficiary_region' => $onlineIntake->beneficiary_region ?: 'Region IV-A',
            'beneficiary_contact_number' => $onlineIntake->beneficiary_contact_number,
            'beneficiary_birthday' => $onlineIntake->beneficiary_birthday,
            'beneficiary_age' => $onlineIntake->beneficiary_age,
            'beneficiary_sex' => $onlineIntake->beneficiary_sex,
            'beneficiary_civil_status' => $onlineIntake->beneficiary_civil_status,
            'beneficiary_occupation' => $onlineIntake->beneficiary_occupation,
            'beneficiary_monthly_salary' => $onlineIntake->beneficiary_monthly_salary,
            'beneficiary_category' => $onlineIntake->beneficiary_category,
            'beneficiary_category_other' => $onlineIntake->beneficiary_category_other,
            'beneficiary_categories' => $onlineIntake->beneficiary_categories,
            'beneficiary_relationship' => $onlineIntake->beneficiary_relationship,
            'has_representative' => (bool) $onlineIntake->has_representative,
            'rep_last_name' => $onlineIntake->rep_last_name,
            'rep_first_name' => $onlineIntake->rep_first_name,
            'rep_middle_name' => $onlineIntake->rep_middle_name,
            'rep_extension_name' => $onlineIntake->rep_extension_name,
            'rep_street_address' => $onlineIntake->rep_street_address,
            'rep_barangay' => $onlineIntake->rep_barangay,
            'rep_city' => $onlineIntake->rep_city,
            'rep_province' => $onlineIntake->rep_province,
            'rep_region' => $onlineIntake->rep_region,
            'rep_contact_number' => $onlineIntake->rep_contact_number,
            'rep_birthday' => $onlineIntake->rep_birthday,
            'rep_age' => $onlineIntake->rep_age,
            'rep_sex' => $onlineIntake->rep_sex,
            'rep_civil_status' => $onlineIntake->rep_civil_status,
            'rep_occupation' => $onlineIntake->rep_occupation,
            'rep_monthly_salary' => $onlineIntake->rep_monthly_salary,
            'rep_relationship' => $onlineIntake->rep_relationship,
            'family_composition' => $onlineIntake->family_composition,
            'medical_conditions' => $onlineIntake->medical_conditions,
            'medical_condition_other' => $onlineIntake->medical_condition_other,
            'assistance_purpose' => $onlineIntake->assistance_purpose,
            'service_provided' => $onlineIntake->service_provided ?: 'Financial Assistance Intake',
            'purpose' => $onlineIntake->purpose ?: ($onlineIntake->assistance_purpose ?: 'General Assistance Request'),
            'purpose_other' => $onlineIntake->purpose_other,
            'submitted_to' => $onlineIntake->submitted_to ?: 'MSWDO Silang Main Office',
        ];

        // Create the regular Step 1 intake record
        $beneficiaryIntake = BeneficiaryIntake::create($intakeData);

        // Update online application status to Accepted and persist assigned Control Number
        $onlineIntake->update([
            'status' => 'Accepted',
            'control_number' => $controlNumber,
            'reviewed_by' => $encoderId,
            'reviewed_at' => now(),
            'review_notes' => $request->input('review_notes'),
            'beneficiary_intake_id' => $beneficiaryIntake->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Matagumpay na tinanggap ang online application at inilipat sa regular na Step 1 Intake!',
            'data' => [
                'online_id' => $onlineIntake->id,
                'intake_id' => $beneficiaryIntake->id,
                'control_number' => $controlNumber,
                'beneficiary_name' => $beneficiaryIntake->beneficiary_full_name,
                'step1_url' => route('admin.financial.financialstep1'),
            ]
        ]);
    }

    /**
     * Reject the online application.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $onlineIntake = OnlineFinancialIntake::findOrFail($id);

        if ($onlineIntake->status === 'Accepted') {
            return response()->json([
                'success' => false,
                'message' => 'Hindi na maaaring tanggihan ang aplikasyong ito dahil ito ay na-accept na.',
            ], 422);
        }

        $userId = auth()->id() ?? session('admin_user_id');

        $onlineIntake->update([
            'status' => 'Rejected',
            'rejection_reason' => $request->rejection_reason,
            'review_notes' => $request->review_notes,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ang online application ay tinanggihan (Rejected).',
            'data' => [
                'id' => $onlineIntake->id,
                'status' => 'Rejected',
                'rejection_reason' => $onlineIntake->rejection_reason,
            ]
        ]);
    }
}
