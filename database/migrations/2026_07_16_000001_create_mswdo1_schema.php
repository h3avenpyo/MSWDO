<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->enum('role', ['admin', 'social_worker', 'encoder', 'staff'])->default('staff');
            $table->string('phone', 20)->nullable();
            $table->string('position')->nullable();
            $table->string('employee_id', 50)->nullable()->unique();
            $table->string('address', 500)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('signature_image', 500)->nullable();
            $table->enum('signature_position', ['osca_head', 'mswdo_officer'])->nullable();
            $table->timestamps();
            $table->index('role');
            $table->index('status');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration')->index();
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration')->index();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Senior Citizen Records
        Schema::create('senior_citizen_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_number', 50)->nullable()->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('year_applied', 4)->nullable();
            $table->string('control_number', 50)->nullable()->unique();
            $table->string('senior_id_number', 50)->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('barangay')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('sex', ['Male', 'Female'])->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('philsys_number')->nullable();
            $table->string('rrn_number')->nullable();
            $table->string('osca_id')->nullable();
            $table->string('blood_type', 10)->nullable();
            $table->string('civil_status', 20)->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number', 20)->nullable();
            $table->string('emergency_contact_relationship', 50)->nullable();
            $table->string('photo', 500)->nullable();
            $table->string('avatar_image', 500)->nullable();
            $table->text('qr_code')->nullable();
            $table->string('qr_code_image', 500)->nullable();
            $table->date('date_issued')->nullable();
            $table->timestamp('last_printed_at')->nullable();
            $table->integer('print_count')->default(0);
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['active', 'pending', 'archived'])->default('active');
            $table->timestamps();
            $table->index('barangay');
            $table->index('status');
            $table->index('birth_date');
            $table->index('year_applied');
            $table->index(['last_name', 'first_name']);
        });

        // Birthday Payouts
        Schema::create('birthday_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('senior_id')->constrained('senior_citizen_records')->cascadeOnDelete();
            $table->integer('payout_year');
            $table->decimal('amount', 10, 2)->default(500.00);
            $table->enum('status', ['pending', 'released', 'cancelled'])->default('pending');
            $table->foreignId('released_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('released_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['senior_id', 'payout_year'], 'unique_senior_payout_year');
            $table->index('status');
        });

        Schema::create('birthday_payout_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_id')->nullable()->constrained('birthday_payouts')->nullOnDelete();
            $table->foreignId('senior_id')->nullable()->constrained('senior_citizen_records')->cascadeOnDelete();
            $table->string('action', 50);
            $table->text('details')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index('payout_id');
            $table->index('senior_id');
            $table->index('action');
            $table->index('created_at');
        });

        // Clients
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('birthdate');
            $table->enum('gender', ['Male', 'Female']);
            $table->text('address');
            $table->string('barangay')->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->timestamps();
            $table->index(['last_name', 'first_name']);
            $table->index('barangay');
        });

        // Social Case Studies
        Schema::create('social_case_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('case_number', 50)->unique();
            $table->date('date_processed')->nullable();
            $table->string('service_provided')->nullable();
            $table->string('purpose')->nullable();
            $table->string('submitted_to')->nullable();
            $table->foreignId('encoded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 50)->default('Open');
            $table->text('summary')->nullable();
            $table->date('interview_date')->nullable();
            $table->string('workflow_step', 50)->default('requirements_verification');
            $table->boolean('requirements_complete')->default(false);
            $table->boolean('interview_complete')->default(false);
            $table->boolean('evaluation_complete')->default(false);
            $table->boolean('report_generated')->default(false);
            $table->boolean('assistance_released')->default(false);
            $table->decimal('assistance_amount', 10, 2)->nullable();
            $table->date('assistance_date')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->foreignId('released_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('released_to')->nullable();
            $table->timestamps();
            $table->index('client_id');
            $table->index('officer_id');
            $table->index('status');
            $table->index('workflow_step');
            $table->index('encoded_by');
        });

        // Case Interviews (extracted from social_case_studies for 3NF)
        Schema::create('case_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_case_study_id')->unique()->constrained('social_case_studies')->cascadeOnDelete();
            $table->text('interview_reason')->nullable();
            $table->text('interview_situation')->nullable();
            $table->text('interview_household')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->decimal('monthly_expenses', 10, 2)->nullable();
            $table->text('family_illnesses')->nullable();
            $table->string('previous_assistance')->nullable();
            $table->text('interview_notes')->nullable();
            $table->text('social_worker_assessment')->nullable();
            $table->string('recommendation')->nullable();
            $table->decimal('recommended_amount', 10, 2)->nullable();
            $table->text('additional_requirements')->nullable();
            $table->timestamps();
        });

        // Family Members
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_case_study_id')->constrained('social_case_studies')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('relationship', 100);
            $table->unsignedTinyInteger('age')->nullable();
            $table->enum('sex', ['Male', 'Female'])->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('monthly_income', 12, 2)->nullable();
            $table->boolean('is_dependent')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('social_case_study_id');
        });

        // Beneficiary Intakes
        Schema::create('beneficiary_intakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('social_case_study_id')->nullable()->constrained('social_case_studies')->nullOnDelete();
            $table->string('control_number', 50)->unique();
            $table->date('date_processed');
            $table->foreignId('encoder')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_client_beneficiary')->default(true);
            $table->string('beneficiary_last_name')->nullable();
            $table->string('beneficiary_first_name')->nullable();
            $table->string('beneficiary_middle_name')->nullable();
            $table->date('beneficiary_birthday')->nullable();
            $table->integer('beneficiary_age')->nullable();
            $table->string('beneficiary_sex', 20)->nullable();
            $table->string('beneficiary_barangay')->nullable();
            $table->string('beneficiary_relationship', 100)->nullable();
            $table->json('medical_conditions')->nullable();
            $table->string('medical_condition_other')->nullable();
            $table->string('service_provided');
            $table->string('purpose');
            $table->string('purpose_other')->nullable();
            $table->string('submitted_to');
            $table->timestamps();
            $table->index('client_id');
            $table->index('social_case_study_id');
        });

        // Assistance Records
        Schema::create('assistance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('social_case_study_id')->nullable()->constrained('social_case_studies')->nullOnDelete();
            $table->string('assistance_type');
            $table->string('status', 50);
            $table->date('release_date')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index('client_id');
            $table->index('social_case_study_id');
            $table->index(['client_id', 'release_date']);
        });

        // Case Rejections
        Schema::create('case_rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('blocking_assistance_id')->nullable()->constrained('assistance_records')->nullOnDelete();
            $table->foreignId('social_case_study_id')->nullable()->constrained('social_case_studies')->nullOnDelete();
            $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('officer_name')->nullable();
            $table->text('reason');
            $table->date('last_assistance_date')->nullable();
            $table->string('last_assistance_type')->nullable();
            $table->date('next_eligible_date')->nullable();
            $table->timestamp('rejected_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->unique(['client_id', 'blocking_assistance_id']);
            $table->index('blocking_assistance_id');
            $table->index('social_case_study_id');
            $table->index('officer_id');
        });

        // Eligibility Audit Logs
        Schema::create('eligibility_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('client_name');
            $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('officer_name')->nullable();
            $table->string('result', 50);
            $table->text('result_details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedInteger('search_duration_ms')->default(0);
            $table->timestamps();
            $table->index('client_id');
            $table->index('officer_id');
        });

        // Social Case Reports
        Schema::create('social_case_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_case_study_id')->nullable()->constrained('social_case_studies')->nullOnDelete();
            $table->string('case_number', 50)->unique();
            $table->string('title');
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->longText('body')->nullable();
            $table->json('snapshot')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('status', 50)->default('draft');
            $table->timestamps();
            $table->index('social_case_study_id');
            $table->index('generated_by');
            $table->index('created_by');
        });

        // Social Case Report Release Logs
        Schema::create('social_case_report_release_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_case_study_id')->constrained('social_case_studies')->cascadeOnDelete();
            $table->foreignId('social_case_report_id')->nullable()->constrained('social_case_reports')->nullOnDelete();
            $table->foreignId('released_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('released_by_name')->nullable();
            $table->string('released_to');
            $table->timestamp('released_at');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index('social_case_study_id');
            $table->index('social_case_report_id');
            $table->index('released_by');
        });

        // Financial Assistance Applications
        Schema::create('financial_assistance_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('application_number', 50)->unique();
            $table->string('applicant_name');
            $table->string('assistance_type');
            $table->decimal('amount_requested', 12, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('status', 50)->default('pending');
            $table->timestamps();
            $table->index('client_id');
            $table->index('status');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_assistance_applications');
        Schema::dropIfExists('social_case_report_release_logs');
        Schema::dropIfExists('social_case_reports');
        Schema::dropIfExists('eligibility_audit_logs');
        Schema::dropIfExists('case_rejections');
        Schema::dropIfExists('assistance_records');
        Schema::dropIfExists('beneficiary_intakes');
        Schema::dropIfExists('family_members');
        Schema::dropIfExists('case_interviews');
        Schema::dropIfExists('social_case_studies');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('birthday_payout_history');
        Schema::dropIfExists('birthday_payouts');
        Schema::dropIfExists('senior_citizen_records');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
