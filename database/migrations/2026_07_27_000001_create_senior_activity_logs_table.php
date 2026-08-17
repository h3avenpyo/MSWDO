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
        Schema::create('senior_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('name');
            $table->string('identifier')->nullable();
            $table->string('admin')->default('Admin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('senior_activity_logs');
    }
};
