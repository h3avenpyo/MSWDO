<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_financial')->create('financial_assistance_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->string('applicant_name');
            $table->string('assistance_type');
            $table->decimal('amount_requested', 12, 2)->default(0);
            $table->unsignedBigInteger('created_by');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_financial')->dropIfExists('financial_assistance_applications');
    }
};
