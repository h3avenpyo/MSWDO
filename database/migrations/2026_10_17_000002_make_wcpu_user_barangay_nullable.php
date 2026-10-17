<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('wcpu');

        if (! $schema->hasTable('users') || ! $schema->hasColumn('users', 'barangay')) {
            throw new RuntimeException('The WCPU users.barangay column does not exist.');
        }

        $barangay = collect($schema->getColumns('users'))
            ->firstWhere('name', 'barangay');
        $hasForeignKey = $this->hasBarangayForeignKey();

        if (! ($barangay['nullable'] ?? false) || ($barangay['collation'] ?? null) !== 'utf8mb4_general_ci') {
            if ($hasForeignKey) {
                $schema->table('users', function (Blueprint $table): void {
                    $table->dropForeign('user_brgy');
                });
            }

            $schema->table('users', function (Blueprint $table): void {
                $table->string('barangay', 100)
                    ->collation('utf8mb4_general_ci')
                    ->nullable()
                    ->change();
            });
        }

        if (! $hasForeignKey) {
            $schema->table('users', function (Blueprint $table): void {
                $table->foreign('barangay', 'user_brgy')->references('name')->on('barangays');
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('wcpu');

        if (! $schema->hasTable('users') || ! $schema->hasColumn('users', 'barangay')) {
            return;
        }

        if (DB::connection('wcpu')->table('users')->whereNull('barangay')->exists()) {
            throw new RuntimeException('Cannot require a barangay while WCPU users have no barangay assigned.');
        }

        if ($this->hasBarangayForeignKey()) {
            $schema->table('users', function (Blueprint $table): void {
                $table->dropForeign('user_brgy');
            });
        }

        $schema->table('users', function (Blueprint $table): void {
            $table->string('barangay', 100)
                ->collation('utf8mb4_general_ci')
                ->nullable(false)
                ->change();
        });

        if (! $this->hasBarangayForeignKey()) {
            $schema->table('users', function (Blueprint $table): void {
                $table->foreign('barangay', 'user_brgy')->references('name')->on('barangays');
            });
        }
    }

    private function hasBarangayForeignKey(): bool
    {
        $result = DB::connection('wcpu')->selectOne(
            "SELECT COUNT(*) AS aggregate
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
                AND TABLE_NAME = 'users'
                AND CONSTRAINT_NAME = 'user_brgy'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
        );

        return (int) $result->aggregate > 0;
    }
};
