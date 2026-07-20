<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'gender',
        'age',
        'address',
        'barangay',
        'contact_number',
        'birthplace',
        'religion',
        'education',
        'civil_status',
        'occupation',
        'income',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function socialCaseStudies(): HasMany
    {
        return $this->hasMany(SocialCaseStudy::class);
    }

    public function assistanceRecords(): HasMany
    {
        return $this->hasMany(AssistanceRecord::class);
    }

    public function beneficiaryIntakes(): HasMany
    {
        return $this->hasMany(BeneficiaryIntake::class);
    }

    public function caseRejections(): HasMany
    {
        return $this->hasMany(CaseRejection::class);
    }

    public function eligibilityLogs(): HasMany
    {
        return $this->hasMany(EligibilityAuditLog::class);
    }

    public function financialApplications(): HasMany
    {
        return $this->hasMany(\App\Models\Financial\FinancialAssistanceApplication::class);
    }

    protected $appends = ['full_name', 'name', 'age', 'sex', 'civil_status'];

    public function getFullNameAttribute(): string
    {
        return trim(sprintf('%s %s %s', $this->first_name, $this->middle_name, $this->last_name));
    }

    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birthdate ? \Carbon\Carbon::parse($this->birthdate)->age : null;
    }

    public function getSexAttribute(): ?string
    {
        return $this->gender;
    }

    public function getCivilStatusAttribute(): ?string
    {
        return $this->attributes['civil_status'] ?? null;
    }
}
