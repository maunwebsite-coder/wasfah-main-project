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
        Schema::table('visibility_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('visibility_settings', 'section')) {
                $table->string('section')->unique()->after('id');
            }
            if (!Schema::hasColumn('visibility_settings', 'is_visible')) {
                $table->boolean('is_visible')->default(true)->after('section');
            }
            if (!Schema::hasColumn('visibility_settings', 'description')) {
                $table->text('description')->nullable()->after('is_visible');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visibility_settings', function (Blueprint $table) {
            $table->dropColumn(['section', 'is_visible', 'description']);
        });
    }
};
