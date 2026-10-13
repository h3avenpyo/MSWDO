<?php

namespace App\Models\Wcpu;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BcpcCaseMonitoring extends WcpuBaseModel
{
    protected $table = 'bcpc_case_monitoring';

    protected $fillable = [
        'barangay_id',
        'quarter',
        'year',
        'data',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'archived_at' => 'datetime',
        'data' => 'array',
        'quarter' => 'integer',
        'year' => 'integer',
    ];

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by')->withTrashed();
    }

    public function scopeVisibleToAdmin($query, int $adminId)
    {
        return $query->where(fn ($q) => $q->where('created_by', $adminId)
            ->orWhere(fn ($sent) => $sent->whereNotNull('submitted_at')
                ->whereNotNull('submitted_by')));
    }

    public static function quarterTotals(int $barangayId, int $quarter, int $year, ?int $adminId = null): array
    {
        $result = [];
        $organizations = collect();
        $records = static::where('barangay_id', $barangayId)->where('quarter', $quarter)
            ->where('year', $year)->whereNull('archived_at')
            ->when($adminId !== null, fn ($query) => $query->visibleToAdmin($adminId))->get();
        foreach ($records as $record) {
            foreach ($record->data ?? [] as $table => $rows) {
                if ($table === 'bcpc_organizations') {
                    $organizations = $organizations->concat($rows);
                    continue;
                }
                foreach ($rows as $row => $years) {
                    if ($row === 'total' || !is_array($years)) continue;
                    foreach ($years as $yearKey => $values) {
                        if (!is_array($values)) continue;
                        foreach (['male', 'female'] as $sex) {
                            $result[$table][$row][$yearKey][$sex] = ($result[$table][$row][$yearKey][$sex] ?? 0) + (int) ($values[$sex] ?? 0);
                        }
                    }
                }
            }
        }
        foreach ($result as $table => &$rows) {
            $totals = [];
            foreach ($rows as &$years) {
                foreach ($years as $yearKey => &$values) {
                    $values['total'] = $values['male'] + $values['female'];
                    foreach (['male', 'female', 'total'] as $sex) {
                        $totals[$yearKey][$sex] = ($totals[$yearKey][$sex] ?? 0) + $values[$sex];
                    }
                }
                unset($values);
            }
            unset($years);
            $rows['total'] = $totals;
        }
        unset($rows);
        $result['bcpc_organizations'] = $organizations->filter(fn ($row) => is_array($row) && count(array_filter($row)) > 0)
            ->unique(fn ($row) => json_encode($row))->values()->all();
        return $result;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
