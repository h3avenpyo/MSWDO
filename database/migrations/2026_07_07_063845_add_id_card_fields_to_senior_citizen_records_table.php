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
            $table->string('senior_id_number')->nullable()->unique()->after('control_number');
            $table->string('photo')->nullable()->after('full_name');
            $table->text('qr_code')->nullable()->after('senior_id_number');
            $table->date('date_issued')->nullable()->after('qr_code');
            $table->timestamp('last_printed_at')->nullable()->after('date_issued');
            $table->integer('print_count')->default(0)->after('last_printed_at');
            $table->string('blood_type')->nullable()->after('remarks');
            $table->string('civil_status')->nullable()->after('blood_type');
            $table->string('emergency_contact_name')->nullable()->after('civil_status');
            $table->string('emergency_contact_number')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mswdo_senior')->table('senior_citizen_records', function (Blueprint $table) {
            $table->dropColumn([
                'senior_id_number',
                'photo',
                'qr_code',
                'date_issued',
                'last_printed_at',
                'print_count',
                'blood_type',
                'civil_status',
                'emergency_contact_name',
                'emergency_contact_number',
                'emergency_contact_relationship',
            ]);
        });
    }
};
