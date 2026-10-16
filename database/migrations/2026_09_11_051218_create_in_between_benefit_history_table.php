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
        Schema::create('in_between_benefit_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('senior_id')->constrained('senior_citizen_records')->cascadeOnDelete();
            $table->string('full_name', 255);
            $table->date('birth_date');
            $table->integer('current_age');
            $table->string('benefit_type', 50)->default('IN_BETWEEN_BIRTHDAY_GIFT');
            $table->string('eligibility_interval', 10); // e.g., "81-84", "86-89"
            $table->integer('birthday_year');
            $table->decimal('amount', 10, 2);
            $table->date('application_date');
            $table->date('payout_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'released', 'rejected', 'cancelled'])->default('pending');
            $table->boolean('is_exported')->default(false);
            $table->string('reference_number', 50)->unique();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->json('audit_trail')->nullable();
            $table->timestamps();
            
            // Unique constraint to prevent duplicate claims for same interval
            $table->unique(['senior_id', 'eligibility_interval'], 'unique_senior_interval');
            
            // Indexes for performance
            $table->index('status');
            $table->index('eligibility_interval');
            $table->index('birthday_year');
            $table->index('payout_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_between_benefit_history');
    }
};
