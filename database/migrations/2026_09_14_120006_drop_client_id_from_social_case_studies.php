<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Final step of the main-client migration: remove the legacy client_id column
     * from social_case_studies now that ALL application code writes and reads the
     * authoritative main_client_id column. Guarded so it is safe to run whenever.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('social_case_studies', 'client_id')) {
            return;
        }

        $constraints = DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'social_case_studies'
               AND COLUMN_NAME = 'client_id' AND REFERENCED_TABLE_NAME IS NOT NULL",
            [DB::connection()->getDatabaseName()]
        );

        foreach ($constraints as $constraint) {
            Schema::table('social_case_studies', function (Blueprint $table) use ($constraint) {
                $table->dropForeign($constraint->CONSTRAINT_NAME);
            });
        }

        Schema::table('social_case_studies', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('social_case_studies', 'client_id')) {
            return;
        }

        Schema::table('social_case_studies', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->after('id');
            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
        });
    }
};