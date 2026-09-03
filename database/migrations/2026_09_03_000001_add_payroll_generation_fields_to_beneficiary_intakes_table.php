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
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            if (!Schema::hasColumn('beneficiary_intakes', 'is_payroll_generated')) {
                $table->boolean('is_payroll_generated')->default(false)->after('recommended_amount')->index();
            }
            if (!Schema::hasColumn('beneficiary_intakes', 'payroll_generated_at')) {
                $table->timestamp('payroll_generated_at')->nullable()->after('is_payroll_generated');
            }
            if (!Schema::hasColumn('beneficiary_intakes', 'payroll_date')) {
                $table->date('payroll_date')->nullable()->after('payroll_generated_at')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            $table->dropColumn(['is_payroll_generated', 'payroll_generated_at', 'payroll_date']);
        });
    }
};
