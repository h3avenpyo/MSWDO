<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EligibilityAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_name',
        'officer_id',
        'officer_name',
        'result',
        'result_details',
        'ip_address',
        'user_agent',
        'search_duration_ms',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }
}
