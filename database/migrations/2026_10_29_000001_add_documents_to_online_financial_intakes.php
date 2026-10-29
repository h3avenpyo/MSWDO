<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('online_financial_intakes', function (Blueprint $table) {
            $table->json('documents')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('online_financial_intakes', function (Blueprint $table) {
            $table->dropColumn('documents');
        });
    }
};
