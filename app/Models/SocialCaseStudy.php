<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialCaseStudy extends Model
{
    protected $fillable = [
        'control_no',
        'status',
        'released_date',
        'client',
        'household',
        'interview',
        'signers',
        'purpose',
        'agencies',
        'requirements',
        'status_history',
    ];

    protected $casts = [
        'client' => 'array',
        'household' => 'array',
        'interview' => 'array',
        'signers' => 'array',
        'agencies' => 'array',
        'requirements' => 'array',
        'status_history' => 'array',
        'released_date' => 'date',
    ];
}
