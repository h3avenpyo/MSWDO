<?php

namespace App\Http\Controllers\Admin\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialCase\StoreBeneficiaryIntakeRequest;
use App\Http\Requests\SocialCase\UpdateBeneficiaryIntakeRequest;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\Client;
use App\Services\SocialCase\EligibilityChecker;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinancialIntakeController extends Controller
{
    /**
     * Display a listing of beneficiary intakes with search and filtering for Financial Module.
     */
    public function index(Request $request)
    {
        $query = BeneficiaryIntake::query();

        // Search by name (beneficiary or representative) or control number
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('control_number', 'like', "%{$search}%")
                  ->orWhere('beneficiary_first_name', 'like', "%{$search}%")
                  ->orWhere('beneficiary_last_name', 'like', "%{$search}%")
                  ->orWhere('beneficiary_middle_name', 'like', "%{$search}%")
                  ->orWhere('rep_first_name', 'like', "%{$search}%")
                  ->orWhere('rep_last_name', 'like', "%{$search}%");
            });
        }

        // Filter by Barangay
        if ($request->filled('barangay') && $request->barangay !== 'All') {
            $query->where('beneficiary_barangay', $request->barangay);
        }

        // Filter by Beneficiary Category
        if ($request->filled('category') && $request->category !== 'All') {
            $cat = $request->category;
            $query->where(function($q) use ($cat) {
                $q->where('beneficiary_category', $cat)
                  ->orWhereJsonContains('beneficiary_categories', $cat);
            });
        }

        $intakes = $query->latest()->paginate(15)->withQueryString();

        $barangays = [
            'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Acacia', 'Anabu',
            'Balite I', 'Balite II', 'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho',
            'Caballero', 'Carmen', 'Hukay', 'Iba', 'Kalubkob', 'Kaong', 'Lalaan I',
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
            'Indigent Resident',
            'Other',
        ];

        return view('admin.beneficiary-intake.index', compact('intakes', 'barangays', 'categories'));
    }

    /**
     * Show the form for creating a new intake sheet in Financial Module.
     */
    public function create(EligibilityChecker $checker, ?Client $client = null)
    {
        if ($client && ! $checker->check($client)['eligible']) {
            return redirect()->route('admin.social-case-eligibility.show', $client)
                ->with('error', 'This client is not eligible to proceed to beneficiary intake.');
        }

        $controlNumber = 'MSWDO-' . date('Y') . '-' . str_pad(BeneficiaryIntake::count() + 1, 5, '0', STR_PAD_LEFT);
        $encoder = session('admin_user_name') ?? 'Admin User';

        $barangays = [
            'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Acacia', 'Anabu',
            'Balite I', 'Balite II', 'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho',
            'Caballero', 'Carmen', 'Hukay', 'Iba', 'Kalubkob', 'Kaong', 'Lalaan I',
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
            'Burial Assistance',
            'Educational Assistance',
            'Other Medical Conditions',
        ];

        return view('admin.beneficiary-intake.create', compact(
            'controlNumber',
            'encoder',
            'client',
            'barangays',
            'categories',
            'relationships',
            'medicalConditions'
        ));
    }

    /**
     * Store a newly created intake sheet in database for Financial Module.
     */
    public function store(StoreBeneficiaryIntakeRequest $request, EligibilityChecker $checker)
    {
        $data = $request->validated();

        if (! empty($data['beneficiary_birthday'])) {
            $data['beneficiary_age'] = Carbon::parse($data['beneficiary_birthday'])->age;
        }

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
            $data['rep_age'] = Carbon::parse($data['rep_birthday'])->age;
        }

        if (isset($data['family_composition']) && is_array($data['family_composition'])) {
            $data['family_composition'] = array_values(array_filter($data['family_composition'], function ($row) {
                return ! empty($row['name']);
            }));
        }

        $userId = session('admin_user_id') ?? auth()->id();
        $data['encoder'] = ($userId && \App\Models\User::where('id', $userId)->exists()) ? $userId : null;

        $data['beneficiary_city'] = $data['beneficiary_city'] ?? 'Silang';
        $data['beneficiary_province'] = $data['beneficiary_province'] ?? 'Cavite';
        $data['beneficiary_region'] = $data['beneficiary_region'] ?? 'Region IV-A';
        $data['service_provided'] = $data['service_provided'] ?? ($data['recommended_assistance_type'] ?? 'Financial Assistance Intake');
        $data['purpose'] = $data['purpose'] ?? ($data['assistance_purpose'] ?? 'General Assistance Request');
        $data['submitted_to'] = $data['submitted_to'] ?? 'MSWDO Silang Main Office';

        $client = ! empty($data['client_id'])
            ? Client::find($data['client_id'])
            : null;

        if ($client && ! $checker->check($client)['eligible']) {
            return redirect()->route('admin.social-case-eligibility.show', $client)
                ->with('error', 'This client is not eligible to proceed to intake.');
        }

        $intake = BeneficiaryIntake::create($data);

        return redirect()->route('admin.beneficiary-intake.show', $intake)
            ->with('success', 'General Intake Sheet has been successfully saved.');
    }

    /**
     * Display the specified intake sheet.
     */
    public function show(BeneficiaryIntake $intake)
    {
        return view('admin.beneficiary-intake.show', compact('intake'));
    }

    /**
     * Show the form for editing the specified intake sheet in Financial Module.
     */
    public function edit(BeneficiaryIntake $intake)
    {
        $barangays = [
            'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Acacia', 'Anabu',
            'Balite I', 'Balite II', 'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho',
            'Caballero', 'Carmen', 'Hukay', 'Iba', 'Kalubkob', 'Kaong', 'Lalaan I',
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
            'Burial Assistance',
            'Educational Assistance',
            'Other Medical Conditions',
        ];

        return view('admin.beneficiary-intake.edit', compact(
            'intake',
            'barangays',
            'categories',
            'relationships',
            'medicalConditions'
        ));
    }

    /**
     * Update the specified intake sheet in database for Financial Module.
     */
    public function update(UpdateBeneficiaryIntakeRequest $request, BeneficiaryIntake $intake)
    {
        $data = $request->validated();

        if (! empty($data['beneficiary_birthday'])) {
            $data['beneficiary_age'] = Carbon::parse($data['beneficiary_birthday'])->age;
        }

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
            $data['rep_age'] = Carbon::parse($data['rep_birthday'])->age;
        }

        if (isset($data['family_composition']) && is_array($data['family_composition'])) {
            $data['family_composition'] = array_values(array_filter($data['family_composition'], function ($row) {
                return ! empty($row['name']);
            }));
        }

        $userId = session('admin_user_id') ?? auth()->id();
        if ($userId && \App\Models\User::where('id', $userId)->exists()) {
            $data['encoder'] = $userId;
        }

        $intake->update($data);

        return redirect()->route('admin.beneficiary-intake.show', $intake)
            ->with('success', 'General Intake Sheet has been updated successfully.');
    }

    /**
     * Remove the specified intake sheet from database.
     */
    public function destroy(BeneficiaryIntake $intake)
    {
        $intake->delete();

        return redirect()->route('admin.beneficiary-intake.index')
            ->with('success', 'Intake Sheet record has been deleted successfully.');
    }
}
