<?php

namespace App\Http\Controllers\Wcpu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wcpu\User;

class AdminUserController extends Controller
{
    private function scopeToAdminDepartments($query)
    {
        $departments = auth()->user()?->configuredCaseDepartments() ?? [];

        // Include officers assigned to this department, including multirole officers.
        if (count($departments) === 1) {
            $adminDept = $departments[0];
            
            return $query->whereRaw('LOWER(department) LIKE ?', ['%' . strtolower($adminDept) . '%']);
        }

        // If admin has both departments, show all users
        return $query;
    }
    // ...existing code...

    public function ajaxTable(Request $request)
    {
        try {
            $query = $this->scopeToAdminDepartments(User::where('position', 'Desk Officer'));

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
                });
            }

            if ($request->filled('role')) {
                $query->where('position', $request->input('role'));
            }
            if ($request->filled('status')) {
                $query->where('is_active', $request->input('status'));
            }
            if ($request->filled('department')) {
                $query->where('department', $request->input('department'));
            }

            // Only select columns that exist in the users table
            $users = $query->select([
                'id',
                'first_name',
                'last_name',
                'email',
                'position',
                'department',
                'barangay',
                'is_active',
                // 'addtl_info', // Make sure this is NOT present
            ])->paginate(15);

            return view('admin.admin_users_table', compact('users'))->render();
        } catch (\Exception $e) {
            \Log::error('AJAX user table failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);
            // Show a simple error for AJAX
            return response()->json([
                'success' => false,
                'message' => 'AJAX user table failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = $this->scopeToAdminDepartments(User::where('position', 'Desk Officer'));
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }
        
        $users = $query->where('is_active', 1)
                   ->orderBy('created_at', 'desc')
                   ->paginate(10)
                   ->appends(['search' => $search]);
        
        // Check if this is an AJAX request
        if ($request->ajax() || $request->input('ajax')) {
            return view('admin.admin_users_table', compact('users'));
        }
        
        return view('admin.admin_users_table', compact('users'));
    }

    /**
     * Get all users as JSON for client-side filtering
     */
    public function getUsersJson(Request $request)
    {
        $hasUsername = \Illuminate\Support\Facades\Schema::connection('wcpu')->hasColumn('vawc_users', 'username')
            || \Illuminate\Support\Facades\Schema::connection('wcpu')->hasColumn('users', 'username');
        $columns = ['id', 'first_name', 'last_name', 'email', 'position', 'department', 'barangay', 'is_active', 'bcpc_is_active', 'vawc_is_active', 'created_at'];
        if ($hasUsername) {
            $columns[] = 'username';
        }

        $users = $this->scopeToAdminDepartments(User::where('position', 'Desk Officer'))
            ->select($columns)
            ->orderByDesc('updated_at')
            ->get()
            ->map(function($user) use ($hasUsername) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'username' => ($hasUsername ? $user->username : null) ?: $user->email,
                    'position' => $user->position,
                    'department' => $user->department,
                    'barangay_name' => $user->barangay ?? 'Not assigned',
                    'is_active' => $user->is_active,
                    'bcpc_is_active' => $user->bcpc_is_active,
                    'vawc_is_active' => $user->vawc_is_active,
                    'created_at' => $user->created_at,
                ];
            });

        return response()->json($users);
    }

    public function archive($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = false;
        $user->archived_at = now();
        $user->save();

        return response()->json(['success' => true]);
    }
}
