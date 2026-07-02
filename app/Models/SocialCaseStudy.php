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
        'status',
        'summary',
        'interview_date',
    ];

    protected $casts = [
        'interview_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
