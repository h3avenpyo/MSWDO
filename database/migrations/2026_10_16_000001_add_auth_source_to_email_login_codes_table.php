<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $foreignKeys = collect(Schema::getForeignKeys('email_login_codes'))
            ->pluck('name');

        if ($foreignKeys->contains('email_login_codes_user_id_foreign')) {
            Schema::table('email_login_codes', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }

        Schema::table('email_login_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('auth_source')->default('mswdo')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('email_login_codes', function (Blueprint $table) {
            $table->dropColumn('auth_source');
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });

        $foreignKeys = collect(Schema::getForeignKeys('email_login_codes'))
            ->pluck('name');

        if (! $foreignKeys->contains('email_login_codes_user_id_foreign')) {
            Schema::table('email_login_codes', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }
};
