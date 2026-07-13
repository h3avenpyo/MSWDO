<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const STEPS = "'client_search','eligibility','beneficiary_intake','requirements_verification','assessment_interview','family_composition','social_case_assessment','supervisor_review','report_generation','print_export','release_report','assistance_release','case_closed'";

    public function up(): void
    {
        if (Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'workflow_step')) {
            DB::connection('mswdo_social_case')->statement(
                'ALTER TABLE `social_case_studies` MODIFY `workflow_step` ENUM('.self::STEPS.") NOT NULL DEFAULT 'requirements_verification'"
            );
        }
    }

    public function down(): void
    {
        if (Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'workflow_step')) {
            DB::connection('mswdo_social_case')->statement(
                "ALTER TABLE `social_case_studies` MODIFY `workflow_step` ENUM('requirements_verification','assessment_interview','evaluation_approval','report_generation','assistance_release','case_closed') NOT NULL DEFAULT 'requirements_verification'"
            );
        }
    }
};
