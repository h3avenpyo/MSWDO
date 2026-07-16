<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialAssistanceApplication extends Model
{
    protected $fillable = [
        'client_id',
        'application_number',
        'applicant_name',
        'assistance_type',
        'amount_requested',
        'created_by',
        'status',
    ];

    protected $casts = [
        'amount_requested' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
