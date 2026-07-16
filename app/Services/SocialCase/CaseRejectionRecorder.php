<?php

namespace App\Services\SocialCase;

use App\Models\CaseRejection;
use App\Models\Client;

class CaseRejectionRecorder
{
    public function record(Client $client, array $eligibility, bool $close = false): CaseRejection
    {
        $blockingRecord = $eligibility['blockingRecord'];

        $rejection = CaseRejection::firstOrNew([
            'client_id' => $client->id,
            'blocking_assistance_id' => $blockingRecord?->id,
        ]);

        $rejection->fill([
            'officer_id' => session('admin_user_id'),
            'officer_name' => session('admin_user_name'),
            'reason' => 'Client received approved or released assistance within the last six months.',
            'last_assistance_date' => $eligibility['lastAssistanceDate'],
            'last_assistance_type' => $eligibility['latestAssistance']?->assistance_type,
            'next_eligible_date' => $eligibility['eligibleAgainDate'],
        ]);

        if (! $rejection->exists) {
            $rejection->rejected_at = now();
        }

        if ($close && $rejection->closed_at === null) {
            $rejection->closed_at = now();
        }

        $rejection->save();

        return $rejection;
    }
}
