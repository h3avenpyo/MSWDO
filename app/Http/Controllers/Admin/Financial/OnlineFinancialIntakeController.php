<?php

namespace App\Http\Controllers\Admin\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\OnlineFinancialIntake;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Services\Financial\FinancialControlNumberService;
use App\Services\Financial\FinancialDuplicateChecker;
use App\Services\Financial\OnlineFinancialNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OnlineFinancialIntakeController extends Controller
{
    protected OnlineFinancialNotificationService $notificationService;

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

    public function __construct(OnlineFinancialNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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
    public function downloadDocument($id, string $document)
    {
        $application = OnlineFinancialIntake::findOrFail($id);
        $file = ($application->documents ?? [])[$document] ?? null;
        abort_unless($file && Storage::disk('local')->exists($file['path']), 404);

        return Storage::disk('local')->download($file['path'], $file['name'], ['X-Content-Type-Options' => 'nosniff']);
    }

    public function show($id)
    {
        $application = OnlineFinancialIntake::with(['reviewer', 'beneficiaryIntake'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $application->id,
                'reference_number' => $application->reference_number,
                'documents' => collect($application->documents ?? [])->map(function ($file, $key) use ($application) {
                    return [
                        'label' => OnlineFinancialIntake::DOCUMENT_LABELS[$key] ?? $key,
                        'name' => $file['name'],
                        'url' => route('admin.financial.online-intakes.documents.download', ['id' => $application->id, 'document' => $key]),
                    ];
                })->values(),
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
                'medical_conditions' => (!empty($application->medical_conditions) && is_array($application->medical_conditions) && count($application->medical_conditions) > 0)
                    ? array_values(array_filter($application->medical_conditions, fn($m) => !empty($m) && trim((string)$m) !== ''))
                    : ($application->display_medical_condition ? [$application->display_medical_condition] : []),
                'medical_condition_other' => $application->medical_condition_other,
                'display_medical_condition' => $application->display_medical_condition,
                'display_assistance_purpose' => $application->display_assistance_purpose,
                'assistance_purpose' => $application->assistance_purpose ?: 'General Assistance Request',
                'assistance_type' => $application->assistance_type,
                'service_provided' => $application->service_provided ?: 'Financial Assistance Intake',
                'purpose' => $application->purpose ?: 'General Assistance Request',
                'review_info' => [
                    'reviewed_by' => $application->reviewer ? $application->reviewer->name : null,
                    'reviewed_at' => $application->reviewed_at ? $application->reviewed_at->format('F d, Y h:i A') : null,
                    'review_notes' => $application->review_notes,
                    'rejection_reason' => $application->rejection_reason,
                    'beneficiary_intake_id' => $application->beneficiary_intake_id,
                ],
                'sms_info' => [
                    'sms_status' => $application->sms_status,
                    'sms_error' => $application->sms_error,
                    'sms_notified_at' => $application->sms_notified_at ? $application->sms_notified_at->format('F d, Y h:i A') : null,
                    'approval_sms_sent_at' => $application->approval_sms_sent_at ? $application->approval_sms_sent_at->format('F d, Y h:i A') : null,
                    'rejection_sms_sent_at' => $application->rejection_sms_sent_at ? $application->rejection_sms_sent_at->format('F d, Y h:i A') : null,
                    'applicant_contact' => $application->applicant_contact_number,
                ],
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
            $controlNumber = FinancialControlNumberService::generate();
        }

        // Helper closures to format text values to uppercase
        $toUpper = function ($value) {
            if ($value === null || $value === '') {
                return $value;
            }
            return mb_strtoupper(trim((string)$value), 'UTF-8');
        };

        $upperArray = function ($arr) use ($toUpper) {
            if (empty($arr)) {
                return $arr;
            }
            if (is_string($arr)) {
                $decoded = json_decode($arr, true);
                if (is_array($decoded)) {
                    return array_map(fn($item) => is_string($item) ? $toUpper($item) : $item, $decoded);
                }
                return $toUpper($arr);
            }
            if (is_array($arr)) {
                return array_map(function ($item) use ($toUpper) {
                    return is_string($item) ? $toUpper($item) : $item;
                }, $arr);
            }
            return $arr;
        };

        $upperFamily = function ($fam) use ($toUpper) {
            if (empty($fam)) {
                return $fam;
            }
            if (is_string($fam)) {
                $decoded = json_decode($fam, true);
                if (is_array($decoded)) {
                    $fam = $decoded;
                }
            }
            if (!is_array($fam)) {
                return $fam;
            }
            return array_map(function ($member) use ($toUpper) {
                if (!is_array($member)) {
                    return $member;
                }
                foreach (['name', 'relationship', 'occupation', 'civil_status', 'educational_attainment'] as $field) {
                    if (isset($member[$field]) && is_string($member[$field])) {
                        $member[$field] = $toUpper($member[$field]);
                    }
                }
                return $member;
            }, $fam);
        };

        $medConditions = $upperArray($onlineIntake->medical_conditions);
        if (empty($medConditions) && !empty($onlineIntake->display_medical_condition)) {
            $medConditions = [$onlineIntake->display_medical_condition];
        }

        $intakeData = [
            'control_number' => $controlNumber,
            'client_type' => $toUpper($onlineIntake->client_type ?: 'NEW'),
            'date_processed' => Carbon::today()->toDateString(),
            'encoder' => $encoderId,
            'is_client_beneficiary' => $onlineIntake->is_client_beneficiary,
            'beneficiary_last_name' => $toUpper($onlineIntake->beneficiary_last_name),
            'beneficiary_first_name' => $toUpper($onlineIntake->beneficiary_first_name),
            'beneficiary_middle_name' => $toUpper($onlineIntake->beneficiary_middle_name),
            'beneficiary_extension_name' => $toUpper($onlineIntake->beneficiary_extension_name),
            'beneficiary_street_address' => $toUpper($onlineIntake->beneficiary_street_address),
            'beneficiary_barangay' => $toUpper($onlineIntake->beneficiary_barangay),
            'beneficiary_city' => $toUpper($onlineIntake->beneficiary_city ?: 'SILANG'),
            'beneficiary_province' => $toUpper($onlineIntake->beneficiary_province ?: 'CAVITE'),
            'beneficiary_region' => $toUpper($onlineIntake->beneficiary_region ?: 'REGION IV-A'),
            'beneficiary_contact_number' => $onlineIntake->beneficiary_contact_number,
            'beneficiary_birthday' => $onlineIntake->beneficiary_birthday,
            'beneficiary_age' => $onlineIntake->beneficiary_age,
            'beneficiary_sex' => $toUpper($onlineIntake->beneficiary_sex),
            'beneficiary_civil_status' => $toUpper($onlineIntake->beneficiary_civil_status),
            'beneficiary_occupation' => $toUpper($onlineIntake->beneficiary_occupation),
            'beneficiary_monthly_salary' => $onlineIntake->beneficiary_monthly_salary,
            'beneficiary_category' => $toUpper($onlineIntake->beneficiary_category),
            'beneficiary_category_other' => $toUpper($onlineIntake->beneficiary_category_other),
            'beneficiary_categories' => $upperArray($onlineIntake->beneficiary_categories),
            'beneficiary_relationship' => $toUpper($onlineIntake->beneficiary_relationship),
            'has_representative' => (bool) $onlineIntake->has_representative,
            'rep_last_name' => $toUpper($onlineIntake->rep_last_name),
            'rep_first_name' => $toUpper($onlineIntake->rep_first_name),
            'rep_middle_name' => $toUpper($onlineIntake->rep_middle_name),
            'rep_extension_name' => $toUpper($onlineIntake->rep_extension_name),
            'rep_street_address' => $toUpper($onlineIntake->rep_street_address),
            'rep_barangay' => $toUpper($onlineIntake->rep_barangay),
            'rep_city' => $toUpper($onlineIntake->rep_city),
            'rep_province' => $toUpper($onlineIntake->rep_province),
            'rep_region' => $toUpper($onlineIntake->rep_region),
            'rep_contact_number' => $onlineIntake->rep_contact_number,
            'rep_birthday' => $onlineIntake->rep_birthday,
            'rep_age' => $onlineIntake->rep_age,
            'rep_sex' => $toUpper($onlineIntake->rep_sex),
            'rep_civil_status' => $toUpper($onlineIntake->rep_civil_status),
            'rep_occupation' => $toUpper($onlineIntake->rep_occupation),
            'rep_monthly_salary' => $onlineIntake->rep_monthly_salary,
            'rep_relationship' => $toUpper($onlineIntake->rep_relationship),
            'family_composition' => $upperFamily($onlineIntake->family_composition),
            'medical_conditions' => $medConditions,
            'medical_condition_other' => $toUpper($onlineIntake->medical_condition_other),
            'assistance_purpose' => $toUpper($onlineIntake->assistance_purpose),
            'assistance_type' => $toUpper($onlineIntake->assistance_type),
            'service_provided' => $toUpper($onlineIntake->service_provided ?: 'FINANCIAL ASSISTANCE INTAKE'),
            'purpose' => $toUpper($onlineIntake->purpose ?: ($onlineIntake->assistance_purpose ?: 'GENERAL ASSISTANCE REQUEST')),
            'purpose_other' => $toUpper($onlineIntake->purpose_other),
            'submitted_to' => $toUpper($onlineIntake->submitted_to ?: 'MSWDO SILANG MAIN OFFICE'),
        ];

        // Create the regular Step 1 intake record and update online intake within a database transaction
        $beneficiaryIntake = null;
        DB::transaction(function () use (&$beneficiaryIntake, $onlineIntake, $intakeData, $controlNumber, $encoderId, $request) {
            $beneficiaryIntake = BeneficiaryIntake::create($intakeData);

            // Update online application status to Accepted, persist assigned Control Number, and uppercase text data
            $onlineIntake->update([
                'status' => 'Accepted',
                'control_number' => $controlNumber,
                'reviewed_by' => $encoderId,
                'reviewed_at' => now(),
                'review_notes' => $request->input('review_notes'),
                'beneficiary_intake_id' => $beneficiaryIntake->id,
                'beneficiary_last_name' => $intakeData['beneficiary_last_name'],
                'beneficiary_first_name' => $intakeData['beneficiary_first_name'],
                'beneficiary_middle_name' => $intakeData['beneficiary_middle_name'],
                'beneficiary_extension_name' => $intakeData['beneficiary_extension_name'],
                'beneficiary_street_address' => $intakeData['beneficiary_street_address'],
                'beneficiary_barangay' => $intakeData['beneficiary_barangay'],
                'beneficiary_city' => $intakeData['beneficiary_city'],
                'beneficiary_province' => $intakeData['beneficiary_province'],
                'beneficiary_region' => $intakeData['beneficiary_region'],
                'beneficiary_sex' => $intakeData['beneficiary_sex'],
                'beneficiary_civil_status' => $intakeData['beneficiary_civil_status'],
                'beneficiary_occupation' => $intakeData['beneficiary_occupation'],
                'beneficiary_category' => $intakeData['beneficiary_category'],
                'beneficiary_category_other' => $intakeData['beneficiary_category_other'],
                'beneficiary_categories' => $intakeData['beneficiary_categories'],
                'beneficiary_relationship' => $intakeData['beneficiary_relationship'],
                'rep_last_name' => $intakeData['rep_last_name'],
                'rep_first_name' => $intakeData['rep_first_name'],
                'rep_middle_name' => $intakeData['rep_middle_name'],
                'rep_extension_name' => $intakeData['rep_extension_name'],
                'rep_street_address' => $intakeData['rep_street_address'],
                'rep_barangay' => $intakeData['rep_barangay'],
                'rep_city' => $intakeData['rep_city'],
                'rep_province' => $intakeData['rep_province'],
                'rep_region' => $intakeData['rep_region'],
                'rep_sex' => $intakeData['rep_sex'],
                'rep_civil_status' => $intakeData['rep_civil_status'],
                'rep_occupation' => $intakeData['rep_occupation'],
                'rep_relationship' => $intakeData['rep_relationship'],
                'family_composition' => $intakeData['family_composition'],
                'medical_conditions' => $intakeData['medical_conditions'],
                'medical_condition_other' => $intakeData['medical_condition_other'],
                'assistance_purpose' => $intakeData['assistance_purpose'],
                'assistance_type' => $intakeData['assistance_type'],
                'purpose' => $intakeData['purpose'],
                'purpose_other' => $intakeData['purpose_other'],
            ]);
        });

        // Send automatic Tagalog approval SMS notification to applicant strictly after successful status change
        $smsResult = null;
        try {
            $smsResult = $this->notificationService->notifyApproval($onlineIntake, $beneficiaryIntake);
        } catch (\Throwable $e) {
            Log::error("[OnlineFinancialIntake] Approval SMS dispatch exception: " . $e->getMessage());
            $smsResult = [
                'success' => false,
                'error' => 'Hindi naipadala ang SMS abiso: ' . $e->getMessage(),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Matagumpay na tinanggap ang online application at inilipat sa regular na Step 1 Intake!',
            'data' => [
                'online_id' => $onlineIntake->id,
                'intake_id' => $beneficiaryIntake->id,
                'control_number' => $controlNumber,
                'beneficiary_name' => $beneficiaryIntake->beneficiary_full_name,
                'step1_url' => route('admin.financial.financialstep1'),
                'sms' => $smsResult,
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

        if ($onlineIntake->status === 'Rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Ang online application na ito ay tinanggihan na dati (Rejected).',
            ], 422);
        }

        $userId = auth()->id() ?? session('admin_user_id');

        DB::transaction(function () use ($onlineIntake, $request, $userId) {
            $onlineIntake->update([
                'status' => 'Rejected',
                'rejection_reason' => mb_strtoupper(trim($request->rejection_reason), 'UTF-8'),
                'review_notes' => $request->review_notes ? mb_strtoupper(trim($request->review_notes), 'UTF-8') : null,
                'reviewed_by' => $userId,
                'reviewed_at' => now(),
            ]);
        });

        // Send automatic Tagalog rejection SMS notification to applicant strictly after successful status change
        $smsResult = null;
        try {
            $smsResult = $this->notificationService->notifyRejection($onlineIntake);
        } catch (\Throwable $e) {
            Log::error("[OnlineFinancialIntake] Rejection SMS dispatch exception: " . $e->getMessage());
            $smsResult = [
                'success' => false,
                'error' => 'Hindi naipadala ang SMS abiso: ' . $e->getMessage(),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Ang online application ay tinanggihan (Rejected).',
            'data' => [
                'id' => $onlineIntake->id,
                'status' => 'Rejected',
                'rejection_reason' => $onlineIntake->rejection_reason,
                'sms' => $smsResult,
            ]
        ]);
    }

    /**
     * Resend/retry SMS notification for a previously reviewed online application.
     */
    public function resendSms(Request $request, $id)
    {
        $onlineIntake = OnlineFinancialIntake::with(['beneficiaryIntake'])->findOrFail($id);

        if ($onlineIntake->status === 'For Review') {
            return response()->json([
                'success' => false,
                'message' => 'Hindi maaaring magpadala ng abiso sa aplikasyong nakabinbin pa (For Review).',
            ], 422);
        }

        $officer = session('admin_user_name') ?? 'MSWDO Staff';
        $smsResult = $this->notificationService->retryNotification($onlineIntake, $officer);

        if ($smsResult['success']) {
            return response()->json([
                'success' => true,
                'message' => $smsResult['message'] ?? 'Matagumpay na naipadala ang SMS sa aplikante.',
                'data' => $smsResult,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $smsResult['error'] ?? 'Nabigo ang pagpapadala ng SMS.',
            'data' => $smsResult,
        ], 422);
    }
}
