<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Expand the users.role enum to include the eligibility_checker role
        // while preserving the existing officer roles.
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','social_worker','encoder','staff','eligibility_checker','Senior Citizen officer','Financial assistance officer') NOT NULL DEFAULT 'staff'");

        Schema::table('social_case_studies', function (Blueprint $table) {
            $table->enum('eligibility_status', ['pending', 'eligible', 'ineligible'])
                ->default('pending')
                ->after('status');
            $table->unsignedBigInteger('eligible_by')->nullable()->after('eligibility_status');
            $table->timestamp('eligible_at')->nullable()->after('eligible_by');
            $table->text('ineligible_reason')->nullable()->after('eligible_at');

            $table->foreign('eligible_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_case_studies', function (Blueprint $table) {
            $table->dropForeign(['eligible_by']);
            $table->dropColumn(['eligibility_status', 'eligible_by', 'eligible_at', 'ineligible_reason']);
        });

        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','social_worker','encoder','staff','Senior Citizen officer','Financial assistance officer') NOT NULL DEFAULT 'staff'");
    }
};
