<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_admin')->table('users', function (Blueprint $table) {
            if (!Schema::connection('mswdo_admin')->hasColumn('users', 'signature_image')) {
                $table->string('signature_image')->nullable()->after('status');
            }
            if (!Schema::connection('mswdo_admin')->hasColumn('users', 'signature_position')) {
                $table->string('signature_position')->nullable()->after('signature_image');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_admin')->table('users', function (Blueprint $table) {
            $table->dropColumn(['signature_image', 'signature_position']);
        });
    }
};
