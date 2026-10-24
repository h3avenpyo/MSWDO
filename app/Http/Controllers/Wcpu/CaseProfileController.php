<?php

namespace App\Http\Controllers\Wcpu;

use App\Http\Controllers\Controller;
use App\Models\Wcpu\Barangay;
use App\Models\Wcpu\CaseModel;
use Illuminate\Http\Request;

class CaseProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = strtolower($user->position) === 'administrator';
        $query = CaseModel::with('caseType')->whereIn('type', $user->caseDepartments());

        if (!$isAdmin) {
            $barangay = Barangay::where('name', $user->barangay)->orWhere('id', $user->barangay)->first();
            $query->where('case_designation', $barangay?->id ?? 0);
        }

        $profileDepartment = null;
        if (!$isAdmin) {
            $profileDepartment = strtoupper((string) $request->input('type', $user->defaultCaseDepartment()));
            abort_unless($user->canAccessCaseDepartment($profileDepartment), 403);
            $query->where('type', $profileDepartment);
        }

        $cases = $query->orderByDesc('updated_at')->orderByDesc('created_at')->orderByDesc('id')->get();
        $victims = $this->profiles($cases, 'victim_name', 'victim_address');
        $perpetrators = $this->profiles($cases, 'abuser_name', 'abuser_address');

        if ($user->isDepartmentAdmin() || !$isAdmin) {
            $request->validate(['age' => 'nullable|integer|min:0|max:120', 'search' => 'nullable|string|max:255']);
            $search = mb_strtolower(trim((string) $request->input('search')));
            $filter = function ($records, $name, $age, $address) use ($request, $search) {
                return $records->filter(function ($person) use ($request, $search, $name, $age, $address) {
                    return (!$request->filled('age') || ($person->$age !== null && $person->$age == $request->age))
                        && ($search === '' || str_contains(mb_strtolower($person->$name . ' ' . $person->$address), $search));
                })->values();
            };
            $victims = $victims->map(fn ($records) => $filter($records, 'victim_name', 'victim_age', 'victim_address'))
                ->filter(fn ($records) => $records->isNotEmpty());
            $perpetrators = $perpetrators->map(fn ($records) => $filter($records, 'abuser_name', 'abuser_age', 'abuser_address'))
                ->filter(fn ($records) => $records->isNotEmpty());
            $paginate = function ($profiles, $pageName) use ($request) {
                $page = max(1, (int) $request->input($pageName, 1));
                return (new \Illuminate\Pagination\LengthAwarePaginator(
                    $profiles->values()->forPage($page, 4)->values(), $profiles->count(), 4, $page,
                    ['path' => $request->url(), 'pageName' => $pageName]
                ))->withQueryString();
            };
            $victims = $paginate($victims, 'victims_page');
            $perpetrators = $paginate($perpetrators, 'perpetrators_page');
        }

        return view('case-profiles.index', compact('victims', 'perpetrators', 'isAdmin', 'profileDepartment'));
    }

    private function profiles($cases, string $name, string $address)
    {
        // Use name and address together so people with the same name at different addresses stay separate.
        return $cases->filter(fn ($case) => trim((string) $case->$name) !== '')
            ->groupBy(fn ($case) => json_encode([
                mb_strtolower(preg_replace('/\s+/', ' ', trim($case->$name))),
                mb_strtolower(preg_replace('/\s+/', ' ', trim((string) $case->$address))),
                $name === 'victim_name' ? mb_strtolower(trim((string) $case->victim_gender)) : null,
                $case->case_designation,
            ]))
            ->sortByDesc(fn ($records) => $records->max('updated_at') ?? $records->max('created_at'));
    }
}
