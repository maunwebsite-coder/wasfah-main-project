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
        Schema::table('users', function (Blueprint $table) {
            // Make password nullable for users who sign up with a social provider
            $table->string('password')->nullable()->change();

            // Add fields for Socialite
            $table->string('provider')->nullable()->after('id');
            $table->string('provider_id')->nullable()->after('provider');
            $table->text('provider_token')->nullable()->after('password');
            $table->string('avatar')->nullable()->after('email');
            
            // Add a unique constraint for provider and provider_id
            $table->unique(['provider', 'provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the unique constraint first
            $table->dropUnique(['provider', 'provider_id']);
            
            // Drop the columns we added
            $table->dropColumn(['provider', 'provider_id', 'provider_token', 'avatar']);
            
            // Revert the password column to not be nullable
            $table->string('password')->nullable(false)->change();
        });
    }
};
