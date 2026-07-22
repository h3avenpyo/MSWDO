<?php

namespace App\Models\SocialCase;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseRejection extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'blocking_assistance_id',
        'social_case_study_id',
        'officer_id',
        'officer_name',
        'reason',
        'last_assistance_date',
        'last_assistance_type',
        'next_eligible_date',
        'rejected_at',
        'closed_at',
    ];

    protected $casts = [
        'last_assistance_date' => 'date',
        'next_eligible_date' => 'date',
        'rejected_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function blockingAssistance(): BelongsTo
    {
        return $this->belongsTo(AssistanceRecord::class, 'blocking_assistance_id');
    }

    public function socialCaseStudy(): BelongsTo
    {
        return $this->belongsTo(SocialCaseStudy::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }
}
