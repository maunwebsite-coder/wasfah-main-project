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
        if (! Schema::hasTable('chef_link_pages')) {
            return;
        }

        if (Schema::hasColumn('chef_link_pages', 'show_upcoming_workshop')) {
            return;
        }

        Schema::table('chef_link_pages', function (Blueprint $table): void {
            $table->boolean('show_upcoming_workshop')
                ->default(false)
                ->after('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('chef_link_pages')) {
            return;
        }

        if (! Schema::hasColumn('chef_link_pages', 'show_upcoming_workshop')) {
            return;
        }

        Schema::table('chef_link_pages', function (Blueprint $table): void {
            $table->dropColumn('show_upcoming_workshop');
        });
    }
};
