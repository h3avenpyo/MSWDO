<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssistanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'social_case_study_id',
        'assistance_type',
        'status',
        'release_date',
        'amount',
        'remarks',
    ];

    protected $casts = [
        'release_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function socialCaseStudy(): BelongsTo
    {
        return $this->belongsTo(SocialCaseStudy::class);
    }

    public function scopeApprovedReleased($query)
    {
        return $query->whereIn('status', ['Approved', 'Released']);
    }

    public function scopeWithinSixMonths($query)
    {
        return $query->approvedReleased()
            ->whereDate('release_date', '>=', now()->subMonths(6)->toDateString());
    }
}
