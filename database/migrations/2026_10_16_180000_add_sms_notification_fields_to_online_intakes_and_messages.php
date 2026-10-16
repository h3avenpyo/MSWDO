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
        if (Schema::hasTable('financial_assistance_messages')) {
            Schema::table('financial_assistance_messages', function (Blueprint $table) {
                if (!Schema::hasColumn('financial_assistance_messages', 'online_intake_id')) {
                    $table->foreignId('online_intake_id')->nullable()->after('intake_id')->constrained('online_financial_intakes')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('online_financial_intakes')) {
            Schema::table('online_financial_intakes', function (Blueprint $table) {
                if (!Schema::hasColumn('online_financial_intakes', 'sms_status')) {
                    $table->string('sms_status', 30)->nullable()->after('beneficiary_intake_id');
                }
                if (!Schema::hasColumn('online_financial_intakes', 'sms_error')) {
                    $table->text('sms_error')->nullable()->after('sms_status');
                }
                if (!Schema::hasColumn('online_financial_intakes', 'sms_notified_at')) {
                    $table->timestamp('sms_notified_at')->nullable()->after('sms_error');
                }
                if (!Schema::hasColumn('online_financial_intakes', 'approval_sms_sent_at')) {
                    $table->timestamp('approval_sms_sent_at')->nullable()->after('sms_notified_at');
                }
                if (!Schema::hasColumn('online_financial_intakes', 'rejection_sms_sent_at')) {
                    $table->timestamp('rejection_sms_sent_at')->nullable()->after('approval_sms_sent_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('financial_assistance_messages')) {
            Schema::table('financial_assistance_messages', function (Blueprint $table) {
                if (Schema::hasColumn('financial_assistance_messages', 'online_intake_id')) {
                    $table->dropForeign(['online_intake_id']);
                    $table->dropColumn('online_intake_id');
                }
            });
        }

        if (Schema::hasTable('online_financial_intakes')) {
            Schema::table('online_financial_intakes', function (Blueprint $table) {
                $columns = ['sms_status', 'sms_error', 'sms_notified_at', 'approval_sms_sent_at', 'rejection_sms_sent_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('online_financial_intakes', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
