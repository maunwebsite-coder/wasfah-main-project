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
        if (! $this->usingMySql() || ! Schema::hasTable('contact_messages')) {
            return;
        }

        $columnState = $this->getIdColumnState();

        if ($columnState === null) {
            return;
        }

        $isPrimary = $this->isPrimaryKey($columnState);
        $isAutoIncrement = $this->isAutoIncrement($columnState);

        if ($isPrimary && $isAutoIncrement) {
            return;
        }

        $statements = [];

        if (! $isAutoIncrement) {
            $statements[] = 'MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT';
        }

        if (! $isPrimary) {
            $statements[] = 'ADD PRIMARY KEY (`id`)';
        }

        if ($statements !== []) {
            DB::statement('ALTER TABLE `contact_messages` ' . implode(', ', $statements));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! $this->usingMySql() || ! Schema::hasTable('contact_messages')) {
            return;
        }

        $columnState = $this->getIdColumnState();

        if ($columnState === null) {
            return;
        }

        $isPrimary = $this->isPrimaryKey($columnState);
        $isAutoIncrement = $this->isAutoIncrement($columnState);

        if (! $isPrimary && ! $isAutoIncrement) {
            return;
        }

        $statements = [];

        if ($isAutoIncrement) {
            $statements[] = 'MODIFY `id` BIGINT UNSIGNED NOT NULL';
        }

        if ($isPrimary) {
            $statements[] = 'DROP PRIMARY KEY';
        }

        if ($statements !== []) {
            DB::statement('ALTER TABLE `contact_messages` ' . implode(', ', $statements));
        }
    }

    /**
     * Fetch the current metadata for the id column.
     */
    protected function getIdColumnState(): ?object
    {
        return DB::table('information_schema.columns')
            ->select(['COLUMN_KEY', 'EXTRA'])
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'contact_messages')
            ->where('COLUMN_NAME', 'id')
            ->first();
    }

    protected function isPrimaryKey(object $columnState): bool
    {
        return ($columnState->COLUMN_KEY ?? null) === 'PRI';
    }

    protected function isAutoIncrement(object $columnState): bool
    {
        return str_contains(strtolower($columnState->EXTRA ?? ''), 'auto_increment');
    }

    /**
     * Only perform MySQL-specific operations when on a MySQL connection.
     */
    protected function usingMySql(): bool
    {
        return Schema::getConnection()
            ->getDriverName() === 'mysql';
    }
};
