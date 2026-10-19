<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $schema = Schema::connection('wcpu');
        if ($schema->hasTable('users') && !$schema->hasColumn('users', 'username')) {
            $schema->table('users', function (Blueprint $table) {
                $table->string('username', 255)->nullable()->after('email');
            });

            try {
                DB::connection('wcpu')->table('users')
                    ->where(function ($query) {
                        $query->whereNull('username')->orWhere('username', '');
                    })
                    ->update(['username' => DB::raw('email')]);
            } catch (\Throwable $e) {
                // Ignore if update fails
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $schema = Schema::connection('wcpu');
        if ($schema->hasTable('users') && $schema->hasColumn('users', 'username')) {
            $schema->table('users', function (Blueprint $table) {
                $table->dropColumn('username');
            });
        }
    }
};
