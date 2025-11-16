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
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if ($this->hasPrimaryKey()) {
            return;
        }

        DB::statement('ALTER TABLE `users` ADD PRIMARY KEY (`id`)');
        DB::statement('ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible safely.
    }

    private function hasPrimaryKey(): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            return true;
        }

        return DB::table('information_schema.table_constraints')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'users')
            ->where('constraint_type', 'PRIMARY KEY')
            ->exists();
    }
};
