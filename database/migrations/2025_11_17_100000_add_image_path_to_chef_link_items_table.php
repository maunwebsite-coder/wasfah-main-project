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
        Schema::table('chef_link_items', function (Blueprint $table): void {
            if (!Schema::hasColumn('chef_link_items', 'image_path')) {
                $table->string('image_path')
                    ->nullable()
                    ->after('icon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chef_link_items', function (Blueprint $table): void {
            if (Schema::hasColumn('chef_link_items', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }
};
