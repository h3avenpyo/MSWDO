<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DashboardRedirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)
            ->where('status', 'active')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

<<<<<<< HEAD
        $moduleRoleMap = [
            'Social Case Study' => ['social_worker'],
            'Senior Citizen' => ['staff', 'Senior Citizen officer'],
            'Financial Assistance Officer' => ['staff', 'Financial assistance officer', 'financialstep1', 'financialstep2'],
            'Financial Step 1' => ['staff', 'financialstep1', 'Financial assistance officer'],
            'Financial Step 2' => ['staff', 'financialstep2', 'Financial assistance officer'],
            'Admin' => ['admin'],
        ];

        $moduleRedirects = [
            'Social Case Study' => 'admin.social-case.dashboard',
            'Senior Citizen' => 'admin.senior',
            'Financial Assistance Officer' => 'admin.financial.dashboard',
            'Financial Step 1' => 'admin.financial.dashboard',
            'Financial Step 2' => 'admin.financial.dashboard',
            'Admin' => 'admin.dashboard',
        ];

        $selectedModule = $request->role;
        $allowedRoles = (array) ($moduleRoleMap[$selectedModule] ?? []);

        if (! empty($allowedRoles) && ! in_array($user->role->value, $allowedRoles, true)) {
            return back()->withErrors(['role' => "This account is not authorized for the selected role."])->withInput();
        }

=======
>>>>>>> 5c79a03401b44599faa0ee97242d93d2ff55b903
        session([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_user_role' => $user->role->value,
            'admin_just_logged_in' => true,
        ]);

        return redirect()->route(DashboardRedirector::routeFor($user->role));
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_user_id', 'admin_user_name', 'admin_user_role']);

        return redirect()->route('admin.login.form');
    }

    public function clearWelcome(Request $request)
    {
        $request->session()->forget('admin_just_logged_in');
        return response()->json(['ok' => true]);
    }
}
