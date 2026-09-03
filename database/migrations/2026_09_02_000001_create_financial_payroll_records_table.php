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
        Schema::create('financial_payroll_records', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_number', 60)->unique();
            $table->date('payroll_date')->index();
            $table->foreignId('generated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('generated_by_name')->nullable();
            $table->string('disbursing_officer')->nullable();
            $table->string('certified_by')->default('MSWDO HEAD / OFFICER-IN-CHARGE');
            $table->string('approved_by')->default('HON. MUNICIPAL MAYOR');
            $table->integer('total_beneficiaries')->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('status', 50)->default('Completed');
            $table->text('notes')->nullable();
            $table->longText('payroll_data')->nullable(); // JSON Snapshot containing all intakes, names, amounts, etc.
            $table->timestamps();

            $table->index(['payroll_date', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_payroll_records');
    }
};
