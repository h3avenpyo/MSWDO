<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $schema = Schema::connection('mswdo_social_case');

        $schema->table('social_case_reports', function (Blueprint $table) use ($schema) {
            if (! $schema->hasColumn('social_case_reports', 'social_case_study_id')) {
                $table->unsignedBigInteger('social_case_study_id')->nullable()->after('id');
                $table->foreign('social_case_study_id')->references('id')->on('social_case_studies')->nullOnDelete();
            }
            if (! $schema->hasColumn('social_case_reports', 'generated_at')) {
                $table->timestamp('generated_at')->nullable()->after('title');
            }
            if (! $schema->hasColumn('social_case_reports', 'generated_by')) {
                $table->unsignedBigInteger('generated_by')->nullable()->after('generated_at');
            }
            if (! $schema->hasColumn('social_case_reports', 'body')) {
                $table->longText('body')->nullable()->after('description');
            }
            if (! $schema->hasColumn('social_case_reports', 'snapshot')) {
                $table->json('snapshot')->nullable()->after('body');
            }
        });
    }

    public function down(): void
    {
        $schema = Schema::connection('mswdo_social_case');
        $schema->table('social_case_reports', function (Blueprint $table) use ($schema) {
            if ($schema->hasColumn('social_case_reports', 'social_case_study_id')) {
                $table->dropForeign(['social_case_study_id']);
            }
            $table->dropColumn(['social_case_study_id', 'generated_at', 'generated_by', 'body', 'snapshot']);
        });
    }
};
