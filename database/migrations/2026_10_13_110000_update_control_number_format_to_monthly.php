<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Safely updates existing Control Numbers from MSWDO-YYYY-XXXXX to MSWDO-YYYY-MM-XXXX
     * while preserving all links to Step 2 records, payroll records, and online intake records.
     */
    public function up(): void
    {
        $intakes = DB::table('beneficiary_intakes')
            ->select('id', 'control_number', 'date_processed', 'created_at')
            ->whereNotNull('control_number')
            ->orderBy('id', 'asc')
            ->get();

        if ($intakes->isEmpty()) {
            return;
        }

        // Group by Year and Month
        $grouped = [];
        foreach ($intakes as $intake) {
            $rawDate = $intake->date_processed ?: $intake->created_at;
            $date = $rawDate ? Carbon::parse($rawDate) : Carbon::now();
            $key = $date->format('Y-m');

            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }
            $grouped[$key][] = [
                'id' => $intake->id,
                'old_control_number' => $intake->control_number,
                'date' => $date,
            ];
        }

        $mapping = []; // old => new

        foreach ($grouped as $yearMonth => $records) {
            [$year, $month] = explode('-', $yearMonth);

            // Sort chronologically within the month
            usort($records, function ($a, $b) {
                if ($a['date']->eq($b['date'])) {
                    return $a['id'] <=> $b['id'];
                }
                return $a['date'] <=> $b['date'];
            });

            $seq = 1;
            foreach ($records as $item) {
                $newControlNumber = sprintf('MSWDO-%04d-%02d-%04d', (int) $year, (int) $month, $seq);
                $mapping[$item['id']] = [
                    'old' => $item['old_control_number'],
                    'new' => $newControlNumber,
                ];
                $seq++;
            }
        }

        // Save backup mapping to storage/app for safe disaster recovery
        try {
            $backupPath = storage_path('app/control_number_migration_backup.json');
            file_put_contents($backupPath, json_encode($mapping, JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            // Log or ignore if filesystem permission fails
        }

        // 1. Update beneficiary_intakes
        foreach ($mapping as $intakeId => $nums) {
            DB::table('beneficiary_intakes')
                ->where('id', $intakeId)
                ->update(['control_number' => $nums['new']]);
        }

        // 2. Update online_financial_intakes
        foreach ($mapping as $intakeId => $nums) {
            DB::table('online_financial_intakes')
                ->where('beneficiary_intake_id', $intakeId)
                ->orWhere('control_number', $nums['old'])
                ->update(['control_number' => $nums['new']]);
        }

        // 3. Update financial_payroll_records JSON payroll_data
        $payrollRecords = DB::table('financial_payroll_records')->get();
        foreach ($payrollRecords as $pr) {
            if (empty($pr->payroll_data)) {
                continue;
            }
            $data = json_decode($pr->payroll_data, true);
            if (!is_array($data)) {
                continue;
            }

            $updated = false;
            foreach ($data as &$item) {
                $itemIntakeId = $item['intake_id'] ?? null;
                $itemOldCtrl = $item['control_number'] ?? null;

                if ($itemIntakeId && isset($mapping[$itemIntakeId])) {
                    $item['control_number'] = $mapping[$itemIntakeId]['new'];
                    $updated = true;
                } elseif ($itemOldCtrl) {
                    foreach ($mapping as $map) {
                        if ($map['old'] === $itemOldCtrl) {
                            $item['control_number'] = $map['new'];
                            $updated = true;
                            break;
                        }
                    }
                }
            }
            unset($item);

            if ($updated) {
                DB::table('financial_payroll_records')
                    ->where('id', $pr->id)
                    ->update(['payroll_data' => json_encode($data)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $backupPath = storage_path('app/control_number_migration_backup.json');
        if (!file_exists($backupPath)) {
            return;
        }

        $mapping = json_decode(file_get_contents($backupPath), true);
        if (!is_array($mapping)) {
            return;
        }

        foreach ($mapping as $intakeId => $nums) {
            DB::table('beneficiary_intakes')
                ->where('id', $intakeId)
                ->update(['control_number' => $nums['old']]);

            DB::table('online_financial_intakes')
                ->where('beneficiary_intake_id', $intakeId)
                ->orWhere('control_number', $nums['new'])
                ->update(['control_number' => $nums['old']]);
        }

        // Revert payroll_data
        $payrollRecords = DB::table('financial_payroll_records')->get();
        foreach ($payrollRecords as $pr) {
            if (empty($pr->payroll_data)) {
                continue;
            }
            $data = json_decode($pr->payroll_data, true);
            if (!is_array($data)) {
                continue;
            }

            $updated = false;
            foreach ($data as &$item) {
                $itemIntakeId = $item['intake_id'] ?? null;
                $itemNewCtrl = $item['control_number'] ?? null;

                if ($itemIntakeId && isset($mapping[$itemIntakeId])) {
                    $item['control_number'] = $mapping[$itemIntakeId]['old'];
                    $updated = true;
                } elseif ($itemNewCtrl) {
                    foreach ($mapping as $map) {
                        if ($map['new'] === $itemNewCtrl) {
                            $item['control_number'] = $map['old'];
                            $updated = true;
                            break;
                        }
                    }
                }
            }
            unset($item);

            if ($updated) {
                DB::table('financial_payroll_records')
                    ->where('id', $pr->id)
                    ->update(['payroll_data' => json_encode($data)]);
            }
        }
    }
};
