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
        Schema::table('email_verification_codes', function (Blueprint $table) {
            $table->foreignId('referrer_id')
                ->nullable()
                ->after('role')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_verification_codes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referrer_id');
        });
    }
};
