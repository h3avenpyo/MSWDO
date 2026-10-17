<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection('wcpu');

        if ($schema->hasTable('events')) {
            return;
        }

        $schema->create('events', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->string('target_audience', 50)->default('all');
            $table->string('location')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('event_date');
            $table->index('target_audience');
        });
    }

    public function down(): void
    {
        Schema::connection('wcpu')->dropIfExists('events');
    }
};
