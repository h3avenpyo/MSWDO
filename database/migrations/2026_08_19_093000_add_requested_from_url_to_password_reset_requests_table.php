<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_reset_requests', function (Blueprint $table) {
            $table->string('requested_from_url', 500)->nullable()->after('token');
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_requests', function (Blueprint $table) {
            $table->dropColumn('requested_from_url');
        });
    }
};