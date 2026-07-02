<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mswdo_senior')->create('senior_citizen_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_number')->unique();
            $table->string('full_name');
            $table->date('birth_date')->nullable();
            $table->string('osca_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mswdo_senior')->dropIfExists('senior_citizen_records');
    }
};
