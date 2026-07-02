<?php

namespace App\Models\Senior;

use Illuminate\Database\Eloquent\Model;

class SeniorCitizenRecord extends Model
{
    protected $connection = 'mswdo_senior';

    protected $table = 'senior_citizen_records';

    protected $fillable = [
        'record_number',
        'full_name',
        'birth_date',
        'osca_id',
        'created_by',
        'status',
    ];
}
