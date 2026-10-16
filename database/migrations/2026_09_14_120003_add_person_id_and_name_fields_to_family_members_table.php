<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a person linkage (person_id -> clients.id) plus structured name fields to
     * family_members. A member recorded here is a RELATIVE of the case, NOT the main
     * client; relatives must never be treated as the main client for eligibility.
     *
     * Idempotent: each column / foreign key is guarded.
     */
    public function up(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            if (!Schema::hasColumn('family_members', 'person_id')) {
                $table->unsignedBigInteger('person_id')->nullable()->after('social_case_study_id');
            }

            $columns = [
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
            ];

            foreach ($columns as $column) {
                if (!Schema::hasColumn('family_members', $column)) {
                    $table->string($column, 255)->nullable()->after('person_id');
                }
            }
        });

        $hasFk = collect(DB::select('SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?', [
            DB::connection()->getDatabaseName(),
            'family_members',
            'person_id',
        ]))->where('CONSTRAINT_NAME', 'family_members_person_id_foreign')->isNotEmpty();

        if (!$hasFk && Schema::hasColumn('family_members', 'person_id')) {
            Schema::table('family_members', function (Blueprint $table) {
                $table->foreign('person_id')
                    ->references('id')
                    ->on('clients')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            if (Schema::hasColumn('family_members', 'person_id')) {
                $table->dropForeign(['person_id']);
            }

            foreach (['first_name', 'middle_name', 'last_name', 'suffix'] as $column) {
                if (Schema::hasColumn('family_members', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('family_members', 'person_id')) {
                $table->dropColumn('person_id');
            }
        });
    }
};