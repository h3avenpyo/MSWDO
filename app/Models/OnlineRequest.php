<?php

namespace App\Models;

use App\Models\SocialCase\SocialCaseStudy;
use Illuminate\Database\Eloquent\Model;

class OnlineRequest extends Model
{
    protected $fillable = [
        'request_for',
        'first_name',
        'last_name',
        'dob',
        'barangay',
        'contact_number',
        'email',
        'address',
        'service_type',
        'assistance_type',
        'situation',
        'status',
        'notes',
        'processed_by',
        'case_id',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function attachments()
    {
        return $this->hasMany(OnlineRequestAttachment::class);
    }

    public function case()
    {
        return $this->belongsTo(SocialCaseStudy::class, 'case_id');
    }
}
