<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseRejection extends Model
{
    use HasFactory;

    protected $connection = 'mswdo_social_case';

    protected $fillable = [
        'client_id',
        'blocking_assistance_id',
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
}
