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
        Schema::create('social_case_studies', function (Blueprint $table) {
            $table->id();
            $table->string('control_no')->unique();
            $table->string('status')->default('Draft');
            $table->date('released_date')->nullable();
            
            // Client information
            $table->json('client');
            
            // Household composition
            $table->json('household');
            
            // Interview information
            $table->json('interview');
            
            // Signatories
            $table->json('signers');
            
            // Purpose and agencies
            $table->string('purpose');
            $table->json('agencies');
            
            // Requirements
            $table->json('requirements');
            
            // Status history
            $table->json('status_history');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_case_studies');
    }
};
