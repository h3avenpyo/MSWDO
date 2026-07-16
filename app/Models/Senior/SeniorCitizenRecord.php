<?php

namespace App\Models\Senior;

use App\Enums\SeniorStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SeniorCitizenRecord extends Model
{
    protected $table = 'senior_citizen_records';

    protected $fillable = [
        'record_number',
        'first_name',
        'middle_name',
        'last_name',
        'year_applied',
        'control_number',
        'senior_id_number',
        'address',
        'barangay',
        'birth_date',
        'sex',
        'contact_number',
        'philsys_number',
        'rrn_number',
        'osca_id',
        'blood_type',
        'civil_status',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_relationship',
        'photo',
        'avatar_image',
        'qr_code',
        'qr_code_image',
        'date_issued',
        'last_printed_at',
        'print_count',
        'remarks',
        'created_by',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'date_issued' => 'date',
        'last_printed_at' => 'datetime',
        'print_count' => 'integer',
        'status' => SeniorStatus::class,
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(BirthdayPayout::class, 'senior_id');
    }

    public function payoutHistories(): HasMany
    {
        return $this->hasMany(BirthdayPayoutHistory::class, 'senior_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->age : null;
    }

    public function getBirthMonthAttribute(): ?string
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->format('F') : null;
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo && file_exists(public_path($this->photo))) {
            return asset($this->photo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&background=1A237E&color=fff&size=128';
    }

    public function generateSeniorIdNumber()
    {
        if ($this->senior_id_number) {
            return $this->senior_id_number;
        }

        if ($this->control_number) {
            if (preg_match('/SC-[A-Z0-9]+-(\d{4})-(\d+)/i', $this->control_number, $matches)) {
                $year = $matches[1];
                $sequence = str_pad($matches[2], 6, '0', STR_PAD_LEFT);
                return "SC-{$year}-{$sequence}";
            }
        }

        $year = $this->year_applied ?? now()->format('Y');
        $sequence = str_pad($this->id, 6, '0', STR_PAD_LEFT);
        return "SC-{$year}-{$sequence}";
    }
}
