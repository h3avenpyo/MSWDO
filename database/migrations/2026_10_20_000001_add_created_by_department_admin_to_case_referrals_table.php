<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('wcpu');

        if ($schema->hasTable('case_referrals') && ! $schema->hasColumn('case_referrals', 'created_by_department_admin')) {
            $schema->table('case_referrals', function (Blueprint $table): void {
                $table->boolean('created_by_department_admin')->default(false)->after('referred_by_user_id');
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('wcpu');

        if ($schema->hasTable('case_referrals') && $schema->hasColumn('case_referrals', 'created_by_department_admin')) {
            $schema->table('case_referrals', function (Blueprint $table): void {
                $table->dropColumn('created_by_department_admin');
            });
        }
    }
};
