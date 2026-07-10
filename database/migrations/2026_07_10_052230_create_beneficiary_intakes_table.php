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
        Schema::create('beneficiary_intakes', function (Blueprint $table) {
            $table->id();
            
            // Processing Information
            $table->string('control_number')->unique();
            $table->date('date_processed');
            $table->string('encoder');
            
            // Client Information
            $table->string('client_last_name');
            $table->string('client_first_name');
            $table->string('client_middle_name')->nullable();
            $table->date('client_birthday');
            $table->integer('client_age');
            $table->string('client_sex');
            $table->string('client_civil_status');
            $table->text('client_address');
            $table->string('client_barangay');
            $table->string('client_contact_number');
            $table->string('client_occupation')->nullable();
            $table->decimal('client_monthly_income', 10, 2)->nullable();
            
            // Beneficiary Information
            $table->boolean('is_client_beneficiary')->default(true);
            $table->string('beneficiary_last_name')->nullable();
            $table->string('beneficiary_first_name')->nullable();
            $table->string('beneficiary_middle_name')->nullable();
            $table->date('beneficiary_birthday')->nullable();
            $table->integer('beneficiary_age')->nullable();
            $table->string('beneficiary_sex')->nullable();
            $table->string('beneficiary_barangay')->nullable();
            $table->string('beneficiary_relationship')->nullable();
            
            // Medical Condition
            $table->json('medical_conditions')->nullable();
            $table->string('medical_condition_other')->nullable();
            
            // Service Provided
            $table->string('service_provided');
            
            // Purpose
            $table->string('purpose');
            $table->string('purpose_other')->nullable();
            
            // Submitted To
            $table->string('submitted_to');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_intakes');
    }
};
