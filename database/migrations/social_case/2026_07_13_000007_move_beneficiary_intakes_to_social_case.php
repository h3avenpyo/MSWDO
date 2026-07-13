<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('mswdo_social_case');

        if (! $schema->hasTable('beneficiary_intakes')) {
            $schema->create('beneficiary_intakes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
                $table->foreignId('social_case_study_id')->nullable()->constrained('social_case_studies')->nullOnDelete();
                $table->string('control_number')->unique();
                $table->date('date_processed');
                $table->string('encoder');
                $table->string('client_last_name'); $table->string('client_first_name'); $table->string('client_middle_name')->nullable();
                $table->date('client_birthday'); $table->integer('client_age'); $table->string('client_sex'); $table->string('client_civil_status');
                $table->text('client_address'); $table->string('client_barangay'); $table->string('client_contact_number');
                $table->string('client_occupation')->nullable(); $table->decimal('client_monthly_income', 10, 2)->nullable();
                $table->boolean('is_client_beneficiary')->default(true);
                $table->string('beneficiary_last_name')->nullable(); $table->string('beneficiary_first_name')->nullable(); $table->string('beneficiary_middle_name')->nullable();
                $table->date('beneficiary_birthday')->nullable(); $table->integer('beneficiary_age')->nullable(); $table->string('beneficiary_sex')->nullable();
                $table->string('beneficiary_barangay')->nullable(); $table->string('beneficiary_relationship')->nullable();
                $table->json('medical_conditions')->nullable(); $table->string('medical_condition_other')->nullable();
                $table->string('service_provided'); $table->string('purpose'); $table->string('purpose_other')->nullable(); $table->string('submitted_to');
                $table->timestamps();
            });
        }

        if (Schema::connection('mswdo_admin')->hasTable('beneficiary_intakes')) {
            DB::connection('mswdo_admin')->table('beneficiary_intakes')->orderBy('id')->chunkById(100, function ($intakes) {
                foreach ($intakes as $intake) {
                    DB::connection('mswdo_social_case')->table('beneficiary_intakes')->insertOrIgnore([
                        ...((array) $intake),
                        'client_id' => null,
                        'social_case_study_id' => null,
                    ]);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::connection('mswdo_social_case')->dropIfExists('beneficiary_intakes');
    }
};
