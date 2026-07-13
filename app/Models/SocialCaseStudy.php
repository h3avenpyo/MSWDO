<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialCaseStudy extends Model
{
    use HasFactory;

    protected $connection = 'mswdo_social_case';
    protected $table = 'social_case_studies';

    protected $fillable = [
        'client_id',
        'officer_id',
        'case_number',
        'date_processed',
        'client_last_name',
        'client_first_name',
        'client_middle_name',
        'client_age',
        'client_sex',
        'client_barangay',
        'beneficiary_last_name',
        'beneficiary_first_name',
        'beneficiary_middle_name',
        'beneficiary_age',
        'beneficiary_birthday',
        'beneficiary_sex',
        'beneficiary_barangay',
        'medical_conditions',
        'service_provided',
        'purpose',
        'submitted_to',
        'encoded_by',
        'status',
        'workflow_step',
        'requirements_complete',
        'interview_complete',
        'evaluation_complete',
        'report_generated',
        'assistance_released',
        'assistance_amount',
        'assistance_date',
        'summary',
        'interview_date',
    ];

    protected $casts = [
        'date_processed' => 'date',
        'beneficiary_birthday' => 'date',
        'interview_date' => 'date',
        'assistance_date' => 'date',
        'medical_conditions' => 'array',
        'requirements_complete' => 'boolean',
        'interview_complete' => 'boolean',
        'evaluation_complete' => 'boolean',
        'report_generated' => 'boolean',
        'assistance_released' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
