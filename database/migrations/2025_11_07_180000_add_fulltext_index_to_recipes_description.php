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
        if (! $this->supportsFullText()) {
            return;
        }

        Schema::table('recipes', function (Blueprint $table) {
            $table->fullText('description', 'recipes_description_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! $this->supportsFullText()) {
            return;
        }

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropFullText('recipes_description_fulltext');
        });
    }

    private function supportsFullText(): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        return in_array($driver, ['mysql', 'mariadb'], true);
    }
};
