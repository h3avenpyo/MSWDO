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
        Schema::table('social_case_studies', function (Blueprint $table) {
            if (!Schema::hasColumn('social_case_studies', 'signers')) {
                $table->json('signers')->nullable()->after('requirements_complete');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_case_studies', function (Blueprint $table) {
            if (Schema::hasColumn('social_case_studies', 'signers')) {
                $table->dropColumn('signers');
            }
        });
    }
};
