<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_social_case')->table('social_case_studies', function (Blueprint $table) {
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'additional_requirements')) {
                $table->text('additional_requirements')->nullable()->after('medical_conditions');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'interview_reason')) {
                $table->text('interview_reason')->nullable()->after('interview_date');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'interview_situation')) {
                $table->text('interview_situation')->nullable()->after('interview_reason');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'interview_household')) {
                $table->text('interview_household')->nullable()->after('interview_situation');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'monthly_income')) {
                $table->decimal('monthly_income', 10, 2)->nullable()->after('interview_household');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'monthly_expenses')) {
                $table->decimal('monthly_expenses', 10, 2)->nullable()->after('monthly_income');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'family_illnesses')) {
                $table->text('family_illnesses')->nullable()->after('monthly_expenses');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'previous_assistance')) {
                $table->string('previous_assistance')->nullable()->after('family_illnesses');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'interview_notes')) {
                $table->text('interview_notes')->nullable()->after('previous_assistance');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'social_worker_assessment')) {
                $table->text('social_worker_assessment')->nullable()->after('interview_notes');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'recommendation')) {
                $table->string('recommendation')->nullable()->after('social_worker_assessment');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'recommended_amount')) {
                $table->decimal('recommended_amount', 10, 2)->nullable()->after('recommendation');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'supervisor_notes')) {
                $table->text('supervisor_notes')->nullable()->after('recommended_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_social_case')->table('social_case_studies', function (Blueprint $table) {
            $table->dropColumn([
                'additional_requirements',
                'interview_reason',
                'interview_situation',
                'interview_household',
                'monthly_income',
                'monthly_expenses',
                'family_illnesses',
                'previous_assistance',
                'interview_notes',
                'social_worker_assessment',
                'recommendation',
                'recommended_amount',
                'supervisor_notes',
            ]);
        });
    }
};
