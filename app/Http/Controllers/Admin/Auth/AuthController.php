<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'role' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)
            ->where('status', 'active')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        $moduleRoleMap = [
            'Social Case Study' => ['admin', 'social_worker'],
            'Senior Citizen' => ['admin', 'staff', 'Senior Citizen officer'],
            'Financial Assistance Officer' => ['admin', 'staff', 'Financial assistance officer'],
            'Admin' => ['admin'],
        ];

        $moduleRedirects = [
            'Social Case Study' => 'admin.social-case.dashboard',
            'Senior Citizen' => 'admin.senior',
            'Financial Assistance Officer' => 'admin.financial',
            'Admin' => 'admin.dashboard',
        ];

        $selectedModule = $request->role;
        $allowedRoles = (array) ($moduleRoleMap[$selectedModule] ?? []);

        if (! empty($allowedRoles) && ! in_array($user->role->value, $allowedRoles, true)) {
            return back()->withErrors(['role' => "This account is not authorized for the selected role."])->withInput();
        }

        session([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_user_role' => $user->role->value,
            'admin_just_logged_in' => true,
        ]);

        $redirectRoute = $moduleRedirects[$selectedModule] ?? 'admin.dashboard';

        return redirect()->route($redirectRoute);
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
