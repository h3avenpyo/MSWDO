<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OfficerController extends Controller
{
    public function addOfficers()
    {
        $officerCreated = session('officer_created', false);

        if ($officerCreated) {
            session()->forget('officer_created');
        }

        return view('admin.add-officers', compact('officerCreated'));
    }

    public function officersDirectory(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');
        $status = $request->get('status');
        $perPage = $request->get('per_page', 10);

        $query = User::where('email', '!=', 'admin@mswdo.test')
            ->select('id', 'name', 'email', 'role', 'phone', 'created_at', 'status');

        if ($search) {
            $matchingRoles = [];
            foreach (UserRole::cases() as $case) {
                if (stripos($case->value, $search) !== false || stripos($case->label(), $search) !== false) {
                    $matchingRoles[] = $case->value;
                }
            }

            $query->where(function($q) use ($search, $matchingRoles) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");

                if (!empty($matchingRoles)) {
                    $q->orWhereIn('role', $matchingRoles);
                } else {
                    $q->orWhere('role', 'like', "%{$search}%");
                }
            });
        }

        if ($role && $role !== 'All Roles') {
            // Map display names to database values
            $roleMap = [
                'Administrator' => 'admin',
                'Admin' => 'admin',
                'admin' => 'admin',
                'Social Case Worker (Encoder)' => 'social_worker',
                'Social Worker (Encoder)' => 'social_worker',
                'social_worker' => 'social_worker',
                'Social Case Worker (Checker)' => 'eligibility_checker',
                'Social Worker (Checker)' => 'eligibility_checker',
                'eligibility_checker' => 'eligibility_checker',
                'Encoder' => 'encoder',
                'encoder' => 'encoder',
                'Staff' => 'staff',
                'staff' => 'staff',
                'Senior Citizen Officer' => 'Senior Citizen officer',
                'Senior Citizen' => 'Senior Citizen officer',
                'Senior Citizen officer' => 'Senior Citizen officer',
                'Financial Assistance Officer' => 'Financial assistance officer',
                'Financial Assistance Step 1' => 'financialstep1',
                'Financial Step 1' => 'financialstep1',
                'financialstep1' => 'financialstep1',
                'Financial Assistance Step 2' => 'financialstep2',
                'Financial Step 2' => 'financialstep2',
                'financialstep2' => 'financialstep2',
            ];
            
            $dbRole = $roleMap[$role] ?? $role;
            $query->where('role', $dbRole);
        }

        if ($status && in_array(strtolower($status), ['active', 'inactive'])) {
            $query->where('status', strtolower($status));
        }

        $officers = $query->orderByDesc('created_at')->paginate($perPage);

        $officerStats = [
            'total' => User::where('email', '!=', 'admin@mswdo.test')->count(),
            'admins' => User::where('role', 'admin')->where('email', '!=', 'admin@mswdo.test')->count(),
            'socialWorkers' => User::whereIn('role', ['social_worker', 'eligibility_checker'])->count(),
            'active' => User::where('status', 'active')->where('email', '!=', 'admin@mswdo.test')->count(),
            'inactive' => User::where('status', 'inactive')->where('email', '!=', 'admin@mswdo.test')->count(),
        ];

        return view('admin.officers-directory', compact('officers', 'officerStats'));
    }

    public function storeOfficer(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                $exists = User::
                    whereRaw('LOWER(name) = ?', [strtolower($value)])
                    ->exists();
                if ($exists) {
                    $fail('An officer with this name already exists.');
                }
            }],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'in:active,inactive'],
            'signature_position' => ['nullable', 'in:osca_head,mswdo_officer,mswdo_staff'],
            'signature_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $signatureImagePath = null;
        if ($request->hasFile('signature_image')) {
            $file = $request->file('signature_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/signatures'), $filename);
            $signatureImagePath = 'images/signatures/' . $filename;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => $request->status === 'inactive' ? \App\Enums\UserStatus::Inactive : \App\Enums\UserStatus::Active,
            'signature_position' => $request->signature_position,
            'signature_image' => $signatureImagePath,
        ]);

        return redirect()->route('admin.add-officers')->with('success', 'Officer created successfully.')->with('officer_created', true);
    }

    public function editOfficer($id)
    {
        $officer = User::findOrFail($id);
        return view('admin.edit-officer', compact('officer'));
    }

    public function updateOfficer(Request $request, $id)
    {
        $officer = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) use ($officer) {
                $exists = User::
                    whereRaw('LOWER(name) = ?', [strtolower($value)])
                    ->where('id', '!=', $officer->id)
                    ->exists();
                if ($exists) {
                    $fail('An officer with this name already exists.');
                }
            }],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($officer->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'in:active,inactive'],
            'signature_position' => ['nullable', 'in:osca_head,mswdo_officer,mswdo_staff'],
            'signature_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $signatureImagePath = $officer->signature_image;
        if ($request->hasFile('signature_image')) {
            // Delete old signature if exists
            if ($officer->signature_image && file_exists(public_path($officer->signature_image))) {
                unlink(public_path($officer->signature_image));
            }
            $file = $request->file('signature_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/signatures'), $filename);
            $signatureImagePath = 'images/signatures/' . $filename;
        }

        $officer->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => $request->status ? ($request->status === 'active' ? \App\Enums\UserStatus::Active : \App\Enums\UserStatus::Inactive) : $officer->status,
            'signature_position' => $request->signature_position,
            'signature_image' => $signatureImagePath,
        ]);

        return redirect()->route('admin.officers-directory')->with('success', 'Officer updated successfully.');
    }

    public function deactivateOfficer($id)
    {
        $officer = User::findOrFail($id);
        $officer->status = \App\Enums\UserStatus::Inactive;
        $officer->save();
        return redirect()->route('admin.officers-directory')->with('success', 'Officer deactivated successfully.');
    }

    public function activateOfficer($id)
    {
        $officer = User::findOrFail($id);
        $officer->status = \App\Enums\UserStatus::Active;
        $officer->save();
        return redirect()->route('admin.officers-directory')->with('success', 'Officer activated successfully.');
    }
}
