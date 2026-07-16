<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SocialCaseStudy extends Model
{
    protected $fillable = [
        'client_id',
        'officer_id',
        'case_number',
        'date_processed',
        'service_provided',
        'purpose',
        'submitted_to',
        'encoded_by',
        'status',
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
    ];

    protected $casts = [
        'date_processed' => 'date',
        'interview_date' => 'date',
        'assistance_date' => 'date',
        'released_at' => 'datetime',
        'requirements_complete' => 'boolean',
        'interview_complete' => 'boolean',
        'evaluation_complete' => 'boolean',
        'report_generated' => 'boolean',
        'assistance_released' => 'boolean',
        'assistance_amount' => 'decimal:2',
    ];

    protected $appends = ['control_no', 'released_date'];

    public function getControlNoAttribute(): ?string
    {
        return $this->case_number;
    }

    public function getReleasedDateAttribute(): ?string
    {
        return $this->assistance_date ? $this->assistance_date->format('Y-m-d') : null;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function encoder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encoded_by');
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
}
