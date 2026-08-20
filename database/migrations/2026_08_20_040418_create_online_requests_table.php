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
        Schema::create('online_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_for'); // myself, child, parent, family, assisting
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
            $table->string('barangay');
            $table->string('contact_number');
            $table->string('email');
            $table->text('address')->nullable();
            $table->string('service_type'); // financial_assistance, social_case_study, etc.
            $table->string('assistance_type'); // medical, educational, etc.
            $table->text('situation');
            $table->string('status')->default('pending'); // pending, approved, rejected, in_progress
            $table->text('notes')->nullable();
            $table->string('processed_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_requests');
    }
};
