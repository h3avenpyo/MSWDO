<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the authoritative main-client relationship (main_client_id -> clients.id)
     * and the intake snapshot columns to social_case_studies.
     *
     * The case's MAIN CLIENT is identified by main_client_id. The intake_* columns
     * preserve exactly what was recorded during that specific intake so later edits
     * to the person master data never rewrite a historical case.
     *
     * Idempotent: each column / foreign key is guarded so the migration is safe to
     * re-run against databases where the redesign was already applied manually.
     */
    public function up(): void
    {
        Schema::table('social_case_studies', function (Blueprint $table) {
            if (!Schema::hasColumn('social_case_studies', 'main_client_id')) {
                $table->unsignedBigInteger('main_client_id')->nullable()->after('id');
            }

            $columns = [
                'intake_first_name',
                'intake_middle_name',
                'intake_last_name',
                'intake_suffix',
                'intake_full_name',
                'intake_civil_status',
                'intake_religion',
                'intake_birthplace',
                'intake_education',
                'intake_occupation',
                'intake_income',
                'intake_barangay',
            ];

            foreach ($columns as $column) {
                if (!Schema::hasColumn('social_case_studies', $column)) {
                    $table->string($column, 255)->nullable()->after('date_processed');
                }
            }

            if (!Schema::hasColumn('social_case_studies', 'intake_birthdate')) {
                $table->date('intake_birthdate')->nullable()->after('date_processed');
            }

            if (!Schema::hasColumn('social_case_studies', 'intake_age')) {
                $table->unsignedTinyInteger('intake_age')->nullable()->after('intake_birthdate');
            }

            if (!Schema::hasColumn('social_case_studies', 'intake_gender')) {
                $table->string('intake_gender', 20)->nullable()->after('intake_age');
            }

            if (!Schema::hasColumn('social_case_studies', 'intake_address')) {
                $table->text('intake_address')->nullable()->after('intake_barangay');
            }

            if (!Schema::hasColumn('social_case_studies', 'intake_contact_number')) {
                $table->string('intake_contact_number', 20)->nullable()->after('intake_address');
            }
        });

        // Add the foreign key + index for main_client_id (guarded by index existence).
        Schema::table('social_case_studies', function (Blueprint $table) {
            $hasFk = collect(DB::select('SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?', [
                DB::connection()->getDatabaseName(),
                'social_case_studies',
                'main_client_id',
            ]))->where('CONSTRAINT_NAME', 'social_case_studies_main_client_id_foreign')->isNotEmpty();

            if (!$hasFk && Schema::hasColumn('social_case_studies', 'main_client_id')) {
                $table->foreign('main_client_id')
                    ->references('id')
                    ->on('clients')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('social_case_studies', function (Blueprint $table) {
            if (Schema::hasColumn('social_case_studies', 'main_client_id')) {
                $table->dropForeign(['main_client_id']);
            }

            $columns = [
                'main_client_id',
                'intake_first_name',
                'intake_middle_name',
                'intake_last_name',
                'intake_suffix',
                'intake_full_name',
                'intake_birthdate',
                'intake_age',
                'intake_gender',
                'intake_civil_status',
                'intake_religion',
                'intake_birthplace',
                'intake_education',
                'intake_occupation',
                'intake_income',
                'intake_address',
                'intake_barangay',
                'intake_contact_number',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('social_case_studies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};