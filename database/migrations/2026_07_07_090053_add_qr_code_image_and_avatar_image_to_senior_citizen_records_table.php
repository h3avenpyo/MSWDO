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
        Schema::connection('mswdo_senior')->table('senior_citizen_records', function (Blueprint $table) {
            $table->string('qr_code_image')->nullable()->after('qr_code');
            $table->string('avatar_image')->nullable()->after('qr_code_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('senior_citizen_records', function (Blueprint $table) {
            //
        });
    }
};
