<?php

namespace App\Http\Controllers;

use App\Http\Requests\SocialCase\StoreBeneficiaryIntakeRequest;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Services\Financial\FinancialDuplicateChecker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialAssistanceController extends Controller
{
    /**
     * Show the public Financial Assistance intake application form.
     */
    public function create()
    {
        $controlNumber = 'MSWDO-' . date('Y') . '-' . str_pad(BeneficiaryIntake::count() + 1, 5, '0', STR_PAD_LEFT);

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
            'Senior Citizen',
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
            'Hospital Bill / Medical Needs',
            'Cancer',
            'Cardiovascular',
            'Kidney Diseases',
            'Neurological Disorders',
            'Respiratory Diseases',
            'Infectious Disease',
            'Diabetes',
            'Surgical',
            'Trauma and Injury',
            'Other Medical Conditions',
        ];

        return view('financial-assistance', compact(
            'controlNumber',
            'barangays',
            'categories',
            'relationships',
            'medicalConditions'
        ));
    }

    /**
     * Real-time duplicate check endpoint for public intake form.
     */
    public function checkDuplicate(Request $request, FinancialDuplicateChecker $duplicateChecker)
    {
        $data = $request->all();
        $data['date_processed'] = Carbon::today()->toDateString();
        $res = $duplicateChecker->checkDuplicate($data);
        return response()->json($res);
    }

    /**
     * Store a client-submitted Financial Assistance intake application.
     */
    public function store(StoreBeneficiaryIntakeRequest $request, FinancialDuplicateChecker $duplicateChecker)
    {
        $data = $request->validated();
        $data['date_processed'] = Carbon::today()->toDateString();

        // Strict 6-Month Validity Policy Check
        $duplicateCheck = $duplicateChecker->checkDuplicate($data);
        if ($duplicateCheck['is_duplicate']) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'is_duplicate' => true,
                    'message' => $duplicateCheck['warning_message'],
                    'matches' => $duplicateCheck['matches'],
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('duplicate_check_error', $duplicateCheck['warning_message'])
                ->with('duplicate_matches', $duplicateCheck['matches']);
        }

        // Beneficiary Age computation
        if (!empty($data['beneficiary_birthday'])) {
            $data['beneficiary_age'] = Carbon::parse($data['beneficiary_birthday'])->age;
        }

        // Representative cleanup if disabled
        if (!$request->boolean('has_representative')) {
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
        } else if (!empty($data['rep_birthday'])) {
            $data['rep_age'] = Carbon::parse($data['rep_birthday'])->age;
        }

        // Family composition cleanup
        if (isset($data['family_composition']) && is_array($data['family_composition'])) {
            $data['family_composition'] = array_values(array_filter($data['family_composition'], function ($row) {
                return !empty($row['name']);
            }));
        }

        // Ensure unique control number
        if (BeneficiaryIntake::where('control_number', $data['control_number'])->exists()) {
            $data['control_number'] = 'MSWDO-' . date('Y') . '-' . str_pad(BeneficiaryIntake::count() + 1, 5, '0', STR_PAD_LEFT);
        }

        $data['beneficiary_city'] = $data['beneficiary_city'] ?? 'Silang';
        $data['beneficiary_province'] = $data['beneficiary_province'] ?? 'Cavite';
        $data['beneficiary_region'] = $data['beneficiary_region'] ?? 'Region IV-A';
        $data['service_provided'] = 'Financial Assistance Intake';
        $data['purpose'] = $data['assistance_purpose'] ?? 'General Assistance Request';
        $data['submitted_to'] = 'MSWDO Silang Main Office';

        $intake = BeneficiaryIntake::create($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'control_number' => $intake->control_number,
                'beneficiary_name' => $intake->beneficiary_full_name,
                'message' => 'Your financial assistance intake application has been submitted successfully.',
                'data' => [
                    'id' => $intake->id,
                    'control_number' => $intake->control_number,
                    'beneficiary_name' => $intake->beneficiary_full_name,
                    'date_processed' => Carbon::parse($intake->date_processed)->format('F d, Y'),
                    'barangay' => $intake->beneficiary_barangay,
                    'purpose' => $intake->display_assistance_purpose,
                ]
            ]);
        }

        return redirect()->route('financial-assistance.create')
            ->with('success', 'Your intake application has been submitted successfully. Control Number: ' . $intake->control_number)
            ->with('intake_submitted', $intake);
    }
}
