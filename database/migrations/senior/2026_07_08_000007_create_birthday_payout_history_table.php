<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_senior')->create('birthday_payout_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_id')->nullable()->constrained('birthday_payouts')->onDelete('cascade');
            $table->foreignId('senior_id')->nullable()->constrained('senior_citizen_records')->onDelete('cascade');
            $table->string('action'); // generated, released, cancelled, reset, etc.
            $table->text('details')->nullable(); // additional information
            $table->foreignId('performed_by')->nullable()->constrained('mswdo_admin.users')->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->timestamps();

            // Index for faster queries
            $table->index('payout_id');
            $table->index('senior_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_senior')->dropIfExists('birthday_payout_history');
    }
};
