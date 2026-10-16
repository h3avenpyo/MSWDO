<?php

namespace App\Http\Controllers\Wcpu;

use App\Http\Controllers\Controller;
use App\Models\Wcpu\User;
use App\Models\Wcpu\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Older WCPU installations authenticate by email and do not have a
     * `username` column. Keep the registration form compatible with both
     * schemas while migrations are rolled out.
     */
    private function hasUsernameColumn(): bool
    {
        return Schema::connection('wcpu')->hasColumn('users', 'username');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $hasUsernameColumn = $this->hasUsernameColumn();
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'last_name'  => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'username'   => [
                'required',
                'string',
                'max:255',
                $hasUsernameColumn
                    ? Rule::unique(User::class, 'username')->whereNull('deleted_at')
                    : Rule::unique(User::class, 'email')->whereNull('deleted_at'),
            ],
            'email'      => [
                'nullable',
                'string',
                'max:255',
                Rule::unique(User::class, 'email')->whereNull('deleted_at'),
                ...($hasUsernameColumn ? [Rule::unique(User::class, 'username')->whereNull('deleted_at')] : []),
            ],
            'department' => 'required|string|max:255',
            'position'   => ['required', 'string', Rule::in(['Administrator', 'Desk Officer'])],
            'barangay'   => [
                'nullable',
                Rule::requiredIf(fn () => $request->input('position') !== 'Administrator' || !Auth::user()->isDepartmentAdmin()),
            ],
            'password'   => 'required|string|min:8|confirmed',
        ]);

        if ($validated['position'] === 'Administrator') {
            $validated['department'] = Auth::user()->isVawcAdmin() ? 'VAWC' : 'BCPC';
        } elseif ($validated['position'] === 'Desk Officer') {
            $deskOfficerDummy = new User(['department' => $validated['department']]);
            $assignedDepartments = $deskOfficerDummy->configuredCaseDepartments();

            // Normalize canonical department representation for desk officers
            if (count($assignedDepartments) > 1) {
                $validated['department'] = 'BCPC/VAWC';
            } elseif (in_array('BCPC', $assignedDepartments, true)) {
                $validated['department'] = 'BCPC';
            } else {
                $validated['department'] = 'VAWC';
            }

            $this->ensureAdminCanAssignDepartments($assignedDepartments);
        }

        try {
            if ($validated['position'] === 'Administrator' && Auth::user()->isDepartmentAdmin()) {
                $barangayToStore = null;
            } else {
                // Look up barangay by ID (the form select sends the ID)
                $barangayModel = Barangay::find($validated['barangay'] ?? null);

                // Fallback: try matching by name in case a name string was provided
                if (!$barangayModel) {
                    $barangayModel = Barangay::where('name', $validated['barangay'] ?? null)->first();
                }

                if (!$barangayModel) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['barangay' => 'Selected barangay not recognized. Please choose a valid barangay from the list.']);
                }

                // Use the canonical name stored in barangays table (this satisfies the FK)
                $barangayToStore = $barangayModel->name;
            }
            $username = $validated['username'];
            $email = !empty($validated['email']) ? $validated['email'] : $username;
            if (!$hasUsernameColumn) {
                unset($validated['username']);
            }

            $isDeskOfficer = $validated['position'] === 'Desk Officer';
            $assignedDepartments = $isDeskOfficer
                ? (new User(['department' => $validated['department']]))->configuredCaseDepartments()
                : ['VAWC', 'BCPC'];

            $user = User::create([
                'first_name'     => $validated['first_name'],
                'last_name'      => $validated['last_name'],
                ...($hasUsernameColumn ? ['username' => $username] : []),
                'email'          => $email,
                'position'       => $validated['position'],
                'department'     => $validated['department'],
                'barangay'       => $barangayToStore,
                'password'       => \Illuminate\Support\Facades\Hash::make($validated['password']),
                'is_active'      => true,
                'bcpc_is_active' => in_array('BCPC', $assignedDepartments, true),
                'vawc_is_active' => in_array('VAWC', $assignedDepartments, true),
            ]);
            // Log the user creation (Spatie Activity Log)
            activity('user')
                ->causedBy(Auth::user())
                ->performedOn($user)
                ->withProperties([
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now()->toDateTimeString(),
                    'user_id'    => $user->id,
                    'username'   => $user->email,
                    'position'   => $user->position,
                    'department' => $user->department,
                    'barangay'   => $user->barangay,
                ])
                ->log('Created new user account');

            return redirect()->route('admin.users.create')
                ->with('success', 'User "' . $user->first_name . ' ' . $user->last_name . '" created successfully!')
                ->with('created_user', [
                    'id'         => $user->id,
                    'name'       => $user->first_name . ' ' . $user->last_name,
                    'username'   => ($hasUsernameColumn ? $user->username : null) ?: $user->email,
                    'email'      => $user->email,
                    'position'   => $user->position,
                    'department' => $user->department,
                    'barangay'   => $user->barangay,
                ]);

        } catch (\Exception $e) {
            \Log::error('User creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'input'     => $request->all(),
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create user. ' . (config('app.debug') ? $e->getMessage() : 'Please try again.')]);
        }
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $this->ensureCanManageDeskOfficer($user);

            return response()->json([
                'success' => true,
                'user'    => [
                    'id'            => $user->id,
                    'first_name'    => $user->first_name,
                    'last_name'     => $user->last_name,
                    'email'         => $user->email,
                    'username'      => $user->username,
                    'position'      => $user->position,
                    'department'    => $user->department,
                    'barangay'      => $user->barangay,
                    'is_active'     => $user->is_active,
                    'bcpc_is_active' => $user->bcpc_is_active,
                    'vawc_is_active' => $user->vawc_is_active,
                    'deleted_at'    => $user->deleted_at,
                    'created_at'    => $user->created_at,
                    'updated_at'    => $user->updated_at,
                    'last_login_at' => $user->last_login_at, // <-- added
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error'   => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error fetching user details: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id'   => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user details',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanManageDeskOfficer($user);
        $hasUsernameColumn = $this->hasUsernameColumn();

        if (Auth::user()->isBcpcAdmin() || Auth::user()->isVawcAdmin()) {
            $request->merge(['position' => $user->position]);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'last_name'  => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'username'   => [
                'required',
                'string',
                'max:255',
                $hasUsernameColumn
                    ? Rule::unique(User::class, 'username')->ignore($user->id)
                    : Rule::unique(User::class, 'email')->ignore($user->id),
            ],
            'email'      => [
                'nullable',
                'string',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($user->id),
                ...($hasUsernameColumn ? [Rule::unique(User::class, 'username')->ignore($user->id)] : []),
            ],
            'position'   => ['nullable', 'string', Rule::in(['Administrator', 'Desk Officer'])],
            'department' => ['nullable', 'string', 'max:255'],
            'barangay'   => ['nullable'],
            'is_active' => ['nullable', 'boolean'],
            'bcpc_is_active' => ['nullable', 'boolean'],
            'vawc_is_active' => ['nullable', 'boolean'],
            'password'   => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*]/',
            ],
        ]);

        // Convert barangay ID to name if needed
        if (!empty($validated['barangay'])) {
            $barangayModel = \App\Models\Wcpu\Barangay::find($validated['barangay']);
            if ($barangayModel) {
                $validated['barangay'] = $barangayModel->name;
            }
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        if (empty($validated['email'])) {
            $validated['email'] = $validated['username'];
        }

        if (!$hasUsernameColumn) {
            unset($validated['username']);
        }

        $currentDepartments = $user->configuredCaseDepartments();
        $requestedDepartments = (new User(['department' => $validated['department']]))->configuredCaseDepartments();
        if ($requestedDepartments !== $currentDepartments) {
            abort_unless(
                empty(array_diff($currentDepartments, $this->adminCaseDepartments())),
                403
            );
            $this->ensureAdminCanAssignDepartments($requestedDepartments);
        }

        // Store the canonical dual-department value so the account is recognized
        // consistently as having both BCPC and VAWC access after an edit.
        if ($user->position === 'Desk Officer') {
            $validated['department'] = count($requestedDepartments) > 1
                ? 'BCPC/VAWC'
                : ($requestedDepartments[0] ?? 'VAWC');
        }

        foreach (['BCPC', 'VAWC'] as $department) {
            if (!in_array($department, $this->adminCaseDepartments(), true)) {
                unset($validated[strtolower($department) . '_is_active']);
            }
        }

        if ($user->position === 'Desk Officer') {
            $requestedAccountStatus = array_key_exists('is_active', $validated)
                ? (bool) $validated['is_active']
                : null;
            $departmentStatus = function (string $department) use ($requestedDepartments, $currentDepartments, $requestedAccountStatus, $validated, $user): bool {
                if (!in_array($department, $requestedDepartments, true)) {
                    return false;
                }

                if ($requestedAccountStatus !== null) {
                    return $requestedAccountStatus;
                }

                // A department added through Edit must be active when the
                // existing Desk Officer account is active, so it is available
                // immediately after selecting BCPC/VAWC.
                if (!in_array($department, $currentDepartments, true)) {
                    return (bool) ($user->is_active ?? true);
                }

                return (bool) ($validated[strtolower($department) . '_is_active']
                    ?? $user->{strtolower($department) . '_is_active'}
                    ?? true);
            };

            $bcpcIsActive = $departmentStatus('BCPC');
            $vawcIsActive = $departmentStatus('VAWC');

            $validated['bcpc_is_active'] = $bcpcIsActive;
            $validated['vawc_is_active'] = $vawcIsActive;
            $validated['is_active'] = $bcpcIsActive || $vawcIsActive;
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user'    => $user->fresh(),
        ]);
    }

    public function toggleActive(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanManageDeskOfficer($user);

        if ($user->id === optional(auth()->user())->id) {
            return response()->json(['success' => false, 'message' => 'You cannot disable your own account.'], 403);
        }

        $isActive = $request->boolean('is_active');

        if ($user->position === 'Desk Officer') {
            $statuses = ['is_active' => $isActive];
            foreach ($user->configuredCaseDepartments() as $department) {
                $statuses[strtolower($department) . '_is_active'] = $isActive;
            }
            $user->update($statuses);
        } else {
            $user->update(['is_active' => $isActive]);
        }

        return response()->json([
            'success' => true,
            'is_active' => (bool) $user->fresh()->is_active,
        ]);
    }

    public function toggleDepartmentAccess(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanManageDeskOfficer($user);
        $department = strtoupper($request->validate([
            'department' => ['required', Rule::in(['BCPC', 'VAWC'])],
            'is_active' => ['required', 'boolean'],
        ])['department']);

        abort_unless(in_array($department, $user->configuredCaseDepartments(), true), 404);
        abort_unless(in_array($department, $this->adminCaseDepartments(), true), 403);

        $departmentStatus = $request->boolean('is_active');
        $activeDepartments = collect($user->configuredCaseDepartments())->contains(function ($configuredDepartment) use ($department, $departmentStatus, $user) {
            return $configuredDepartment === $department
                ? $departmentStatus
                : (bool) $user->{strtolower($configuredDepartment) . '_is_active'};
        });

        $user->update([
            strtolower($department) . '_is_active' => $departmentStatus,
            'is_active' => $activeDepartments,
        ]);

        $updatedUser = $user->fresh();

        return response()->json([
            'success' => true,
            'is_active' => (bool) $updatedUser->is_active,
            'department_is_active' => (bool) $updatedUser->{strtolower($department) . '_is_active'},
        ]);
    }

    /**
     * Archive the specified user (soft delete the user).
     */
    public function archive($id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanManageDeskOfficer($user);

        if ($user->id === optional(auth()->user())->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot archive your own account.',
            ], 403);
        }

        try {
            $user->delete(); // Soft delete the user

            return response()->json([
                'success' => true,
                'message' => 'User archived successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to archive user: ' . $e->getMessage(), ['user_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive user.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Restore the specified archived user (restore the soft-deleted user).
     */
    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id); // Find the soft-deleted user
            $this->ensureCanManageDeskOfficer($user);
            $replacementExists = User::query()
                ->where(function ($query) use ($user) {
                    $query->where('email', $user->email)->orWhere('username', $user->username);
                })
                ->exists();
            if ($replacementExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This archived account cannot be restored because its email or username is now used by an active account.',
                ], 422);
            }
            $user->restore(); // Restore the user

            return response()->json([
                'success' => true,
                'message' => 'User restored successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to restore user: ' . $e->getMessage(), ['user_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore user.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Permanently delete the specified user (force delete the user).
     */
    public function forceDelete($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id); // Find the soft-deleted user
            $this->ensureCanManageDeskOfficer($user);

            DB::transaction(function () use ($user) {
                $replacementUserId = Auth::id();

                DB::table('cases')->where('created_by', $user->id)->update(['created_by' => $replacementUserId]);
                DB::table('cases')->where('updated_by', $user->id)->update(['updated_by' => null]);
                DB::table('cases')->where('assigned_to', $user->id)->update(['assigned_to' => $replacementUserId]);
                DB::table('case_notes')->where('user_id', $user->id)->update(['user_id' => $replacementUserId]);
                DB::table('case_referrals')->where('referred_by_user_id', $user->id)->update(['referred_by_user_id' => $replacementUserId]);
                DB::table('case_status_histories')->where('performed_by', $user->id)->update(['performed_by' => $replacementUserId]);
                DB::table('events')->where('created_by', $user->id)->update(['created_by' => $replacementUserId]);
                DB::table('quarterly_reports')->where('user_id', $user->id)->update(['user_id' => $replacementUserId]);
                DB::table('activity_log')->where('causer_type', User::class)->where('causer_id', $user->id)->update(['causer_id' => null, 'causer_type' => null]);
                DB::table('archived_users')->where('original_user_id', $user->id)->update(['original_user_id' => null]);
                DB::table('archived_users')->where('archived_by', $user->id)->update(['archived_by' => null]);
                DB::table('password_reset_requests')->where('user_id', $user->id)->delete();
                DB::table('password_reset_requests')->where('admin_id', $user->id)->update(['admin_id' => null]);
                DB::table('user_roles')->where('user_id', $user->id)->delete();
                $user->forceDelete();
            });

            return response()->json([
                'success' => true,
                'message' => 'User permanently deleted successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to permanently delete user: ' . $e->getMessage(), ['user_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete user.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    private function adminCaseDepartments(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        if (strtolower((string) $user->position) === 'administrator' || $user->hasRole('Administrator')) {
            return ['VAWC', 'BCPC'];
        }

        return $user->configuredCaseDepartments();
    }

    private function ensureCanManageDeskOfficer(User $user): void
    {
        abort_unless($user->position === 'Desk Officer', 403);

        abort_unless(
            count(array_intersect($this->adminCaseDepartments(), $user->configuredCaseDepartments())) > 0,
            403
        );
    }

    private function ensureAdminCanAssignDepartments(array $departments): void
    {
        abort_unless(empty(array_diff($departments, $this->adminCaseDepartments())), 403);
    }

    /**
     * Get active recipients from report_recipients table for case referral.
     */
    public function getRecipients()
    {
        try {
            $recipients = [
                [
                    'id' => 1,
                    'name' => 'Divine Joy Garcia',
                    'email' => 'divinejoy@example.com',
                    'role' => 'MSWD Staff',
                ],
                [
                    'id' => 2,
                    'name' => 'Charlot Reyes',
                    'email' => 'charlot@example.com',
                    'role' => 'BCPC Focal',
                ],
            ];

            return response()->json([
                'success' => true,
                'recipients' => $recipients,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching recipients: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading recipients',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
