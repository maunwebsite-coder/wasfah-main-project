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
            DB::statement('ALTER TABLE workshops MODIFY instructor_bio TEXT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->usingMySql()) {
            DB::statement('ALTER TABLE workshops MODIFY instructor_bio VARCHAR(255) NULL');
        }
    }

    protected function usingMySql(): bool
    {
        return in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true);
    }
};
