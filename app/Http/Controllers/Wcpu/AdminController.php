<?php

namespace App\Http\Controllers\Wcpu;

use App\Http\Controllers\Controller;
use App\Models\Wcpu\CaseModel;
use App\Models\Wcpu\User;
use App\Models\Wcpu\CaseStatus;
use App\Models\Wcpu\CaseType;
use App\Models\Wcpu\Barangay;
use App\Models\Wcpu\BcpcCaseMonitoring;
use App\Models\Wcpu\QuarterlyReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with statistics and recent cases.
     */
    /**
     * Get color for status
     * 
     * @param string $status The status to get the color for
     * @return string The color code
     */
    public static function getStatusColor($status)
    {
        $colors = [
            'Case Referred' => 'fd7e14',  // Orange
            'Case Filed' => '28a745',  // Green
            // Legacy support for old statuses
            'Pending' => 'ffc107',  // Yellow
            'In Progress' => '17a2b8',  // Teal
            'Resolved' => '28a745',  // Green
            'Closed' => '6c757d',  // Gray
            'Referred' => 'fd7e14',  // Orange
        ];
        
        return $colors[$status] ?? '6c757d';  // Default to gray
    }
    /**
     * Get color for abuse type
     * 
     * @param string $type The abuse type to get the color for
     * @return string The color code
     */
    public static function getAbuseTypeColor($type)
    {
        $colors = [
            'RA 11930' => 'dc3545',  // Red
            'RA 9262' => '6f42c1',  // Purple
            'RA 8353' => 'e83e8c',  // Pink
            'RA 7610' => '20c997',  // Teal
            'RA 9208/10364' => 'fd7e14',  // Orange
            'RA 7877' => '17a2b8',  // Cyan
            'RA 11313' => '6610f2',  // Indigo
            'RA 11596' => '6c757d',  // Gray
            'RA 9775' => '6f42c1',  // Purple
            'RA 9344' => 'e83e8c',  // Pink
        ];
        
        return $colors[$type] ?? '6c757d';  // Default to gray
    }
    
    public function dashboard()
    {
        $isSwdAdmin = Auth::user()->isVawcAdmin();
        $isBcpcAdmin = Auth::user()->isBcpcAdmin();
        $swdDepartment = Auth::user()->defaultCaseDepartment();
        $dashboardCaseDepartment = $isSwdAdmin ? $swdDepartment : ($isBcpcAdmin ? 'BCPC' : null);

        // Debug: Check database connection and tables
        try {
            // Check database connection
            DB::connection()->getPdo();
            
            // Check if tables exist and have data
            $tables = ['cases', 'users', 'barangays'];
            $tableInfo = [];
            
            foreach ($tables as $table) {
                try {
                    $count = DB::table($table)->count();
                    $tableInfo[$table] = [
                        'exists' => true,
                        'count' => $count,
                        'columns' => Schema::hasTable($table) ? Schema::getColumnListing($table) : []
                    ];
                } catch (\Exception $e) {
                    $tableInfo[$table] = [
                        'exists' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            Log::info('Database Debug Info:', ['tables' => $tableInfo]);
            
        } catch (\Exception $e) {
            Log::error('Database connection failed: ' . $e->getMessage());
            return view('admin.admin_dashboard', [
                'dbError' => true,
                'errorMessage' => 'Database connection error: ' . $e->getMessage()
            ]);
        }
        try {
            // Debug: Log database connection status
            DB::connection()->getPdo();
            Log::info('Database connection successful');
            
            // Get counts for dashboard cards with better error handling
            $totalCases = 0;
            $activeCases = 0;
            $totalUsers = 0;
            $todayCases = 0;
            
            try {
                // Get total cases count
                $totalCases = CaseModel::when($dashboardCaseDepartment, fn ($query) => $query->where('type', $dashboardCaseDepartment))->count();
                Log::info('Total cases count: ' . $totalCases);
                
                // SWD dashboard shows only filed VAWC cases.
                $activeCases = CaseModel::when(
                    $isSwdAdmin,
                    fn ($query) => $query->where('type', $swdDepartment)->where('status', 'Case Filed'),
                    fn ($query) => $query
                        ->when($isBcpcAdmin, fn ($caseQuery) => $caseQuery
                            ->where('type', 'BCPC')
                            ->where('status', 'Case Filed'))
                        ->when(!$isBcpcAdmin, fn ($caseQuery) => $caseQuery->whereNotIn('status', ['Resolved', 'Closed', 'Case Closed', 'Case closed', 'Complete Case', 'Complete case']))
                )->count();
                Log::info('Active cases count: ' . $activeCases);
                
                // Get total users count
                $totalUsers = User::where('is_active', true)
                    ->when($isSwdAdmin, fn ($query) => $query
                        ->whereRaw('LOWER(department) LIKE ?', ['%' . strtolower($swdDepartment) . '%'])
                        ->whereRaw('LOWER(TRIM(position)) = ?', ['desk officer']))
                    ->when($isBcpcAdmin, fn ($query) => $query
                        ->whereRaw('LOWER(department) LIKE ?', ['%bcpc%'])
                        ->where('bcpc_is_active', true)
                        ->whereRaw('LOWER(TRIM(position)) <> ?', ['administrator']))
                    ->count();
                Log::info('Total active users: ' . $totalUsers);
                
                // Get today's cases count
                $todayCases = CaseModel::when($dashboardCaseDepartment, fn ($query) => $query->where('type', $dashboardCaseDepartment))
                    ->whereDate('created_at', today())
                    ->count();
                Log::info("Today's cases: " . $todayCases);
                
                // Get referred cases count
                $referredCases = CaseModel::when($dashboardCaseDepartment, fn ($query) => $query->where('type', $dashboardCaseDepartment))
                    ->where('status', 'Case Referred')
                    ->count();
                Log::info("Referred cases: " . $referredCases);
                
                // Get barangay count
                $barangayCount = \App\Models\Wcpu\Barangay::count();
                Log::info('Total barangays: ' . $barangayCount);
                
            } catch (\Exception $e) {
                Log::error('Error getting dashboard statistics: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
                
                // Set default values in case of error
                $totalCases = 0;
                $activeCases = 0;
                $totalUsers = 0;
                $todayCases = 0;
                $referredCases = 0;
                $barangayCount = 0;
            }
            
            // Get recent cases with only the fields we need
            $recentCases = CaseModel::when($isSwdAdmin, fn ($query) => $query->where('type', $swdDepartment))->select([
                    'id',
                    'case_number',
                    'victim_name',
                    'abuse_type',
                    'status',
                    'created_at',
                    'updated_at'
                ])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($case) {
                    return (object)[
                        'id' => $case->id,
                        'case_number' => $case->case_number,
                        'victim_name' => $case->victim_name,
                        'abuse_type' => $case->abuse_type,
                        'status' => $case->status,
                        'created_at' => $case->created_at,
                        'updated_at' => $case->updated_at
                    ];
                });
                
            Log::info('Recent cases loaded:', ['count' => $recentCases->count()]);
                
            // Get case status distribution directly from cases table
            $caseStatuses = CaseModel::when($isSwdAdmin, fn ($query) => $query->where('type', $swdDepartment))
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->map(function($status) {
                    return (object)[
                        'name' => $status->status,
                        'count' => $status->count,
                        'color' => self::getStatusColor($status->status),
                        'slug' => strtolower($status->status)
                    ];
                });
                
            Log::info('Case statuses loaded:', $caseStatuses->toArray());
                
            // Get case types distribution directly from cases table
            $caseTypes = CaseModel::when($dashboardCaseDepartment, fn ($query) => $query->where('type', $dashboardCaseDepartment))
                ->select('abuse_type', DB::raw('count(*) as count'))
                ->groupBy('abuse_type')
                ->get()
                ->map(function($type) {
                    return (object)[
                        'name' => ucfirst(str_replace('_', ' ', $type->abuse_type)),
                        'count' => $type->count,
                        'color' => self::getAbuseTypeColor($type->abuse_type),
                        'slug' => $type->abuse_type
                    ];
                });
                
            Log::info('Case types loaded:', $caseTypes->toArray());
                
            // Get case counts by barangay for the chart
            $barangayCases = [];
            try {
                // Get all barangays with their case counts
                $barangayCases = \App\Models\Wcpu\Barangay::select('barangays.id', 'barangays.name', 'barangays.code')
                    ->withCount(['cases as case_count' => function ($query) use ($dashboardCaseDepartment) {
                        if ($dashboardCaseDepartment) {
                            $query->where('type', $dashboardCaseDepartment);
                        }
                    }])
                    ->having('case_count', '>', 0)
                    ->orderBy('case_count', 'desc')
                    ->get()
                    ->map(function($item) {
                        return [
                            'barangay' => $item->name,
                            'code' => $item->code,
                            'count' => (int)$item->case_count,
                            'color' => '#' . substr(md5($item->id), 0, 6)
                        ];
                    });

                if ($isSwdAdmin) {
                    $unassignedCaseCount = max(0, $totalCases - $barangayCases->sum('count'));
                    if ($unassignedCaseCount > 0) {
                        $barangayCases->push([
                            'barangay' => 'Unassigned',
                            'code' => 'UNASSIGNED',
                            'count' => $unassignedCaseCount,
                            'color' => '#6c757d',
                        ]);
                    }
                }
                
                // If no barangays with cases found, check if there are any cases at all
                if ($barangayCases->isEmpty()) {
                    $caseCount = CaseModel::when($dashboardCaseDepartment, fn ($query) => $query->where('type', $dashboardCaseDepartment))->count();
                    if ($caseCount > 0) {
                        $barangayCases = collect([[
                            'barangay' => 'Unassigned',
                            'code' => 'UNASSIGNED',
                            'count' => $caseCount,
                            'color' => '#6c757d'
                        ]]);
                    }
                }
                
                // If no barangays with cases found, check if there are any cases at all
                if ($barangayCases->isEmpty()) {
                    $caseCount = CaseModel::when($dashboardCaseDepartment, fn ($query) => $query->where('type', $dashboardCaseDepartment))->count();
                    Log::info('No barangays with cases found. Total cases in system: ' . $caseCount);
                    
                    if ($caseCount > 0) {
                        // There are cases but no barangay association, show a single bar for unassigned cases
                        $barangayCases = collect([
                            [
                                'barangay' => 'Unassigned',
                                'count' => $caseCount,
                                'color' => '#6c757d'
                            ]
                        ]);
                        Log::info('Created Unassigned entry with count: ' . $caseCount);
                    }
                } else {
                    // Log the first few barangays and their case counts for verification
                    $sampleData = $barangayCases->take(3)->map(function($item) {
                        return $item['barangay'] . ': ' . $item['count'] . ' cases';
                    })->implode(', ');
                        Log::info('Sample barangay data: ' . $sampleData . '... (total: ' . $barangayCases->count() . ' barangays)');
                }
                
                // Log the complete data being sent to the view
                Log::debug('Complete barangay cases data:', $barangayCases->toArray());
                Log::info('Barangay cases loaded. Total barangays with cases: ' . $barangayCases->count());
            } catch (\Exception $e) {
                Log::error('Error loading barangay cases: ' . $e->getMessage());
                // Return empty array if there's an error
                $barangayCases = [];
            }
                
            // Generate recent activities (placeholder - replace with actual activities from your system)
            $recentActivities = [
                [
                    'icon' => 'user-plus',
                    'text' => 'New case <strong>#2023-001</strong> was created',
                    'time' => '2 hours ago'
                ],
                [
                    'icon' => 'file-alt',
                    'text' => 'Case <strong>#2023-045</strong> was updated',
                    'time' => '5 hours ago'
                ],
                [
                    'icon' => 'user-shield',
                    'text' => 'New user <strong>john.doe</strong> was registered',
                    'time' => '1 day ago'
                ]
            ];

            // Prepare data for the view
            $viewData = [
                'totalCases' => $totalCases,
                'activeCases' => $activeCases,
                'totalUsers' => $totalUsers,
                'todayCases' => $todayCases,
                'referredCases' => $referredCases,
                'barangayCount' => $barangayCount,
                'recentCases' => $recentCases,
                'caseStatuses' => $caseStatuses,
                'caseTypes' => $caseTypes,
                'barangayCases' => $barangayCases,
                'recentActivities' => $recentActivities,
                'dbError' => false
            ];
            
            return view('admin-tailwind.dashboard', $viewData);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Dashboard error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // Return view with error flag and empty data
            return view('admin-tailwind.dashboard', [
                'totalCases' => 0,
                'activeCases' => 0,
                'totalUsers' => 0,
                'todayCases' => 0,
                'referredCases' => 0,
                'barangayCount' => 0,
                'recentCases' => collect(),
                'caseStatuses' => collect(),
                'caseTypes' => collect(),
                'barangayCases' => collect(),
                'recentActivities' => [],
                'dbError' => true,
                'errorMessage' => 'An error occurred while loading the dashboard. Please try again later.'
            ]);
        }
        
        // This code is unreachable due to the return statement in the try-catch block
        // Removed duplicate code to fix the issue
    }
    
    /**
     * Display a listing of all cases.
     */
    public function casesIndex()
    {
        $cases = CaseModel::with(['status', 'caseType', 'assignedTo', 'createdBy'])->when(Auth::user()->isVawcAdmin(), fn ($query) => $query->where('type', 'VAWC'))
            ->latest()
            ->paginate(Auth::user()->isDepartmentAdmin() ? 5 : 10);
            
        $statuses = CaseStatus::orderBy('order')->get();
        $types = CaseType::orderBy('name')->get();
        $users = User::where('is_active', true)->get();
        
        return view('admin.admin_barangay_cases', [
            'cases' => $cases,
            'statuses' => $statuses,
            'types' => $types,
            'users' => $users
        ]);
    }
    
    /**
     * NEW METHOD - Display barangay cases with working buttons
     */
    public function casesIndexNew()
    {
        // SWD Desk Officers work only with their assigned barangay.  The
        // barangay directory is not part of their case-management flow.
        if ($this->isSwdDeskOfficer()) {
            $barangayId = $this->deskOfficerBarangayId();
            abort_unless($barangayId, 404, 'Assigned barangay not found');

            return redirect()->route('admin.barangay-cases.show', $barangayId);
        }

        return view('admin-tailwind.barangay-cases');
    }

    private function barangayCasesDepartment(): ?string
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        if ($user->isDepartmentAdmin()
            || ($user->position === 'Desk Officer' && in_array($user->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))) {
            return $user->defaultCaseDepartment();
        }

        return null;
    }

    private function isBcpcDeskOfficer(): bool
    {
        $user = Auth::user();
        return $user?->position === 'Desk Officer' && $user->defaultCaseDepartment() === 'BCPC';
    }

    private function isSwdDeskOfficer(): bool
    {
        $user = Auth::user();
        return $user?->position === 'Desk Officer' && $user->defaultCaseDepartment() === 'VAWC';
    }

    private function excludeArchivedSwdReferrals($query): void
    {
        if (!$this->isSwdDeskOfficer() || Auth::user()->isMultiCaseDepartment()) {
            return;
        }

        $query->whereNotIn('cases.id', \App\Models\Wcpu\CaseReferral::onlyTrashed()
            ->select('case_id')
            ->whereNotNull('case_id')
            ->whereNotIn('case_id', \App\Models\Wcpu\CaseReferral::query()->select('case_id')->whereNotNull('case_id')));
    }

    private function deskOfficerBarangayId(): ?int
    {
        $user = Auth::user();
        if (!$user?->barangay) {
            return null;
        }

        if ($this->isSwdDeskOfficer()) {
            return Barangay::where('name', $user->barangay)
                ->orWhere('id', $user->barangay)
                ->value('id');
        }

        if (!$this->isBcpcDeskOfficer()) {
            return null;
        }

        // BCPC Desk Officers use the complete barangay list; department access
        // is applied to the case queries separately.
        return null;
    }

    private function deskOfficerBarangayCaseDepartments(): array
    {
        if ($this->isSwdDeskOfficer()) {
            return ['VAWC'];
        }

        if (!$this->isBcpcDeskOfficer()) {
            return [];
        }

        $departments = Auth::user()->caseDepartments();
        $requested = strtolower((string) request('type'));
        if ($requested === 'bcpc' || $requested === 'vawc') {
            $department = strtoupper($requested);
            abort_unless(in_array($department, $departments, true), 403);
            return [$department];
        }

        return $departments;
    }
    
    /**
     * Display a listing of all users.
     */
    /**
     * Display a listing of the users.
     */
    public function usersIndex(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $status = $request->input('status');
        $department = $request->input('department');
        $resetRequestId = $request->input('reset_request');
        $tab = $request->input('tab');
        
        // Get password reset request if specified
        $passwordResetRequest = null;
        if ($resetRequestId) {
            $passwordResetRequest = \App\Models\Wcpu\PasswordResetRequest::with('user')
                ->find($resetRequestId);
        }
        
        // Get pending password reset requests for the tab
        $pendingRequests = \App\Models\Wcpu\PasswordResetRequest::with('user')
            ->pending()
            ->recent()
            ->get();
        $pendingRequestsCount = $pendingRequests->count();
        
        $adminDepartments = Auth::user()?->configuredCaseDepartments() ?? [];

        $users = User::with(['roles'])
            ->where('position', 'Desk Officer')
            ->where(function ($query) use ($adminDepartments) {
                foreach ($adminDepartments as $adminDepartment) {
                    $query->orWhereRaw('LOWER(department) LIKE ?', ['%' . strtolower($adminDepartment) . '%']);
                }
            })
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function($query) use ($role) {
                $query->whereHas('roles', function($q) use ($role) {
                    $q->where('slug', $role);
                });
            })
            ->when($status !== null, function($query) use ($status) {
                $query->where('is_active', $status);
            })
            ->when($department, function($query) use ($department) {
                $query->where('department', $department);
            })
            ->latest();
        
        // Paginate - 10 items per page
        $perPage = Auth::user()->isDepartmentAdmin() ? (request()->boolean('portrait_cards') ? 4 : 5) : 10;
        $users = $users->paginate($perPage)
            ->appends([
                'search' => $search,
                'role' => $role,
                'status' => $status,
                'department' => $department,
                'tab' => $tab,
            ]);
            
        // Get unique departments for filter
        $departments = User::select('department')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->filter();
            
        // Get all roles for filter
        $roles = \App\Models\Wcpu\Role::all();
            
        return view('admin-tailwind.users-table', compact(
            'users', 
            'search', 
            'role',
            'status',
            'department',
            'departments',
            'roles',
            'passwordResetRequest',
            'pendingRequests',
            'pendingRequestsCount'
        ));
    }

    /**
     * Allow an authenticated admin to update their own account email.
     */
    public function changeAccountEmail(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }
        /** @var User $user */

        $validated = $request->validate([
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['required', 'current_password'],
        ]);

        $user->email = $validated['email'];
        $user->save();

        return redirect()
            ->route('admin.users.index', ['tab' => 'account-settings'])
            ->with('status', 'Email updated successfully.');
    }
    
    /**
     * Update admin account settings (password)
     */
    public function updateAccountSettings(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }
        /** @var User $user */

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
            'new_password.confirmed' => 'The password confirmation does not match.',
        ]);

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()
            ->route('admin.users.index', ['tab' => 'account-settings'])
            ->with('success', 'Password updated successfully.');
    }
    
    /**
     * Get user details for the modal
     * 
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function userDetails(User $user)
    {
        try {
            // Load the roles relationship if it's not already loaded
            if (!$user->relationLoaded('roles')) {
                $user->load('roles');
            }
            
            // Get the first role name or default to 'N/A'
            $roleName = 'N/A';
            if (method_exists($user, 'roles') && $user->roles->isNotEmpty()) {
                $role = $user->roles->first();
                $roleName = $role->name ?? 'N/A';
            }
            
            // Format the response
            return response()->json([
                'id' => $user->id,
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
                'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'email' => $user->email ?? 'N/A',
                'role' => $roleName,
                'roles' => $user->roles->pluck('name')->toArray(),
                'department' => $user->department ?? 'N/A',
                'barangay' => $user->barangay,
                'is_active' => (bool) ($user->is_active ?? false),
                'last_login_at' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never',
                'position' => $user->position ?? 'N/A',
                'phone_number' => $user->phone_number ?? 'N/A',
                'created_at' => $user->created_at ? $user->created_at->format('M d, Y') : 'N/A',
                '_token' => csrf_token(),
            ]);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error in userDetails: ' . $e->getMessage());
            
            // Return a basic error response
            return response()->json([
                'error' => 'Failed to load user details',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = \App\Models\Wcpu\Role::orderBy('name')->get();
        $barangays = \App\Models\Wcpu\Barangay::orderBy('name')->get(['id', 'name']);
        
        return view('admin-tailwind.register-user', compact('roles', 'barangays'));
    }


    
     
    public function edit(User $user)
    {
        $roles = \App\Models\Wcpu\Role::orderBy('name')->get();
        $user->load('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }
    
    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'last_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone_number' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(null, 20)],
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'bcpc_is_active' => 'nullable|boolean',
            'vawc_is_active' => 'nullable|boolean',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);
        
        $userData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'position' => $validated['position'] ?? null,
            'department' => $validated['department'] ?? null,
            'is_active' => $request->has('is_active'),
        ];
        
        if (!empty($validated['password'])) {
            $userData['password'] = bcrypt($validated['password']);
        }

        foreach (['bcpc_is_active', 'vawc_is_active'] as $departmentStatus) {
            if (array_key_exists($departmentStatus, $validated)) {
                $userData[$departmentStatus] = $validated[$departmentStatus];
            }
        }
        
        $user->update($userData);
        $user->roles()->sync($validated['roles']);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }
    
    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deactivated successfully.');
    }
    
    /**
     * Restore the specified user.
     */
    public function restoreUser($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        if (User::query()->where(fn ($query) => $query->where('email', $user->email)->orWhere('username', $user->username))->exists()) {
            return redirect()->route('admin.users.archived')
                ->with('error', 'This archived account cannot be restored because its email or username is now used by an active account.');
        }
        $user->restore();
        
        return redirect()->route('admin.users.archived')
            ->with('success', 'User restored successfully.');
    }
    
    /**
     * Display a listing of archived users.
     */
    public function archivedUsers()
    {
        $adminDepartments = Auth::user()?->configuredCaseDepartments() ?? [];

        $users = User::onlyTrashed()
            ->with(['roles'])
            ->where('position', 'Desk Officer')
            ->where(function ($query) use ($adminDepartments) {
                foreach ($adminDepartments as $adminDepartment) {
                    $query->orWhereRaw('LOWER(department) LIKE ?', ['%' . strtolower($adminDepartment) . '%']);
                }
            })
            ->select('id', 'first_name', 'last_name', 'email', 'position', 'department', 'barangay', 'deleted_at')
            ->latest('deleted_at')
            ->paginate(Auth::user()->isDepartmentAdmin() ? (request()->boolean('portrait_cards') ? 4 : 5) : 10)->withQueryString();
        
        return view('admin-tailwind.archived-users', compact('users'));
    } 

    /**
     * Get browser name from user agent
     */
    private function getBrowser($userAgent)
    {
        $browser = 'Unknown';
        
        if (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
            $browser = 'Internet Explorer';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Mozilla Firefox';
        } elseif (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Google Chrome';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR/') !== false) {
            $browser = 'Opera';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Microsoft Edge';
        }
        
        return $browser;
    }
    
    /**
     * Get platform from user agent
     */
    private function getPlatform($userAgent)
    {
        $platform = 'Unknown';
        
        if (preg_match('/windows|win32/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platform = 'Mac OS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $platform = 'iOS';
        }
        
        return $platform;
    }
    
    /**
     * Format additional data for display
     */
    private function formatAdditionalData($properties)
    {
        $exclude = ['ip_address', 'user_agent', 'browser', 'platform'];
        $result = [];
        $userFields = ['user_id', 'causer_id', 'subject_id', 'created_by', 'updated_by', 'deleted_by'];
        
        // First, extract all user IDs we need to look up
        $userIds = [];
        foreach ($properties as $key => $value) {
            if (!in_array($key, $exclude) && !empty($value)) {
                if (in_array($key, $userFields) && is_numeric($value)) {
                    $userIds[] = $value;
                } elseif (is_array($value) && isset($value['id']) && is_numeric($value['id'])) {
                    $userIds[] = $value['id'];
                }
            }
        }
        
        // Pre-fetch all users in a single query
        $users = [];
        if (!empty($userIds)) {
            $users = \App\Models\Wcpu\User::whereIn('id', array_unique($userIds))->get()->keyBy('id');
        }
        
        // Process each property
        foreach ($properties as $key => $value) {
            if (in_array($key, $exclude) || empty($value)) {
                continue;
            }
            
            // Handle array/object values
            if (is_array($value) || is_object($value)) {
                $result[$key] = $this->formatComplexValue($value, $users);
            } 
            // Handle user ID fields
            elseif (in_array($key, $userFields) && is_numeric($value)) {
                $result[$key] = $this->formatUserId($value, $users);
            }
            // Handle other values
            else {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Format a user ID with the user's full name if available
     */
    private function formatUserId($userId, $users)
    {
        if (isset($users[$userId])) {
            $user = $users[$userId];
            return $userId . ' - ' . trim($user->first_name . ' ' . $user->last_name);
        }
        return $userId . ' - Unknown User';
    }
    
    /**
     * Format complex values (arrays/objects)
     */
    private function isArrayOfFiles($arr)
    {
        if (!is_array($arr) || empty($arr)) return false;
        foreach ($arr as $item) {
            if (!is_string($item) || strpos($item, '/') === false) return false;
        }
        return true;
    }

    private function formatComplexValue($value, $users)
    {
        if (is_object($value)) {
            $value = (array) $value;
        }

        // If this looks like a CaseModel or full case data, show only summary fields
        if (isset($value['case_number']) && isset($value['victim_name'])) {
            return 'Case: ' . $value['case_number'] . ' (Victim: ' . $value['victim_name'] . ')';
        }

        // If this looks like documents/files, show only filenames/count
        if (isset($value['documents']) && is_array($value['documents'])) {
            $files = array_map(function($f) {
                return basename($f);
            }, $value['documents']);
            if (empty($files)) return '';
            $html = '<ul style="padding-left:20px; margin:0;">';
            foreach ($files as $file) {
                $html .= '<li>' . htmlspecialchars($file) . '</li>';
            }
            $html .= '</ul>';
            return $html;
        }
        // If the value itself is a list of files
        if (is_array($value) && $this->isArrayOfFiles($value)) {
            $files = array_map(function($f) {
                return basename($f);
            }, $value);
            return 'Documents: ' . implode(', ', $files);
        }
        
        // If it's a user object/array
        if (isset($value['id']) && is_numeric($value['id'])) {
            return $this->formatUserId($value['id'], $users);
        }
        
        // Recursively process arrays
        if (is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                if (is_array($v) || is_object($v)) {
                    $result[$k] = $this->formatComplexValue($v, $users);
                } elseif (is_numeric($v) && in_array($k, ['user_id', 'causer_id', 'subject_id'])) {
                    $result[$k] = $this->formatUserId($v, $users);
                } else {
                    $result[$k] = $v;
                }
            }
            return json_encode($result, JSON_PRETTY_PRINT);
        }
        
        return json_encode($value, JSON_PRETTY_PRINT);
    }
    
    public function userLogs($userId = null)
    {
        try {
            // Get activities with pagination and eager loading
            $query = \Spatie\Activitylog\Models\Activity::with(['causer']);
            
            // Only show Desk Officer logs, exclude Administrator
            $query->whereHas('causer', function($q) {
                $q->where('position', 'Desk Officer');
                if (Auth::user()->isVawcAdmin()) {
                    $q->whereRaw('LOWER(department) LIKE ?', ['%vawc%']);
                }
            });

            if (request()->filled('search')) {
                $search = trim((string) request('search'));
                $query->where(function ($activityQuery) use ($search) {
                    $activityQuery->where('description', 'like', "%{$search}%")
                        ->orWhere('log_name', 'like', "%{$search}%")
                        ->orWhereHas('causer', function ($userQuery) use ($search) {
                            $userQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%")
                                ->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ["%{$search}%"]);
                        });
                });
            }
            
            $activities = $query->orderBy('created_at', 'desc')
                              ->paginate(Auth::user()->isDepartmentAdmin() ? 5 : 10)
                              ->withQueryString();
            
            Log::info('User Logs Debug', [
                'total_count' => $activities->total(),
                'current_page' => $activities->currentPage(),
                'per_page' => $activities->perPage(),
                'items_count' => $activities->count()
            ]);

            // Format the activities for the view
            $formattedLogs = $activities->getCollection()->map(function($activity) {
                try {
                    // Safely get causer information
                    $causer = $activity->causer;
                    
                    // Get user's full name and position
                    $userName = 'System';
                    $userPosition = 'System';
                    
                    if ($causer) {
                        // Get first name and last name
                        $firstName = $causer->first_name ?? '';
                        $lastName = $causer->last_name ?? '';
                        $userName = trim("$firstName $lastName");
                        
                        // Get position from the user's role or position field
                        $userPosition = $causer->position ?? 
                                     ($causer->role ? ucfirst($causer->role) : 'User');
                    }

                    // Get properties safely
                    $properties = $activity->properties ?? [];
                    if (is_string($properties)) {
                        $properties = json_decode($properties, true) ?? [];
                    }

                    // Format the date and time
                    $date = $activity->created_at ? $activity->created_at->format('M d, Y') : 'N/A';
                    $time = $activity->created_at ? $activity->created_at->format('h:i A') : 'N/A';

                    // Determine the action type and icon
                    $action = $activity->description ?? 'Performed an action';
                    $icon = 'fa-info-circle';
                    
                    $actionLower = strtolower($action);
                    if (str_contains($actionLower, 'login')) {
                        $icon = 'fa-sign-in-alt';
                    } elseif (str_contains($actionLower, 'create') || str_contains($actionLower, 'add')) {
                        $icon = 'fa-plus-circle';
                    } elseif (str_contains($actionLower, 'update') || str_contains($actionLower, 'edit')) {
                        $icon = 'fa-edit';
                    } elseif (str_contains($actionLower, 'delete') || str_contains($actionLower, 'remove')) {
                        $icon = 'fa-trash';
                    } elseif (str_contains($actionLower, 'export') || str_contains($actionLower, 'download')) {
                        $icon = 'fa-file-export';
                    }

                    return [
                        'id' => $activity->id,
                        'date' => $date,
                        'time' => $time,
                        'action' => $action,
                        'user' => $causer ? ($causer->id . ' - ' . $userName) : 'System',
                        'role' => $userPosition,
                        'icon' => $icon,
                        'causer' => $causer,
                        'properties' => $properties,
                        'log_name' => $activity->log_name ?? 'default',
                        'created_at' => $activity->created_at,
                        'updated_at' => $activity->updated_at,
                        'ip_address' => $properties['ip_address'] ?? null,
                        'user_agent' => $properties['user_agent'] ?? null,
                        'full_details' => [
                            'log_id' => $activity->id,
                            'action' => $action,
                            'description' => $activity->description,
                            'user' => $userName,
                            'user_id' => $causer ? $causer->id . ' - ' . $userName : 'System',
                            // Keep this key aligned with the user-log modal.
                            'role' => $userPosition,
                            'date' => $date,
                            'time' => $time,
                            'ip_address' => $properties['ip_address'] ?? 'N/A',
                            'user_agent' => $properties['user_agent'] ?? 'N/A',
                            'browser' => $this->getBrowser($properties['user_agent'] ?? ''),
                            'platform' => $this->getPlatform($properties['user_agent'] ?? ''),
                            'properties' => $this->formatAdditionalData($properties),
                            'log_type' => $activity->log_name ?? 'default'
                        ]
                    ];
                } catch (\Exception $e) {
                    Log::error('Error formatting activity log', [
                        'activity_id' => $activity->id ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    return [
                        'id' => $activity->id ?? 'error',
                        'date' => 'Error',
                        'time' => 'Error',
                        'action' => 'Error processing this log',
                        'user' => 'System',
                        'role' => 'System',
                        'icon' => 'fa-exclamation-triangle',
                        'error' => $e->getMessage()
                    ];
                }
            });
            
            // Set the formatted collection back to the paginator
            $activities->setCollection($formattedLogs);
            
            Log::info('Before returning view', [
                'activities_count' => $activities->count(),
                'first_item' => $activities->first(),
                'is_paginator' => $activities instanceof \Illuminate\Pagination\LengthAwarePaginator
            ]);

            return view('admin-tailwind.user-logs', [
                'activities' => $activities,
                'pagination' => $activities
            ]);

        } catch (\Exception $e) {
            Log::error('Error in AdminController@userLogs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return view('admin.admin_user_logs', [
                'activities' => collect([
                    [
                        'id' => 'error',
                        'date' => now()->format('M d, Y'),
                        'time' => now()->format('h:i A'),
                        'action' => 'Error loading activity logs',
                        'user' => 'System',
                        'role' => 'System',
                        'icon' => 'fa-exclamation-circle',
                        'error' => $e->getMessage()
                    ]
                ]),
                'pagination' => null,
                'error' => $e->getMessage()
            ]);
        }
    }
    

    /**
     * Display the form to add a new case.
     */
    public function createCase()
    {
        $caseTypes = CaseType::orderBy('name')->get();
        $statuses = CaseStatus::orderBy('order')->get();
        $users = User::where('is_active', true)->get();
        
        return view('admin_add_new_case', [
            'caseTypes' => $caseTypes,
            'statuses' => $statuses,
            'users' => $users
        ]);
    }
    
    /**
     * Store a newly created case in storage.
     */
    public function storeCase(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            // Case Information
            'case_designation' => 'required|string|max:255',
            'case_number' => 'required|string|unique:cases,case_number',
            
            // Incident Details
            'incident_date' => 'required|date',
            'incident_time' => 'required|date_format:H:i',
            'date_reported' => 'required|date',
            'abuse_type' => 'required|string|max:255',
            'perpetrator_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'relationship_to_victim' => 'nullable|string|max:255',
            'case_details' => 'required|string',
            'status' => 'required|string|max:50',
            'assigned_to' => 'nullable|exists:users,id',
            
            // Victim Information
            'victim_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'victim_age' => 'required|integer|min:0|max:120',
            'victim_gender' => 'required|string|max:50',
            'victim_contact' => ["required", "string", "max:20", new \App\Rules\ContactNumber(11)],
            'victim_address' => 'required|string|max:255',
            
            // Abuser Information
            'abuser_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'abuser_age' => 'nullable|integer|min:0|max:120',
            'abuse_relationship' => 'required|string|max:255',
            'incident_location' => 'required|string|max:255',
            'incident_details' => 'required|string',
            
            // Witness Information
            'witness_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'witness_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
            
            // Documents
            'statement' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'evaluation_form' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'medico_legal' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        try {
            // Handle file uploads
            $fileFields = [
                'statement' => 'statement_document',
                'evaluation_form' => 'evaluation_form',
                'medico_legal' => 'medico_legal'
            ];
            
            $filePaths = [];
            
            foreach ($fileFields as $field => $dbField) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $path = $file->store('case_documents', 'public');
                    $filePaths[$dbField] = $path;
                }
            }

            // Prepare case data
            $caseData = [
                // Case Information
                'case_designation' => $validated['case_designation'],
                'case_number' => $validated['case_number'],
                
                // Incident Details
                'incident_date' => $validated['incident_date'],
                'incident_time' => $validated['incident_time'],
                'date_reported' => $validated['date_reported'],
                'abuse_type' => $validated['abuse_type'],
                'perpetrator_name' => $validated['perpetrator_name'] ?? null,
                'relationship_to_victim' => $validated['relationship_to_victim'] ?? null,
                'case_details' => $validated['case_details'],
                'status' => $validated['status'],
                'assigned_to' => $validated['assigned_to'] ?? null,
                
                // Victim Information
                'victim_name' => $validated['victim_name'],
                'victim_age' => $validated['victim_age'],
                'victim_gender' => $validated['victim_gender'],
                'victim_contact' => $validated['victim_contact'],
                'victim_address' => $validated['victim_address'],
                
                // Abuser Information
                'abuser_name' => $validated['abuser_name'],
                'abuser_age' => $validated['abuser_age'] ?? null,
                'abuse_relationship' => $validated['abuse_relationship'],
                'incident_location' => $validated['incident_location'],
                'incident_details' => $validated['incident_details'],
                
                // Witness Information
                'witness_name' => $validated['witness_name'] ?? null,
                'witness_contact' => $validated['witness_contact'] ?? null,
                
                // System fields
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            // Create the case
            $case = CaseModel::create($caseData);

            // Manually log the case creation
            activity('case')
                ->causedBy(Auth::user())
                ->performedOn($case)
                ->withProperties(['case_number' => $case->case_number])
                ->log('Case created: ' . $case->case_number);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Case created successfully!',
                    'case_id' => $case->id,
                    'barangay_id' => $case->case_designation,
                ]);
            }

            return redirect()->route('admin-barangay-cases')
                ->with('success', 'Case created successfully!');

        } catch (\Exception $e) {
            // If there's an error, delete any uploaded files
            if (!empty($uploads)) {
                foreach ($uploads as $filePath) {
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }
            }
            
            return back()->withInput()
                ->with('error', 'Error creating case: ' . $e->getMessage());
        }
    }
    
    /**
     * Show dedicated page for barangay cases
     * 
     * @param int $barangay The barangay ID
     * @return \Illuminate\View\View
     */
    public function showBarangayCases($barangay)
    {
        try {
            $deskOfficerBarangayId = $this->deskOfficerBarangayId();
            abort_if($deskOfficerBarangayId !== null && (int) $barangay !== $deskOfficerBarangayId, 403);

            // Get the barangay data
            $barangayData = \App\Models\Wcpu\Barangay::find($barangay);
            if (!$barangayData) {
                abort(404, 'Barangay not found');
            }
            
            // Get filter parameters
            $status = request('status');
            $dateFrom = request('date_from');
            $dateTo = request('date_to');
            
            // Get cases for the specific barangay with filters
            $department = $this->barangayCasesDepartment();
            $deskOfficerDepartments = $this->deskOfficerBarangayCaseDepartments();
            $casesQuery = CaseModel::where('case_designation', $barangay)
                ->when($deskOfficerDepartments, fn ($query) => $query->whereIn('type', $deskOfficerDepartments), fn ($query) => $query->when($department, fn ($query) => $query->where('type', $department)));
            $this->excludeArchivedSwdReferrals($casesQuery);
            
            // Apply status filter
            if ($status) {
                if ($status === 'Case closed') {
                    $casesQuery->whereIn('status', ['Case closed', 'Case Closed', 'Complete case', 'Complete Case']);
                } else {
                    $casesQuery->where('status', $status);
                }
            }
            
            // Apply date range filter
            if ($dateFrom) {
                $casesQuery->whereDate('date_reported', '>=', $dateFrom);
            }
            if ($dateTo) {
                $casesQuery->whereDate('date_reported', '<=', $dateTo);
            }
            
            $cases = $casesQuery->select([
                    'id',
                    'case_number',
                    'date_reported',
                    'victim_name',
                    'abuse_type',
                    'status',
                    'priority',
                    'created_at',
                    'updated_at'
                ])
                ->withExists('referrals as has_referral')
                // Desk Officers and Department Admins return here after saving an edit; show that updated case first.
                ->when($deskOfficerBarangayId !== null || Auth::user()->isDepartmentAdmin(), fn ($query) => $query->orderByDesc('updated_at'))
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($department ? (Auth::user()->isDepartmentAdmin() && request()->boolean('tablet_cases') ? 4 : 5) : 10)
                ->withQueryString()
                ->appends([
                    'status' => $status,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ]);
            
            // Get totals for summary cards (unfiltered to show overall stats)
            if ((Auth::user()->isDepartmentAdmin() || ($this->isBcpcDeskOfficer() && !Auth::user()->isMultiCaseDepartment())) && $cases->currentPage() > $cases->lastPage()) {
                return redirect()->route('admin.barangay-cases.show', array_merge(request()->query(), ['barangay' => $barangay, 'page' => $cases->lastPage()]));
            }
            $allCases = CaseModel::where('case_designation', $barangay)
                ->when($deskOfficerDepartments, fn ($query) => $query->whereIn('type', $deskOfficerDepartments), fn ($query) => $query->when($department, fn ($query) => $query->where('type', $department)))
                ->when($this->isSwdDeskOfficer(), function ($query) { $this->excludeArchivedSwdReferrals($query); })
                ->get();
            
            return view('admin-tailwind.barangay-cases-view', [
                'barangay' => $barangayData,
                'cases' => $cases,
                'allCases' => $allCases,
                'currentFilters' => [
                    'status' => $status,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error showing barangay cases: ' . $e->getMessage());
            abort(500, 'Error loading barangay cases');
        }
    }
    
    /**
     * Print all barangay cases
     * 
     * @return \Illuminate\Http\Response
     */
    public function printAllBarangayCases()
    {
        try {
            if (($deskOfficerBarangayId = $this->deskOfficerBarangayId()) !== null) {
                return $this->printBarangayCases($deskOfficerBarangayId);
            }
            // Get filter parameters
            $dateFrom = request('date_from');
            $dateTo = request('date_to');
            $status = request('status');
            
            // Build the query with optional date/status filters
            $query = \App\Models\Wcpu\Barangay::query();
            
            // Define the count queries with optional filters
            $countQueries = [
                'cases as total_cases' => function($q) use ($dateFrom, $dateTo, $status) {
                            $q->when($this->barangayCasesDepartment(), fn ($query) => $query->where('type', $this->barangayCasesDepartment()));
                    if ($dateFrom) {
                        $q->whereDate('date_reported', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $q->whereDate('date_reported', '<=', $dateTo);
                    }
                    if ($status) {
                        $q->where('status', $status);
                    }
                },
                'cases as case_filed' => function($q) use ($dateFrom, $dateTo) {
                            $q->when($this->barangayCasesDepartment(), fn ($query) => $query->where('type', $this->barangayCasesDepartment()));
                    $q->where('status', 'Case Filed');
                    if ($dateFrom) {
                        $q->whereDate('date_reported', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $q->whereDate('date_reported', '<=', $dateTo);
                    }
                },
                'cases as case_referred' => function($q) use ($dateFrom, $dateTo) {
                            $q->when($this->barangayCasesDepartment(), fn ($query) => $query->where('type', $this->barangayCasesDepartment()));
                    $q->where('status', 'Case Referred');
                    if ($dateFrom) {
                        $q->whereDate('date_reported', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $q->whereDate('date_reported', '<=', $dateTo);
                    }
                },
                'cases as case_closed' => function($q) use ($dateFrom, $dateTo) {
                            $q->when($this->barangayCasesDepartment(), fn ($query) => $query->where('type', $this->barangayCasesDepartment()));
                    $q->whereIn('status', ['Case closed', 'Case Closed', 'Complete case', 'Complete Case']);
                    if ($dateFrom) {
                        $q->whereDate('date_reported', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $q->whereDate('date_reported', '<=', $dateTo);
                    }
                }
            ];
            
            $barangays = $query->withCount($countQueries)->get();
            
            // Filter to only include barangays that have cases and format data
            $barangayData = $barangays->filter(function($barangay) {
                return $barangay->total_cases > 0;
            })->map(function($barangay) {
                return [
                    'barangay' => $barangay,
                    'total_cases' => $barangay->total_cases,
                    'case_referred' => $barangay->case_referred,
                    'case_filed' => $barangay->case_filed,
                    'case_closed' => $barangay->case_closed
                ];
            });
            
            // Calculate grand totals
            $grandTotals = [
                'total_cases' => $barangayData->sum('total_cases'),
                'case_referred' => $barangayData->sum('case_referred'),
                'case_filed' => $barangayData->sum('case_filed'),
                'case_closed' => $barangayData->sum('case_closed')
            ];
            
            // Build date range text for the report
            $dateRangeText = '';
            if ($dateFrom && $dateTo) {
                $dateRangeText = \Carbon\Carbon::parse($dateFrom)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($dateTo)->format('M d, Y');
            } elseif ($dateFrom) {
                $dateRangeText = 'From ' . \Carbon\Carbon::parse($dateFrom)->format('M d, Y');
            } elseif ($dateTo) {
                $dateRangeText = 'Until ' . \Carbon\Carbon::parse($dateTo)->format('M d, Y');
            }
            
            $printView = Auth::user()?->isBcpcAdmin()
                ? 'admin-tailwind.barangay-cases-print-bcpc'
                : (Auth::user()?->isVawcAdmin()
                    ? 'admin-tailwind.barangay-cases-print-swd'
                    : 'admin-tailwind.barangay-cases-print');

            return view($printView, [
                'barangayData' => $barangayData,
                'grandTotals' => $grandTotals,
                'department' => $this->barangayCasesDepartment() ?? request('type') ?? Auth::user()?->defaultCaseDepartment() ?? (Auth::user()?->isBcpcAdmin() ? 'BCPC' : 'VAWC'),
                'printDate' => now()->format('F d, Y'),
                'dateRangeText' => $dateRangeText
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error printing barangay cases: ' . $e->getMessage());
            abort(500, 'Error loading cases for printing');
        }
    }
    
    /**
     * Print cases for a specific barangay
     * 
     * @param int $barangay The barangay ID
     * @return \Illuminate\Http\Response
     */
    public function printBarangayCases($barangay)
    {
        try {
            $deskOfficerBarangayId = $this->deskOfficerBarangayId();
            abort_if($deskOfficerBarangayId !== null && (int) $barangay !== $deskOfficerBarangayId, 403);

            // Get the barangay data
            $barangayData = \App\Models\Wcpu\Barangay::find($barangay);
            if (!$barangayData) {
                abort(404, 'Barangay not found');
            }
            
            // Get filter parameters
            $dateFrom = request('date_from');
            $dateTo = request('date_to');
            $status = request('status');
            
            // Get cases for the specific barangay with filters
            $department = $this->barangayCasesDepartment();
            $deskOfficerDepartments = $this->deskOfficerBarangayCaseDepartments();
            $casesQuery = CaseModel::where('case_designation', $barangay)
                ->when($deskOfficerDepartments, fn ($query) => $query->whereIn('type', $deskOfficerDepartments), fn ($query) => $query->when($department, fn ($query) => $query->where('type', $department)));
            
            // Apply status filter
            if ($status) {
                $status === 'Case closed'
                    ? $casesQuery->whereIn('status', ['Case closed', 'Case Closed', 'Complete case', 'Complete Case'])
                    : $casesQuery->where('status', $status);
            }
            
            // Apply date range filter
            if ($dateFrom) {
                $casesQuery->whereDate('date_reported', '>=', $dateFrom);
            }
            if ($dateTo) {
                $casesQuery->whereDate('date_reported', '<=', $dateTo);
            }
            
            $cases = $casesQuery->select([
                    'id',
                    'type',
                    'case_number',
                    'date_reported',
                    'victim_name',
                    'abuse_type',
                    'status',
                    'created_at',
                    'updated_at'
                ])
                ->orderBy('date_reported', 'desc')
                ->get();
            
            // Calculate statistics using new status system
            $totalCases = $cases->count();
            $caseReferred = $cases->where('status', 'Case Referred')->count();
            $caseFiled = $cases->where('status', 'Case Filed')->count();
            $caseClosed = $cases->whereIn('status', ['Case closed', 'Case Closed', 'Complete case', 'Complete Case'])->count();
            
            // Build date range text for the report
            $dateRangeText = '';
            if ($dateFrom && $dateTo) {
                $dateRangeText = \Carbon\Carbon::parse($dateFrom)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($dateTo)->format('M d, Y');
            } elseif ($dateFrom) {
                $dateRangeText = 'From ' . \Carbon\Carbon::parse($dateFrom)->format('M d, Y');
            } elseif ($dateTo) {
                $dateRangeText = 'Until ' . \Carbon\Carbon::parse($dateTo)->format('M d, Y');
            }
            
            $printView = Auth::user()?->isBcpcAdmin()
                ? 'admin-tailwind.barangay-cases-print-individual-bcpc'
                : (Auth::user()?->isVawcAdmin()
                    ? 'admin-tailwind.barangay-cases-print-individual-swd'
                    : 'admin-tailwind.barangay-cases-print-individual');

            return view($printView, [
                'barangay' => $barangayData,
                'cases' => $cases,
                'totalCases' => $totalCases,
                'caseReferred' => $caseReferred,
                'caseFiled' => $caseFiled,
                'caseClosed' => $caseClosed,
                'printDate' => now()->format('F d, Y'),
                'dateRangeText' => $dateRangeText,
                'statusFilter' => $status,
                'isBcpcAdminPrint' => Auth::user()?->isBcpcAdmin(),
                'isSwdAdminPrint' => Auth::user()?->isVawcAdmin(),
                'department' => count($deskOfficerDepartments) === 1 ? $deskOfficerDepartments[0] : ($department ?? request('type') ?? Auth::user()?->defaultCaseDepartment() ?? (Auth::user()?->isBcpcAdmin() ? 'BCPC' : 'VAWC')),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error printing barangay cases: ' . $e->getMessage());
            abort(500, 'Error loading barangay cases for printing');
        }
    }
    
    /**
     * Print individual case report (desk officer format)
     * 
     * @param int $case The case ID
     * @return \Illuminate\View\View
     */
    public function printCaseReport($case)
    {
        try {
            // Get the case data
            $caseData = CaseModel::find($case);
            if (!$caseData) {
                abort(404, 'Case not found');
            }
            
            // Get barangay name
            $barangay = \App\Models\Wcpu\Barangay::find($caseData->case_designation);
            $barangayName = $barangay ? $barangay->name : 'Unknown';
            
            return view('admin-tailwind.case-print', [
                'case' => $caseData,
                'barangayName' => $barangayName,
                'printDate' => now()->format('F d, Y')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error printing case report: ' . $e->getMessage());
            abort(500, 'Error loading case for printing');
        }
    }
    
    /**
     * Get cases data for a specific barangay or all barangays
     * 
     * @param string|null $barangay The barangay name (optional)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBarangayCases($barangay = null)
    {
        try {
            $status = request('status');
            $dateFrom = request('date_from');
            $dateTo = request('date_to');
            $department = $this->barangayCasesDepartment();
            $deskOfficerBarangayId = $this->deskOfficerBarangayId();
            $deskOfficerDepartments = $this->deskOfficerBarangayCaseDepartments();
            $applyFilters = function ($query) use ($status, $dateFrom, $dateTo, $department, $deskOfficerDepartments) {
                $this->excludeArchivedSwdReferrals($query);
                if ($deskOfficerDepartments) {
                    $query->whereIn('type', $deskOfficerDepartments);
                } elseif ($department) {
                    $query->where('type', $department);
                }
                if ($status) {
                    $status === 'Case closed'
                        ? $query->whereIn('status', ['Case closed', 'Case Closed', 'Complete case', 'Complete Case'])
                        : $query->where('status', $status);
                }
                if ($dateFrom) {
                    $query->whereDate('date_reported', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->whereDate('date_reported', '<=', $dateTo);
                }
            };

            if ($barangay) {
                abort_if($deskOfficerBarangayId !== null && (int) $barangay !== $deskOfficerBarangayId, 403);
                $barangayData = \App\Models\Wcpu\Barangay::find($barangay);
                if (!$barangayData) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Barangay not found',
                        'barangay_id' => $barangay
                    ], 404);
                }
                
                $casesQuery = CaseModel::where('case_designation', $barangay);
                if ($deskOfficerDepartments) {
                    $casesQuery->whereIn('type', $deskOfficerDepartments);
                }
                $applyFilters($casesQuery);
                $cases = $casesQuery->select([
                        'id',
                        'case_number',
                        'date_reported',
                        'victim_name',
                        'abuse_type',
                        'status',
                        'created_at',
                        'updated_at'
                    ])
                    ->orderBy('date_reported', 'desc')
                    ->get()
                    ->map(function($case) {
                        return [
                            'id' => $case->id,
                            'case_number' => $case->case_number,
                            'date_reported' => $case->date_reported,
                            'victim_name' => $case->victim_name,
                            'abuse_type' => $case->abuse_type,
                            'status' => $case->status,
                            'created_at' => $case->created_at,
                            'updated_at' => $case->updated_at
                        ];
                    });
                
                return response()->json([
                    'success' => true,
                    'barangay_id' => $barangay,
                    'barangay_name' => $barangayData->name,
                    'cases' => $cases
                ]);
            }

            $query = Barangay::query()
                ->when($deskOfficerBarangayId, fn ($barangays) => $barangays->whereKey($deskOfficerBarangayId))
                ->withCount([
                'cases as total_cases' => $applyFilters,
                'cases as case_filed' => function ($query) use ($applyFilters) {
                    $applyFilters($query);
                    $query->where('status', 'Case Filed');
                },
                'cases as case_referred' => function ($query) use ($applyFilters) {
                    $applyFilters($query);
                    $query->where('status', 'Case Referred');
                },
                'cases as case_closed' => function ($query) use ($applyFilters) {
                    $applyFilters($query);
                    $query->whereIn('status', ['Case closed', 'Case Closed', 'Complete case', 'Complete Case']);
                },
            ]);

            $hasFilters = filled($status) || filled($dateFrom) || filled($dateTo);

            return response()->json($query->get()
                ->when($hasFilters, fn ($barangays) => $barangays->filter(fn ($barangay) => $barangay->total_cases > 0))
                ->map(function ($barangay) {
                    return [
                        'id' => $barangay->id,
                        'name' => $barangay->name,
                        'code' => $barangay->code,
                        'total_cases' => $barangay->total_cases ?? 0,
                        'case_filed' => $barangay->case_filed ?? 0,
                        'case_referred' => $barangay->case_referred ?? 0,
                        'case_closed' => $barangay->case_closed ?? 0,
                    ];
                })->values());
        } catch (\Exception $e) {
            Log::error('Error fetching barangay cases: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch barangay cases',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('status', 'You have been logged out.');
    }

    /**
     * Approve a password reset request and reset the user's password
     */
    public function approvePasswordResetRequest(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);

        $resetRequest = \App\Models\Wcpu\PasswordResetRequest::findOrFail($id);
        
        // Update the user's password
        $user = $resetRequest->user;
        $user->password = Hash::make($request->new_password);
        
        // Unlock account and reset failed login attempts
        $user->account_locked = false;
        $user->failed_login_attempts = 0;
        $user->locked_until = null;
        
        $user->save();

        // Mark the request as completed
        $resetRequest->status = 'completed';
        $resetRequest->admin_id = Auth::id();
        $resetRequest->resolved_at = now();
        $resetRequest->admin_notes = $request->admin_notes;
        $resetRequest->new_password = $request->new_password;
        $resetRequest->save();

        $accessUrl = URL::temporarySignedRoute(
            'password.reset.access',
            now()->addHours(24),
            ['id' => $resetRequest->id]
        );

        $isBcpc = Auth::user()->isBcpcAdmin();
        $senderName = $isBcpc ? 'BCPC Silang' : 'SWD Silang';
        $senderEmail = $isBcpc ? 'bcpcsilang@gmail.com' : 'swdsilang01@gmail.com';

        Mail::send('emails.password-reset-access', [
            'user' => $user,
            'accessUrl' => $accessUrl,
            'requester' => $resetRequest->username ?: ($user->username ?: $user->email),
            'brandColor' => '#C41E3A',
        ], function ($message) use ($user, $senderName, $senderEmail) {
            $message->to($user->email)
                ->from($senderEmail, $senderName)
                ->subject('Your Password Reset Access Link - iSERVE');
        });

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully for user: ' . $user->username
        ]);
    }

    /**
     * Reject a password reset request
     */
    public function rejectPasswordResetRequest(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        $resetRequest = \App\Models\Wcpu\PasswordResetRequest::findOrFail($id);
        $reason = trim((string) ($request->input('reason') ?: $request->input('admin_notes') ?: 'Your password reset request could not be approved at this time.'));
        
        // Mark the request as rejected
        $resetRequest->status = 'rejected';
        $resetRequest->admin_id = Auth::id();
        $resetRequest->resolved_at = now();
        $resetRequest->admin_notes = $reason;
        $resetRequest->save();

        // Resolve requester user account
        $user = $resetRequest->user;
        if (!$user && $resetRequest->user_id) {
            $user = \App\Models\Wcpu\User::find($resetRequest->user_id);
        }
        if (!$user && $resetRequest->username) {
            $user = \App\Models\Wcpu\User::where('username', $resetRequest->username)->orWhere('email', $resetRequest->username)->first();
        }

        // Send email to requester containing the reason
        if ($user && $user->email) {
            $isBcpc = Auth::user()->isBcpcAdmin();
            $senderName = $isBcpc ? 'BCPC Silang' : 'SWD Silang';
            $senderEmail = $isBcpc ? 'bcpcsilang@gmail.com' : 'swdsilang01@gmail.com';

            try {
                Mail::send('emails.password-reset-rejected', [
                    'user' => $user,
                    'requester' => $resetRequest->username ?: ($user->username ?: $user->email),
                    'reason' => $reason,
                    'brandColor' => $isBcpc ? '#b91c1c' : '#1d4ed8',
                    'senderName' => $senderName,
                ], function ($message) use ($user, $senderName, $senderEmail) {
                    $message->to($user->email)
                        ->from($senderEmail, $senderName)
                        ->subject('Password Reset Request Rejected - WCPU');
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send password reset rejection email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset request rejected and notification email sent to requester.'
        ]);
    }

    public function dismissPasswordResetRequest($id)
    {
        // Store the dismissed request ID in session
        session(['dismissed_password_request_' . $id => true]);

        return response()->json([
            'success' => true,
            'message' => 'Alert dismissed'
        ]);
    }

    /**
     * Display all referrals (admin view - all barangays)
     */
    public function referrals(Request $request)
    {
        // Get all referrals from all barangays
        $query = \App\Models\Wcpu\CaseReferral::with(['case.barangay', 'referredBy', 'recipient'])
            ->withExists('departmentAdminReferralsForCase')
            ->orderByDesc((Auth::user()->isBcpcAdmin() || Auth::user()->isVawcAdmin()) ? 'updated_at' : 'created_at');

        if (Auth::user()->isBcpcAdmin()) {
            $query->where(function ($referralQuery) {
                $referralQuery
                    ->where('referred_by_user_id', Auth::id())
                    ->orWhereHas('case', fn ($caseQuery) => $caseQuery->where('type', Auth::user()->defaultCaseDepartment()));
            });
        } elseif (Auth::user()->isDepartmentAdmin()) {
            $query->whereHas('case', fn ($caseQuery) => $caseQuery->where('type', Auth::user()->defaultCaseDepartment()));
        }
        // Apply status filter if provided
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $search = trim((string) $request->input('search'));
        if ($search !== '') {
            $query->where(function ($referralQuery) use ($search) {
                $referralQuery->where('client_name', 'like', "%{$search}%")
                    ->orWhere('referral_number', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('recipient_email', 'like', "%{$search}%")
                    ->orWhere('feedback', 'like', "%{$search}%");
            });
        }

        $referrals = $query->paginate(Auth::user()->isDepartmentAdmin() ? ($request->boolean('portrait_cards') ? 4 : 5) : 10)->withQueryString();
        
        return view('admin-tailwind.referrals', compact('referrals'));
    }

    public function archiveReferral($id)
    {
        abort_unless(Auth::user()->isDepartmentAdmin(), 403);
        $referral = \App\Models\Wcpu\CaseReferral::findOrFail($id);
        app(\App\Services\Wcpu\ReferralArchiveService::class)->archive($referral);
        return response()->json(['success' => true, 'message' => 'Referral archived successfully']);
    }

    public function archivedReferrals()
    {
        abort_unless(Auth::user()->isDepartmentAdmin(), 403);
        $department = Auth::user()->defaultCaseDepartment();
        $referrals = \App\Models\Wcpu\CaseReferral::onlyTrashed()
            ->whereHas('case', fn ($query) => $query->where('type', $department))
            ->latest('deleted_at')
            ->paginate(request()->boolean('portrait_cards') ? 4 : 5, ['*'], 'referrals_page')
            ->withQueryString();
        $recipients = \App\Models\Wcpu\ReportRecipient::onlyTrashed()->latest('deleted_at')->paginate(request()->boolean('portrait_cards') ? 4 : 5, ['*'], 'info_page')->withQueryString();
        return view('admin-tailwind.archived-referrals', compact('referrals', 'recipients'));
    }

    public function restoreArchivedReferral($type, $id)
    {
        $record = $this->findArchivedReferral($type, $id);
        app(\App\Services\Wcpu\ReferralArchiveService::class)->restore($record);
        return response()->json(['success' => true, 'message' => 'Referral and linked referral info restored successfully']);
    }

    public function deleteArchivedReferral($type, $id)
    {
        $record = $this->findArchivedReferral($type, $id);
        app(\App\Services\Wcpu\ReferralArchiveService::class)->permanentlyDelete($record);
        return response()->json(['success' => true, 'message' => 'Archived referral and linked referral info permanently deleted']);
    }

    private function findArchivedReferral($type, $id)
    {
        abort_unless(Auth::user()->isDepartmentAdmin(), 403);
        abort_unless(in_array($type, ['referral', 'info'], true), 404);
        $model = $type === 'referral' ? \App\Models\Wcpu\CaseReferral::class : \App\Models\Wcpu\ReportRecipient::class;
        $query = $model::onlyTrashed();
        if ($type === 'referral') {
            $department = Auth::user()->defaultCaseDepartment();
            $query->whereHas('case', fn ($caseQuery) => $caseQuery->where('type', $department));
        }

        return $query->findOrFail($id);
    }

    /**
     * Show referral details for admin
     */
    public function showReferral($id)
    {
        $referral = \App\Models\Wcpu\CaseReferral::with(['case.barangay', 'referredBy'])->findOrFail($id);
        return view('admin.partials.referral-details', compact('referral'));
    }

    /**
     * Get referral details as JSON for Tailwind admin views
     */
    public function getReferralJson($id)
    {
        $referral = \App\Models\Wcpu\CaseReferral::with(['case.barangay', 'referredBy'])->findOrFail($id);
        $canReferCase = (Auth::user()->isBcpcAdmin() || Auth::user()->isVawcAdmin())
            && $referral->case
            && ! preg_match('/closed|complet/i', trim((string) $referral->case->status))
            && ! $referral->caseHasDepartmentAdminReferral()
            && Auth::user()->canAccessCaseDepartment((string) $referral->case->type);
        
        return response()->json([
            'id' => $referral->id,
            'referral_number' => $referral->referral_number,
            'date_of_referral' => $referral->date_of_referral ? \Carbon\Carbon::parse($referral->date_of_referral)->format('F d, Y') : null,
            'date_of_referral_input' => $referral->date_of_referral ? \Carbon\Carbon::parse($referral->date_of_referral)->format('Y-m-d') : null,
            'client_name' => $referral->case ? $referral->case->victim_name : null,
            'client_age' => $referral->case ? $referral->case->victim_age : null,
            'client_sex' => $referral->case ? $referral->case->victim_gender : null,
            'specific_services_requested' => $referral->specific_services_requested,
            'recipient_name' => $referral->recipient_name,
            'recipient_email' => $referral->recipient_email,
            'feedback' => $referral->feedback,
            'feedback_received_at' => $referral->feedback_received_at ? \Carbon\Carbon::parse($referral->feedback_received_at)->format('F d, Y') : null,
            'additional_notes' => $referral->additional_notes,
            'referred_by' => $referral->referredBy ? $referral->referredBy->name : null,
            'refer_url' => $canReferCase
                ? route('cases.refer.form', [
                    'id' => $referral->case->id,
                    'source_referral' => $referral->id,
                ])
                : null,
        ]);
    }

    /**
     * Update referral details (admin can edit)
     */
    public function updateReferral(Request $request, $id)
    {
        $referral = \App\Models\Wcpu\CaseReferral::with(['case', 'recipient'])->findOrFail($id);

        $validated = $request->validate([
            'date_of_referral' => ['nullable', 'date'],
            'action_taken' => ['nullable', 'string'],
            'received_at' => (Auth::user()->isBcpcAdmin() || Auth::user()->isVawcAdmin()) ? ['required', 'date'] : ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
        ]);
        
        $updates = [
            'feedback_received_at' => $validated['received_at'] ?? null,
            'feedback' => $validated['action_taken'] ?? null,
            'additional_notes' => $validated['remarks'] ?? null,
        ];

        // Department-admin referral dates are retained as part of the original referral record.
        if (!Auth::user()->isDepartmentAdmin()) {
            $updates['date_of_referral'] = $validated['date_of_referral'] ?? null;
        }

        $referral->update($updates);

        return response()->json([
            'success' => true,
            'message' => 'Referral updated successfully',
            'redirect' => (Auth::user()->isBcpcAdmin() || Auth::user()->isVawcAdmin()) ? route('admin.referrals') : null,
        ]);
    }

    /**
     * Print single referral form (VAW Form 2)
     */
    public function printReferral($id)
    {
        $referral = \App\Models\Wcpu\CaseReferral::with(['case.barangay', 'referredBy'])->findOrFail($id);

        return view('admin-tailwind.referrals.print', compact('referral'));
    }

    /**
     * Print all referrals
     */
    public function printAllReferrals(Request $request)
    {
        $referrals = \App\Models\Wcpu\CaseReferral::with(['case.barangay', 'referredBy', 'recipient'])
            ->when(Auth::user()->isBcpcAdmin(), function ($query) {
                $query->where(function ($referralQuery) {
                    $referralQuery
                        ->where('referred_by_user_id', Auth::id())
                        ->orWhereHas('case', fn ($caseQuery) => $caseQuery->where('type', Auth::user()->defaultCaseDepartment()));
                });
            })
            ->when(Auth::user()->isVawcAdmin(), function ($query) {
                $query->whereHas('case', fn ($caseQuery) => $caseQuery->where('type', 'VAWC'));
            })
            ->latest()->get();

        return view('admin-tailwind.referrals.print-all', compact('referrals'));
    }

    /**
     * Render the current editable referral preview as a clean PDF document.
     */
    public function printEditableReferralRegistry(Request $request)
    {
        $user = $request->user();

        // BCPC/SWD administrators and Desk Officers use the same editable
        // referral registry, so both must be able to render its PDF.
        abort_unless(
            $user
                && in_array(strtolower(trim((string) $user->position)), ['administrator', 'desk officer'], true)
                && ! empty($user->caseDepartments()),
            403
        );

        $html = $request->validate([
            'html' => ['required', 'string', 'max:2000000'],
        ])['html'];

        return \Barryvdh\DomPDF\Facade\Pdf::setOptions([
            'isHtml5ParserEnabled' => true,
        ])->loadHtml($html)
            ->setPaper('a4', 'landscape')
            ->stream('referral-registry.pdf');
    }

    /**
     * Render an authenticated user's editable print preview as a clean PDF.
     */
    public function printEditedDocument(Request $request)
    {
        abort_unless($request->user(), 403);

        $html = $request->validate([
            'html' => ['required', 'string', 'max:2000000'],
        ])['html'];

        return \Barryvdh\DomPDF\Facade\Pdf::loadHtml($html)
            ->setPaper('a4', 'landscape')
            ->stream('print-preview.pdf');
    }

    /**
     * Display BCPC Case Monitoring for Admin.
     */
    public function bcpcCaseMonitoring(Request $request)
    {
        abort_unless(Auth::user()->canAccessCaseDepartment('BCPC'), 403);
        $barangays = Barangay::orderBy('name')->get();
        $barangayId = $request->input('barangay_id');
        $quarter = $request->input('quarter');
        $year = $request->input('year');
        $viewId = $request->input('view');
        $editId = $request->input('edit');
        $generate = $request->input('generate');
        $add = $request->input('add');

        // If viewing or editing a specific monitoring record
        if ($viewId || $editId) {
            $monitoring = BcpcCaseMonitoring::query()->when(Auth::user()->isBcpcAdmin(), fn ($query) => $query->visibleToAdmin(Auth::id()))->with('barangay')->findOrFail($viewId ?? $editId);
            $barangay = $monitoring->barangay;
            $quarter = $monitoring->quarter;
            $year = $monitoring->year;
            $isView = $viewId !== null;

            return view('admin-tailwind.bcpc-case-monitoring-form', compact(
                'barangays',
                'barangay',
                'quarter',
                'year',
                'monitoring',
                'isView'
            ));
        }

        // If adding a new monitoring record
        if ($add) {
            if (!$barangayId || !$quarter || !$year) {
                return redirect()->route('admin.bcpc-case-monitoring.edit')
                    ->with('error', 'Please select barangay, quarter, and year to add monitoring.');
            }

            $barangay = Barangay::findOrFail($barangayId);
            $monitoring = null;
            $isView = false;

            return view('admin-tailwind.bcpc-case-monitoring-form', compact(
                'barangays',
                'barangay',
                'quarter',
                'year',
                'monitoring',
                'isView'
            ));
        }

        // If generating a report from barangay data
        if ($generate) {
            if (!$barangayId || !$quarter || !$year) {
                return redirect()->route('admin.bcpc-case-monitoring.edit')
                    ->with('error', 'Please select barangay, quarter, and year to generate a report.');
            }

            $barangay = Barangay::findOrFail($barangayId);
            $monitoring = BcpcCaseMonitoring::query()->when(Auth::user()->isBcpcAdmin(), fn ($query) => $query->visibleToAdmin(Auth::id()))->where([
                'barangay_id' => $barangay->id,
                'quarter' => $quarter,
                'year' => $year,
            ])->first();

            $isView = false;
            if (Auth::user()->isBcpcAdmin()) {
                $monitoring = new BcpcCaseMonitoring(['data' => BcpcCaseMonitoring::quarterTotals($barangay->id, (int) $quarter, (int) $year, Auth::id())]);
                $isView = true;
            }

            return view('admin-tailwind.bcpc-case-monitoring-form', compact(
                'barangays',
                'barangay',
                'quarter',
                'year',
                'monitoring',
                'isView'
            ));
        }

        // Get all submitted monitoring records
        $query = BcpcCaseMonitoring::query()->when(Auth::user()->isBcpcAdmin(), fn ($query) => $query->visibleToAdmin(Auth::id()))->with('barangay', 'creator', 'submitter', 'updater');
        if (Auth::user()->isBcpcAdmin()) {
            $query->whereNull('archived_at');
        }

        if ($barangayId) {
            $query->where('barangay_id', $barangayId);
        }
        if ($quarter) {
            $query->where('quarter', $quarter);
        }
        if ($year) {
            $query->where('year', $year);
        }

        $monitorings = $query->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->orderBy('created_at', 'desc')
            ->when(Auth::user()->isBcpcAdmin(), fn ($query) => $query->orderByDesc('id'))
            ->paginate(Auth::user()->isDepartmentAdmin() ? ($request->boolean('portrait_cards') ? 4 : 5) : 10)->withQueryString();

        $archivedCount = 0;
        $existingMonitoringPeriods = BcpcCaseMonitoring::whereNull('archived_at')
            ->get(['barangay_id', 'quarter', 'year'])
            ->map(fn ($monitoring) => [
                'barangay_id' => (string) $monitoring->barangay_id,
                'quarter' => (string) $monitoring->quarter,
                'year' => (string) $monitoring->year,
            ])
            ->values();

        return view('admin-tailwind.bcpc-case-monitoring', compact(
            'barangays',
            'barangayId',
            'quarter',
            'year',
            'monitorings',
            'archivedCount',
            'existingMonitoringPeriods'
        ));
    }

    /**
     * Save/Update BCPC Case Monitoring for Admin.
     */
    public function bcpcCaseMonitoringUpdate(Request $request)
    {
        abort_unless(Auth::user()->canAccessCaseDepartment('BCPC'), 403);
        $data = $request->validate([
            'monitoring_id' => 'nullable|integer',
            'barangay_id' => 'required|exists:wcpu.barangays,id',
            'quarter' => 'required|integer|between:1,4',
            'year' => 'required|integer|between:2020,2100',
            'bcpc_crimes_against_children' => 'nullable|array',
            'bcpc_crimes_against_children.*.*.*' => 'nullable|integer|min:0',
            'bcpc_cicl_crimes' => 'nullable|array',
            'bcpc_cicl_crimes.*.*.*' => 'nullable|integer|min:0',
            'bcpc_cicl_status' => 'nullable|array',
            'bcpc_cicl_status.*.*.*' => 'nullable|integer|min:0',
            'bcpc_children_at_risk' => 'nullable|array',
            'bcpc_children_at_risk.*.*.*' => 'nullable|integer|min:0',
            'bcpc_special_protection' => 'nullable|array',
            'bcpc_special_protection.*.*.*' => 'nullable|integer|min:0',
            'bcpc_organizations' => 'nullable|array',
            'bcpc_organizations.*.name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} ]+$/u"],
            'bcpc_organizations.*.responsible_person' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} ]+$/u"],
            'bcpc_organizations.*.phone' => ['nullable', 'regex:/^[0-9]+$/', new \App\Rules\ContactNumber(null)],
        ], [
            'bcpc_organizations.*.name.regex' => 'The organization name may contain only letters and spaces.',
            'bcpc_organizations.*.responsible_person.regex' => 'The responsible person and designation may contain only letters and spaces.',
            'bcpc_organizations.*.phone.regex' => 'The phone or mobile number may contain numbers only.',
        ]);

        $barangay = Barangay::findOrFail($data['barangay_id']);

        $monitoringData = collect($data)
            ->except(['barangay_id', 'quarter', 'year', 'monitoring_id'])
            ->map(fn ($value) => $value ?? [])
            ->all();

        $period = [
            'barangay_id' => $barangay->id,
            'quarter' => (int) $data['quarter'],
            'year' => (int) $data['year'],
        ];
        if (Auth::user()->isBcpcAdmin() && !$request->filled('monitoring_id')
            && BcpcCaseMonitoring::where($period)->whereNull('archived_at')->exists()) {
            return redirect()->route('admin.bcpc-case-monitoring.edit')
                ->with('error', 'A BCPC monitoring record already exists for the selected barangay, quarter, and year.');
        }
        $monitoring = Auth::user()->isBcpcAdmin()
            ? ($request->filled('monitoring_id')
                ? BcpcCaseMonitoring::query()->when(Auth::user()->isBcpcAdmin(), fn ($query) => $query->visibleToAdmin(Auth::id()))->where($period)->whereNull('archived_at')->findOrFail($request->monitoring_id)
                : new BcpcCaseMonitoring($period))
            : BcpcCaseMonitoring::firstOrNew($period);

        if (!$monitoring->exists) {
            $monitoring->created_by = Auth::id();
            if (Auth::user()->isDepartmentAdmin()) {
                $monitoring->submitted_at = now();
                $monitoring->submitted_by = Auth::id();
            }
        }
        if (!Auth::user()->isBcpcAdmin()) $monitoring->deleted_at = null;
        $monitoring->updated_by = Auth::id();
        $monitoring->data = $monitoringData;
        $monitoring->save();

        if (Auth::user()->isBcpcAdmin()) {
            return redirect()->route('admin.bcpc-case-monitoring.edit')
                ->with('success', 'BCPC case monitoring saved successfully.')
                ->with('saved_monitoring_id', $monitoring->id);
        }

        return redirect()->route('admin.bcpc-case-monitoring.edit', [
            'barangay_id' => $barangay->id,
            'quarter' => $data['quarter'],
            'year' => $data['year'],
        ])->with('success', "BCPC case monitoring (Q{$data['quarter']} {$data['year']}) saved successfully.");
    }

    /**
     * Print BCPC Case Monitoring for Admin.
     */
    public function bcpcCaseMonitoringPrint(Request $request)
    {
        abort_unless(Auth::user()->canAccessCaseDepartment('BCPC'), 403);
        $request->validate([
            'barangay_id' => ['required', Rule::exists(Barangay::class, 'id')],
            'quarter' => 'required|integer|min:1|max:4',
            'year' => 'required|integer|min:2020|max:2100',
        ]);
        $barangayId = $request->input('barangay_id');
        $quarter = $request->input('quarter');
        $year = $request->input('year');

        $barangay = Barangay::findOrFail($barangayId);
        $monitoring = BcpcCaseMonitoring::query()->when(Auth::user()->isBcpcAdmin(), fn ($query) => $query->visibleToAdmin(Auth::id()))->where([
            'barangay_id' => $barangay->id,
            'quarter' => $quarter,
            'year' => $year,
        ])->first();

        if (Auth::user()->isBcpcAdmin()) {
            $monitoring = new BcpcCaseMonitoring(['data' => BcpcCaseMonitoring::quarterTotals($barangay->id, (int) $quarter, (int) $year, Auth::id())]);
        }
        if (!$monitoring) {
            return back()->with('error', 'No BCPC monitoring data exists for this barangay and period.');
        }
        $data = $monitoring->data ?? [];
        $yearGroups = ['year_2023' => 'Year 2023', 'year_2024' => 'Year 2024', 'total_2025' => 'Total 2025'];
        $sexColumns = ['male' => 'Male', 'female' => 'Female', 'total' => 'Total'];
        $tables = [
            [
                'title' => 'Crimes Committed Against Children',
                'note' => 'What types of crime have been committed against children in the past 2 years?',
                'name' => 'bcpc_crimes_against_children',
                'label' => 'Crimes Against Children',
                'rows' => [
                    'rape' => 'Rape',
                    'incestuous_rape' => 'Incestuous Rape',
                    'attempted_frustrated_rape' => 'Attempted/Frustrated Rape',
                    'acts_of_lasciviousness' => 'Acts of Lasciviousness',
                    'physical_injuries' => 'Physical Injuries',
                    'kidnapping_illegal_detention' => 'Kidnapping/Illegal Detention',
                    'trafficking' => 'RA 9208 amended by RA 10634 (Trafficking in persons)',
                    'drugs_substance_abuse' => 'Drugs/Substance Abuse',
                    'others' => 'Others, specify',
                    'total' => 'TOTAL'
                ]
            ],
            [
                'title' => 'Crimes Committed by Children in Conflict with the Law (CICL)',
                'note' => 'What types of crime have been committed by children in conflict with the law (CICL) in the past 2 years?',
                'name' => 'bcpc_cicl_crimes',
                'label' => 'Crimes Against Children',
                'rows' => [
                    'rape' => 'Rape',
                    'incestuous_rape' => 'Incestuous Rape',
                    'attempted_frustrated_rape' => 'Attempted/Frustrated Rape',
                    'acts_of_lasciviousness' => 'Acts of Lasciviousness',
                    'physical_injuries' => 'Physical Injuries'
                ]
            ],
            [
                'title' => 'Children in Conflict with the Law (CICL) and Status',
                'note' => 'Summary: Number of Children in Conflict with the Law (CICL) and their status. Choose one category and avoid double counting.',
                'name' => 'bcpc_cicl_status',
                'label' => 'Categories of CICL',
                'rows' => [
                    'diversion_program' => 'Number of CICL undergoing Diversion Program',
                    'jail_detention_center' => 'Number of CICL in Jail/Detention Center',
                    'bahay_pagasa' => 'Number of CICL in Bahay Pag-asa',
                    'ijisc' => 'Number of CICL under Intensive Juvenile Intervention and Support for Center (IJISC)',
                    'rrcy' => 'Number of CICL in RRCY',
                    'after_care_program' => 'Number of CICL undergoing After-Care Program',
                    're_offended' => 'Number of CICL that have re-offended',
                    'fully_integrated' => 'Number of CICL that have been fully integrated',
                    'community_based' => 'Number of CICL undergoing community-based',
                    'cbdr' => 'Number of CICL undergoing Community-Based Rehabilitation Program (CBDR)',
                    'others' => 'Others, please specify:',
                    'total' => 'Total'
                ]
            ],
            [
                'title' => 'Children at Risk',
                'note' => 'Number of Children at Risk as defined in RA 9344 as amended. Choose only one category for the child and avoid double counting.',
                'name' => 'bcpc_children_at_risk',
                'label' => 'Categories of CAR',
                'rows' => [
                    'curfew_hour' => 'Curfew Hour',
                    'truancy' => 'Truancy',
                    'parental_disobedience' => 'Parental Disobedience',
                    'anti_smoking' => 'Anti-Smoking',
                    'anti_drinking' => 'Anti-Drinking',
                    'disorderly_conduct' => 'Disorderly conduct',
                    'public_scandal' => 'Public scandal',
                    'harassment' => 'Harassment',
                    'public_intoxication' => 'Public intoxication',
                    'criminal_nuisance' => 'Criminal nuisance',
                    'vandalism' => 'Vandalism',
                    'gambling' => 'Gambling',
                    'mendicancy' => 'Mendicancy (Presidential Decree No. 1563)'
                ]
            ],
            [
                'title' => 'Children Needing Special Protection',
                'note' => 'Children needing special protection and other child protection categories.',
                'name' => 'bcpc_special_protection',
                'label' => 'Category',
                'rows' => [
                    'parents_lost_jobs_covid' => 'Children whose parents lost jobs due to COVID-19',
                    'children_with_disabilities' => 'Children with disabilities',
                    'indigenous_families' => 'Children of indigenous families',
                    'four_ps_families' => '4Ps families',
                    'child_surrenderers_drug_campaign' => 'Child-surrenderers in relation to the Philippine Campaign Against Illegal Drugs',
                    'total' => 'TOTAL'
                ]
            ],
        ];

        return view('pdf.bcpc-case-monitoring', compact(
            'barangay',
            'quarter',
            'year',
            'data',
            'yearGroups',
            'sexColumns',
            'tables'
        ));
    }

    /**
     * Display archived BCPC Case Monitoring records.
     */
    public function bcpcCaseMonitoringArchived(Request $request)
    {
        $barangays = Barangay::orderBy('name')->get();
        $barangayId = $request->input('barangay_id');
        $quarter = $request->input('quarter');
        $year = $request->input('year');
        $search = trim((string) $request->input('search'));

        $monitorings = BcpcCaseMonitoring::with(['barangay', 'creator'])
            ->whereNotNull('archived_at')
            ->when($request->user()->isBcpcAdmin(), fn ($query) => $query->visibleToAdmin($request->user()->id))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->whereHas('barangay', fn ($barangayQuery) => $barangayQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('creator', fn ($creatorQuery) => $creatorQuery
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%"));
                });
            })
            ->when($barangayId, fn ($query) => $query->where('barangay_id', $barangayId))
            ->when($quarter, fn ($query) => $query->where('quarter', $quarter))
            ->when($year, fn ($query) => $query->where('year', $year))
            ->orderByDesc('archived_at')
            ->paginate($request->user()->isDepartmentAdmin() && $request->boolean('portrait_cards') ? 4 : 5)
            ->withQueryString();
        $activeCount = BcpcCaseMonitoring::count();
        $archivedCount = BcpcCaseMonitoring::whereNotNull('archived_at')
            ->when($request->user()->isBcpcAdmin(), fn ($query) => $query->visibleToAdmin($request->user()->id))
            ->count();
        $activeMonitoringPeriods = BcpcCaseMonitoring::whereNull('archived_at')
            ->get(['barangay_id', 'quarter', 'year'])
            ->mapWithKeys(fn ($monitoring) => [
                "{$monitoring->barangay_id}-{$monitoring->quarter}-{$monitoring->year}" => true,
            ])
            ->all();

        return view('admin-tailwind.bcpc-case-monitoring-archived', compact(
            'barangays',
            'barangayId',
            'quarter',
            'year',
            'monitorings',
            'activeCount',
            'archivedCount',
            'activeMonitoringPeriods'
        ));
    }

    /**
     * Archive a BCPC Case Monitoring record.
     */
    public function bcpcCaseMonitoringArchive($id)
    {
        try {
            $monitoring = BcpcCaseMonitoring::query()->when(Auth::user()->isBcpcAdmin(), fn ($query) => $query->visibleToAdmin(Auth::id()))->findOrFail($id);
            if (Auth::user()->isBcpcAdmin()) {
                $monitoring->archived_at = now();
                $monitoring->save();
            } else {
                $monitoring->delete();
            }
        } catch (\Throwable $exception) {
            report($exception);
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unable to archive this BCPC case monitoring record.'], 422);
            }
            return back()->with('error', 'Unable to archive this BCPC case monitoring record.');
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'BCPC case monitoring record has been moved to archive.',
            ]);
        }

        return redirect()->back()->with('success', 'BCPC case monitoring record has been moved to archive.');
    }

    /**
     * Restore an archived BCPC Case Monitoring record.
     */
    public function bcpcCaseMonitoringRestore($id)
    {
        if (Auth::user()->isBcpcAdmin()) {
            $monitoring = BcpcCaseMonitoring::query()->when(Auth::user()->isBcpcAdmin(), fn ($query) => $query->visibleToAdmin(Auth::id()))->whereNotNull('archived_at')->findOrFail($id);

            if (BcpcCaseMonitoring::where('barangay_id', $monitoring->barangay_id)
                ->where('quarter', $monitoring->quarter)
                ->where('year', $monitoring->year)
                ->whereNull('archived_at')
                ->exists()) {
                $message = 'An active BCPC monitoring record already exists for this barangay, quarter, and year.';
                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return redirect()->back()->with('error', $message);
            }

            $monitoring->archived_at = null;
            $monitoring->save();
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'BCPC monitoring report restored.']);
            }
            return redirect()->route('admin.bcpc-case-monitoring.archived')
                ->with('success', 'BCPC monitoring report restored.');
        }
        // Soft deletes not enabled - return error
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => false, 'message' => 'Restore functionality not available'], 422);
        }
        return redirect()->back()->with('error', 'Restore functionality not available');
    }

    public function bcpcCaseMonitoringForceDelete($id)
    {
        abort_unless(Auth::user()?->isBcpcAdmin(), 403);
        $monitoring = BcpcCaseMonitoring::whereNotNull('archived_at')->findOrFail($id);
        $monitoring->delete();
        return response()->json(['success' => true, 'message' => 'BCPC monitoring report permanently deleted.']);
    }

    /**
     * Calculate total BCPC cases from monitoring data.
     */
    public function calculateBcpcTotalCases(array $monitoringData): int
    {
        $total = 0;
        
        if (!empty($monitoringData['bcpc_crimes_against_children'])) {
            $table = $monitoringData['bcpc_crimes_against_children'];
            $tableSum = 0;
            if (isset($table['total']) && is_array($table['total'])) {
                foreach ($table['total'] as $yearCol) {
                    if (is_array($yearCol)) {
                        $val = (int) ($yearCol['total'] ?? 0);
                        if ($val === 0) {
                            $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                        }
                        $tableSum += $val;
                    }
                }
            }
            if ($tableSum === 0) {
                foreach ($table as $rowKey => $rowYears) {
                    if ($rowKey === 'total' || !is_array($rowYears)) continue;
                    foreach ($rowYears as $yearCol) {
                        if (is_array($yearCol)) {
                            $val = (int) ($yearCol['total'] ?? 0);
                            if ($val === 0) {
                                $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                            }
                            $tableSum += $val;
                        }
                    }
                }
            }
            $total += $tableSum;
        }

        if (!empty($monitoringData['bcpc_cicl_crimes'])) {
            $table = $monitoringData['bcpc_cicl_crimes'];
            $ciclSum = 0;
            foreach ($table as $rowKey => $rowYears) {
                if ($rowKey === 'total' || !is_array($rowYears)) continue;
                foreach ($rowYears as $yearCol) {
                    if (is_array($yearCol)) {
                        $val = (int) ($yearCol['total'] ?? 0);
                        if ($val === 0) {
                            $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                        }
                        $ciclSum += $val;
                    }
                }
            }
            $total += $ciclSum;
        }

        if (!empty($monitoringData['bcpc_children_at_risk'])) {
            $table = $monitoringData['bcpc_children_at_risk'];
            $carSum = 0;
            foreach ($table as $rowKey => $rowYears) {
                if ($rowKey === 'total' || !is_array($rowYears)) continue;
                foreach ($rowYears as $yearCol) {
                    if (is_array($yearCol)) {
                        $val = (int) ($yearCol['total'] ?? 0);
                        if ($val === 0) {
                            $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                        }
                        $carSum += $val;
                    }
                }
            }
            $total += $carSum;
        }

        if (!empty($monitoringData['bcpc_special_protection'])) {
            $table = $monitoringData['bcpc_special_protection'];
            $spSum = 0;
            if (isset($table['total']) && is_array($table['total'])) {
                foreach ($table['total'] as $yearCol) {
                    if (is_array($yearCol)) {
                        $val = (int) ($yearCol['total'] ?? 0);
                        if ($val === 0) {
                            $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                        }
                        $spSum += $val;
                    }
                }
            }
            if ($spSum === 0) {
                foreach ($table as $rowKey => $rowYears) {
                    if ($rowKey === 'total' || !is_array($rowYears)) continue;
                    foreach ($rowYears as $yearCol) {
                        if (is_array($yearCol)) {
                            $val = (int) ($yearCol['total'] ?? 0);
                            if ($val === 0) {
                                $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                            }
                            $spSum += $val;
                        }
                    }
                }
            }
            $total += $spSum;
        }

        if ($total === 0 && !empty($monitoringData['bcpc_cicl_status'])) {
            $table = $monitoringData['bcpc_cicl_status'];
            if (isset($table['total']) && is_array($table['total'])) {
                foreach ($table['total'] as $yearCol) {
                    if (is_array($yearCol)) {
                        $val = (int) ($yearCol['total'] ?? 0);
                        if ($val === 0) {
                            $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                        }
                        $total += $val;
                    }
                }
            }
        }

        return $total;
    }

    private function getBcpcMonitoringTables(): array
    {
        return [
            [
                'title' => 'Crimes Committed Against Children',
                'note' => 'What types of crime have been committed against children in the past 2 years?',
                'name' => 'bcpc_crimes_against_children',
                'label' => 'Crimes Against Children',
                'rows' => [
                    'rape' => 'Rape',
                    'incestuous_rape' => 'Incestuous Rape',
                    'attempted_frustrated_rape' => 'Attempted/Frustrated Rape',
                    'acts_of_lasciviousness' => 'Acts of Lasciviousness',
                    'physical_injuries' => 'Physical Injuries',
                    'kidnapping_illegal_detention' => 'Kidnapping/Illegal Detention',
                    'trafficking' => 'RA 9208 amended by RA 10634 (Trafficking in persons)',
                    'drugs_substance_abuse' => 'Drugs/Substance Abuse',
                    'others' => 'Others, specify',
                    'total' => 'TOTAL'
                ]
            ],
            [
                'title' => 'Crimes Committed by Children in Conflict with the Law (CICL)',
                'note' => 'What types of crime have been committed by children in conflict with the law (CICL) in the past 2 years?',
                'name' => 'bcpc_cicl_crimes',
                'label' => 'Crimes Against Children',
                'rows' => [
                    'rape' => 'Rape',
                    'incestuous_rape' => 'Incestuous Rape',
                    'attempted_frustrated_rape' => 'Attempted/Frustrated Rape',
                    'acts_of_lasciviousness' => 'Acts of Lasciviousness',
                    'physical_injuries' => 'Physical Injuries'
                ]
            ],
            [
                'title' => 'Children in Conflict with the Law (CICL) and Status',
                'note' => 'Summary: Number of Children in Conflict with the Law (CICL) and their status. Choose one category and avoid double counting.',
                'name' => 'bcpc_cicl_status',
                'label' => 'Categories of CICL',
                'rows' => [
                    'diversion_program' => 'Number of CICL undergoing Diversion Program',
                    'jail_detention_center' => 'Number of CICL in Jail/Detention Center',
                    'bahay_pagasa' => 'Number of CICL in Bahay Pag-asa',
                    'ijisc' => 'Number of CICL under Intensive Juvenile Intervention and Support for Center (IJISC)',
                    'rrcy' => 'Number of CICL in RRCY',
                    'after_care_program' => 'Number of CICL undergoing After-Care Program',
                    're_offended' => 'Number of CICL that have re-offended',
                    'fully_integrated' => 'Number of CICL that have been fully integrated',
                    'community_based' => 'Number of CICL undergoing community-based',
                    'cbdr' => 'Number of CICL undergoing Community-Based Rehabilitation Program (CBDR)',
                    'others' => 'Others, please specify:',
                    'total' => 'Total'
                ]
            ],
            [
                'title' => 'Children at Risk',
                'note' => 'Number of Children at Risk as defined in RA 9344 as amended. Choose only one category for the child and avoid double counting.',
                'name' => 'bcpc_children_at_risk',
                'label' => 'Categories of CAR',
                'rows' => [
                    'curfew_hour' => 'Curfew Hour',
                    'truancy' => 'Truancy',
                    'parental_disobedience' => 'Parental Disobedience',
                    'anti_smoking' => 'Anti-Smoking',
                    'anti_drinking' => 'Anti-Drinking',
                    'disorderly_conduct' => 'Disorderly conduct',
                    'public_scandal' => 'Public scandal',
                    'harassment' => 'Harassment',
                    'public_intoxication' => 'Public intoxication',
                    'criminal_nuisance' => 'Criminal nuisance',
                    'vandalism' => 'Vandalism',
                    'gambling' => 'Gambling',
                    'mendicancy' => 'Mendicancy (Presidential Decree No. 1563)'
                ]
            ],
            [
                'title' => 'Children Needing Special Protection',
                'note' => 'Children needing special protection and other child protection categories.',
                'name' => 'bcpc_special_protection',
                'label' => 'Category',
                'rows' => [
                    'parents_lost_jobs_covid' => 'Children whose parents lost jobs due to COVID-19',
                    'children_with_disabilities' => 'Children with disabilities',
                    'indigenous_families' => 'Children of indigenous families',
                    'four_ps_families' => '4Ps families',
                    'child_surrenderers_drug_campaign' => 'Child-surrenderers in relation to the Philippine Campaign Against Illegal Drugs',
                    'total' => 'TOTAL'
                ]
            ],
        ];
    }
}
