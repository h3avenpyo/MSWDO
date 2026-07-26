<?php

namespace App\Http\Controllers\Admin\Financial;

use App\Http\Controllers\Controller;
use App\Models\SocialCase\BeneficiaryIntake;
use Illuminate\Http\Request;

class FinancialDashboardController extends Controller
{
    public function financialDashboard()
    {
        $totalIntakes = class_exists(BeneficiaryIntake::class) 
            ? BeneficiaryIntake::count() 
            : 0;
        $recentIntakes = class_exists(BeneficiaryIntake::class) 
            ? BeneficiaryIntake::latest()->take(5)->get() 
            : collect();

        return view('admin.financial.financial-dashboard', compact('totalIntakes', 'recentIntakes'));
    }

    public function financialStep1()
    {
        $totalIntakes = class_exists(BeneficiaryIntake::class) 
            ? BeneficiaryIntake::count() 
            : 0;
        $recentIntakes = class_exists(BeneficiaryIntake::class) 
            ? BeneficiaryIntake::latest()->paginate(10)
            : collect();

        return view('admin.financial.financialstep1', compact('totalIntakes', 'recentIntakes'));
    }

    public function financialStep2()
    {
        return view('admin.financial.financialstep2');
    }
}
