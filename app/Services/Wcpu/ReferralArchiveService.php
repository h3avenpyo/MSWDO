<?php

namespace App\Services\Wcpu;

use App\Models\Wcpu\CaseReferral;
use App\Models\Wcpu\ReportRecipient;
use Illuminate\Support\Facades\DB;

class ReferralArchiveService
{
    public function restore(CaseReferral|ReportRecipient $record): void
    {
        $record->restore();
    }

    public function permanentlyDelete(CaseReferral|ReportRecipient $record): void
    {
        $record->forceDelete();
    }

    private function updateArchived(CaseReferral|ReportRecipient $record, bool $delete): void
    {
        DB::connection('wcpu')->transaction(function () use ($record, $delete) {
            $email = $record instanceof CaseReferral ? $record->recipient_email : $record->email;
            $method = $delete ? 'forceDelete' : 'restore';
            $record->$method();
            if (filled($email)) {
                CaseReferral::onlyTrashed()->where('recipient_email', $email)->$method();
                ReportRecipient::onlyTrashed()->where('email', $email)->$method();
            }
        });
    }

    public function archive(CaseReferral|ReportRecipient $record, bool $confirmLinkedReferrals = false): void
    {
        DB::connection('wcpu')->transaction(function () use ($record, $confirmLinkedReferrals) {
            if ($record instanceof ReportRecipient) {
                $record->delete();
                return;
            }

            $record->delete();
        });
    }
}
