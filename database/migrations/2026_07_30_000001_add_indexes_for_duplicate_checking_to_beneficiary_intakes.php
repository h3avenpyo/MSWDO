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
            $table->index(['beneficiary_last_name', 'beneficiary_first_name'], 'idx_ben_names');
            $table->index(['beneficiary_birthday'], 'idx_ben_dob');
            $table->index(['rep_last_name', 'rep_first_name'], 'idx_rep_names');
            $table->index(['rep_birthday'], 'idx_rep_dob');
            $table->index(['date_processed'], 'idx_date_processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            $table->dropIndex('idx_ben_names');
            $table->dropIndex('idx_ben_dob');
            $table->dropIndex('idx_rep_names');
            $table->dropIndex('idx_rep_dob');
            $table->dropIndex('idx_date_processed');
        });
    }
};
