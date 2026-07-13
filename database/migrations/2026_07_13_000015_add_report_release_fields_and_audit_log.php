<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $schema = Schema::connection('mswdo_social_case');
        $schema->table('social_case_studies', function (Blueprint $table) use ($schema) {
            if (! $schema->hasColumn('social_case_studies', 'released_at')) {
                $table->timestamp('released_at')->nullable()->after('report_generated');
                $table->unsignedBigInteger('released_by')->nullable()->after('released_at');
                $table->string('released_to')->nullable()->after('released_by');
            }
        });

        if (! $schema->hasTable('social_case_report_release_logs')) {
            $schema->create('social_case_report_release_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('social_case_study_id');
                $table->unsignedBigInteger('social_case_report_id')->nullable();
                $table->unsignedBigInteger('released_by')->nullable();
                $table->string('released_by_name')->nullable();
                $table->string('released_to');
                $table->timestamp('released_at');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->foreign('social_case_study_id')->references('id')->on('social_case_studies')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('mswdo_social_case');
        $schema->dropIfExists('social_case_report_release_logs');
        $schema->table('social_case_studies', function (Blueprint $table) {
            $table->dropColumn(['released_at', 'released_by', 'released_to']);
        });
    }
};
