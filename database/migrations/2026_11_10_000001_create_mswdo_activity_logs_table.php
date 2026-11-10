<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mswdo_activity_logs', function (Blueprint $table) {
            $table->id();
            // Snapshots survive account changes and deletion; deliberately no cascading FK.
            $table->unsignedBigInteger('user_id')->index();
            $table->string('user_name');
            $table->string('role')->index();
            $table->string('activity_type', 50)->index();
            $table->string('description');
            $table->string('module', 100)->index();
            $table->string('subject_type')->nullable();
            $table->string('subject_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamp('created_at')->index();
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mswdo_activity_logs');
    }
};
