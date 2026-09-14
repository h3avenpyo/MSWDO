<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a suffix column (Jr., Sr., III, IV, ...) to the clients (person master) table.
     * Idempotent: safely skips when the column already exists.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'suffix')) {
                $table->string('suffix', 50)->nullable()->after('last_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'suffix')) {
                $table->dropColumn('suffix');
            }
        });
    }
};