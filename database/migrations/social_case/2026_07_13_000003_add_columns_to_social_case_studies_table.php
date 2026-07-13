<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_social_case')->table('social_case_studies', function (Blueprint $table) {
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'date_processed')) {
                $table->date('date_processed')->nullable()->after('case_number');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'client_last_name')) {
                $table->string('client_last_name')->after('date_processed');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'client_first_name')) {
                $table->string('client_first_name')->after('client_last_name');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'client_middle_name')) {
                $table->string('client_middle_name')->nullable()->after('client_first_name');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'client_age')) {
                $table->integer('client_age')->after('client_middle_name');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'client_sex')) {
                $table->enum('client_sex', ['Male', 'Female'])->after('client_age');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'client_barangay')) {
                $table->string('client_barangay')->after('client_sex');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'beneficiary_last_name')) {
                $table->string('beneficiary_last_name')->nullable()->after('client_barangay');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'beneficiary_first_name')) {
                $table->string('beneficiary_first_name')->nullable()->after('beneficiary_last_name');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'beneficiary_middle_name')) {
                $table->string('beneficiary_middle_name')->nullable()->after('beneficiary_first_name');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'beneficiary_age')) {
                $table->integer('beneficiary_age')->nullable()->after('beneficiary_middle_name');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'beneficiary_birthday')) {
                $table->date('beneficiary_birthday')->nullable()->after('beneficiary_age');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'beneficiary_sex')) {
                $table->enum('beneficiary_sex', ['Male', 'Female'])->nullable()->after('beneficiary_birthday');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'beneficiary_barangay')) {
                $table->string('beneficiary_barangay')->nullable()->after('beneficiary_sex');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'medical_conditions')) {
                $table->json('medical_conditions')->nullable()->after('beneficiary_barangay');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'service_provided')) {
                $table->string('service_provided')->after('medical_conditions');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'purpose')) {
                $table->string('purpose')->after('service_provided');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'submitted_to')) {
                $table->string('submitted_to')->after('purpose');
            }
            if (!Schema::connection('mswdo_social_case')->hasColumn('social_case_studies', 'encoded_by')) {
                $table->string('encoded_by')->after('submitted_to');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_social_case')->table('social_case_studies', function (Blueprint $table) {
            $table->dropColumn([
                'date_processed',
                'client_last_name',
                'client_first_name',
                'client_middle_name',
                'client_age',
                'client_sex',
                'client_barangay',
                'beneficiary_last_name',
                'beneficiary_first_name',
                'beneficiary_middle_name',
                'beneficiary_age',
                'beneficiary_birthday',
                'beneficiary_sex',
                'beneficiary_barangay',
                'medical_conditions',
                'service_provided',
                'purpose',
                'submitted_to',
                'encoded_by',
            ]);
        });
    }
};
