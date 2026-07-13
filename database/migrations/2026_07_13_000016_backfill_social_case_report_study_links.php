<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::connection('mswdo_social_case')->statement(
            'UPDATE social_case_reports AS reports '
            .'INNER JOIN social_case_studies AS studies ON studies.case_number = reports.case_number '
            .'SET reports.social_case_study_id = studies.id '
            .'WHERE reports.social_case_study_id IS NULL'
        );
    }

    public function down(): void
    {
        // Existing report links are retained on rollback.
    }
};
