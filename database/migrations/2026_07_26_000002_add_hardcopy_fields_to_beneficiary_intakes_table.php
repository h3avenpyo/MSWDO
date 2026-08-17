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
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            $table->string('client_type', 20)->default('New')->after('control_number');
            $table->string('time_start', 20)->nullable()->after('date_processed');
            $table->string('time_end', 20)->nullable()->after('time_start');
            $table->json('beneficiary_categories')->nullable()->after('beneficiary_category_other');
            $table->json('family_composition')->nullable()->after('rep_relationship');
            $table->text('social_worker_assessment')->nullable()->after('family_composition');
            $table->string('recommended_assistance_type', 150)->nullable()->after('social_worker_assessment');
            $table->string('assistance_purpose', 255)->nullable()->after('recommended_assistance_type');
            $table->decimal('recommended_amount', 12, 2)->nullable()->after('assistance_purpose');
            $table->string('interviewed_by', 150)->nullable()->after('recommended_amount');
            $table->string('reviewed_by', 150)->nullable()->after('interviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            $table->dropColumn([
                'client_type',
                'time_start',
                'time_end',
                'beneficiary_categories',
                'family_composition',
                'social_worker_assessment',
                'recommended_assistance_type',
                'assistance_purpose',
                'recommended_amount',
                'interviewed_by',
                'reviewed_by',
            ]);
        });
    }
};
