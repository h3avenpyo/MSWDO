<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\SignaturePosition;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'position',
        'employee_id',
        'address',
        'status',
        'signature_image',
        'signature_position',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
            'signature_position' => SignaturePosition::class,
        ];
    }

    public function socialCaseStudies(): HasMany
    {
        return $this->hasMany(\App\Models\SocialCase\SocialCaseStudy::class, 'officer_id');
    }

    public function encodedCases(): HasMany
    {
        return $this->hasMany(\App\Models\SocialCase\SocialCaseStudy::class, 'encoded_by');
    }

    public function releasedPayouts(): HasMany
    {
        return $this->hasMany(\App\Models\Senior\BirthdayPayout::class, 'released_by');
    }

    public function payoutHistories(): HasMany
    {
        return $this->hasMany(\App\Models\Senior\BirthdayPayoutHistory::class, 'performed_by');
    }
}
