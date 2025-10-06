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
            if (!Schema::hasColumn('visibility_settings', 'page_name')) {
                $table->string('page_name')->nullable()->after('description');
            }
            if (!Schema::hasColumn('visibility_settings', 'section_name')) {
                $table->string('section_name')->nullable()->after('page_name');
            }
            if (!Schema::hasColumn('visibility_settings', 'element_key')) {
                $table->string('element_key')->nullable()->after('section_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visibility_settings', function (Blueprint $table) {
            $table->dropColumn(['page_name', 'section_name', 'element_key']);
        });
    }
};
