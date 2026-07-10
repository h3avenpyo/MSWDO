<?php

namespace App\Http\Controllers\Admin;

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

        $user = User::on('mswdo_admin')
            ->where('email', $request->email)
            ->where('status', 'active')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        $roleMap = [
            'Senior Citizen' => 'Senior Citizen officer',
            'Financial Assistance Officer' => 'Financial assistance officer',
            'Social Case Study' => 'Social Case Study officer',
            'Admin' => 'Admin',
        ];

        $expectedRole = $roleMap[$request->role] ?? null;

        if ($expectedRole && $user->role !== $expectedRole) {
            return back()->withErrors(['role' => "This account is not authorized for the selected role. User role: {$user->role}, Expected: {$expectedRole}"])->withInput();
        }

        session([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_user_role' => $user->role,
            'admin_just_logged_in' => true,
        ]);

        if ($user->role === 'Financial assistance officer') {
            return redirect()->route('admin.financial');
        }

        if ($user->role === 'Senior Citizen officer') {
            return redirect()->route('admin.senior');
        }

        if ($user->role === 'Social Case Study officer') {
            return redirect()->route('admin.social-case.dashboard');
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_user_id', 'admin_user_name', 'admin_user_role']);

        return redirect()->route('admin.login.form');
    }
}
