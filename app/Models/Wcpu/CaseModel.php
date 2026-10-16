<?php

namespace App\Models\Wcpu;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CaseModel extends WcpuBaseModel
{
    use SoftDeletes, LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cases';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Get the options for the activity log.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontSubmitEmptyLogs()
            ->useLogName('case'); // disables auto logging, only manual logs will be created
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'case_designation',
        'case_number',
        'incident_date',
        'incident_time',
        'date_reported',
        'abuse_type',
        'perpetrator_name',
        'relationship_to_victim',
        'case_details',
        'status',
        'priority',
        'assigned_to',
        'victim_name',
        'victim_age',
        'victim_gender',
        'victim_contact',
        'victim_address',
        'guardian',
        'guardian_contact',
        'guardian_address',
        'abuser_name',
        'abuser_address',
        'abuser_age',
        'abuse_relationship',
        'incident_location',
        'incident_details',
        'witness_name',
        'witness_contact',
        'created_by',
        'updated_by',
        'closed_at',
        'guardian_abuser_name',
        'guardian_abuser_contact',
        'guardian_abuser_address',
        'relationship_to_abuser',
        'bcpc_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'incident_date' => 'date',
        'date_reported' => 'date',
        'victim_age' => 'integer',
        'abuser_age' => 'integer',
        'incident_time' => 'datetime',
        'closed_at' => 'datetime',
        'documents' => 'array',
        'bcpc_data' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_name',
        'type_name',
        'assigned_to_name',
        'created_by_name',
        'is_closed',
    ];

    /**
     * The case type that this case belongs to.
     */
    public function caseType(): BelongsTo
    {
        return $this->belongsTo(CaseType::class, 'abuse_type', 'republic_act');
    }

    /**
     * The status of this case.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(CaseStatus::class, 'status_id');
    }

    /**
     * The user assigned to this case.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * The user who created this case.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The barangay this case belongs to.
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'case_designation');
    }

    /**
     * The notes associated with this case.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(CaseNote::class, 'case_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(CaseReferral::class, 'case_id')->withTrashed();
    }

    public function hasBeenReferred(): bool
    {
        if (preg_match('/referred/i', trim((string) $this->status))) {
            return true;
        }

        if (array_key_exists('has_referral', $this->attributes)) {
            return (bool) $this->attributes['has_referral'];
        }

        return $this->referrals()->exists();
    }

    /**
     * Get the status name attribute.
     *
     * @return string|null
     */
    public function getStatusNameAttribute(): ?string
    {
        return ($this->status && is_object($this->status) && isset($this->status->name)) ? $this->status->name : null;
    }

    /**
     * Get the type name attribute.
     *
     * @return string|null
     */
    public function getTypeNameAttribute(): ?string
    {
        return ($this->caseType && is_object($this->caseType) && isset($this->caseType->name)) ? $this->caseType->name : null;
    }

    /**
     * Get the assigned to name attribute.
     *
     * @return string|null
     */
    public function getAssignedToNameAttribute(): ?string
    {
        return ($this->assignedTo && is_object($this->assignedTo) && isset($this->assignedTo->full_name)) ? $this->assignedTo->full_name : null;
    }

    /**
     * Get the created by name attribute.
     *
     * @return string|null
     */
    public function getCreatedByNameAttribute(): ?string
    {
        return ($this->createdBy && is_object($this->createdBy) && isset($this->createdBy->full_name)) ? $this->createdBy->full_name : null;
    }

    /**
     * Check if the case is closed.
     *
     * @return bool
     */
    public function getIsClosedAttribute(): bool
    {
        return $this->closed_at !== null;
    }

    /**
     * Scope a query to only include open cases.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->whereNull('closed_at');
    }

    /**
     * Scope a query to only include closed cases.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClosed($query)
    {
        return $query->whereNotNull('closed_at');
    }

    /**
     * Scope a query to only include cases with the given status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->whereHas('status', function ($q) use ($status) {
            $q->where('slug', $status);
        });
    }

    /**
     * Get the case number with a prefix.
     *
     * @return string
     */
    public function getFormattedCaseNumberAttribute(): string
    {
        return 'CASE-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the number of days since the case was created.
     *
     * @return int
     */
    public function getDaysOpenAttribute(): int
    {
        return $this->created_at ? $this->created_at->diffInDays(now()) : 0;
    }

    /**
     * Close the case.
     *
     * @param string $resolution
     * @param string $status
     * @return bool
     */
    public function close(string $resolution, string $status = 'resolved'): bool
    {
        $this->resolution = $resolution;
        $this->resolution_status = $status;
        $this->resolution_date = now();
        $this->closed_at = now();
        
        return $this->save();
    }

    /**
     * Reopen the case.
     *
     * @return bool
     */
    public function reopen(): bool
    {
        $this->closed_at = null;
        $this->resolution = null;
        $this->resolution_status = null;
        $this->resolution_date = null;
        
        return $this->save();
    }
}
