<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_admin')->table('users', function (Blueprint $table) {
            if (!Schema::connection('mswdo_admin')->hasColumn('users', 'role')) {
                $table->string('role')->nullable()->after('email');
            }
            if (!Schema::connection('mswdo_admin')->hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('role');
            }
            if (!Schema::connection('mswdo_admin')->hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('phone');
            }
            if (!Schema::connection('mswdo_admin')->hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('position');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_admin')->table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'position', 'status']);
        });
    }
};
