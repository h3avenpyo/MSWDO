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
        Schema::create('social_case_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // created, updated, archived, printed, restored, etc.
            $table->text('details'); // description of the action
            $table->json('case_info')->nullable(); // {client_name, control_no}
            $table->string('admin')->default('Social Case Study Officer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_case_activity_logs');
    }
};
