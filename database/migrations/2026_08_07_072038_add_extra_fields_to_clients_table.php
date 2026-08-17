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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('birthplace', 255)->nullable()->after('birthdate');
            $table->string('religion', 100)->nullable()->after('birthplace');
            $table->string('education', 100)->nullable()->after('religion');
            $table->string('civil_status', 50)->nullable()->after('education');
            $table->string('occupation', 255)->nullable()->after('civil_status');
            $table->string('income', 100)->nullable()->after('occupation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['birthplace', 'religion', 'education', 'civil_status', 'occupation', 'income']);
        });
    }
};
