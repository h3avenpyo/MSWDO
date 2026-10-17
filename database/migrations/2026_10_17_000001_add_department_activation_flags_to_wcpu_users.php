<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COLUMNS = ['bcpc_is_active', 'vawc_is_active'];

    public function up(): void
    {
        $schema = Schema::connection('wcpu');

        if (! $schema->hasTable('users')) {
            throw new RuntimeException('The WCPU users table does not exist.');
        }

        $columnsToAdd = array_values(array_filter(
            self::COLUMNS,
            fn (string $column): bool => ! $schema->hasColumn('users', $column)
        ));

        if ($columnsToAdd === []) {
            return;
        }

        $schema->table('users', function (Blueprint $table) use ($columnsToAdd): void {
            foreach ($columnsToAdd as $column) {
                $table->boolean($column)->default(true);
            }
        });
    }

    public function down(): void
    {
        $schema = Schema::connection('wcpu');
        $columnsToDrop = array_values(array_filter(
            self::COLUMNS,
            fn (string $column): bool => $schema->hasColumn('users', $column)
        ));

        if ($columnsToDrop === []) {
            return;
        }

        $schema->table('users', function (Blueprint $table) use ($columnsToDrop): void {
            $table->dropColumn($columnsToDrop);
        });
    }
};
