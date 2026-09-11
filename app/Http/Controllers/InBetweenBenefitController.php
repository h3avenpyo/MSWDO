<?php

namespace App\Http\Controllers;

use App\Models\InBetweenBenefitConfiguration;
use App\Models\InBetweenBenefitHistory;
use App\Models\Senior\SeniorCitizenRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InBetweenBenefitController extends Controller
{
    private $config;

    public function __construct()
    {
        $this->config = InBetweenBenefitConfiguration::active()->first();
        if (!$this->config) {
            $this->config = InBetweenBenefitConfiguration::create([
                'benefit_name' => 'In-Between Birthday Cash Gift',
                'description' => 'Municipality of Silang In-Between Birthday Cash Gift for Senior Citizens',
                'benefit_amount' => 1000.00,
                'milestone_ages' => [80, 85, 90, 95, 100],
                'eligible_intervals' => ['81-84', '86-89', '91-94', '96-99'],
                'is_active' => true,
                'effective_date' => now(),
            ]);
        }
    }

    public function dashboard()
    {
        $totalEligible = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->get()
            ->filter(function ($senior) {
                return $this->isEligible($senior);
            })
            ->count();

        $totalClaimed = InBetweenBenefitHistory::claimed()->count();
        $totalPending = InBetweenBenefitHistory::pending()->count();
        $totalAmountReleased = InBetweenBenefitHistory::released()->sum('amount');

        $upcomingEligible = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->get()
            ->filter(function ($senior) {
                $age = $senior->age;
                $nextBirthday = Carbon::parse($senior->birth_date)->addYear($age + 1);
                $nextAge = $nextBirthday->age;
                return $this->isEligible($senior) && in_array($nextAge, [81, 86, 91, 96]);
            })
            ->take(10);

        $approachingNextInterval = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->get()
            ->filter(function ($senior) {
                $age = $senior->age;
                $nextBirthday = Carbon::parse($senior->birth_date)->addYear($age + 1);
                $nextAge = $nextBirthday->age;
                return in_array($nextAge, [86, 91, 96]);
            })
            ->take(10);

        return view('admin.senior.in-between-benefits.dashboard', compact(
            'totalEligible',
            'totalClaimed',
            'totalPending',
            'totalAmountReleased',
            'upcomingEligible',
            'approachingNextInterval'
        ));
    }

    public function eligibilityList(Request $request)
    {
        $sixYearsAgo = Carbon::now()->subYears(6);
        
        $query = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->where(function ($q) {
                $q->whereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 81 AND 84")
                    ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 86 AND 89")
                    ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 91 AND 94")
                    ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 96 AND 99");
            })
            ->whereDoesntHave('inBetweenBenefits', function ($q) use ($sixYearsAgo) {
                $q->claimed()->where('payout_date', '>=', $sixYearsAgo);
            })
            ->whereDoesntHave('inBetweenBenefits', function ($q) {
                $q->pending();
            });

        // Search filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('control_number', 'like', "%{$search}%")
                    ->orWhere('senior_id_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('barangay')) {
            $query->where('barangay', $request->barangay);
        }

        if ($request->filled('interval')) {
            $interval = $request->interval;
            $startAge = (int)substr($interval, 0, 2);
            $endAge = (int)substr($interval, 3, 2);
            $query->whereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN ? AND ?", [$startAge, $endAge]);
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'eligible') {
                $sixYearsAgo = Carbon::now()->subYears(6);
                $query->where(function ($q) {
                    $q->whereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 81 AND 84")
                        ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 86 AND 89")
                        ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 91 AND 94")
                        ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 96 AND 99");
                })->whereDoesntHave('inBetweenBenefits', function ($q) use ($sixYearsAgo) {
                    $q->claimed()->where('payout_date', '>=', $sixYearsAgo);
                })->whereDoesntHave('inBetweenBenefits', function ($q) {
                    $q->pending();
                });
            } elseif ($status === 'claimed') {
                $query->whereHas('inBetweenBenefits', function ($q) {
                    $q->claimed();
                });
            } elseif ($status === 'pending') {
                $query->whereHas('inBetweenBenefits', function ($q) {
                    $q->pending();
                });
            }
        }

        // Prioritize in-between interval seniors at the top, followed by newest registrations
        $query->orderByRaw("
            CASE 
                WHEN (TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 81 AND 84
                   OR TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 86 AND 89
                   OR TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 91 AND 94
                   OR TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 96 AND 99) THEN 0
                ELSE 1
            END ASC, id DESC
        ");

        $seniors = $query->paginate(20)->withQueryString();

        // Add eligibility information to each senior
        $seniors->getCollection()->transform(function ($senior) {
            $senior->eligibility_interval = $this->getEligibilityInterval($senior);
            $senior->has_claimed = $this->hasClaimedInterval($senior, $senior->eligibility_interval);
            $senior->is_pending = $this->hasPendingClaim($senior, $senior->eligibility_interval);
            $senior->is_eligible = $this->isEligible($senior);
            $senior->benefit_amount = $this->config->benefit_amount;
            return $senior;
        });

        // Summary Metric Aggregates
        $benefitAmount = (float)($this->config->benefit_amount ?? 1000.00);
        $totalEligible = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->where(function ($q) {
                $q->whereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 81 AND 84")
                    ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 86 AND 89")
                    ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 91 AND 94")
                    ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 96 AND 99");
            })
            ->count();

        $totalEstimatedBudget = $totalEligible * $benefitAmount;
        $totalClaimed = InBetweenBenefitHistory::whereIn('status', ['approved', 'released'])->count();
        $totalPending = InBetweenBenefitHistory::where('status', 'pending')->count();

        return view('admin.senior.in-between-benefits.eligibility-list', compact(
            'seniors',
            'totalEligible',
            'totalEstimatedBudget',
            'totalClaimed',
            'totalPending'
        ));
    }

    public function checkEligibility($seniorId)
    {
        $senior = SeniorCitizenRecord::findOrFail($seniorId);
        
        $eligibility = [
            'senior_id' => $senior->senior_id_number ?? $senior->control_number ?? ('#' . $senior->id),
            'database_id' => $senior->id,
            'senior_id_number' => $senior->senior_id_number,
            'control_number' => $senior->control_number,
            'full_name' => $senior->full_name,
            'birth_date' => $senior->birth_date,
            'current_age' => $senior->age,
            'eligibility_interval' => $this->getEligibilityInterval($senior),
            'is_eligible' => $this->isEligible($senior),
            'benefit_amount' => (float)($this->config->benefit_amount ?? 1000.00),
            'reason' => null,
            'previous_claims' => $this->getPreviousClaims($senior),
        ];

        if (!$eligibility['is_eligible']) {
            $eligibility['reason'] = $this->getIneligibilityReason($senior);
        }

        return response()->json($eligibility);
    }

    public function processClaim(Request $request, $seniorId)
    {
        $request->validate([
            'confirmation' => 'required|accepted',
        ]);

        $senior = SeniorCitizenRecord::findOrFail($seniorId);
        
        // Final eligibility check
        if (!$this->isEligible($senior)) {
            return response()->json([
                'success' => false,
                'message' => $this->getIneligibilityReason($senior),
            ], 400);
        }

        // Check for duplicate claim
        $interval = $this->getEligibilityInterval($senior);
        if ($this->hasClaimedInterval($senior, $interval)) {
            return response()->json([
                'success' => false,
                'message' => "This senior citizen has already received the in-between birthday cash gift for the {$interval} age interval.",
            ], 400);
        }

        try {
            DB::beginTransaction();

            $adminId = session('admin_user_id') ?? auth()->id();
            $adminName = session('admin_user_name') ?? (auth()->user() ? auth()->user()->name : 'Admin');

            $benefit = InBetweenBenefitHistory::create([
                'senior_id' => $senior->id,
                'full_name' => $senior->full_name,
                'birth_date' => $senior->birth_date,
                'current_age' => $senior->age,
                'benefit_type' => 'IN_BETWEEN_BIRTHDAY_GIFT',
                'eligibility_interval' => $interval,
                'birthday_year' => Carbon::parse($senior->birth_date)->year + $senior->age,
                'amount' => $this->config->benefit_amount,
                'application_date' => now(),
                'payout_date' => now(),
                'status' => 'released',
                'reference_number' => InBetweenBenefitHistory::generateNextReferenceNumber(),
                'processed_by' => $adminId,
                'approved_by' => $adminId,
                'remarks' => $request->remarks ?? null,
            ]);

            // Sync reference number with record id if preferred
            $formattedRef = 'IBG-' . now()->format('Y') . '-' . str_pad($benefit->id, 8, '0', STR_PAD_LEFT);
            if ($benefit->reference_number !== $formattedRef && !InBetweenBenefitHistory::where('reference_number', $formattedRef)->exists()) {
                $benefit->reference_number = $formattedRef;
                $benefit->save();
            }

            // Add audit trail
            $benefit->addAuditTrail('CREATED', 'Benefit claim created and released', $adminId);
            $benefit->addAuditTrail('RELEASED', 'Benefit released by ' . $adminName, $adminId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'In-between birthday cash gift claim processed successfully',
                'benefit' => $benefit,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process claim: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkProcessClaims(Request $request)
    {
        $selectAll = $request->boolean('select_all');

        if ($selectAll) {
            $query = SeniorCitizenRecord::where('status', 'active')
                ->whereNotNull('birth_date')
                ->where(function ($q) {
                    $q->whereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 81 AND 84")
                        ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 86 AND 89")
                        ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 91 AND 94")
                        ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 96 AND 99");
                });

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('control_number', 'like', "%{$search}%")
                        ->orWhere('senior_id_number', 'like', "%{$search}%");
                });
            }

            if ($request->filled('barangay')) {
                $query->where('barangay', $request->barangay);
            }

            if ($request->filled('interval')) {
                $interval = $request->interval;
                $startAge = (int)substr($interval, 0, 2);
                $endAge = (int)substr($interval, 3, 2);
                $query->whereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN ? AND ?", [$startAge, $endAge]);
            }

            if ($request->filled('status')) {
                $status = $request->status;
                if ($status === 'eligible') {
                    $query->where(function ($q) {
                        $q->whereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 81 AND 84")
                            ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 86 AND 89")
                            ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 91 AND 94")
                            ->orWhereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 96 AND 99");
                    });
                } elseif ($status === 'claimed') {
                    $query->whereHas('inBetweenBenefits', function ($q) {
                        $q->claimed();
                    });
                } elseif ($status === 'pending') {
                    $query->whereHas('inBetweenBenefits', function ($q) {
                        $q->pending();
                    });
                }
            }

            $seniors = $query->get();
        } else {
            $ids = $request->input('ids', []);
            if (is_string($ids)) {
                $ids = json_decode($ids, true) ?? explode(',', $ids);
            }
            if (empty($ids) || !is_array($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No seniors selected.',
                ], 400);
            }
            $seniors = SeniorCitizenRecord::whereIn('id', $ids)
                ->where('status', 'active')
                ->whereNotNull('birth_date')
                ->get();
        }

        if ($seniors->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active senior citizen records found for processing.',
            ], 400);
        }

        $adminId = session('admin_user_id') ?? auth()->id();
        $adminName = session('admin_user_name') ?? (auth()->user() ? auth()->user()->name : 'Admin');
        $processedCount = 0;
        $skippedCount = 0;
        $errors = [];

        foreach ($seniors as $senior) {
            // Check eligibility
            if (!$this->isEligible($senior)) {
                $skippedCount++;
                continue;
            }

            // Check if already claimed
            $interval = $this->getEligibilityInterval($senior);
            if ($this->hasClaimedInterval($senior, $interval)) {
                $skippedCount++;
                continue;
            }

            try {
                DB::beginTransaction();

                    $benefit = InBetweenBenefitHistory::create([
                        'senior_id' => $senior->id,
                        'full_name' => $senior->full_name,
                        'birth_date' => $senior->birth_date,
                        'current_age' => $senior->age,
                        'benefit_type' => 'IN_BETWEEN_BIRTHDAY_GIFT',
                        'eligibility_interval' => $interval,
                        'birthday_year' => Carbon::parse($senior->birth_date)->year + $senior->age,
                        'amount' => $this->config->benefit_amount,
                        'application_date' => now(),
                        'payout_date' => now(),
                        'status' => 'released',
                        'reference_number' => InBetweenBenefitHistory::generateNextReferenceNumber(),
                        'processed_by' => $adminId,
                        'approved_by' => $adminId,
                        'remarks' => $request->remarks ?? 'Bulk processed claim',
                    ]);

                    $formattedRef = 'IBG-' . now()->format('Y') . '-' . str_pad($benefit->id, 8, '0', STR_PAD_LEFT);
                    if ($benefit->reference_number !== $formattedRef && !InBetweenBenefitHistory::where('reference_number', $formattedRef)->exists()) {
                        $benefit->reference_number = $formattedRef;
                        $benefit->save();
                    }

                $benefit->addAuditTrail('CREATED', 'Benefit claim created and released via bulk processing', $adminId);
                $benefit->addAuditTrail('RELEASED', 'Benefit released by ' . $adminName, $adminId);

                DB::commit();
                $processedCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "Failed for {$senior->full_name} (ID: {$senior->id}): " . $e->getMessage();
            }
        }

        if ($processedCount === 0 && $skippedCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "All {$skippedCount} selected senior(s) were skipped because they are either not eligible or have already claimed.",
                'processed_count' => 0,
                'skipped_count' => $skippedCount,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$processedCount} claim(s)." . ($skippedCount > 0 ? " ({$skippedCount} skipped: ineligible or already claimed)" : ""),
            'processed_count' => $processedCount,
            'skipped_count' => $skippedCount,
            'errors' => $errors,
        ]);
    }

    public function benefitHistory(Request $request)
    {
        $query = InBetweenBenefitHistory::with(['senior', 'processedBy', 'approvedBy'])
            ->where('is_exported', false); // Only show non-exported records

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('senior_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('senior_id')) {
            $query->where('senior_id', $request->senior_id);
        }

        if ($request->filled('interval')) {
            $query->where('eligibility_interval', $request->interval);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('barangay')) {
            $query->whereHas('senior', function ($q) use ($request) {
                $q->where('barangay', $request->barangay);
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('application_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('application_date', '<=', $request->to_date);
        }

        $totalRecords = InBetweenBenefitHistory::count();
        $totalAmount = (float) InBetweenBenefitHistory::sum('amount');
        $approvedCount = InBetweenBenefitHistory::where('status', 'approved')->count();
        $releasedCount = InBetweenBenefitHistory::where('status', 'released')->count();

        $benefits = $query->orderBy('application_date', 'desc')->paginate(15)->withQueryString();

        return view('admin.senior.in-between-benefits.history', compact(
            'benefits',
            'totalRecords',
            'totalAmount',
            'approvedCount',
            'releasedCount'
        ))->with('totalAllRecords', $totalRecords);
    }

    public function markExported(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:in_between_benefit_history,id'],
        ]);

        $updated = InBetweenBenefitHistory::whereIn('id', $validated['ids'])
            ->where('status', 'released')
            ->update(['is_exported' => true]);

        return response()->json([
            'success' => true,
            'updated_count' => $updated,
        ]);
    }

    public function seniorBenefitCard($seniorId)
    {
        $senior = SeniorCitizenRecord::findOrFail($seniorId);
        $interval = $this->getEligibilityInterval($senior);
        $isEligible = $this->isEligible($senior);
        $hasClaimed = $this->hasClaimedInterval($senior, $interval);
        
        $previousClaims = $this->getPreviousClaims($senior);
        $currentClaim = $hasClaimed ? InBetweenBenefitHistory::bySenior($seniorId)
            ->byInterval($interval)
            ->first() : null;

        return view('admin.senior.in-between-benefits.senior-card', compact(
            'senior',
            'interval',
            'isEligible',
            'hasClaimed',
            'previousClaims',
            'currentClaim'
        ));
    }

    public function reports(Request $request)
    {
        $reportType = $request->report_type ?? 'eligible';
        
        switch ($reportType) {
            case 'eligible':
                return $this->generateEligibleReport($request);
            case 'claimed':
                return $this->generateClaimedReport($request);
            case 'unclaimed':
                return $this->generateUnclaimedReport($request);
            case 'by_barangay':
                return $this->generateBarangayReport($request);
            case 'by_interval':
                return $this->generateIntervalReport($request);
            case 'monthly':
                return $this->generateMonthlyReport($request);
            case 'annual':
                return $this->generateAnnualReport($request);
            default:
                return response()->json(['error' => 'Invalid report type'], 400);
        }
    }

    // Helper methods for eligibility calculation

    private function isEligible(SeniorCitizenRecord $senior): bool
    {
        $age = $senior->age;
        
        // Check if age is within eligible range
        $interval = $this->getEligibilityInterval($senior);
        if (!$interval) {
            return false;
        }

        // Check if already claimed for this interval
        if ($this->hasClaimedInterval($senior, $interval)) {
            return false;
        }

        // Check if has a pending claim for this interval
        if ($this->hasPendingClaim($senior, $interval)) {
            return false;
        }

        return true;
    }

    private function getEligibilityInterval(SeniorCitizenRecord $senior): ?string
    {
        $age = $senior->age;
        return $this->config->getEligibilityIntervalForAge($age);
    }

    private function hasClaimedInterval(SeniorCitizenRecord $senior, ?string $interval): bool
    {
        if (!$interval) {
            return false;
        }

        return InBetweenBenefitHistory::bySenior($senior->id)
            ->byInterval($interval)
            ->claimed()
            ->exists();
    }

    private function hasPendingClaim(SeniorCitizenRecord $senior, ?string $interval): bool
    {
        if (!$interval) {
            return false;
        }

        return InBetweenBenefitHistory::bySenior($senior->id)
            ->byInterval($interval)
            ->pending()
            ->exists();
    }

    private function getPreviousClaims(SeniorCitizenRecord $senior): array
    {
        return InBetweenBenefitHistory::bySenior($senior->id)
            ->claimed()
            ->get()
            ->map(function ($claim) {
                return [
                    'interval' => $claim->eligibility_interval,
                    'amount' => $claim->amount,
                    'claim_date' => $claim->payout_date,
                    'reference_number' => $claim->reference_number,
                ];
            })
            ->toArray();
    }

    private function getIneligibilityReason(SeniorCitizenRecord $senior): string
    {
        $age = $senior->age;
        
        if ($this->config->isMilestoneAge($age)) {
            return "Age {$age} is a milestone age and not eligible for in-between birthday cash gift.";
        }

        $interval = $this->getEligibilityInterval($senior);
        if (!$interval) {
            return "Age {$age} is not within any eligible age interval (81-84, 86-89, 91-94, 96-99).";
        }

        if ($this->hasClaimedInterval($senior, $interval)) {
            return "Already claimed the in-between birthday cash gift for the {$interval} age interval.";
        }

        return "Not eligible for in-between birthday cash gift.";
    }

    // Report generation methods

    private function generateEligibleReport(Request $request)
    {
        $seniors = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->get()
            ->filter(function ($senior) {
                return $this->isEligible($senior);
            });

        return response()->json([
            'report_type' => 'eligible_seniors',
            'total_count' => $seniors->count(),
            'data' => $seniors,
        ]);
    }

    private function generateClaimedReport(Request $request)
    {
        $benefits = InBetweenBenefitHistory::claimed()
            ->with('senior')
            ->get();

        return response()->json([
            'report_type' => 'claimed_benefits',
            'total_count' => $benefits->count(),
            'total_amount' => $benefits->sum('amount'),
            'data' => $benefits,
        ]);
    }

    private function generateUnclaimedReport(Request $request)
    {
        $benefits = InBetweenBenefitHistory::pending()
            ->with('senior')
            ->get();

        return response()->json([
            'report_type' => 'unclaimed_benefits',
            'total_count' => $benefits->count(),
            'total_amount' => $benefits->sum('amount'),
            'data' => $benefits,
        ]);
    }

    private function generateBarangayReport(Request $request)
    {
        $benefits = InBetweenBenefitHistory::claimed()
            ->with('senior')
            ->get()
            ->groupBy(function ($benefit) {
                return $benefit->senior->barangay ?? 'Unknown';
            })
            ->map(function ($group) {
                return [
                    'barangay' => $group->first()->senior->barangay ?? 'Unknown',
                    'count' => $group->count(),
                    'total_amount' => $group->sum('amount'),
                ];
            })
            ->sortByDesc('count')
            ->values();

        return response()->json([
            'report_type' => 'benefits_by_barangay',
            'data' => $benefits,
        ]);
    }

    private function generateIntervalReport(Request $request)
    {
        $benefits = InBetweenBenefitHistory::claimed()
            ->get()
            ->groupBy('eligibility_interval')
            ->map(function ($group) {
                return [
                    'interval' => $group->first()->eligibility_interval,
                    'count' => $group->count(),
                    'total_amount' => $group->sum('amount'),
                ];
            })
            ->sortBy('interval')
            ->values();

        return response()->json([
            'report_type' => 'benefits_by_interval',
            'data' => $benefits,
        ]);
    }

    private function generateMonthlyReport(Request $request)
    {
        $benefits = InBetweenBenefitHistory::claimed()
            ->get()
            ->groupBy(function ($benefit) {
                return Carbon::parse($benefit->payout_date)->format('Y-m');
            })
            ->map(function ($group) {
                return [
                    'month' => $group->first()->payout_date->format('F Y'),
                    'count' => $group->count(),
                    'total_amount' => $group->sum('amount'),
                ];
            })
            ->sortByDesc('month')
            ->values();

        return response()->json([
            'report_type' => 'monthly_benefits',
            'data' => $benefits,
        ]);
    }

    private function generateAnnualReport(Request $request)
    {
        $benefits = InBetweenBenefitHistory::claimed()
            ->get()
            ->groupBy(function ($benefit) {
                return Carbon::parse($benefit->payout_date)->format('Y');
            })
            ->map(function ($group) {
                return [
                    'year' => $group->first()->payout_date->format('Y'),
                    'count' => $group->count(),
                    'total_amount' => $group->sum('amount'),
                ];
            })
            ->sortByDesc('year')
            ->values();

        return response()->json([
            'report_type' => 'annual_benefits',
            'data' => $benefits,
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No records selected'], 400);
        }

        try {
            $deleted = InBetweenBenefitHistory::whereIn('id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deleted} record(s)"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting records: ' . $e->getMessage()
            ], 500);
        }
    }
}
