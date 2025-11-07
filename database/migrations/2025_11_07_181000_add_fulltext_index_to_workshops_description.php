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
        if (! $this->supportsFullText() || $this->fullTextExists('workshops', 'workshops_description_fulltext')) {
            return;
        }

        Schema::table('workshops', function (Blueprint $table) {
            $table->fullText('description', 'workshops_description_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! $this->supportsFullText() || ! $this->fullTextExists('workshops', 'workshops_description_fulltext')) {
            return;
        }

        Schema::table('workshops', function (Blueprint $table) {
            $table->dropFullText('workshops_description_fulltext');
        });
    }

    private function supportsFullText(): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        return in_array($driver, ['mysql', 'mariadb'], true);
    }

    private function fullTextExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        $prefixedTable = $connection->getTablePrefix() . $table;

        $count = $connection->table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $prefixedTable)
            ->where('index_name', $index)
            ->count();

        return $count > 0;
    }
};
