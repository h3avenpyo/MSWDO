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
            if (!Schema::hasColumn('beneficiary_intakes', 'is_historical')) {
                $table->boolean('is_historical')->default(false)->after('submitted_to')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            if (Schema::hasColumn('beneficiary_intakes', 'is_historical')) {
                $table->dropColumn('is_historical');
            }
        });
    }
};
