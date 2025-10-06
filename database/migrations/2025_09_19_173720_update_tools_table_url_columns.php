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
        Schema::table('tools', function (Blueprint $table) {
            // Change amazon_url and affiliate_url to text to handle long URLs
            $table->text('amazon_url')->nullable()->change();
            $table->text('affiliate_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            // Revert back to string (255 characters)
            $table->string('amazon_url')->nullable()->change();
            $table->string('affiliate_url')->nullable()->change();
        });
    }
};
