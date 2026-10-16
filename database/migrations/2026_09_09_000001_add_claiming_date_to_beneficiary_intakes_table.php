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
            if (!Schema::hasColumn('beneficiary_intakes', 'claiming_date')) {
                $table->date('claiming_date')->nullable()->after('payroll_date')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            if (Schema::hasColumn('beneficiary_intakes', 'claiming_date')) {
                $table->dropColumn('claiming_date');
            }
        });
    }
};
