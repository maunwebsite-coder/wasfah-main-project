<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        if (! Schema::hasTable('recipes')) {
            return;
        }

        $hasPrimaryKey = collect(
            DB::select("SHOW INDEX FROM `recipes` WHERE Key_name = 'PRIMARY'")
        )->isNotEmpty();

        if ($hasPrimaryKey) {
            return;
        }

        DB::statement('ALTER TABLE `recipes` ADD PRIMARY KEY (`recipe_id`)');
        DB::statement('ALTER TABLE `recipes` MODIFY `recipe_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration to avoid dropping an expected primary key.
    }
};
