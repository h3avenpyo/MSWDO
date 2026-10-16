<?php

namespace App\Models\Wcpu;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CaseType extends WcpuBaseModel
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'republic_act'
    ];

    /**
     * Get the cases associated with this type.
     */
    public function cases(): HasMany
    {
        return $this->hasMany(CaseModel::class, 'abuse_type', 'republic_act');
    }

    /**
     * Scope a query to only include active case types.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by the order column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Get the CSS class for this case type.
     *
     * @return string
     */
    public function getCssClassAttribute(): string
    {
        return 'case-type-' . Str::slug($this->name);
    }

    /**
     * Get the icon HTML for this case type.
     *
     * @return string
     */
    public function getIconHtmlAttribute(): string
    {
        if (isset($this->icon) && strpos($this->icon, 'fa-') === 0) {
            return '<i class="fas ' . e($this->icon) . '"></i>';
        }

        return (isset($this->icon) && $this->icon) ? $this->icon : '<i class="fas fa-folder"></i>';
    }
}
