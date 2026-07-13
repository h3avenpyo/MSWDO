<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::connection('mswdo_social_case')->hasTable('social_case_studies')) {
            return;
        }

        // The workflow creates the case before every report field is completed.
        // These were previously required by the legacy one-page form.
        foreach ([
            'date_processed DATE NULL', 'client_last_name VARCHAR(255) NULL',
            'client_first_name VARCHAR(255) NULL', 'client_age INT NULL',
            "client_sex ENUM('Male','Female') NULL", 'client_barangay VARCHAR(255) NULL',
            'service_provided VARCHAR(255) NULL', 'purpose VARCHAR(255) NULL',
            'submitted_to VARCHAR(255) NULL', 'encoded_by VARCHAR(255) NULL',
        ] as $definition) {
            DB::connection('mswdo_social_case')->statement("ALTER TABLE social_case_studies MODIFY {$definition}");
        }
    }

    public function down(): void {}
};
