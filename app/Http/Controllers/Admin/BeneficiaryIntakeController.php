<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBeneficiaryIntakeRequest;
use App\Models\BeneficiaryIntake;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BeneficiaryIntakeController extends Controller
{
    public function index()
    {
        $intakes = BeneficiaryIntake::latest()->paginate(20);
        return view('admin.beneficiary-intake.index', compact('intakes'));
    }

    public function create()
    {
        // Generate control number
        $controlNumber = 'MSWDO-' . date('Y') . '-' . str_pad(BeneficiaryIntake::count() + 1, 5, '0', STR_PAD_LEFT);
        $encoder = session('admin_user_name') ?? 'Admin User';
        
        return view('admin.beneficiary-intake.create', compact('controlNumber', 'encoder'));
    }

    public function store(StoreBeneficiaryIntakeRequest $request)
    {
        $data = $request->validated();
        
        // Handle medical conditions array
        if (isset($data['medical_conditions'])) {
            $data['medical_conditions'] = json_encode($data['medical_conditions']);
        }
        
        // Handle beneficiary fields if client is beneficiary
        if ($data['is_client_beneficiary'] ?? true) {
            $data['beneficiary_last_name'] = null;
            $data['beneficiary_first_name'] = null;
            $data['beneficiary_middle_name'] = null;
            $data['beneficiary_birthday'] = null;
            $data['beneficiary_age'] = null;
            $data['beneficiary_sex'] = null;
            $data['beneficiary_barangay'] = null;
            $data['beneficiary_relationship'] = null;
        }
        
        BeneficiaryIntake::create($data);
        
        return redirect()->route('admin.beneficiary-intake.index')
            ->with('success', 'Beneficiary intake form has been saved successfully.');
    }

    public function show(BeneficiaryIntake $intake)
    {
        return view('admin.beneficiary-intake.show', compact('intake'));
    }
}
