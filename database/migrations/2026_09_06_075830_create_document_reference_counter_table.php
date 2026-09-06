<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_reference_counters', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('social_case')->unique();
            $table->integer('current_number')->default(0);
            $table->timestamps();
        });

        // Initialize the counter
        DB::table('document_reference_counters')->insert([
            'type' => 'social_case',
            'current_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_reference_counters');
    }
};
