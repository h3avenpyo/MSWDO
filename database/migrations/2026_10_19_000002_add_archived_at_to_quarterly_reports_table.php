<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('wcpu');

        if ($schema->hasTable('quarterly_reports') && ! $schema->hasColumn('quarterly_reports', 'archived_at')) {
            $schema->table('quarterly_reports', function (Blueprint $table): void {
                $table->timestamp('archived_at')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('wcpu');

        if ($schema->hasTable('quarterly_reports') && $schema->hasColumn('quarterly_reports', 'archived_at')) {
            $schema->table('quarterly_reports', function (Blueprint $table): void {
                $table->dropIndex(['archived_at']);
                $table->dropColumn('archived_at');
            });
        }
    }
};
