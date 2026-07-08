<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_senior')->create('birthday_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('senior_id')->constrained('senior_citizen_records')->onDelete('cascade');
            $table->string('birth_month'); // January, February, etc.
            $table->integer('payout_year');
            $table->decimal('amount', 10, 2)->default(500.00);
            $table->string('status')->default('pending'); // pending, released, cancelled
            $table->foreignId('released_by')->nullable()->constrained('mswdo_admin.users')->onDelete('set null');
            $table->timestamp('released_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Prevent duplicate payouts for same senior in same year
            $table->unique(['senior_id', 'payout_year'], 'unique_senior_payout_year');
            
            // Index for faster queries
            $table->index(['birth_month', 'payout_year']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_senior')->dropIfExists('birthday_payouts');
    }
};
