<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const STEPS = "'client_search','eligibility','beneficiary_intake','requirements_verification','assessment_interview','family_composition','social_case_assessment','report_generation','print_export','release_report','assistance_release','case_closed'";

    public function up(): void
    {
        $connection = DB::connection('mswdo_social_case');
        $schema = Schema::connection('mswdo_social_case');

        if ($schema->hasColumn('social_case_studies', 'workflow_step')) {
            $connection->table('social_case_studies')
                ->where('workflow_step', 'supervisor_review')
                ->update(['workflow_step' => 'report_generation']);
            $connection->statement("ALTER TABLE `social_case_studies` MODIFY `workflow_step` ENUM(".self::STEPS.") NOT NULL DEFAULT 'requirements_verification'");
        }

        $columns = array_filter(['supervisor_notes', 'supervisor_id', 'supervisor_decision', 'supervisor_approved_at'], fn (string $column) => $schema->hasColumn('social_case_studies', $column));
        if ($columns !== []) {
            $schema->table('social_case_studies', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }

    public function down(): void
    {
        // Supervisor review has been intentionally removed and is not restored on rollback.
    }
};
