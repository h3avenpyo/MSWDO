<?php

namespace App\Models\SocialCase;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SocialCaseStudy extends Model
{
    protected $fillable = [
        'main_client_id',
        'officer_id',
        'case_number',
        'date_processed',
        'service_provided',
        'purpose',
        'submitted_to',
        'encoded_by',
        'status',
        'eligibility_status',
        'eligible_by',
        'eligible_at',
        'ineligible_reason',
        'summary',
        'interview_date',
        'workflow_step',
        'requirements_complete',
        'interview_complete',
        'evaluation_complete',
        'report_generated',
        'assistance_released',
        'assistance_amount',
        'assistance_date',
        'released_at',
        'released_by',
        'released_to',
        'signers',
        'document_ref_number',
        'intake_first_name',
        'intake_middle_name',
        'intake_last_name',
        'intake_suffix',
        'intake_full_name',
        'intake_birthdate',
        'intake_age',
        'intake_gender',
        'intake_civil_status',
        'intake_religion',
        'intake_birthplace',
        'intake_education',
        'intake_occupation',
        'intake_income',
        'intake_address',
        'intake_barangay',
        'intake_contact_number',
    ];

    protected $casts = [
        'date_processed' => 'date',
        'interview_date' => 'date',
        'assistance_date' => 'date',
        'released_at' => 'datetime',
        'eligible_at' => 'datetime',
        'requirements_complete' => 'boolean',
        'interview_complete' => 'boolean',
        'evaluation_complete' => 'boolean',
        'report_generated' => 'boolean',
        'assistance_released' => 'boolean',
        'assistance_amount' => 'decimal:2',
        'signers' => 'array',
        'document_ref_number' => 'integer',
        'intake_birthdate' => 'date',
        'intake_age' => 'integer',
    ];

    protected $appends = ['control_no', 'released_date', 'latest_event_date'];

    public function getControlNoAttribute(): ?string
    {
        return $this->case_number;
    }

    public function getReleasedDateAttribute(): ?string
    {
        return $this->released_at ? $this->released_at->format('Y-m-d') : null;
    }

    /**
     * The most recent event date of a case — used as the anchor for the 6-month
     * eligibility window. Whichever of date_processed / released_at /
     * assistance_date / created_at is latest.
     */
    public function getLatestEventDateAttribute(): ?\Illuminate\Support\Carbon
    {
        $candidates = array_filter([
            $this->date_processed,
            $this->released_at?->toDate() ?? $this->released_at,
            $this->assistance_date,
            $this->created_at?->toDate() ?? $this->created_at,
        ]);

        if (empty($candidates)) {
            return null;
        }

        return collect($candidates)
            ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d))
            ->max();
    }

    /**
     * The authoritative main client of the case.
     */
    public function mainClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'main_client_id');
    }

    /**
     * Legacy alias for the main client relationship so existing views and the
     * front-end (c.client.*) keep working while the codebase migrates fully to
     * main_client_id. Binds to the same column.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'main_client_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function encoder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encoded_by');
    }

    public function eligibleByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'eligible_by');
    }

    public function releasedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    public function interview(): HasOne
    {
        return $this->hasOne(CaseInterview::class);
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function beneficiaryIntake(): HasOne
    {
        return $this->hasOne(BeneficiaryIntake::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(\App\Models\SocialCase\SocialCaseReport::class);
    }

    public function releaseLogs(): HasMany
    {
        return $this->hasMany(\App\Models\SocialCase\SocialCaseReportReleaseLog::class);
    }

    public function assistanceRecords(): HasMany
    {
        return $this->hasMany(AssistanceRecord::class);
    }

    public function rejections(): HasMany
    {
        return $this->hasMany(CaseRejection::class);
    }

    public function scopePendingEligibility($query)
    {
        return $query->where('eligibility_status', 'pending');
    }

    public function scopeEligibleForEncoding($query)
    {
        return $query->where('eligibility_status', 'eligible');
    }

    public function scopeIneligible($query)
    {
        return $query->where('eligibility_status', 'ineligible');
    }
}
