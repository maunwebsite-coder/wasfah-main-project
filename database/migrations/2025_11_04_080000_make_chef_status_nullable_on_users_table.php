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
        if ($this->usingMySql()) {
            DB::statement('ALTER TABLE users MODIFY chef_status VARCHAR(32) NULL DEFAULT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->whereNull('chef_status')
            ->update(['chef_status' => 'needs_profile']);

        if ($this->usingMySql()) {
            DB::statement("ALTER TABLE users MODIFY chef_status VARCHAR(32) NOT NULL DEFAULT 'needs_profile'");
        }
    }

    protected function usingMySql(): bool
    {
        return in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true);
    }
};
