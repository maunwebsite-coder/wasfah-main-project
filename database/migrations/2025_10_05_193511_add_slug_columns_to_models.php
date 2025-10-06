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
        // Add slug to workshops table if it doesn't exist
        if (!Schema::hasColumn('workshops', 'slug')) {
            Schema::table('workshops', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('title');
            });
        }
        
        // Add slug to recipes table if it doesn't exist
        if (!Schema::hasColumn('recipes', 'slug')) {
            Schema::table('recipes', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('title');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove slug from workshops table
        if (Schema::hasColumn('workshops', 'slug')) {
            Schema::table('workshops', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
        
        // Remove slug from recipes table
        if (Schema::hasColumn('recipes', 'slug')) {
            Schema::table('recipes', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};
