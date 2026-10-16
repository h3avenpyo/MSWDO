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
        Schema::create('financial_assistance_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intake_id')->nullable()->constrained('beneficiary_intakes')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('beneficiary_name');
            $table->string('recipient_name');
            $table->string('recipient_contact_number', 50);
            $table->text('message_body');
            $table->string('message_type', 50)->default('Unclaimed Assistance');
            $table->date('claiming_date')->nullable();
            $table->string('status', 20)->default('Sent')->index(); // 'Sent', 'Failed'
            $table->string('reference_id', 100)->nullable();
            $table->text('error_message')->nullable();
            $table->string('sent_by')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['intake_id', 'created_at']);
            $table->index('recipient_contact_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_assistance_messages');
    }
};
