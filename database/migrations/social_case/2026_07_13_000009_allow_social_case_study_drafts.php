<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // A case is now created before requirements are supplied, so these legacy
        // all-at-once fields must permit a draft value.
        foreach (['date_processed DATE NULL','client_last_name VARCHAR(255) NULL','client_first_name VARCHAR(255) NULL','client_age INT NULL','client_sex ENUM(\'Male\',\'Female\') NULL','client_barangay VARCHAR(255) NULL','service_provided VARCHAR(255) NULL','purpose VARCHAR(255) NULL','submitted_to VARCHAR(255) NULL','encoded_by VARCHAR(255) NULL'] as $definition) {
            DB::connection('mswdo_social_case')->statement("ALTER TABLE social_case_studies MODIFY {$definition}");
        }
    }
    public function down(): void {}
};
