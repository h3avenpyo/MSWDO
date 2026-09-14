<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Make social_case_studies.main_client_id NOT NULL now that every historical case
     * has been backfilled (main_client_id = client_id) and all new cases are created
     * through the updated application code.
     *
     * The ALTER is guarded so it never runs while nullable main_client_id rows remain.
     */
    public function up(): void
    {
        $nullCount = DB::table('social_case_studies')->whereNull('main_client_id')->count();

        if ($nullCount > 0) {
            throw new \RuntimeException(
                "Cannot enforce NOT NULL on main_client_id: {$nullCount} row(s) still have a NULL main client. Run the backfill first."
            );
        }

        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            DB::statement('ALTER TABLE social_case_studies MODIFY main_client_id BIGINT UNSIGNED NOT NULL');
        } else {
            DB::statement('ALTER TABLE social_case_studies ALTER COLUMN main_client_id SET NOT NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            DB::statement('ALTER TABLE social_case_studies MODIFY main_client_id BIGINT UNSIGNED NULL');
        } else {
            DB::statement('ALTER TABLE social_case_studies ALTER COLUMN main_client_id DROP NOT NULL');
        }
    }
};