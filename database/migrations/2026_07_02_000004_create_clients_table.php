<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_social_case')->create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('birthdate');
            $table->string('gender');
            $table->string('address');
            $table->string('contact_number')->nullable();
            $table->timestamps();
        });

        Schema::connection('mswdo_social_case')->create('assistance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->string('assistance_type');
            $table->string('status');
            $table->date('release_date')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::connection('mswdo_social_case')->create('social_case_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('officer_id')->nullable();
            $table->string('case_number')->unique();
            $table->string('status')->default('Open');
            $table->text('summary')->nullable();
            $table->date('interview_date')->nullable();
            $table->timestamps();
        });

        Schema::connection('mswdo_social_case')->create('eligibility_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->string('client_name');
            $table->foreignId('officer_id')->nullable();
            $table->string('officer_name')->nullable();
            $table->string('result');
            $table->text('result_details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedInteger('search_duration_ms')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_social_case')->dropIfExists('eligibility_audit_logs');
        Schema::connection('mswdo_social_case')->dropIfExists('social_case_studies');
        Schema::connection('mswdo_social_case')->dropIfExists('assistance_records');
        Schema::connection('mswdo_social_case')->dropIfExists('clients');
    }
};
