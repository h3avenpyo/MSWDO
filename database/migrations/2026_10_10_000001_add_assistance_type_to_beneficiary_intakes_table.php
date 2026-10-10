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
            if (!Schema::hasColumn('beneficiary_intakes', 'assistance_type')) {
                $table->string('assistance_type', 100)->nullable()->after('assistance_purpose')->index();
            }
        });

        if (Schema::hasTable('online_financial_intakes')) {
            Schema::table('online_financial_intakes', function (Blueprint $table) {
                if (!Schema::hasColumn('online_financial_intakes', 'assistance_type')) {
                    $table->string('assistance_type', 100)->nullable()->after('assistance_purpose');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            if (Schema::hasColumn('beneficiary_intakes', 'assistance_type')) {
                $table->dropColumn('assistance_type');
            }
        });

        if (Schema::hasTable('online_financial_intakes')) {
            Schema::table('online_financial_intakes', function (Blueprint $table) {
                if (Schema::hasColumn('online_financial_intakes', 'assistance_type')) {
                    $table->dropColumn('assistance_type');
                }
            });
        }
    }
};
