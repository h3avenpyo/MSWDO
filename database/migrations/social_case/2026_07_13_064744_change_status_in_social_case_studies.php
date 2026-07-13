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
        Schema::connection('mswdo_social_case')->table('social_case_studies', function (Blueprint $table) {
            $table->string('status', 50)->default('Open')->change();
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_social_case')->table('social_case_studies', function (Blueprint $table) {
            $table->enum('status', ['Open', 'In Progress', 'Closed'])->default('Open')->change();
        });
    }
};
