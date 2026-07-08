<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_senior')->table('birthday_payout_history', function (Blueprint $table) {
            $table->foreignId('payout_id')->nullable()->change();
            $table->foreignId('senior_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_senior')->table('birthday_payout_history', function (Blueprint $table) {
            $table->foreignId('payout_id')->nullable(false)->change();
            $table->foreignId('senior_id')->nullable(false)->change();
        });
    }
};
