<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Officer\StoreOfficerRequest;
use App\Http\Requests\Admin\Officer\UpdateOfficerRequest;
use App\Models\User;
use App\Models\Wcpu\User as WcpuUser;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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

        return view('admin.mswdo-admin.add-officers', compact('officerCreated'));
    }

    public function officersDirectory(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');
        $status = $request->get('status');
        $perPage = (int) $request->get('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }

        // Map display role names to database values.
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

        // ---- MSWDO officers ----
        $query = User::where('email', '!=', 'admin@mswdo.test')
            ->select('id', 'name', 'email', 'role', 'phone', 'created_at', 'status');

        if ($search) {
            $matchingRoles = [];
            foreach (UserRole::cases() as $case) {
                if (stripos($case->value, $search) !== false || stripos($case->label(), $search) !== false) {
                    $matchingRoles[] = $case->value;
                }
            }

            $query->where(function ($q) use ($search, $matchingRoles) {
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
            $dbRole = $roleMap[$role] ?? $role;
            $query->where('role', $dbRole);
        }

        if ($status && in_array(strtolower($status), ['active', 'inactive'])) {
            $query->where('status', strtolower($status));
        }

        $mswdoOfficers = $query->orderByDesc('created_at')->get()->map(function ($officer) {
            return (object) [
                'id' => $officer->id,
                'raw_id' => $officer->id,
                'source' => 'mswdo',
                'name' => $officer->name,
                'email' => $officer->email,
                'role' => is_object($officer->role) ? $officer->role->value : (string) $officer->role,
                'phone' => $officer->phone,
                'status' => is_object($officer->status) ? $officer->status->value : (string) $officer->status,
                'created_at' => $officer->created_at,
                'portal' => null,
            ];
        });

        // ---- WCPU administrators (BCPC / SWD) ----
        $wcpuAll = collect();
        try {
            $wcpuAll = WcpuUser::query()
                ->select('id', 'first_name', 'last_name', 'email', 'phone_number', 'position', 'department', 'is_active', 'bcpc_is_active', 'vawc_is_active', 'created_at')
                ->where('position', 'Administrator')
                ->get();
        } catch (\Throwable $e) {
            $wcpuAll = collect();
        }

        // WCPU accounts are always administrators; hide them when filtering other roles.
        $wcpuOfficers = ($role && $role !== 'All Roles' && ($roleMap[$role] ?? $role) !== 'admin')
            ? collect()
            : $wcpuAll->map(function ($officer) {
                return (object) [
                    'id' => 'wcpu-' . $officer->id,
                    'raw_id' => $officer->id,
                    'source' => 'wcpu',
                    'name' => $officer->full_name,
                    'email' => $officer->email,
                    'role' => 'admin',
                    'phone' => $officer->phone_number,
                    'status' => $officer->is_active ? 'active' : 'inactive',
                    'created_at' => $officer->created_at,
                    'portal' => $officer->isBcpcAdmin() ? 'BCPC' : 'SWD',
                ];
            });

        if ($status && in_array(strtolower($status), ['active', 'inactive'])) {
            $wantActive = strtolower($status) === 'active';
            $wcpuOfficers = $wcpuOfficers->filter(fn ($officer) => ($officer->status === 'active') === $wantActive)->values();
        }

        if ($search) {
            $needle = strtolower($search);
            $wcpuOfficers = $wcpuOfficers->filter(function ($officer) use ($needle) {
                return str_contains(strtolower($officer->name), $needle)
                    || str_contains(strtolower((string) $officer->email), $needle)
                    || str_contains(strtolower((string) $officer->phone), $needle)
                    || str_contains(strtolower((string) $officer->portal), $needle)
                    || str_contains(strtolower($officer->status), $needle)
                    || str_contains('administrator', $needle);
            })->values();
        }

        $all = $mswdoOfficers->concat($wcpuOfficers)
            ->sortByDesc(fn ($officer) => optional($officer->created_at)->timestamp ?? 0)
            ->values();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $officers = new LengthAwarePaginator(
            $all->forPage($currentPage, $perPage)->values(),
            $all->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        // ---- Stats (include WCPU admins) ----
        $wcpuActiveCount = $wcpuAll->where('is_active', true)->count();
        $wcpuInactiveCount = $wcpuAll->where('is_active', false)->count();

        $officerStats = [
            'total' => User::where('email', '!=', 'admin@mswdo.test')->count() + $wcpuAll->count(),
            'admins' => User::where('role', 'admin')->where('email', '!=', 'admin@mswdo.test')->count() + $wcpuAll->count(),
            'socialWorkers' => User::whereIn('role', ['social_worker', 'eligibility_checker'])->count(),
            'active' => User::where('status', 'active')->where('email', '!=', 'admin@mswdo.test')->count() + $wcpuActiveCount,
            'inactive' => User::where('status', 'inactive')->where('email', '!=', 'admin@mswdo.test')->count() + $wcpuInactiveCount,
        ];

        return view('admin.mswdo-admin.officers-directory', compact('officers', 'officerStats'));
    }

    public function storeOfficer(StoreOfficerRequest $request)
    {
        if (in_array($request->role, ['bcpc_admin', 'swd_admin'], true)) {
            $nameParts = preg_split('/\s+/', trim($request->name), 2);
            $attributes = [
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone,
                'position' => 'Administrator',
                'department' => $request->role === 'bcpc_admin' ? 'BCPC' : 'VAWC',
                'barangay' => null,
                'is_active' => $request->status !== 'inactive',
                'bcpc_is_active' => $request->role === 'bcpc_admin',
                'vawc_is_active' => $request->role === 'swd_admin',
            ];
            $model = new WcpuUser();
            if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'username')) {
                $attributes['username'] = $request->email;
            }
            WcpuUser::create($attributes);

            return redirect()->route('admin.add-officers')->with('success', 'Officer created successfully.')->with('officer_created', true);
        }

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
        return view('admin.mswdo-admin.edit-officer', compact('officer'));
    }

    public function updateOfficer(UpdateOfficerRequest $request, $id)
    {
        $officer = User::findOrFail($id);

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

    public function wcpuEditOfficer($id)
    {
        $officer = WcpuUser::findOrFail($id);

        return view('admin.mswdo-admin.edit-wcpu-officer', compact('officer'));
    }

    public function wcpuUpdateOfficer(Request $request, $id)
    {
        $officer = WcpuUser::findOrFail($id);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $emailTaken = WcpuUser::where('email', $validated['email'])
            ->where('id', '!=', $officer->id)
            ->exists();

        if ($emailTaken) {
            return back()
                ->withErrors(['email' => 'The email address has already been taken.'])
                ->withInput();
        }

        $officer->first_name = $validated['first_name'];
        $officer->last_name = $validated['last_name'];
        $officer->email = $validated['email'];
        $officer->phone_number = $validated['phone_number'] ?? null;
        $officer->is_active = $validated['status'] === 'active';
        $officer->save();

        return redirect()->route('admin.officers-directory')->with('success', 'Officer updated successfully.');
    }

    public function wcpuDeactivateOfficer($id)
    {
        $officer = WcpuUser::findOrFail($id);
        $officer->is_active = false;
        $officer->save();

        return redirect()->route('admin.officers-directory')->with('success', 'Officer deactivated successfully.');
    }

    public function wcpuActivateOfficer($id)
    {
        $officer = WcpuUser::findOrFail($id);
        $officer->is_active = true;
        $officer->save();

        return redirect()->route('admin.officers-directory')->with('success', 'Officer activated successfully.');
    }
}
