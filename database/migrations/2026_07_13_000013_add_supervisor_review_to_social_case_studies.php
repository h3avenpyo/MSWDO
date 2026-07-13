<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $schema = Schema::connection('mswdo_social_case');
        $schema->table('social_case_studies', function (Blueprint $table) use ($schema) {
            if (! $schema->hasColumn('social_case_studies', 'supervisor_id')) {
                $table->unsignedBigInteger('supervisor_id')->nullable()->after('supervisor_notes');
            }
            if (! $schema->hasColumn('social_case_studies', 'supervisor_decision')) {
                $table->enum('supervisor_decision', ['Approved', 'Needs Info', 'Rejected'])->nullable()->after('supervisor_id');
            }
            if (! $schema->hasColumn('social_case_studies', 'supervisor_approved_at')) {
                $table->timestamp('supervisor_approved_at')->nullable()->after('supervisor_decision');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_social_case')->table('social_case_studies', function (Blueprint $table) {
            $table->dropColumn(['supervisor_id', 'supervisor_decision', 'supervisor_approved_at']);
        });
    }
};
