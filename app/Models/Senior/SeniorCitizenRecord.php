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
    ];
}
