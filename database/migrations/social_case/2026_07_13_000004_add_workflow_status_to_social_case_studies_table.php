<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_social_case')->table('social_case_studies', function (Blueprint $table) {
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'workflow_step')) {
                $table->enum('workflow_step', ['requirements_verification', 'assessment_interview', 'evaluation_approval', 'report_generation', 'assistance_release', 'case_closed'])->default('requirements_verification')->after('status');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'requirements_complete')) {
                $table->boolean('requirements_complete')->default(false)->after('workflow_step');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'interview_complete')) {
                $table->boolean('interview_complete')->default(false)->after('requirements_complete');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'evaluation_complete')) {
                $table->boolean('evaluation_complete')->default(false)->after('interview_complete');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'report_generated')) {
                $table->boolean('report_generated')->default(false)->after('evaluation_complete');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'assistance_released')) {
                $table->boolean('assistance_released')->default(false)->after('report_generated');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'assistance_amount')) {
                $table->decimal('assistance_amount', 10, 2)->nullable()->after('assistance_released');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'assistance_date')) {
                $table->date('assistance_date')->nullable()->after('assistance_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_social_case')->table('social_case_studies', function (Blueprint $table) {
            $table->dropColumn([
                'workflow_step',
                'requirements_complete',
                'interview_complete',
                'evaluation_complete',
                'report_generated',
                'assistance_released',
                'assistance_amount',
                'assistance_date',
            ]);
        });
    }
};
