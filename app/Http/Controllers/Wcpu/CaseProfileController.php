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
                $perPage = $request->user()->isDepartmentAdmin() && $request->boolean('portrait_cards') ? 4 : 5;
                $page = max(1, (int) $request->input($pageName, 1));
                return (new \Illuminate\Pagination\LengthAwarePaginator(
                    $profiles->values()->forPage($page, 4)->values(), $profiles->count(), 4, $page,
                    ['path' => $request->url(), 'pageName' => $pageName]
                ))->withQueryString();
            };
            $victims = $paginate($victims, 'victims_page');
            $perpetrators = $paginate($perpetrators, 'perpetrators_page');
        }

        $profileGuardians = function ($records, $isVictim) use ($user) {
            return $records->flatMap(function ($case) use ($isVictim, $user) {
                $entries = [$isVictim
                    ? [$case->guardian, $case->relationship_to_victim, $case->guardian_contact, $case->guardian_address, $case->victim_name]
                    : [$case->guardian_abuser_name, $case->relationship_to_abuser, $case->guardian_abuser_contact, $case->guardian_abuser_address, $case->abuser_name]];
                if (($user->position === 'Desk Officer' || $user->isBcpcAdmin() || $user->isVawcAdmin())) {
                    foreach ($case->bcpc_data[$isVictim ? 'additional_victims' : 'additional_perpetrators'] ?? [] as $person) {
                        $entries[] = $isVictim
                            ? [$person['guardian'] ?? null, $person['relationship_to_victim'] ?? null, $person['guardian_contact'] ?? null, $person['guardian_address'] ?? null, $person['victim_name'] ?? null]
                            : [$person['guardian_abuser_name'] ?? null, $person['relationship_to_abuser'] ?? null, $person['guardian_abuser_contact'] ?? null, $person['guardian_abuser_address'] ?? null, $person['perpetrator_name'] ?? null];
                    }
                }
                $showAdditionalVictims = $isVictim
                    && ($user->isBcpcAdmin() || $user->isVawcAdmin())
                    && !empty($case->bcpc_data['additional_victims']);

                return collect($entries)->filter(fn ($info) => count(array_filter(array_slice($info, 0, 4))) > 0
                    || ($showAdditionalVictims && trim((string) ($info[4] ?? '')) !== ''))->values()->all();
            })
                ->unique(fn ($info) => json_encode(($user->position === 'Desk Officer' || $user->isBcpcAdmin() || $user->isVawcAdmin()) ? $info : array_slice($info, 0, 4)))->values();
        };

        return view('case-profiles.index', compact('victims', 'perpetrators', 'isAdmin', 'profileDepartment', 'profileGuardians'));
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
