<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('wcpu');

        if (! $schema->hasTable('bcpc_case_monitoring')) {
            $schema->create('bcpc_case_monitoring', function (Blueprint $table): void {
                $table->id();
                $table->unsignedInteger('barangay_id');
                $table->unsignedTinyInteger('quarter');
                $table->unsignedSmallInteger('year');
                $table->longText('data');
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamp('archived_at')->nullable()->index();
                $table->timestamps();
                $table->timestamp('submitted_at')->nullable();
                $table->unsignedBigInteger('submitted_by')->nullable();

                $table->index(['barangay_id', 'quarter', 'year'], 'bcpc_case_monitoring_period_index');
            });
        } else {
            if (! $schema->hasColumn('bcpc_case_monitoring', 'archived_at')) {
                $schema->table('bcpc_case_monitoring', function (Blueprint $table): void {
                    $table->timestamp('archived_at')->nullable()->index();
                });
            }
            if (! $schema->hasColumn('bcpc_case_monitoring', 'submitted_at')) {
                $schema->table('bcpc_case_monitoring', function (Blueprint $table): void {
                    $table->timestamp('submitted_at')->nullable();
                    $table->unsignedBigInteger('submitted_by')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('wcpu');

        if ($schema->hasTable('bcpc_case_monitoring')) {
            $schema->dropIfExists('bcpc_case_monitoring');
        }
    }
};
