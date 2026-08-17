<?php

namespace App\Http\Controllers\Admin\SocialCase;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialCase\StoreBeneficiaryIntakeRequest;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\Client;
use App\Services\SocialCase\EligibilityChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BeneficiaryIntakeController extends Controller
{
    public function index()
    {
        $intakes = BeneficiaryIntake::latest()->paginate(20);
        return view('admin.beneficiary-intake.index', compact('intakes'));
    }

    public function create(EligibilityChecker $checker, ?Client $client = null)
    {
        if ($client && ! $checker->check($client)['eligible']) {
            return redirect()->route('admin.beneficiary-intake.create', $client)
                ->with('error', 'This client is not eligible to proceed to beneficiary intake.');
        }

        // Generate control number
        $controlNumber = 'MSWDO-' . date('Y') . '-' . str_pad(BeneficiaryIntake::count() + 1, 5, '0', STR_PAD_LEFT);
        $encoder = session('admin_user_name') ?? 'Admin User';
        
        return view('admin.beneficiary-intake.create', compact('controlNumber', 'encoder', 'client'));
    }

    public function store(StoreBeneficiaryIntakeRequest $request, EligibilityChecker $checker)
    {
        $data = $request->validated();

        if ($request->boolean('is_client_beneficiary')) {
            $data['beneficiary_last_name'] = null;
            $data['beneficiary_first_name'] = null;
            $data['beneficiary_middle_name'] = null;
            $data['beneficiary_birthday'] = null;
            $data['beneficiary_age'] = null;
            $data['beneficiary_sex'] = null;
            $data['beneficiary_barangay'] = null;
            $data['beneficiary_relationship'] = null;
        }

        $client = ! empty($data['client_id'])
            ? Client::findOrFail($data['client_id'])
            : null;

        if ($client && ! $checker->check($client)['eligible']) {
            return redirect()->route('admin.beneficiary-intake.create', $client)
                ->with('error', 'This client is not eligible to proceed to case study creation.');
        }

        $intake = BeneficiaryIntake::create($data);

        if (! $client) {
            return redirect()->route('admin.beneficiary-intake.index')
                ->with('success', 'Beneficiary intake form has been saved successfully.');
        }

        return redirect()->route('admin.social-case.new')
            ->with('success', 'Beneficiary intake saved. Continue with case study requirements.');
    }

    public function show(BeneficiaryIntake $intake)
    {
        return view('admin.beneficiary-intake.show', compact('intake'));
    }
}
