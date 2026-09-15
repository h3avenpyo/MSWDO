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
        Schema::create('online_financial_intakes', function (Blueprint $table) {
            $table->id();

            // Status tracking
            $table->string('status', 30)->default('For Review')->index(); // 'For Review', 'Accepted', 'Rejected'
            $table->timestamp('date_submitted')->useCurrent()->index();

            // General Information
            $table->string('control_number', 50)->nullable()->index();
            $table->string('client_type', 20)->default('New');
            $table->boolean('is_client_beneficiary')->default(true);

            // Beneficiary Details
            $table->string('beneficiary_last_name', 100);
            $table->string('beneficiary_first_name', 100);
            $table->string('beneficiary_middle_name', 100)->nullable();
            $table->string('beneficiary_extension_name', 20)->nullable();
            $table->string('beneficiary_street_address', 255);
            $table->string('beneficiary_barangay', 100)->index();
            $table->string('beneficiary_city', 100)->default('Silang');
            $table->string('beneficiary_province', 100)->default('Cavite');
            $table->string('beneficiary_region', 100)->default('Region IV-A');
            $table->string('beneficiary_contact_number', 20);
            $table->date('beneficiary_birthday');
            $table->integer('beneficiary_age')->default(0);
            $table->string('beneficiary_sex', 10);
            $table->string('beneficiary_civil_status', 50);
            $table->string('beneficiary_occupation', 150)->nullable();
            $table->decimal('beneficiary_monthly_salary', 10, 2)->nullable();
            $table->string('beneficiary_category', 100)->nullable();
            $table->string('beneficiary_category_other', 150)->nullable();
            $table->json('beneficiary_categories')->nullable();
            $table->string('beneficiary_relationship', 100)->nullable();

            // Representative Details
            $table->boolean('has_representative')->default(false);
            $table->string('rep_last_name', 100)->nullable();
            $table->string('rep_first_name', 100)->nullable();
            $table->string('rep_middle_name', 100)->nullable();
            $table->string('rep_extension_name', 20)->nullable();
            $table->string('rep_street_address', 255)->nullable();
            $table->string('rep_barangay', 100)->nullable();
            $table->string('rep_city', 100)->nullable();
            $table->string('rep_province', 100)->nullable();
            $table->string('rep_region', 100)->nullable();
            $table->string('rep_contact_number', 20)->nullable();
            $table->date('rep_birthday')->nullable();
            $table->integer('rep_age')->nullable();
            $table->string('rep_sex', 10)->nullable();
            $table->string('rep_civil_status', 50)->nullable();
            $table->string('rep_occupation', 150)->nullable();
            $table->decimal('rep_monthly_salary', 10, 2)->nullable();
            $table->string('rep_relationship', 100)->nullable();

            // Additional Form Data
            $table->json('family_composition')->nullable();
            $table->json('medical_conditions')->nullable();
            $table->string('medical_condition_other', 255)->nullable();
            $table->string('assistance_purpose', 255)->nullable();
            $table->string('service_provided', 255)->nullable();
            $table->string('purpose', 255)->nullable();
            $table->string('purpose_other', 255)->nullable();
            $table->string('submitted_to', 255)->nullable();

            // Review & Transfer Tracking
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('beneficiary_intake_id')->nullable()->index();

            $table->timestamps();

            // Foreign keys
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('beneficiary_intake_id')->references('id')->on('beneficiary_intakes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_financial_intakes');
    }
};
