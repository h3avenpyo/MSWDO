<?php

namespace App\Services\SocialCase;

use App\Models\SocialCase\SocialCaseStudy;
use App\Models\Client;
use Carbon\Carbon;

class EligibilityChecker
{
    /**
     * Server-side 6-month eligibility rule for Social Case Studies.
     *
     * A person is BLOCKED when they were the MAIN CLIENT of any previous Social
     * Case whose latest event date (the most recent of date_processed,
     * released_at, assistance_date, or created_at) falls within the last 6
     * months. Relatives recorded in a case's family_members are NEVER blocked
     * by that case.
     *
     * assistance_records are intentionally not the gate; the production system
     * records assistance on the case itself, not on that legacy table.
     */
    public function check(Client $client): array
    {
        $sixMonthsAgo = now()->subMonths(6);

        $blockingCase = $client->socialCaseStudies()
            ->where(function ($q) use ($sixMonthsAgo) {
                $q->where('date_processed', '>=', $sixMonthsAgo)
                  ->orWhere('released_at', '>=', $sixMonthsAgo)
                  ->orWhere('assistance_date', '>=', $sixMonthsAgo)
                  ->orWhere('created_at', '>=', $sixMonthsAgo);
            })
            ->orderByRaw(
                'GREATEST(COALESCE(date_processed, created_at), '
                . 'COALESCE(released_at, created_at), '
                . 'COALESCE(assistance_date, created_at), created_at) DESC'
            )
            ->first();

        $eligible = $blockingCase === null;
        $eligibleAgainDate = null;

        if ($blockingCase) {
            $eventDate = $blockingCase->latest_event_date ?? Carbon::parse($blockingCase->created_at);
            $eligibleAgainDate = $eventDate->copy()->addMonths(6)->startOfDay();
        }

        return [
            'eligible'           => $eligible,
            'client'             => $client,
            'blockingCase'       => $blockingCase,
            'eligibleAgainDate'  => $eligibleAgainDate,
            'blockingEventDate'  => $blockingCase ? ($blockingCase->latest_event_date?->toDateString() ?? null) : null,
        ];
    }

    public function hasRecentApprovedAssistance(Client $client): bool
    {
        return $this->check($client)['eligible'] === false;
    }
}