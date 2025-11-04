<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE users MODIFY chef_status VARCHAR(32) NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->whereNull('chef_status')
            ->update(['chef_status' => 'needs_profile']);

        DB::statement("ALTER TABLE users MODIFY chef_status VARCHAR(32) NOT NULL DEFAULT 'needs_profile'");
    }
};
