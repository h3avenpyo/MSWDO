<?php

namespace App\Models\Senior;

use Illuminate\Database\Eloquent\Model;

class SeniorCitizenRecord extends Model
{
    protected $connection = 'mswdo_senior';

    protected $table = 'senior_citizen_records';

    protected $fillable = [
        'record_number',
        'year_applied',
        'control_number',
        'full_name',
        'address',
        'barangay',
        'birth_date',
        'month',
        'sex',
        'age',
        'contact_number',
        'philsys_number',
        'rrn_number',
        'remarks',
        'osca_id',
        'created_by',
        'status',
        'senior_id_number',
        'photo',
        'qr_code',
        'date_issued',
        'last_printed_at',
        'print_count',
        'blood_type',
        'civil_status',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_relationship',
    ];

    /**
     * Generate unique Senior ID Number using the control number.
     */
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

    /**
     * Get URL for the uploaded photo, or a placeholder if none exists.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo && file_exists(public_path($this->photo))) {
            return asset($this->photo);
        }
        
        // Return a public domain default avatar or SVGs
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&background=1A237E&color=fff&size=128';
    }
}
