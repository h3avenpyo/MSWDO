<?php

namespace App\Models\Wcpu;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuarterlyReport extends WcpuBaseModel
{
    protected $fillable = [
        'user_id',
        'barangay_id',
        'type',
        'quarter',
        'year',
        'report_data',
        'pdf_path',
    ];

    protected $casts = [
        'report_data' => 'array',
        'quarter' => 'integer',
        'year' => 'integer',
        'archived_at' => 'datetime',
    ];

    /**
     * The desk officer who submitted this report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The barangay this report is for.
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * Get the quarter label (e.g., "Q1", "Q2").
     */
    public function getQuarterLabelAttribute(): string
    {
        return 'Q' . $this->quarter;
    }

    /**
     * Get the period label (e.g., "Q1 2026").
     */
    public function getPeriodLabelAttribute(): string
    {
        return $this->quarter_label . ' ' . $this->year;
    }

    /**
     * Get start date of the quarter.
     */
    public function getStartDateAttribute(): \Carbon\Carbon
    {
        $month = ($this->quarter - 1) * 3 + 1;
        return \Carbon\Carbon::create($this->year, $month, 1)->startOfDay();
    }

    /**
     * Get end date of the quarter.
     */
    public function getEndDateAttribute(): \Carbon\Carbon
    {
        $month = $this->quarter * 3;
        return \Carbon\Carbon::create($this->year, $month, 1)->endOfMonth()->endOfDay();
    }
}
