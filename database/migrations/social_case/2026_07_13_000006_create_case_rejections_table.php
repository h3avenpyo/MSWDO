<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_social_case')->create('case_rejections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('blocking_assistance_id')->nullable();
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->string('officer_name')->nullable();
            $table->text('reason');
            $table->date('last_assistance_date')->nullable();
            $table->string('last_assistance_type')->nullable();
            $table->date('next_eligible_date')->nullable();
            $table->timestamp('rejected_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'blocking_assistance_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_social_case')->dropIfExists('case_rejections');
    }
};
