<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryIntake extends Model
{
    protected $fillable = [
        'client_id',
        'social_case_study_id',
        'control_number',
        'date_processed',
        'encoder',
        'is_client_beneficiary',
        'beneficiary_last_name',
        'beneficiary_first_name',
        'beneficiary_middle_name',
        'beneficiary_birthday',
        'beneficiary_age',
        'beneficiary_sex',
        'beneficiary_barangay',
        'beneficiary_relationship',
        'medical_conditions',
        'medical_condition_other',
        'service_provided',
        'purpose',
        'purpose_other',
        'submitted_to',
    ];

    protected $casts = [
        'date_processed' => 'date',
        'beneficiary_birthday' => 'date',
        'is_client_beneficiary' => 'boolean',
        'medical_conditions' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function socialCaseStudy(): BelongsTo
    {
        return $this->belongsTo(SocialCaseStudy::class);
    }

    public function encoderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encoder');
    }

    public function getClientFullNameAttribute(): ?string
    {
        return $this->client?->full_name;
    }

    public function getBeneficiaryFullNameAttribute(): ?string
    {
        if ($this->is_client_beneficiary) {
            return $this->client_full_name;
        }
        return trim("{$this->beneficiary_first_name} {$this->beneficiary_middle_name} {$this->beneficiary_last_name}");
    }
}
