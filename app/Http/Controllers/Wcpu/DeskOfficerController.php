<?php

namespace App\Http\Controllers\Wcpu;

use App\Http\Controllers\Controller;
use App\Models\Wcpu\CaseModel;
use App\Models\Wcpu\Barangay;
use App\Models\Wcpu\CaseType;
use App\Models\Wcpu\User;
use App\Models\Wcpu\CaseReferral;
use App\Models\Wcpu\ReportRecipient; // <--- added
use App\Mail\Wcpu\CaseReferralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon; // added for Carbon::parse usage in show()
use Illuminate\Support\Facades\Crypt; // optional, helper decrypt() also works

class DeskOfficerController extends Controller
{
    public function archivedBarangayCases()
    {
        $user = Auth::user();
        $department = $user?->defaultCaseDepartment();
        $instantArchiveSearch = in_array($department, ['VAWC', 'BCPC'], true);
        abort_unless($user && $user->position === 'Desk Officer' && in_array($department, ['VAWC', 'BCPC'], true), 403);

        $barangay = Barangay::where('id', $user->barangay)
            ->orWhere('name', $user->barangay)
            ->firstOrFail();

        $search = trim((string) request('search'));
        $status = trim((string) request('status'));

        $applyFilters = function ($q) use ($search, $status) {
            if ($search !== '') {
                $q->where(function ($sub) use ($search) {
                    $sub->where('case_number', 'like', "%{$search}%")
                        ->orWhere('victim_name', 'like', "%{$search}%")
                        ->orWhere('abuse_type', 'like', "%{$search}%");
                });
            }
            if ($status !== '') {
                $status === 'Case closed'
                    ? $q->whereIn('status', ['Case closed', 'Case Closed', 'Complete case', 'Complete Case'])
                    : $q->where('status', $status);
            }
        };

        $casesQuery = CaseModel::onlyTrashed()
            ->with(['barangay', 'caseType'])
            ->where('case_designation', $barangay->id)
            ->where('type', $department);
        if (!$instantArchiveSearch) $applyFilters($casesQuery);
        $cases = $casesQuery->latest('deleted_at')
            ->paginate($instantArchiveSearch ? PHP_INT_MAX : 5, ['*'], 'page', $instantArchiveSearch ? 1 : null)
            ->withQueryString();

        $caseGroups = [$department => $cases];
        if ($user->isMultiCaseDepartment()) {
            $caseGroups = [];
            foreach ($user->caseDepartments() as $type) {
                $groupQuery = CaseModel::onlyTrashed()->with(['barangay', 'caseType'])
                    ->where('case_designation', $barangay->id)->where('type', $type);
                if (!$instantArchiveSearch) $applyFilters($groupQuery);
                $caseGroups[$type] = $groupQuery->latest('deleted_at')->paginate($instantArchiveSearch ? PHP_INT_MAX : 5, ['*'], strtolower($type) . '_page', $instantArchiveSearch ? 1 : null)->withQueryString();
            }
        }
        return view('desk-officer.archived-barangay-cases', compact('cases', 'caseGroups', 'barangay', 'department'));
    }

    public function restoreArchivedBarangayCase(Request $request, $id)
    {
        $user = Auth::user();
        $department = $user?->defaultCaseDepartment();
        abort_unless($user && $user->position === 'Desk Officer' && in_array($department, ['VAWC', 'BCPC'], true), 403);

        $barangay = Barangay::where('id', $user->barangay)
            ->orWhere('name', $user->barangay)
            ->firstOrFail();

        $case = CaseModel::withTrashed()
            ->where('id', $id)
            ->where('case_designation', $barangay->id)
            ->whereIn('type', $user->caseDepartments())
            ->firstOrFail();

        if ($case->trashed()) {
            $case->restore();
        }

        if ($request->boolean('edit')) {
            return redirect()->route('cases.edit', $case->id);
        }

        return redirect()->route('desk-officer.archived-barangay-cases.index')
            ->with('success', 'Barangay case restored successfully.');
    }

    public function forceDeleteArchivedBarangayCase($id)
    {
        $user = Auth::user();
        abort_unless($user?->position === 'Desk Officer' && in_array($user?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true), 403);
        $barangay = Barangay::where('id', $user->barangay)->orWhere('name', $user->barangay)->firstOrFail();
        $case = CaseModel::onlyTrashed()->where('id', $id)->where('case_designation', $barangay->id)->whereIn('type', $user->caseDepartments())->firstOrFail();
        $case->forceDelete();

        return back()->with('success', 'Archived barangay case deleted successfully.');
    }

    private function normalizeCaseDepartment(?string $department): string
    {
        if ($department === null) {
            return 'VAWC';
        }
        $lowerDept = strtolower((string) $department);
        return $lowerDept === 'bcpc' ? 'BCPC' : ($lowerDept === 'vawc' ? 'VAWC' : 'VAWC');
    }

    private function requestedCaseDepartment(?string $requested = null): string
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403);
        }

        abort_unless($user->caseDepartments(), 403);

        $defaultDepartment = $user->defaultCaseDepartment();

        if (!$user->isMultiCaseDepartment()) {
            if ($requested !== null || request()->has('type')) {
                $requestedDepartment = $this->normalizeCaseDepartment($requested ?? request('type'));
                abort_unless($requestedDepartment === $defaultDepartment, 403);
            }

            return $defaultDepartment;
        }

        $requestedDepartment = $this->normalizeCaseDepartment($requested ?? request('type', $defaultDepartment));
        abort_unless($user->canAccessCaseDepartment($requestedDepartment), 403);

        return $requestedDepartment;
    }

    private function applyCaseDepartmentScope($query, ?string $department = null)
    {
        $user = Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($department) {
            if (!$user->canAccessCaseDepartment($department)) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where('type', $this->normalizeCaseDepartment($department));
        }

        return $query->whereIn('type', $user->caseDepartments());
    }

    public function dashboard(?string $forcedDepartment = null)
    {
        try {
            // Enable error reporting for debugging
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            // Load the active department immediately. A requested department
            // takes precedence; otherwise the account's configured default is
            // used so multi-role dashboards are populated on the first login.
            $hasSelectedDepartment = true;
            $dashboardType = $this->requestedCaseDepartment($forcedDepartment ?? request('type'));
            
            \Log::info('Dashboard accessed by user', [
                'user_id' => $user->id,
                'barangay' => $user->barangay,
                'time' => now()
            ]);
            
            // Get the user's barangay (could be name or ID)
            $barangayIdentifier = $user->barangay;
            
            // Debug logging
            \Log::info('User data:', [
                'user_id' => $user->id,
                'user_barangay' => $barangayIdentifier,
                'barangay_type' => gettype($barangayIdentifier)
            ]);
            
            // Try to find barangay by ID first, then by name
            $barangay = null;
            if ($barangayIdentifier) {
                $barangay = Barangay::where('id', $barangayIdentifier)
                    ->orWhere('name', $barangayIdentifier)
                    ->first();
                
                if (!$barangay) {
                    \Log::warning('Barangay not found', ['identifier' => $barangayIdentifier]);
                }
            }
            
            // Prepare default response data
            $viewData = [
                'stats' => [
                    'totalCases' => 0,
                    'resolvedCases' => 0,
                    'pendingCases' => 0,
                    'todayCases' => 0,
                    'casesReferred' => 0,
                ],
                'caseStatus' => collect(),
                'caseTypes' => collect(),
                'recentCases' => collect(),
                'recentActivities' => [],
                'barangayName' => 'Unknown Barangay',
                'barangayId' => null,
                'departmentSelected' => $hasSelectedDepartment,
            ];
            
            if ($barangay) {
                $viewData['barangayName'] = $barangay->name;
                $viewData['barangayId'] = $barangay->id;
            }

            if ($barangay && $hasSelectedDepartment) {
                // Show all cases in the barangay (shared among desk officers)
                $userId = Auth::id();
                
                // Get cases for this barangay (all officers)
                $viewData['stats'] = [
                    'totalCases' => $this->applyCaseDepartmentScope(CaseModel::where('case_designation', $barangay->id), $dashboardType)
                        ->count(),
                    'resolvedCases' => $this->applyCaseDepartmentScope(
                        CaseModel::where(function ($q) use ($barangay) {
                            $q->where('case_designation', $barangay->id)
                              ->orWhere('case_designation', $barangay->name);
                        }),
                        $dashboardType
                    )->whereIn('status', ['Case Closed', 'Case closed', 'Closed', 'Complete case', 'Complete Case', 'Complete Cases', 'Completed', 'Resolved'])->count(),
                    'pendingCases' => $this->applyCaseDepartmentScope(CaseModel::where('case_designation', $barangay->id), $dashboardType)
                        ->where('status', in_array($dashboardType, ['BCPC', 'VAWC'], true) ? 'Case Filed' : 'Pending')
                        ->count(),
                    'todayCases' => $this->applyCaseDepartmentScope(CaseModel::where('case_designation', $barangay->id), $dashboardType)
                        ->whereDate('created_at', today())
                        ->count(),
                    // Count both legacy and current referral status labels
                    'casesReferred' => $this->applyCaseDepartmentScope(CaseModel::where('case_designation', $barangay->id), $dashboardType)
                        ->whereIn('status', ['Case Referred', 'Referred'])
                        ->count(),
                ];
                
                $viewData['caseStatus'] = $this->applyCaseDepartmentScope(CaseModel::where('case_designation', $barangay->id), $dashboardType)
                    ->select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get();
                
                try {
                    // Get case types that have cases in this barangay (all officers)
                    $viewData['caseTypes'] = CaseType::select('id', 'name', 'republic_act')
                        ->whereHas('cases', function($query) use ($barangay, $dashboardType) {
                            $query->where('case_designation', $barangay->id);
                            $this->applyCaseDepartmentScope($query, $dashboardType);
                        })
                        ->withCount(['cases' => function($query) use ($barangay, $dashboardType) {
                            $query->where('case_designation', $barangay->id);
                            $this->applyCaseDepartmentScope($query, $dashboardType);
                        }])
                        ->orderBy('name')
                        ->get();
                    
                    \Log::info('Case Types with Cases in Barangay ' . $barangay->name . ':', [
                        'total_case_types' => $viewData['caseTypes']->count(),
                        'case_types' => $viewData['caseTypes']->map(fn($type) => [
                            'id' => $type->id,
                            'name' => $type->name,
                            'cases_count' => $type->cases_count
                        ])
                    ]);
                    
                } catch (\Exception $e) {
                    \Log::error('Error fetching case types: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString(),
                        'case_designation' => $barangay->id ?? null
                    ]);
                    $viewData['caseTypes'] = collect();
                }
                
                $isMultiroleDeskOfficer = $user->position === 'Desk Officer' && $user->isMultiCaseDepartment();
                $isBcpcDeskOfficer = $user->position === 'Desk Officer' && $user->defaultCaseDepartment() === 'BCPC' && !$user->isMultiCaseDepartment();
                $isSwdDeskOfficer = $user->position === 'Desk Officer' && $dashboardType === 'VAWC';
                $recentCasesLimit = ($isMultiroleDeskOfficer || $isBcpcDeskOfficer || $isSwdDeskOfficer) ? 3 : 5;

                $recentCasesQuery = CaseModel::with(['caseType', 'createdBy'])
                    ->where('case_designation', $barangay->id)
                    ->where('type', $dashboardType);

                if ($isMultiroleDeskOfficer || $isBcpcDeskOfficer) {
                    $recentCasesQuery->orderByDesc('updated_at')->orderByDesc('created_at');
                } else {
                    $recentCasesQuery->orderBy('created_at', 'desc');
                }

                $viewData['recentCases'] = $recentCasesQuery
                    ->take($recentCasesLimit)
                    ->get();
                
                // Generate recent activities from recent cases
                $viewData['recentActivities'] = $viewData['recentCases']->map(function($case) {
                    return [
                        'icon' => 'file-alt',
                        'text' => 'Case #' . $case->case_number . ' was created',
                        'time' => $case->created_at->diffForHumans()
                    ];
                })->toArray();
            }
            
            // Add debug info to the view
            $viewData['debug_info'] = [
                'user' => [
                    'id' => $user->id,
                    'barangay' => $user->barangay,
                    'case_designation' => $barangay->id ?? null
                ],
                'case_types_count' => $viewData['caseTypes']->count()
            ];
            
            return view('desk-officer.officer-dashboard', $viewData)
                ->with('dashboardType', strtolower($dashboardType));
            
        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Return a simple error page with debug info
            return response()->view('errors.500', [
                'message' => 'An error occurred while loading the dashboard.',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function bcpcDashboard()
    {
       $user = Auth::user();

    if (!$user) {
        abort(403);
    }

    abort_unless($user->canAccessCaseDepartment('BCPC'), 403);

    return $this->dashboard('BCPC');
    }
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            if ($user->position === 'Desk Officer'
                && in_array($user->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)
                && !$user->isMultiCaseDepartment()) {
                $barangay = Barangay::where('id', $user->barangay)
                    ->orWhere('name', $user->barangay)
                    ->firstOrFail();

                return redirect()->route('admin.barangay-cases.show', $barangay->id);
            }

            if ($user->isMultiCaseDepartment() && !$request->filled('type')) {
                return redirect()->route('desk-officer.cases.index', array_merge($request->query(), ['type' => 'bcpc']));
            }

            $barangayName = $user->barangay;
            if (!$barangayName) {
                throw new \Exception('User has no barangay assigned');
            }
            
            // Debug logging
            \Log::info('Desk Officer Cases - Debug Info', [
                'user_id' => $user->id,
                'barangay_name' => $barangayName,
                'request_params' => $request->all()
            ]);
            
            // Find barangay by name (case insensitive)
            $barangay = Barangay::where('name', 'LIKE', $barangayName)->first();
            if (!$barangay) {
                // Try case-insensitive search as fallback
                $barangay = Barangay::whereRaw('LOWER(name) = ?', [strtolower($barangayName)])->first();
                if (!$barangay) {
                    throw new \Exception('Barangay not found with name: ' . $barangayName);
                }
            }
            
            $barangayId = $barangay->id;
            $barangayName = $barangay->name;
            $userId = Auth::id();
            $requestedDepartment = $request->filled('type') ? $this->requestedCaseDepartment($request->type) : null;
            $status = $request->get('status');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            
            // Base query with relationships and barangay filter.
            $query = CaseModel::with(['assignedTo', 'createdBy'])
                ->where('case_designation', $barangayId)
                ->select([
                    'id',
                    'case_number',
                    'date_reported',
                    'victim_name',
                    'abuse_type',
                    'priority',
                    'status',
                    'type as case_type_id',
                    'assigned_to',
                    'created_by',
                    'created_at',
                    'updated_at'
                ])
                ->orderByDesc('updated_at')
                ->orderByDesc('created_at');
            $this->applyCaseDepartmentScope($query, $requestedDepartment);
                
            // Apply status filter if provided
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
            
            // Apply abuse-type filter if provided.
            if ($request->filled('case_type')) {
                $query->where('abuse_type', $request->case_type);
            }
            
            // Get paginated results
            $perPage = 5;
            $cases = $query->paginate($perPage)->withQueryString();
            
            // Log query results
            \Log::info('Cases Query Results', [
                'case_designation' => $barangayId,
                'total_cases' => $cases->total(),
                'current_page' => $cases->currentPage(),
                'per_page' => $perPage,
                'items_count' => $cases->count(),
                'has_pages' => $cases->hasPages()
            ]);
            
            // Ensure we have a LengthAwarePaginator instance
            if (!($cases instanceof \Illuminate\Pagination\LengthAwarePaginator)) {
                throw new \Exception('Query did not return a paginator instance');
            }
            
            // Get statistics for all cases in the barangay.
            $baseQuery = CaseModel::where('case_designation', $barangayId);
            $this->applyCaseDepartmentScope($baseQuery, $requestedDepartment);
            $totalCases = $baseQuery->count();
            
            $stats = [
                'total_cases'    => $totalCases,
                'case_reported'  => $totalCases > 0 ? (clone $baseQuery)->where('status', 'Case Filed')->count() : 0,
                'case_referred'  => $totalCases > 0 ? (clone $baseQuery)->where('status', 'Case Referred')->count() : 0,
                'case_filed'     => $totalCases > 0 ? (clone $baseQuery)->where('status', 'Case Filed')->count() : 0,
                'case_closed'    => $totalCases > 0 ? (clone $baseQuery)->whereIn('status', ['Case closed', 'Case Closed', 'Complete case', 'Complete Case'])->count() : 0,
            ];
            
            // Get case types with counts for the barangay.
            $caseTypes = CaseType::select('id', 'name', 'republic_act')
                ->withCount(['cases' => function($query) use ($barangayId, $requestedDepartment) {
                    $query->where('case_designation', $barangayId);
                    $this->applyCaseDepartmentScope($query, $requestedDepartment);
                }])
                ->orderBy('name')
                ->get();
            
            // Get users for assignment dropdown
            $users = User::where('is_active', true)->get();
            
            // Get active filters for the view
            $activeFilters = [
                'status' => $request->get('status'),
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'type' => $requestedDepartment ? strtolower($requestedDepartment) : $request->get('type'),
                // Add allowed status options for filter dropdown
                'statusOptions' => [
                    'Case Referred',
                    'Case Filed',
                    'Case closed',
                ],
            ];

            return view('desk-officer.officer-cases', [
                'cases' => $cases,
                'stats' => $stats,
                'caseTypes' => $caseTypes,
                'users' => $users,
                'barangayName' => $barangayName,
                'activeFilters' => $activeFilters,
                'totalCases' => $totalCases,
                'perPage' => $perPage,
                'currentUserId' => $userId,
                'error' => null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in DeskOfficerController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'case_designation' => $barangayId ?? null
            ]);
            
            return view('desk-officer.officer-cases', [
                'cases' => collect(),
                'stats' => [
                    'total_cases' => 0,
                    'pending_cases' => 0,
                    'in_progress_cases' => 0,
                    'resolved_cases' => 0,
                ],
                'caseTypes' => collect(),
                'users' => collect(),
                'barangayName' => 'Error',
                'activeFilters' => [],
                'totalCases' => 0,
                'perPage' => 5,
                'error' => 'Error loading cases: ' . $e->getMessage()
            ]);
        }
    }
public function create()
{
    try {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        // If user is multi-department and didn't specify ?type=, redirect to cases index with modal trigger
        if ($user->isMultiCaseDepartment() && !request()->filled('type')) {
            return redirect()->route('desk-officer.cases.index', ['select_case_type' => 1]);
        }

        $caseDepartment = $this->requestedCaseDepartment(request()->get('type'));
        if ($user->isMultiCaseDepartment() && request()->boolean('fresh')) {
            request()->session()->forget('_old_input');
        }
        $isBCPCCase = $caseDepartment === 'BCPC';
        $type = strtolower($caseDepartment);

        \Log::info('Desk Officer Create - Department Debug', [
            'user_id' => $user->id,
            'user_department' => $user->department,
            'user_position' => $user->position,
            'requested_type' => request()->get('type'),
            'caseDepartment' => $caseDepartment,
            'isBCPCCase' => $isBCPCCase,
        ]);

        $caseTypes = \App\Models\Wcpu\CaseType::orderBy('name')->get();
        $barangays = \App\Models\Wcpu\Barangay::orderBy('name')->get();

        // Get the current user's assigned barangay
        $userBarangay = Barangay::where('name', $user->barangay)
            ->orWhere('id', $user->barangay)
            ->first();

        if ($caseTypes->isEmpty() || $barangays->isEmpty()) {
            throw new \Exception('Required data not found. Please check database records.');
        }

        if (!$userBarangay) {
            throw new \Exception('Your assigned barangay not found. Please contact administrator.');
        }

        return view('desk-officer.officers-create', compact(
            'caseTypes',
            'barangays',
            'userBarangay',
            'type',
            'isBCPCCase'
        ));

    } catch (\Exception $e) {
        \Log::error('Error in create method: ' . $e->getMessage());

        return redirect()->back()->with('error', 'Error loading the form: ' . $e->getMessage());
    }
}

    public function store(Request $request)
    {
        if ($request->has('priority') && !$request->has('case_priority')) {
            $request->merge(['case_priority' => $request->input('priority')]);
        }

        $caseDepartment = $this->requestedCaseDepartment($request->input('case_type'));
        $victimAgeRule = $caseDepartment === 'VAWC' ? 'required|integer|min:0' : 'required|integer|min:0|max:17';
        $validated = $request->validate([
            'victim_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'victim_age' => $victimAgeRule,
            'victim_gender' => 'required|string|in:Female,Male,LGBTQIA+,Prefer not to say',
            'victim_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
            'victim_address' => 'required|string',
            'guardian' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'guardian_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
            'guardian_address' => 'nullable|string',
            // guardian relation (kept under guardian info)
            'relationship_to_victim' => 'nullable|string|max:255',
            // abuser info
            'perpetrator_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'abuser_address' => 'required|string|max:2000',
            'abuser_age' => 'required|integer|min:1|max:120',
            'abuse_relationship' => 'required|string|max:255',
            // Guardian of Abuser fields
            'guardian_abuser_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'guardian_abuser_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
            'guardian_abuser_address' => 'nullable|string|max:255',
            'relationship_to_abuser' => 'nullable|string|max:255',
            'abuse_type' => 'required|string|max:255',
            'other_abuse_details' => 'required_if:abuse_type,Others|nullable|string|max:1000',
            'incident_date' => 'required|date',
            'incident_time' => 'required',
            'date_reported' => 'required|date',
            'incident_location' => 'required|string',
            'incident_details' => 'required|string',
            'witness_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'witness_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
            'case_type' => 'nullable|string|in:vawc,bcpc,VAWC,BCPC',
            'case_priority' => 'required|string|in:Urgent,Emergency,Non-urgent',
            'status' => 'required|string|in:Case Filed,Case Closed,Case Referred',
        ], [
            'victim_age.max' => 'Victim age must be 17 or below.',
            'abuser_age.required' => 'Perpetrator age is required.',
            'abuse_relationship.required' => 'Relationship of perpetrator to victim is required.',
            'other_abuse_details.required_if' => 'Please provide an explanation for the specific type of abuse when selecting "Others".',
        ]);

        // Get the current user's assigned barangay
        $user = Auth::user();
        $userBarangay = Barangay::where('name', $user->barangay)->first();
        
        if (!$userBarangay) {
            return redirect()->back()->with('error', 'Your assigned barangay not found. Please contact administrator.');
        }

        // Generate case number for user's barangay
        $caseController = new \App\Http\Controllers\Wcpu\CaseController();
        $caseData = $caseController->generateCaseNumber($userBarangay->id, $caseDepartment);
        
        if (!$caseData->getData()->success) {
            return redirect()->back()->with('error', 'Failed to generate case number.');
        }
        
        $caseNumber = $caseData->getData()->case_number;

        if ($caseDepartment === 'BCPC') {
            $lastDashPosition = strrpos($caseNumber, '-');
            $caseNumberPrefix = substr($caseNumber, 0, $lastDashPosition + 1);
            $sequenceNumber = (int) substr($caseNumber, $lastDashPosition + 1);

            while (CaseModel::withTrashed()->where('case_number', $caseNumber)->exists()) {
                $sequenceNumber++;
                $caseNumber = $caseNumberPrefix . str_pad($sequenceNumber, 3, '0', STR_PAD_LEFT);
            }
        }

        $abuseType = $validated['abuse_type'];
        $incidentDetails = $validated['incident_details'];

        if ($abuseType === 'Others' && !empty($validated['other_abuse_details'])) {
            $otherExplanation = trim($validated['other_abuse_details']);
            $abuseType = 'Others';
            $incidentDetails = "Specific Type of Abuse (Others): " . $otherExplanation . "\n\n" . $incidentDetails;
        }

        $caseDetailsValue = $incidentDetails;

        // Create the case with automatic barangay assignment
        $case = CaseModel::create([
            'type' => $caseDepartment,
            'case_number' => $caseNumber,
            'case_designation' => $userBarangay->id,
            'incident_date' => $validated['incident_date'],
            'incident_time' => $validated['incident_time'],
            'date_reported' => $validated['date_reported'],
            'abuse_type' => $abuseType,
            'victim_name' => $validated['victim_name'],
            'victim_age' => $validated['victim_age'],
            'victim_gender' => $validated['victim_gender'],
            'victim_contact' => $validated['victim_contact'] ?? null,
            'victim_address' => $validated['victim_address'],
            // Guardian of Victim fields
            'guardian' => $validated['guardian'] ?? null,
            'guardian_contact' => $validated['guardian_contact'] ?? null,
            'guardian_address' => $validated['guardian_address'] ?? null,
            'relationship_to_victim' => $validated['relationship_to_victim'] ?? null,
            // Abuser fields
            'abuser_name' => $validated['perpetrator_name'],
            'abuser_address' => $validated['abuser_address'] ?? null,
            'abuser_age' => $validated['abuser_age'],
            'abuse_relationship' => $validated['abuse_relationship'],
            // Guardian of Abuser fields
            'guardian_abuser_name' => $validated['guardian_abuser_name'] ?? null,
            'guardian_abuser_contact' => $validated['guardian_abuser_contact'] ?? null,
            'guardian_abuser_address' => $validated['guardian_abuser_address'] ?? null,
            'relationship_to_abuser' => $validated['relationship_to_abuser'] ?? null,
            // Incident fields
            'incident_location' => $validated['incident_location'],
            'incident_details' => $incidentDetails,
            'witness_name' => $validated['witness_name'] ?? null,
            'witness_contact' => $validated['witness_contact'] ?? null,
            'priority' => $validated['case_priority'],
            'status' => $validated['status'],
            'assigned_to' => Auth::id(),
            'created_by' => Auth::id(),
            'case_details' => $caseDetailsValue,
        ]);

        // Single activity log (no documents)
        activity()
            ->causedBy(Auth::user())
            ->performedOn($case)
            ->withProperties([
                'case_number' => $case->case_number,
                'victim_name' => $case->victim_name,
                'barangay' => $userBarangay->name,
            ])
            ->log('created case '.$case->case_number.' for '.$userBarangay->name.' barangay');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Case created successfully! Case Number: ' . $case->case_number . ' has been assigned to ' . $userBarangay->name . ' barangay.',
                'redirect' => route('desk-officer.cases.show', ['hash' => encrypt($case->id), 'from_create' => 1]),
                'case_url' => route('desk-officer.cases.show', ['hash' => encrypt($case->id)]),
            ]);
        }

        return redirect()->route('desk-officer.cases.show', ['hash' => encrypt($case->id), 'from_create' => 1])
            ->with('success', 'Case created successfully! Case Number: '.$case->case_number.' has been assigned to '.$userBarangay->name.' barangay.');
    }

    public function show(Request $request, $hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Throwable $e) {
            // invalid or tampered hash
            abort(404, 'Case not found.');
        }

        $user = Auth::user();
        if (!$user) abort(403, 'Unauthorized');

        $barangay = Barangay::where('name', $user->barangay)
            ->orWhere('id', $user->barangay)
            ->first();

        if (!$barangay) abort(404, 'Barangay not found for your account.');

        // Allow access to cases in the same barangay.
        $caseQuery = CaseModel::withTrashed()->with(['caseType','createdBy','assignedTo','barangay'])
            ->where('id', $id)
            ->where('case_designation', $barangay->id);
        $this->applyCaseDepartmentScope($caseQuery);
        $case = $caseQuery->first();

        if (!$case) {
            if ($request->ajax() || $request->boolean('modal')) {
                return response()->json(['error' => 'Case not found or not accessible.'], 404);
            }
            abort(404, 'Case not found or not accessible.');
        }

        // Pass ownership flag so modal can conditionally show edit button
        $isOwner = ($case->created_by == $user->id);

        // If AJAX request (for modal), return only the partial content
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $modalView = $user->position === 'Desk Officer' && ($user->defaultCaseDepartment() === 'BCPC' || $user->isMultiCaseDepartment())
                ? 'admin-tailwind._case-details-modal'
                : 'desk-officer._case-details-modal';
            $html = view($modalView, compact('case', 'isOwner'))->render();
            return response($html, 200)->header('Content-Type', 'text/html');
        }

        // Full page view uses the normal layout (with nav, etc.)
        if (in_array($user->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)) {
            return view('admin-tailwind.view-case', compact('case'));
        }

        return view('desk-officer.case-details', compact('case'));
    }

    public function print(Request $request, $hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Throwable $e) {
            abort(404, 'Case not found.');
        }

        $user = Auth::user();
        if (!$user) abort(403, 'Unauthorized');

        $barangay = Barangay::where('name', $user->barangay)
            ->orWhere('id', $user->barangay)
            ->firstOrFail();

        // Allow printing any accessible case in the same barangay
        $caseQuery = CaseModel::with(['caseType','createdBy','assignedTo','barangay'])
            ->where('id', $id)
            ->where('case_designation', $barangay->id);
        $this->applyCaseDepartmentScope($caseQuery);
        $case = $caseQuery->firstOrFail();

        // Normalize timestamps for print
        if ($case->created_at) $case->created_at = $case->created_at->timezone('Asia/Manila');
        if ($case->updated_at) $case->updated_at = $case->updated_at->timezone('Asia/Manila');

        return view('desk-officer.case-print', compact('case'));
    }

    /**
     * Display all matching cases in an editable print preview for the desk officer's barangay.
     */
    public function printAllCases()
    {
        $user = Auth::user();
        if (!$user) abort(403, 'Unauthorized');

        $barangay = Barangay::where('name', $user->barangay)
            ->orWhere('id', $user->barangay)
            ->first();

        if (!$barangay) abort(404, 'Barangay not found for your account.');

        // Get all cases in this barangay (shared among officers)
        $casesQuery = CaseModel::where('case_designation', $barangay->id)
            ->select([
                'id',
                'case_number',
                'type',
                'date_reported',
                'victim_name',
                'abuse_type',
                'status',
                'created_at'
            ]);

        $requestedDept = request('type') ?? request('department');
        if ($user->isMultiCaseDepartment() && !$requestedDept) {
            $requestedDept = 'bcpc';
        }
        $this->applyCaseDepartmentScope($casesQuery, $requestedDept);

        if (request()->filled('status')) {
            $status = request('status');
            $status === 'Case closed'
                ? $casesQuery->whereIn('status', ['Case closed', 'Case Closed', 'Complete case', 'Complete Case'])
                : $casesQuery->where('status', $status);
        }
        if (request()->filled('date_from')) {
            $casesQuery->whereDate('date_reported', '>=', request('date_from'));
        }
        if (request()->filled('date_to')) {
            $casesQuery->whereDate('date_reported', '<=', request('date_to'));
        }

        $cases = $casesQuery
            ->orderBy('date_reported', 'desc')
            ->get();

        $stats = [
            'total_cases'   => $cases->count(),
            'case_reported' => $cases->where('status', 'Case Filed')->count(),
            'case_referred' => $cases->where('status', 'Case Referred')->count(),
            'case_filed'    => $cases->where('status', 'Case Filed')->count(),
            'case_closed'   => $cases->whereIn('status', ['Case closed', 'Case Closed', 'Complete case', 'Complete Case'])->count(),
        ];

        $viewData = [
            'barangay' => $barangay,
            'cases' => $cases,
            'totalCases' => $stats['total_cases'],
            'caseReferred' => $stats['case_referred'],
            'caseFiled' => $stats['case_filed'],
            'caseClosed' => $stats['case_closed'],
            'printDate' => now()->format('F d, Y'),
            'dateRangeText' => '',
            'statusFilter' => request('status'),
            'department' => strtoupper((string) ($cases->first()->type ?? $requestedDept ?? $user->defaultCaseDepartment())),
        ];

        return view('admin-tailwind.barangay-cases-print-individual', $viewData);
    }

    public function edit(Request $request, $hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Throwable $e) {
            abort(404, 'Case not found.');
        }

        $user = Auth::user();
        $barangay = Barangay::where('name', $user->barangay)
            ->orWhere('id', $user->barangay)
            ->first();
        if (!$barangay) abort(404, 'Barangay not found');

        $caseQuery = CaseModel::where('id', $id)
            ->where('case_designation', $barangay->id);
        if (!($user->position === 'Desk Officer' && $user->defaultCaseDepartment() === 'BCPC')) {
            $caseQuery->where('created_by', $user->id);
        }
        $this->applyCaseDepartmentScope($caseQuery);
        $case = $caseQuery->firstOrFail();

        if ($case->status !== 'Case Filed') {
            abort(403, 'Only cases marked Case Filed can be edited.');
        }

        if (in_array(($case->status ?? ''), ['Case closed', 'Case Closed', 'Complete case', 'Complete Case'], true) || preg_match('/closed|complet/i', trim((string) ($case->status ?? '')))) {
            abort(403, 'This case is already closed and can no longer be edited.');
        }

        if (preg_match('/referred/i', trim((string) ($case->status ?? '')))) {
            abort(403, 'This case cannot be edited after it has been referred.');
        }

        $caseTypes = CaseType::all();
        $barangays = Barangay::all();

        $otherAbuseDetails = '';
        $incidentDetailsForEdit = $case->incident_details;
        if ($case->abuse_type === 'Others'
            && preg_match('/^Specific Type of Abuse \(Others\):\s*(.*?)\R\R(.*)$/s', (string) $case->incident_details, $matches)) {
            $otherAbuseDetails = trim($matches[1]);
            $incidentDetailsForEdit = $matches[2];
        }

        // Load active report recipients here (controller side, not in Blade)
        // select only columns that are known to exist on report_recipients
        $recipients = ReportRecipient::active()
            ->select('id', 'name', 'email', 'role')
            ->orderBy('name')
            ->get();

        // If AJAX request (for modal), return only the form content
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $editView = $user->isMultiCaseDepartment() ? 'admin-tailwind._edit-form' : 'desk-officer._edit-form';
            $editAction = route('desk-officer.cases.update', ['hash' => encrypt($case->id)]);
            $html = view($editView, compact('case', 'caseTypes', 'barangays', 'recipients', 'otherAbuseDetails', 'incidentDetailsForEdit', 'editAction'))->render();
            return response($html, 200)->header('Content-Type', 'text/html');
        }

        return view('desk-officer.officers-edit', compact('case', 'caseTypes', 'barangays', 'recipients', 'otherAbuseDetails', 'incidentDetailsForEdit'));
    }

    public function update(Request $request, $hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Throwable $e) {
            abort(404, 'Case not found.');
        }

        // Load case early so we can check status before validating/updating
        $user = Auth::user();
        $barangay = Barangay::where('name', $user->barangay)
            ->orWhere('id', $user->barangay)
            ->firstOrFail();
        $caseQuery = CaseModel::where('id', $id)
            ->where('case_designation', $barangay->id);
        if (!($user->position === 'Desk Officer' && $user->defaultCaseDepartment() === 'BCPC')) {
            $caseQuery->where('created_by', $user->id);
        }
        $this->applyCaseDepartmentScope($caseQuery);
        $case = $caseQuery->first();
        if (!$case) {
            abort(404, 'Case not found or not accessible.');
        }

        if ($case->status !== 'Case Filed') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Only cases marked Case Filed can be edited.'], 403);
            }
            return redirect()->back()->with('error', 'Only cases marked Case Filed can be edited.');
        }
        
        // Prevent editing closed cases
        $isClosed = in_array(($case->status ?? ''), ['Case closed', 'Case Closed', 'Complete case', 'Complete Case'], true) || preg_match('/closed|complet/i', trim((string) ($case->status ?? '')));
        $isReferred = in_array(($case->status ?? ''), ['Case Referred', 'Referred'], true) || preg_match('/referred/i', trim((string) ($case->status ?? '')));
        if ($isClosed) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This case is already closed and can no longer be edited.'
                ], 403);
            }
            return redirect()->back()->with('error', 'This case is already closed and can no longer be edited.');
        }
        if ($isReferred) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This case cannot be edited after it has been referred.'
                ], 403);
            }
            return redirect()->back()->with('error', 'This case cannot be edited after it has been referred.');
        }

        // capture previous status for later comparison
        $previousStatus = $case->status ?? null;
        
        // proceed with validation and update using the loaded $case
        $victimAgeRule = ($case->type === 'BCPC') ? 'required|integer|min:0|max:17' : 'required|integer|min:0|max:120';
        $validated = $request->validate([
            'victim_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'victim_age' => $victimAgeRule,
            'victim_gender' => 'required|string|in:Female,Male,LGBTQIA+,Prefer not to say',
            'victim_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
            'victim_address' => 'required|string',
            'guardian' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'guardian_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
            'guardian_address' => 'nullable|string',
            'relationship_to_victim' => 'nullable|string|max:255',
            // abuser fields
            'perpetrator_name' => ['required', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'abuser_address' => 'required|string|max:2000',
            'abuser_age' => 'nullable|integer|min:1|max:120',
            'abuse_relationship' => 'nullable|string|max:255',
            'guardian_abuser_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'abuse_type' => 'required|string|max:255',
            'guardian_abuser_contact' => ['nullable', new \App\Rules\ContactNumber(11)],
            'client_contact' => ['nullable', new \App\Rules\ContactNumber(11)],
            'other_abuse_details' => 'required_if:abuse_type,Others|nullable|string|max:1000',
            'incident_date' => 'required|date',
            'incident_time' => 'required',
            'date_reported' => 'required|date',
            'incident_location' => 'required|string',
            'incident_details' => 'required|string',
            'witness_name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} '’-]+$/u"],
            'witness_contact' => ["nullable", "string", "max:20", new \App\Rules\ContactNumber(11)],
            'priority' => 'required|string|in:Urgent,Emergency,Non-urgent',
            'status' => 'required|string|in:Case Filed,Case closed,Case Closed,Case Referred,Complete case,Complete Case',
        ]);

         $user = Auth::user();
         $barangay = Barangay::where('name', $user->barangay)->firstOrFail();

         // Explicitly assign properties to avoid mass-assignment blocking and ensure abuser fields persist
         $case->victim_name = $validated['victim_name'];
         $case->victim_age = $validated['victim_age'];
         $case->victim_gender = $validated['victim_gender'];
         $case->victim_contact = $validated['victim_contact'] ?? null;
         $case->victim_address = $validated['victim_address'];
         
         // Abuser fields -> ensure these write to the abuser_name / abuser_age DB columns
         $case->abuser_name = $validated['perpetrator_name'];
         $case->abuser_address = $validated['abuser_address'] ?? $case->abuser_address;
         $case->abuser_age = $validated['abuser_age'] ?? null;
         $case->abuse_relationship = $validated['abuse_relationship'] ?? $case->abuse_relationship;
         
         // Guardian relation (if provided)
         if (isset($validated['relationship_to_victim'])) {
             $case->relationship_to_victim = $validated['relationship_to_victim'];
         }
         
         $case->abuse_type = $validated['abuse_type'];
         $case->incident_date = $validated['incident_date'];
         $case->incident_time = $validated['incident_time'];
         $case->date_reported = $validated['date_reported'];
         $case->incident_location = $validated['incident_location'];
         $case->incident_details = $validated['abuse_type'] === 'Others'
             ? "Specific Type of Abuse (Others): " . trim($validated['other_abuse_details']) . "\n\n" . $validated['incident_details']
             : $validated['incident_details'];
         $case->witness_name = $validated['witness_name'] ?? null;
         $case->witness_contact = $validated['witness_contact'] ?? null;
         $case->priority = $validated['priority'];
         $case->status = $validated['status'];
         
         $case->save();

         // If status just changed to "Case Referred", log into case_referrals table
         if (($case->status ?? '') === 'Case Referred' && $previousStatus !== 'Case Referred') {
            try {
                // generate a unique referral number using the Eloquent model helper
                $referralNumber = \App\Models\Wcpu\CaseReferral::generateReferralNumber();

                // gather referring user information to satisfy NOT NULL DB fields
                $user = Auth::user();
                $referredByName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'Unknown';
                $referredByDesignation = $user->position ?? 'Desk Officer';
                $referredByAddress = $user->barangay ? "WCPU Office, {$user->barangay}" : ($user->address ?? 'N/A');
                $referredByContact = $user->phone_number ?? null;
                $referredByEmail = $user->email ?? null;

                // resolve selected recipient (if posted)
                $recipientName = 'Not specified';
                $recipientEmail = null;
                $recipientContact = null;
                $recipientAddress = 'N/A';
                $recipientId = $request->input('recipient_id');
                if ($recipientId) {
                    $recipient = \App\Models\Wcpu\ReportRecipient::find($recipientId);
                    if ($recipient) {
                        $recipientName = $recipient->name;
                        $recipientEmail = $recipient->email ?? null;
                        // use getAttribute to avoid PHP notices if columns don't exist
                        $recipientContact = $recipient->getAttribute('contact') ?? $recipient->getAttribute('phone') ?? null;
                        $recipientAddress = $recipient->getAttribute('address') ?? $recipient->getAttribute('office_address') ?? 'N/A';
                    }
                }

                // prefer client_* from request (frontend may post them), otherwise fall back to case victim_* fields
                $clientName = $request->input('client_name') ?? $case->victim_name ?? null;
                $clientAge = $request->input('client_age') ?? $case->victim_age ?? null;
                $clientSex = $request->input('client_sex') ?? $case->victim_gender ?? null;
                $clientAddress = $request->input('client_address') ?? $case->victim_address ?? null;
                $clientContact = $request->input('client_contact') ?? $case->victim_contact ?? null;
                
                \Log::info('Preparing case_referrals insert - client values', [
                    'case_id' => $case->id,
                    'client_name' => $clientName,
                    'client_age' => $clientAge,
                    'client_sex' => $clientSex,
                    'client_address' => $clientAddress,
                    'client_contact' => $clientContact,
                ]);

                DB::connection('wcpu')->table('case_referrals')->insert([
                    'referral_number' => $referralNumber,
                    'case_id' => $case->id,
                    'referred_by_user_id' => Auth::id(),
                    'referred_by_name' => $referredByName,
                    'referred_by_designation' => $referredByDesignation,
                    'referred_by_address' => $referredByAddress,
                    'referred_by_contact' => $referredByContact,
                    'referred_by_email' => $referredByEmail,
                    // recipient columns (nullable if report_recipient table doesn't provide contact/address)
                    'recipient_name' => $recipientName,
                    'recipient_email' => $recipientEmail,
                    'recipient_contact' => $recipientContact,
                    'recipient_address' => $recipientAddress,
                    // client (victim) fields required by DB schema
                    'client_name' => $clientName,
                    'client_age' => $clientAge,
                    'client_sex' => $clientSex,
                    'client_address' => $clientAddress ?: 'N/A',
                    'client_contact' => $clientContact,
                    'reason_for_referral' => $request->input('reason_for_referral') ?: ($case->incident_details ?: 'Case referred for assistance.'),
                    'specific_services_requested' => $request->input('specific_services_requested') ?: 'Not specified',
                    'status' => 'Pending',
                    'date_of_referral' => Carbon::now()->toDateString(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                \Log::info('Inserted case_referrals record', ['case_id' => $case->id, 'referral_number' => $referralNumber]);
             } catch (\Exception $e) {
                 \Log::error('Failed to insert into case_referrals: ' . $e->getMessage(), [
                     'case_id' => $case->id,
                     'referral_number' => $referralNumber ?? null,
                 ]);
             }
         }
 
         return redirect()->route('desk-officer.cases.index', $user->isMultiCaseDepartment() ? ['type' => strtolower($case->type)] : [])->with('success', 'Case updated successfully.');
    }

    public function destroy($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Throwable $e) {
            abort(404, 'Case not found.');
        }

        $user = Auth::user();
        $barangay = Barangay::where('name', $user->barangay)
            ->orWhere('id', $user->barangay)
            ->firstOrFail();

        $caseQuery = CaseModel::where('id', $id)
            ->where('case_designation', $barangay->id);
        if (!($user->position === 'Desk Officer' && ($user->defaultCaseDepartment() === 'BCPC' || $user->isMultiCaseDepartment()))) {
            $caseQuery->where('created_by', $user->id);
        }
        $this->applyCaseDepartmentScope($caseQuery);
        $case = $caseQuery->firstOrFail();

        $case->delete();

        $type = request('type');
        if (!$type && $case->type) {
            $type = strtolower($case->type) === 'vawc' ? 'vawc' : 'bcpc';
        }
        $params = $type ? ['type' => $type] : [];

        $redirect = redirect()->route('desk-officer.cases.index', $params)
            ->with('success', 'Case archived successfully.');

        if ($user->position === 'Desk Officer'
            && $user->defaultCaseDepartment() === 'BCPC'
            && !$user->isMultiCaseDepartment()) {
            $redirect->with('bcpc_case_archived_success', true);
        }

        return $redirect;
    }

    /**
     * Show referral form for a case
     */
    public function showReferralForm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Throwable $e) {
            if (is_numeric($hash)) {
                $id = (int) $hash;
            } else {
                abort(404, 'Case not found.');
            }
        }

        $user = Auth::user();
        $barangay = Barangay::where('id', $user->barangay)
            ->orWhere('name', $user->barangay)
            ->first();

        $caseQuery = CaseModel::where('id', $id);
        if ($barangay) {
            $caseQuery->where('case_designation', $barangay->id);
        }
        if (!($user->position === 'Desk Officer' && ($user->defaultCaseDepartment() === 'BCPC' || $user->isMultiCaseDepartment()))) {
            $caseQuery->where('created_by', $user->id);
        }
        $this->applyCaseDepartmentScope($caseQuery);
        $case = $caseQuery->firstOrFail();

        if (preg_match('/referred|closed|complet/i', trim((string) $case->status))) {
            return redirect()->route('desk-officer.cases.index')->with('info', 'This case has already been referred.');
        }

        // Get recipients - only select columns that exist
        $recipients = \App\Models\Wcpu\ReportRecipient::active()
            ->select('id', 'name', 'email', 'role')
            ->orderBy('name')
            ->get();

        if ($user->isMultiCaseDepartment()) {
            return view('admin-tailwind.referral-form', compact('case', 'recipients'));
        }

        return view('desk-officer.referral-form', compact('case', 'recipients'));
    }

    /**
     * Store case referral
     */
    public function storeReferral(Request $request, $hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Throwable $e) {
            if (is_numeric($hash)) {
                $id = (int) $hash;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid case identifier'
                ], 400);
            }
        }

        if ($request->filled('recipient_id') && !$request->filled('recipients')) {
            $recipientEmail = ReportRecipient::find($request->input('recipient_id'))?->email;
            if ($recipientEmail) {
                $request->merge(['recipients' => [$recipientEmail]]);
            }
        }

        $validated = $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'email',
        ]);

        $user = Auth::user();
        $barangay = Barangay::where('id', $user->barangay)
            ->orWhere('name', $user->barangay)
            ->first();

        $caseQuery = CaseModel::where('id', $id);
        if ($barangay) {
            $caseQuery->where('case_designation', $barangay->id);
        }
        if (!($user->position === 'Desk Officer' && ($user->defaultCaseDepartment() === 'BCPC' || $user->isMultiCaseDepartment()))) {
            $caseQuery->where('created_by', $user->id);
        }
        $this->applyCaseDepartmentScope($caseQuery);
        $case = $caseQuery->firstOrFail();

        // Create referrals for each recipient
        foreach ($validated['recipients'] as $recipientEmail) {
            // Find recipient by email
            $recipient = \App\Models\Wcpu\ReportRecipient::where('email', $recipientEmail)->first();
            
            if (!$recipient) {
                continue;
            }

            // Check if referral already exists for this case and recipient
            $existingReferral = CaseReferral::where('case_id', $case->id)
                ->where('recipient_email', $recipientEmail)
                ->first();
            
            if ($existingReferral) {
                continue;
            }

            // Create referral
            $referral = CaseReferral::create([
                'case_id' => $case->id,
                'referral_number' => CaseReferral::generateReferralNumber(),
                'date_of_referral' => now()->format('Y-m-d'),
                'referred_by_name' => $user->first_name . ' ' . $user->last_name,
                'referred_by_designation' => $user->position,
                'referred_by_address' => $barangay->name . ' Barangay',
                'referred_by_contact' => $user->contact_number ?? null,
                'referred_by_email' => $user->email,
                'recipient_name' => $recipient->name,
                'recipient_address' => 'N/A',
                'recipient_contact' => null,
                'recipient_email' => $recipient->email,
                'client_name' => $case->victim_name,
                'client_age' => $case->victim_age,
                'client_sex' => $case->victim_gender,
                'client_address' => $case->victim_address,
                'client_contact' => $case->victim_contact,
                'guardian_name' => null,
                'guardian_contact' => null,
                'guardian_address' => null,
                'relationship_to_victim' => null,
                'reason_for_referral' => 'Case referral',
                'specific_services_requested' => 'General case management',
                'additional_notes' => null,
                'referred_by_user_id' => $user->id,
                'status' => 'Pending',
                ...($user->isMultiCaseDepartment() ? array_merge(
                    \App\Services\Wcpu\MultiroleReferralFields::forCase($user, $case), [
                        'family_guardian_name' => $case->guardian,
                        'family_guardian_contact' => $case->guardian_contact,
                        'family_guardian_address' => $case->guardian_address,
                        'reason_for_referral' => $request->input('reason_for_referral') ?: \App\Services\Wcpu\MultiroleReferralFields::reason($case),
                    ]) : []),
            ]);

            // Send email notification with PDF attachment
            try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin-tailwind.referrals.print', ['referral' => $referral])
                    ->setPaper('a4', 'portrait')
                    ->output();

                $mail = (new \App\Mail\Wcpu\CaseReferralNotification($referral))
                    ->attachData($pdf, 'Referral-' . $referral->referral_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);

                \Illuminate\Support\Facades\Mail::to($referral->recipient_email)->send($mail);
            } catch (\Exception $e) {
                \Log::error('Failed to send referral email: ' . $e->getMessage(), [
                    'referral_number' => $referral->referral_number,
                ]);
            }
        }

        // Update case status to Referred
        $case->update(['status' => 'Case Referred']);
        session()->flash('referral_success', true);

        if (class_exists(\App\Services\Wcpu\MultiroleActionNotifications::class)) {
            \App\Services\Wcpu\MultiroleActionNotifications::recordCaseReferred($case, $user, $referral->referral_number ?? 'N/A');
        }

        return response()->json([
            'success' => true,
            'message' => 'Case referral created successfully',
        ]);
    }

    /**
     * List all referrals
     */
    public function referrals(Request $request)
    {
        $user = Auth::user();
        $barangay = Barangay::where('name', $user->barangay)
            ->when($user->isMultiCaseDepartment(), fn ($q) => $q->orWhere('id', $user->barangay))->first();
        
        if (!$barangay) {
            return view('desk-officer.referrals', [
                'referrals' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5),
                'referralGroups' => [],
                'barangayName' => 'Error',
                'stats' => ['total' => 0, 'pending' => 0, 'accepted' => 0, 'completed' => 0],
            ]);
        }

        // Get referrals for all cases in this barangay (shared among officers)
        $query = CaseReferral::with(['case', 'referredBy'])
            ->withExists('departmentAdminReferralsForCase')
            ->where('created_by_department_admin', false)
            ->whereHas('case', function($q) use ($barangay) {
                $q->where('case_designation', $barangay->id);
                $this->applyCaseDepartmentScope($q);
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at');

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $referrals = (clone $query)->paginate(5);
        if ($user->defaultCaseDepartment() === 'BCPC' && !$user->isMultiCaseDepartment() && $referrals->currentPage() > $referrals->lastPage()) {
            return redirect()->route('desk-officer.referrals', array_merge($request->query(), ['page' => $referrals->lastPage()]));
        }
        $referralGroups = ['' => $referrals];
        if ($user->isMultiCaseDepartment()) {
            $referralGroups = [];
            foreach ($user->caseDepartments() as $type) {
                $referralGroups[$type] = (clone $query)->whereHas('case', fn ($q) => $q->where('type', $type))
                    ->paginate(5, ['*'], strtolower($type) . '_page')->withQueryString();
            }
        }

        // Calculate stats (all referrals in barangay)
        $baseQuery = CaseReferral::where('created_by_department_admin', false)
            ->whereHas('case', function($q) use ($barangay) {
                $q->where('case_designation', $barangay->id);
                $this->applyCaseDepartmentScope($q);
            });

        $stats = [
            'total' => $baseQuery->count(),
            'pending' => (clone $baseQuery)->where('status', 'Pending')->count(),
            'accepted' => (clone $baseQuery)->where('status', 'Accepted')->count(),
            'completed' => (clone $baseQuery)->where('status', 'Completed')->count(),
        ];

        return view('desk-officer.referrals', compact('referrals', 'referralGroups', 'stats'))
            ->with('barangayName', $barangay->name)
            ->with('currentUserId', Auth::id());
    }

    private function officerReferralQuery()
    {
        $user = Auth::user();
        $barangay = Barangay::where('name', $user->barangay)->orWhere('id', $user->barangay)->firstOrFail();
        return CaseReferral::with(['case', 'referredBy', 'recipient'])->whereHas('case', function ($query) use ($barangay, $user) {
            $query->withTrashed()->where('case_designation', $barangay->id)->whereIn('type', $user->caseDepartments());
        });
    }

    public function archiveReferral($id)
    {
        $referral = $this->officerReferralQuery()->findOrFail($id);
        if (Auth::user()->position === 'Desk Officer' && Auth::user()->defaultCaseDepartment() === 'VAWC' && !Auth::user()->isMultiCaseDepartment()) {
            $referral->delete();
        } else {
            app(\App\Services\Wcpu\ReferralArchiveService::class)->archive($referral);
        }
        return response()->json(['success' => true, 'message' => 'Referral archived successfully.']);
    }

    public function archivedReferrals()
    {
        $user = Auth::user();
        $instantArchiveSearch = in_array($user?->defaultCaseDepartment(), ['VAWC', 'BCPC'], true);
        $search = trim((string) request('search'));
        $query = $this->officerReferralQuery()->onlyTrashed()->latest('deleted_at');
        if (!$instantArchiveSearch && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('referral_number', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhereHas('case', fn ($cq) => $cq->where('case_number', 'like', "%{$search}%")->orWhere('victim_name', 'like', "%{$search}%"));
            });
        }
        $referrals = (clone $query)->paginate($instantArchiveSearch ? PHP_INT_MAX : 5, ['*'], 'page', $instantArchiveSearch ? 1 : null)->withQueryString();
        $referralGroups = ['' => $referrals];
        if ($user && $user->isMultiCaseDepartment()) {
            $referralGroups = [];
            foreach ($user->caseDepartments() as $type) {
                $referralGroups[$type] = (clone $query)->whereHas('case', fn ($q) => $q->where('type', $type))
                    ->paginate($instantArchiveSearch ? PHP_INT_MAX : 5, ['*'], strtolower($type) . '_page', $instantArchiveSearch ? 1 : null)->withQueryString();
            }
        }
        return view('desk-officer.archived-referrals', compact('referrals', 'referralGroups'));
    }

    public function restoreArchivedReferral($id)
    {
        abort_unless(in_array(Auth::user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true), 403);
        $referral = $this->officerReferralQuery()->onlyTrashed()->findOrFail($id);
        $referral->restore();

        return back()->with('success', 'Archived referral restored successfully.');
    }

    public function forceDeleteArchivedReferral($id)
    {
        $user = Auth::user();
        abort_unless($user?->position === 'Desk Officer' && in_array($user?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true), 403);
        $referral = $this->officerReferralQuery()->onlyTrashed()->findOrFail($id);
        $referral->forceDelete();

        return back()->with('success', 'Archived referral deleted permanently.');
    }

    /**
     * Show referral details
     */
    public function showReferral($id)
    {
        $user = Auth::user();
        $barangay = Barangay::where('name', $user->barangay)->orWhere('id', $user->barangay)->firstOrFail();

        // Allow viewing referrals for any case in the same barangay
        $referral = CaseReferral::withTrashed()->with(['case', 'referredBy'])
            ->whereHas('case', function($q) use ($barangay) {
                $q->where('case_designation', $barangay->id);
                $this->applyCaseDepartmentScope($q);
            })
            ->findOrFail($id);

        return view('desk-officer.referral-details', compact('referral'));
    }

    /**
     * Print referral form (VAW Form 2)
     */
    public function printReferral($id)
    {
        $user = Auth::user();
        $barangay = Barangay::where('name', $user->barangay)->orWhere('id', $user->barangay)->firstOrFail();

        // Allow viewing/printing referrals for cases in the same barangay within user department scope
        $referral = CaseReferral::with(['case', 'referredBy'])
            ->whereHas('case', function($q) use ($barangay) {
                $q->where('case_designation', $barangay->id);
                $this->applyCaseDepartmentScope($q);
            })
            ->findOrFail($id);

        return view('admin-tailwind.referrals.print', compact('referral'));
    }

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

    public static function getAbuseTypeColor($type)
    {
        // Generate a consistent color based on the abuse type
        $colors = [
            'physical' => 'dc3545',
            'emotional' => 'fd7e14',
            'sexual' => 'e83e8c',
            'economic' => '20c997',
            'psychological' => '6f42c1',
            'verbal' => '17a2b8',
            'digital' => '6c757d',
        ];

        $type = strtolower($type);
        foreach ($colors as $key => $color) {
            if (str_contains($type, $key)) {
                return $color;
            }
        }

        // Default color if no match found
        return '6c757d';
    }
}
