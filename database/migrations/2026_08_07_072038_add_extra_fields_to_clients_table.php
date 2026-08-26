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
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'birthplace')) {
                $table->string('birthplace', 255)->nullable()->after('birthdate');
            }
            if (!Schema::hasColumn('clients', 'religion')) {
                $table->string('religion', 100)->nullable()->after('birthplace');
            }
            if (!Schema::hasColumn('clients', 'education')) {
                $table->string('education', 100)->nullable()->after('religion');
            }
            if (!Schema::hasColumn('clients', 'civil_status')) {
                $table->string('civil_status', 50)->nullable()->after('education');
            }
            if (!Schema::hasColumn('clients', 'occupation')) {
                $table->string('occupation', 255)->nullable()->after('civil_status');
            }
            if (!Schema::hasColumn('clients', 'income')) {
                $table->string('income', 100)->nullable()->after('occupation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $columns = ['birthplace', 'religion', 'education', 'civil_status', 'occupation', 'income'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('clients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
