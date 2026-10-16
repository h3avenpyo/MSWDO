<?php

namespace App\Models\Financial;

use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialPayrollRecord extends Model
{
    protected $table = 'financial_payroll_records';

    protected $fillable = [
        'payroll_number',
        'payroll_date',
        'batch_number',
        'generated_by_id',
        'generated_by_name',
        'disbursing_officer',
        'certified_by',
        'approved_by',
        'total_beneficiaries',
        'total_amount',
        'status',
        'notes',
        'payroll_data',
    ];

    protected $casts = [
        'payroll_date' => 'date',
        'batch_number' => 'integer',
        'total_beneficiaries' => 'integer',
        'total_amount' => 'decimal:2',
        'payroll_data' => 'array',
    ];

    public function generatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by_id');
    }

    public function beneficiaryIntakes(): HasMany
    {
        return $this->hasMany(BeneficiaryIntake::class, 'payroll_record_id');
    }

    public function getRecordTitleAttribute(): string
    {
        $dateStr = $this->payroll_date ? $this->payroll_date->format('M d, Y') : 'N/A';
        $timeStr = $this->created_at ? $this->created_at->format('h:i A') : '';
        return $timeStr ? "{$dateStr} ({$timeStr})" : $dateStr;
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return '₱' . number_format((float) ($this->total_amount ?? 0), 2);
    }

    public function getFormattedPayrollDateAttribute(): string
    {
        return $this->payroll_date ? $this->payroll_date->format('F d, Y') : 'N/A';
    }

    public function getFormattedGeneratedAtAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('M d, Y h:i A') : 'N/A';
    }

    public function getFormattedGeneratedTimeAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('h:i A') : 'N/A';
    }
}
