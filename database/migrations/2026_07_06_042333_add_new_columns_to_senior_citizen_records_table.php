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
        Schema::connection('mswdo_senior')->table('senior_citizen_records', function (Blueprint $table) {
            $table->string('year_applied')->nullable()->after('record_number');
            $table->string('control_number')->nullable()->after('year_applied');
            $table->text('address')->nullable()->after('full_name');
            $table->string('barangay')->nullable()->after('address');
            $table->string('month')->nullable()->after('birth_date');
            $table->string('sex')->nullable()->after('month');
            $table->integer('age')->nullable()->after('sex');
            $table->string('contact_number')->nullable()->after('age');
            $table->string('philsys_number')->nullable()->after('contact_number');
            $table->string('rrn_number')->nullable()->after('philsys_number');
            $table->text('remarks')->nullable()->after('rrn_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mswdo_senior')->table('senior_citizen_records', function (Blueprint $table) {
            $table->dropColumn([
                'year_applied',
                'control_number',
                'address',
                'barangay',
                'month',
                'sex',
                'age',
                'contact_number',
                'philsys_number',
                'rrn_number',
                'remarks'
            ]);
        });
    }
};
