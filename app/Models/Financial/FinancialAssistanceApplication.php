<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;

class FinancialAssistanceApplication extends Model
{
    protected $connection = 'mswdo_financial';

    protected $table = 'financial_assistance_applications';

    protected $fillable = [
        'application_number',
        'applicant_name',
        'assistance_type',
        'amount_requested',
        'created_by',
        'status',
    ];
}
