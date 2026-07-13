<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_social_case')->create('social_case_studies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->string('case_number')->unique();
            $table->date('date_processed');
            $table->string('client_last_name');
            $table->string('client_first_name');
            $table->string('client_middle_name')->nullable();
            $table->integer('client_age');
            $table->enum('client_sex', ['Male', 'Female']);
            $table->string('client_barangay');
            $table->string('beneficiary_last_name')->nullable();
            $table->string('beneficiary_first_name')->nullable();
            $table->string('beneficiary_middle_name')->nullable();
            $table->integer('beneficiary_age')->nullable();
            $table->date('beneficiary_birthday')->nullable();
            $table->enum('beneficiary_sex', ['Male', 'Female'])->nullable();
            $table->string('beneficiary_barangay')->nullable();
            $table->json('medical_conditions')->nullable();
            $table->string('service_provided');
            $table->string('purpose');
            $table->string('submitted_to');
            $table->string('encoded_by');
            $table->enum('status', ['Open', 'In Progress', 'Closed'])->default('Open');
            $table->text('summary')->nullable();
            $table->date('interview_date')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('officer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_social_case')->dropIfExists('social_case_studies');
    }
};
