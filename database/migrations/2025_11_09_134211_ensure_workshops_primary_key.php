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
        if ($this->hasPrimaryKey()) {
            return;
        }

        DB::statement('ALTER TABLE `workshops` ADD PRIMARY KEY (`id`)');
        DB::statement('ALTER TABLE `workshops` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No safe rollback – this migration only repairs schema.
    }

    private function hasPrimaryKey(): bool
    {
        return DB::table('information_schema.table_constraints')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'workshops')
            ->where('constraint_type', 'PRIMARY KEY')
            ->exists();
    }
};
