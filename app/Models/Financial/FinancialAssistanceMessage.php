<?php

namespace App\Models\Financial;

use App\Models\Client;
use App\Models\SocialCase\BeneficiaryIntake;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialAssistanceMessage extends Model
{
    protected $fillable = [
        'intake_id',
        'client_id',
        'beneficiary_name',
        'recipient_name',
        'recipient_contact_number',
        'message_body',
        'message_type',
        'claiming_date',
        'status',
        'reference_id',
        'error_message',
        'sent_by',
        'sent_at',
    ];

    protected $casts = [
        'claiming_date' => 'date',
        'sent_at' => 'datetime',
    ];

    public function intake(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryIntake::class, 'intake_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function getFormattedSentAtAttribute(): string
    {
        if ($this->sent_at) {
            return $this->sent_at->format('F d, Y h:i A');
        }
        return $this->created_at ? $this->created_at->format('F d, Y h:i A') : 'N/A';
    }

    public function getFormattedDateAttribute(): string
    {
        $dt = $this->sent_at ?? $this->created_at;
        return $dt ? $dt->format('F d, Y') : 'N/A';
    }

    public function getFormattedClaimingDateAttribute(): ?string
    {
        return $this->claiming_date ? $this->claiming_date->format('F d, Y') : null;
    }
}
