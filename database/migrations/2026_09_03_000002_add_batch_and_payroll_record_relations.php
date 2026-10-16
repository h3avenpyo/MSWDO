<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add batch_number to financial_payroll_records table
        Schema::table('financial_payroll_records', function (Blueprint $table) {
            if (!Schema::hasColumn('financial_payroll_records', 'batch_number')) {
                $table->integer('batch_number')->default(1)->after('payroll_date')->index();
            }
        });

        // 2. Add payroll_record_id foreign key reference to beneficiary_intakes table
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            if (!Schema::hasColumn('beneficiary_intakes', 'payroll_record_id')) {
                $table->foreignId('payroll_record_id')->nullable()->after('payroll_date')
                    ->constrained('financial_payroll_records')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            if (Schema::hasColumn('beneficiary_intakes', 'payroll_record_id')) {
                $table->dropConstrainedForeignId('payroll_record_id');
            }
        });

        Schema::table('financial_payroll_records', function (Blueprint $table) {
            if (Schema::hasColumn('financial_payroll_records', 'batch_number')) {
                $table->dropColumn('batch_number');
            }
        });
    }
};
