<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_senior')->table('birthday_payout_history', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['payout_id']);
            
            // Add new foreign key without cascade delete
            $table->foreign('payout_id')
                  ->nullable()
                  ->references('id')
                  ->on('birthday_payouts')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_senior')->table('birthday_payout_history', function (Blueprint $table) {
            // Drop the modified foreign key
            $table->dropForeign(['payout_id']);
            
            // Restore original foreign key with cascade delete
            $table->foreign('payout_id')
                  ->nullable()
                  ->references('id')
                  ->on('birthday_payouts')
                  ->onDelete('cascade');
        });
    }
};
