<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $schema = Schema::connection('mswdo_social_case');
        if ($schema->hasTable('family_members')) {
            return;
        }

        $schema->create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_case_study_id')->constrained('social_case_studies')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('relationship');
            $table->unsignedTinyInteger('age')->nullable();
            $table->enum('sex', ['Male', 'Female'])->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('monthly_income', 12, 2)->nullable();
            $table->boolean('is_dependent')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_social_case')->dropIfExists('family_members');
    }
};
