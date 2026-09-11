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
        Schema::create('in_between_benefit_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('benefit_name', 100)->default('In-Between Birthday Cash Gift');
            $table->text('description')->nullable();
            $table->decimal('benefit_amount', 10, 2)->default(1000.00);
            $table->json('milestone_ages')->default('[80, 85, 90, 95, 100]');
            $table->json('eligible_intervals')->default('["81-84", "86-89", "91-94", "96-99"]');
            $table->boolean('is_active')->default(true);
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('ordinance_reference', 100)->nullable();
            $table->text('eligibility_rules')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_between_benefit_configurations');
    }
};
