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
            if (!Schema::hasColumn('beneficiary_intakes', 'claim_status')) {
                $table->string('claim_status', 20)->default('Unclaimed')->after('payroll_record_id')->index();
            }
            if (!Schema::hasColumn('beneficiary_intakes', 'claimed_at')) {
                $table->timestamp('claimed_at')->nullable()->after('claim_status');
            }
            if (!Schema::hasColumn('beneficiary_intakes', 'claimed_by')) {
                $table->string('claimed_by')->nullable()->after('claimed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            $table->dropColumn(['claim_status', 'claimed_at', 'claimed_by']);
        });
    }
};
