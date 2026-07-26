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
            $table->string('beneficiary_extension_name', 20)->nullable()->after('beneficiary_middle_name');
            $table->text('beneficiary_street_address')->nullable()->after('beneficiary_barangay');
            $table->string('beneficiary_city', 100)->nullable()->default('Silang')->after('beneficiary_street_address');
            $table->string('beneficiary_province', 100)->nullable()->default('Cavite')->after('beneficiary_city');
            $table->string('beneficiary_region', 100)->nullable()->default('Region IV-A')->after('beneficiary_province');
            $table->string('beneficiary_contact_number', 50)->nullable()->after('beneficiary_region');
            $table->string('beneficiary_civil_status', 50)->nullable()->after('beneficiary_sex');
            $table->string('beneficiary_occupation', 150)->nullable()->after('beneficiary_civil_status');
            $table->decimal('beneficiary_monthly_salary', 12, 2)->nullable()->after('beneficiary_occupation');
            $table->string('beneficiary_category', 100)->nullable()->after('beneficiary_monthly_salary');
            $table->string('beneficiary_category_other', 150)->nullable()->after('beneficiary_category');

            $table->boolean('has_representative')->default(false)->after('beneficiary_category_other');
            $table->string('rep_last_name', 100)->nullable()->after('has_representative');
            $table->string('rep_first_name', 100)->nullable()->after('rep_last_name');
            $table->string('rep_middle_name', 100)->nullable()->after('rep_first_name');
            $table->string('rep_extension_name', 20)->nullable()->after('rep_middle_name');
            $table->text('rep_street_address')->nullable()->after('rep_extension_name');
            $table->string('rep_barangay', 100)->nullable()->after('rep_street_address');
            $table->string('rep_city', 100)->nullable()->default('Silang')->after('rep_barangay');
            $table->string('rep_province', 100)->nullable()->default('Cavite')->after('rep_city');
            $table->string('rep_region', 100)->nullable()->default('Region IV-A')->after('rep_province');
            $table->string('rep_contact_number', 50)->nullable()->after('rep_region');
            $table->date('rep_birthday')->nullable()->after('rep_contact_number');
            $table->integer('rep_age')->nullable()->after('rep_birthday');
            $table->string('rep_sex', 20)->nullable()->after('rep_age');
            $table->string('rep_civil_status', 50)->nullable()->after('rep_sex');
            $table->string('rep_occupation', 150)->nullable()->after('rep_civil_status');
            $table->decimal('rep_monthly_salary', 12, 2)->nullable()->after('rep_occupation');
            $table->string('rep_relationship', 100)->nullable()->after('rep_monthly_salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            $table->dropColumn([
                'beneficiary_extension_name',
                'beneficiary_street_address',
                'beneficiary_city',
                'beneficiary_province',
                'beneficiary_region',
                'beneficiary_contact_number',
                'beneficiary_civil_status',
                'beneficiary_occupation',
                'beneficiary_monthly_salary',
                'beneficiary_category',
                'beneficiary_category_other',
                'has_representative',
                'rep_last_name',
                'rep_first_name',
                'rep_middle_name',
                'rep_extension_name',
                'rep_street_address',
                'rep_barangay',
                'rep_city',
                'rep_province',
                'rep_region',
                'rep_contact_number',
                'rep_birthday',
                'rep_age',
                'rep_sex',
                'rep_civil_status',
                'rep_occupation',
                'rep_monthly_salary',
                'rep_relationship',
            ]);
        });
    }
};
