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
            $table->boolean('is_archived')->default(false)->index()->after('claimed_by');
            $table->timestamp('archived_at')->nullable()->index()->after('is_archived');
            $table->unsignedBigInteger('archived_by')->nullable()->index()->after('archived_at');
            $table->string('archive_module', 20)->nullable()->index()->after('archived_by'); // 'step1', 'step2'
            $table->text('archive_reason')->nullable()->after('archive_module');

            $table->foreign('archived_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_intakes', function (Blueprint $table) {
            $table->dropForeign(['archived_by']);
            $table->dropColumn([
                'is_archived',
                'archived_at',
                'archived_by',
                'archive_module',
                'archive_reason',
            ]);
        });
    }
};
