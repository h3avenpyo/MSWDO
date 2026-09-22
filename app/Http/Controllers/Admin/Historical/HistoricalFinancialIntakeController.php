<?php

namespace App\Http\Controllers\Admin\Historical;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Historical\StoreHistoricalFinancialIntakeRequest;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\Client;
use App\Models\User;
use App\Enums\UserRole;
use App\Services\Financial\FinancialDuplicateChecker;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HistoricalFinancialIntakeController extends Controller
{
    /**
     * Show the form for encoding historical intake records created before system implementation.
     */
    public function create()
    {
        $currentYear = date('Y');
        $seq = str_pad(BeneficiaryIntake::count() + 1, 5, '0', STR_PAD_LEFT);
        $controlNumber = "MSWDO-{$currentYear}-{$seq}";
        $encoder = session('admin_user_name') ?? 'Admin User';

        $barangays = [
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

        $categories = [
            'Solo Parents',
            'Indigenous People',
            'PWD',
            '4PS DSWD Beneficiary',
            'LGBTQIA+',
            'Psychosocial/Mental/Learning Disability',
            'Stateless Person/Asylum Seekers/Refugees',
            'Others',
        ];

        $relationships = [
            'Parent (Father / Mother)',
            'Spouse (Husband / Wife)',
            'Child (Son / Daughter)',
            'Sibling (Brother / Sister)',
            'Grandparent',
            'Grandchild',
            'Guardian',
            'Relative',
            'Neighbor / Friend',
            'Other',
        ];

        $medicalConditions = [
            'Cancer',
            'Cardiovascular',
            'Kidney Diseases',
            'Neurological Disorders',
            'Respiratory Diseases',
            'Infectious Disease',
            'Diabetes',
            'Surgical',
            'Trauma and Injury',
            'Hospital Bill / Medical Needs',
            'Other Medical Conditions',
        ];

        // Retrieve existing Step 1 officers registered in the system
        $step1Officers = User::where(function ($q) {
            $q->whereIn('role', [
                UserRole::FinancialStep1,
                UserRole::FinancialAssistanceOfficer,
                'financialstep1',
                'Financial assistance officer',
            ]);
        })
        ->where('status', 'active')
        ->orderBy('name')
        ->get()
        ->unique('name');

        if ($step1Officers->isEmpty()) {
            $step1Officers = User::where('status', 'active')->orderBy('name')->get()->unique('name');
        }

        return view('admin.historical.financial-intake', compact(
            'controlNumber',
            'encoder',
            'barangays',
            'categories',
            'relationships',
            'medicalConditions',
            'step1Officers'
        ));
    }

    /**
     * Store a newly encoded historical intake sheet into the existing database structure.
     */
    public function store(StoreHistoricalFinancialIntakeRequest $request, FinancialDuplicateChecker $duplicateChecker)
    {
        $data = $request->validated();

        // 6-Month Validity Check relative to historical intake date
        $duplicateCheck = $duplicateChecker->checkDuplicate($data);
        if ($duplicateCheck['is_duplicate'] && !$request->boolean('confirm_duplicate_override')) {
            return redirect()->back()
                ->withInput()
                ->with('duplicate_check_error', $duplicateCheck['warning_message'])
                ->with('duplicate_matches', $duplicateCheck['matches']);
        }

        // Beneficiary age calculation
        if (! empty($data['beneficiary_birthday'])) {
            $data['beneficiary_age'] = Carbon::parse($data['beneficiary_birthday'])->diffInYears(Carbon::parse($data['date_processed']));
        }

        // Clean up representative data if disabled
        if (! $request->boolean('has_representative')) {
            $data['has_representative'] = false;
            $data['rep_last_name'] = null;
            $data['rep_first_name'] = null;
            $data['rep_middle_name'] = null;
            $data['rep_extension_name'] = null;
            $data['rep_street_address'] = null;
            $data['rep_barangay'] = null;
            $data['rep_city'] = null;
            $data['rep_province'] = null;
            $data['rep_region'] = null;
            $data['rep_contact_number'] = null;
            $data['rep_birthday'] = null;
            $data['rep_age'] = null;
            $data['rep_sex'] = null;
            $data['rep_civil_status'] = null;
            $data['rep_occupation'] = null;
            $data['rep_monthly_salary'] = null;
            $data['rep_relationship'] = null;
        } else if (! empty($data['rep_birthday'])) {
            $data['rep_age'] = Carbon::parse($data['rep_birthday'])->diffInYears(Carbon::parse($data['date_processed']));
        }

        // Filter family composition
        if (isset($data['family_composition']) && is_array($data['family_composition'])) {
            $data['family_composition'] = array_values(array_filter($data['family_composition'], function ($row) {
                return ! empty($row['name']);
            }));
        }

        $userId = session('admin_user_id') ?? auth()->id();
        $data['encoder'] = ($userId && User::where('id', $userId)->exists()) ? $userId : null;

        // If an officer was selected from the dropdown, link the relationship to that officer
        if (!empty($data['interviewed_by'])) {
            $selectedOfficer = is_numeric($data['interviewed_by'])
                ? User::find($data['interviewed_by'])
                : User::where('name', $data['interviewed_by'])->first();

            if ($selectedOfficer) {
                $data['encoder'] = $selectedOfficer->id;
                $data['interviewed_by'] = $selectedOfficer->name;
            }
        }

        // Default geography and assistance text if not provided
        $data['beneficiary_city'] = $data['beneficiary_city'] ?? 'Silang';
        $data['beneficiary_province'] = $data['beneficiary_province'] ?? 'Cavite';
        $data['beneficiary_region'] = $data['beneficiary_region'] ?? 'Region IV-A';
        $data['service_provided'] = $data['service_provided'] ?? ($data['recommended_assistance_type'] ?? 'Financial Assistance Intake');
        $data['purpose'] = $data['purpose'] ?? ($data['assistance_purpose'] ?? 'General Assistance Request');
        $data['submitted_to'] = $data['submitted_to'] ?? 'MSWDO Silang Main Office';

        // Historical metadata
        $data['is_historical'] = true;
        $data['is_archived'] = false;
        $data['service_provided'] = $data['service_provided'] ?? 'Financial Assistance Intake';
        $data['purpose'] = $data['purpose'] ?? ($data['assistance_purpose'] ?? 'General Assistance Request');
        $data['submitted_to'] = $data['submitted_to'] ?? 'MSWDO Silang Main Office';

        // Disbursed / Claim status handling
        $beneficiaryFullName = trim("{$data['beneficiary_first_name']} " . (!empty($data['beneficiary_middle_name']) ? "{$data['beneficiary_middle_name']} " : '') . "{$data['beneficiary_last_name']}" . (!empty($data['beneficiary_extension_name']) ? " {$data['beneficiary_extension_name']}" : ''));

        if (($data['claim_status'] ?? 'Claimed') === 'Claimed') {
            $data['claim_status'] = 'Claimed';
            $data['is_payroll_generated'] = true;
            $claimDate = !empty($data['claiming_date']) ? $data['claiming_date'] : $data['date_processed'];
            $data['claiming_date'] = $claimDate;
            $data['payroll_date'] = $claimDate;
            $data['claimed_at'] = Carbon::parse($claimDate)->setTime(10, 0, 0);
            $data['claimed_by'] = $beneficiaryFullName;
        } else {
            $data['claim_status'] = null;
            $data['is_payroll_generated'] = false;
            $data['claiming_date'] = null;
            $data['payroll_date'] = null;
            $data['claimed_at'] = null;
            $data['claimed_by'] = null;
        }

        // Align created_at with date_processed for accurate reporting periods
        $historicalDate = Carbon::parse($data['date_processed'])->setTime(9, 0, 0);
        $data['created_at'] = $historicalDate;

        $intake = BeneficiaryIntake::create($data);
        $intake->created_at = $historicalDate;
        $intake->save();

        return redirect()->route('admin.historical-data.financial-intake')
            ->with('success', "Historical Intake Record ({$intake->control_number}) for {$beneficiaryFullName} (Intake Date: {$historicalDate->format('M d, Y')}, Amount: ₱" . number_format($intake->recommended_amount, 2) . ") has been successfully encoded and saved.");
    }

    /**
     * AJAX endpoint to check 6-month validity relative to the entered historical date.
     */
    public function checkDuplicate(Request $request, FinancialDuplicateChecker $duplicateChecker)
    {
        $result = $duplicateChecker->checkDuplicate($request->all());
        return response()->json($result);
    }
}
