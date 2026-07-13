<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $connection = 'mswdo_social_case';
    protected $table = 'clients';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'gender',
        'address',
        'contact_number',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function assistanceRecords(): HasMany
    {
        return $this->hasMany(AssistanceRecord::class, 'client_id', 'id');
    }

    public function socialCaseStudies(): HasMany
    {
        return $this->hasMany(SocialCaseStudy::class, 'client_id', 'id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(EligibilityAuditLog::class, 'client_id', 'id');
    }

    public function caseRejections(): HasMany
    {
        return $this->hasMany(CaseRejection::class, 'client_id', 'id');
    }

    public function beneficiaryIntakes(): HasMany
    {
        return $this->hasMany(BeneficiaryIntake::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim(sprintf('%s %s %s', $this->first_name, $this->middle_name, $this->last_name));
    }
}
