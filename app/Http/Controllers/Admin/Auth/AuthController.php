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

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        // Check if account is deactivated
        $status = is_object($user->status) ? $user->status->value : $user->status;
        if ($status === 'inactive') {
            return back()->with('account_deactivated', true)
                ->with('message', 'Your account has been deactivated. Please contact the administrator.')
                ->withInput();
        }

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

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

    public function checkAccountStatus(Request $request)
    {
        // Log for debugging
        \Log::info('Account status check called', [
            'session_user_id' => session('admin_user_id'),
            'all_session' => session()->all()
        ]);

        if (!session('admin_user_id')) {
            return response()->json(['deactivated' => false, 'message' => 'No user in session']);
        }

        $user = User::find(session('admin_user_id'));
        if (!$user) {
            return response()->json(['deactivated' => true, 'message' => 'User not found']);
        }

        $status = is_object($user->status) ? $user->status->value : $user->status;
        $isDeactivated = $status === 'inactive';

        \Log::info('Account status check result', [
            'user_id' => $user->id,
            'status' => $status,
            'is_deactivated' => $isDeactivated
        ]);

        return response()->json(['deactivated' => $isDeactivated, 'status' => $status]);
    }
}
