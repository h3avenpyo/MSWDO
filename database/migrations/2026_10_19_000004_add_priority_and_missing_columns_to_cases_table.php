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
        $schema = Schema::connection('wcpu');
        if ($schema->hasTable('cases')) {
            $schema->table('cases', function (Blueprint $table) use ($schema) {
                if (!$schema->hasColumn('cases', 'priority')) {
                    $table->string('priority', 50)->nullable()->default('Non-urgent')->after('status');
                }
                if (!$schema->hasColumn('cases', 'abuser_address')) {
                    $table->text('abuser_address')->nullable()->after('abuser_name');
                }
                if (!$schema->hasColumn('cases', 'bcpc_data')) {
                    $table->json('bcpc_data')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $schema = Schema::connection('wcpu');
        if ($schema->hasTable('cases')) {
            $schema->table('cases', function (Blueprint $table) use ($schema) {
                $columnsToDrop = [];
                if ($schema->hasColumn('cases', 'priority')) {
                    $columnsToDrop[] = 'priority';
                }
                if ($schema->hasColumn('cases', 'abuser_address')) {
                    $columnsToDrop[] = 'abuser_address';
                }
                if ($schema->hasColumn('cases', 'bcpc_data')) {
                    $columnsToDrop[] = 'bcpc_data';
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
