<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryIntake extends Model
{
    protected $connection = 'mswdo_social_case';

    protected $fillable = [
        'client_id',
        'social_case_study_id',
        'control_number',
        'date_processed',
        'encoder',
        'client_last_name',
        'client_first_name',
        'client_middle_name',
        'client_birthday',
        'client_age',
        'client_sex',
        'client_civil_status',
        'client_address',
        'client_barangay',
        'client_contact_number',
        'client_occupation',
        'client_monthly_income',
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

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function socialCaseStudy(): BelongsTo
    {
        return $this->belongsTo(SocialCaseStudy::class);
    }

    protected $casts = [
        'date_processed' => 'date',
        'client_birthday' => 'date',
        'beneficiary_birthday' => 'date',
        'client_monthly_income' => 'decimal:2',
        'is_client_beneficiary' => 'boolean',
        'medical_conditions' => 'array',
    ];

    public function getClientFullNameAttribute(): string
    {
        return trim("{$this->client_first_name} {$this->client_middle_name} {$this->client_last_name}");
    }

    public function getBeneficiaryFullNameAttribute(): ?string
    {
        if ($this->is_client_beneficiary) {
            return $this->client_full_name;
        }
        return trim("{$this->beneficiary_first_name} {$this->beneficiary_middle_name} {$this->beneficiary_last_name}");
    }
}
