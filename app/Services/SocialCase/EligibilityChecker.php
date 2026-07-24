<?php

namespace App\Services\SocialCase;

use App\Models\SocialCase\AssistanceRecord;
use App\Models\Client;
use Carbon\Carbon;

class EligibilityChecker
{
    public function check(Client $client): array
    {
        $latestAssistance = $client->assistanceRecords()
            ->approvedReleased()
            ->orderByDesc('release_date')
            ->first();

        $recentWithinSixMonths = $client->assistanceRecords()
            ->withinSixMonths()
            ->orderByDesc('release_date')
            ->first();

        $eligible = $recentWithinSixMonths === null;
        $eligibleAgainDate = null;

        if ($recentWithinSixMonths !== null) {
            $eligibleAgainDate = Carbon::parse($recentWithinSixMonths->release_date)
                ->addMonths(6)
                ->startOfDay();
        }

        return [
            'eligible' => $eligible,
            'client' => $client,
            'latestAssistance' => $latestAssistance,
            'blockingRecord' => $recentWithinSixMonths,
            'eligibleAgainDate' => $eligibleAgainDate,
            'lastAssistanceDate' => $latestAssistance?->release_date,
        ];
    }

    public function hasRecentApprovedAssistance(Client $client): bool
    {
        return $client->assistanceRecords()
            ->withinSixMonths()
            ->exists();
    }
}
