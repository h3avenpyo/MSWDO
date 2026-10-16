<?php

namespace App\Models\Wcpu;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Barangay extends WcpuBaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'barangays';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get all cases for this barangay
     *
     * @return HasMany
     */
    public function cases(): HasMany
    {
        return $this->hasMany(CaseModel::class, 'case_designation');
    }
    
    /**
     * Get all barangays ordered by name.
     *
     * @param  bool  $withTrashed
     * @return \Illuminate\Support\Collection
     */
    public static function getForDropdown($withTrashed = false)
    {
        $query = self::query();
        
        if ($withTrashed && method_exists($query, 'withTrashed')) {
            $query->withTrashed();
        }
        
        return $query->orderBy('name')->pluck('name', 'id');
    }
}
