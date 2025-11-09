<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('workshop_reviews')) {
            $this->createTable();
            return;
        }

        $this->ensureColumns();
        $this->ensureIndexes();
        $this->ensureForeignKeys();
    }

    private function createTable(): void
    {
        Schema::create('workshop_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshops')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned();
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();

            $table->index(['workshop_id', 'is_approved'], 'workshop_reviews_workshop_status_index');
            $table->index('user_id', 'workshop_reviews_user_id_index');
            $table->unique(['workshop_id', 'user_id'], 'workshop_reviews_workshop_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback - this migration only rebuilds missing schema.
    }

    private function ensureColumns(): void
    {
        $this->addColumnIfMissing('workshop_id', function (Blueprint $table) {
            $table->unsignedBigInteger('workshop_id')->after('id');
        });

        $this->addColumnIfMissing('user_id', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('workshop_id');
        });

        $this->addColumnIfMissing('rating', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->default(0)->after('user_id');
        });

        $this->addColumnIfMissing('comment', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('rating');
        });

        $this->addColumnIfMissing('is_approved', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('comment');
        });

        $this->addColumnIfMissing('helpful_count', function (Blueprint $table) {
            $table->integer('helpful_count')->default(0)->after('is_approved');
        });

        $this->addColumnIfMissing('created_at', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->after('helpful_count');
        });

        $this->addColumnIfMissing('updated_at', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    private function ensureIndexes(): void
    {
        $this->addIndexIfMissing('workshop_reviews_workshop_status_index', function (Blueprint $table) {
            $table->index(['workshop_id', 'is_approved'], 'workshop_reviews_workshop_status_index');
        });

        $this->addIndexIfMissing('workshop_reviews_user_id_index', function (Blueprint $table) {
            $table->index('user_id', 'workshop_reviews_user_id_index');
        });

        $this->addIndexIfMissing('workshop_reviews_workshop_user_unique', function (Blueprint $table) {
            $table->unique(['workshop_id', 'user_id'], 'workshop_reviews_workshop_user_unique');
        });
    }

    private function ensureForeignKeys(): void
    {
        $this->addForeignKeyIfMissing('workshop_reviews_workshop_id_foreign', function (Blueprint $table) {
            $table->foreign('workshop_id', 'workshop_reviews_workshop_id_foreign')
                ->references('id')
                ->on('workshops')
                ->onDelete('cascade');
        });

        $this->addForeignKeyIfMissing('workshop_reviews_user_id_foreign', function (Blueprint $table) {
            $table->foreign('user_id', 'workshop_reviews_user_id_foreign')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    private function addColumnIfMissing(string $column, callable $definition): void
    {
        if (Schema::hasColumn('workshop_reviews', $column)) {
            return;
        }

        Schema::table('workshop_reviews', function (Blueprint $table) use ($definition) {
            $definition($table);
        });
    }

    private function addIndexIfMissing(string $indexName, callable $callback): void
    {
        if ($this->indexExists($indexName)) {
            return;
        }

        Schema::table('workshop_reviews', function (Blueprint $table) use ($callback) {
            $callback($table);
        });
    }

    private function addForeignKeyIfMissing(string $constraintName, callable $callback): void
    {
        if ($this->foreignKeyExists($constraintName)) {
            return;
        }

        Schema::table('workshop_reviews', function (Blueprint $table) use ($callback) {
            $callback($table);
        });
    }

    private function indexExists(string $indexName): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'workshop_reviews')
            ->where('index_name', $indexName)
            ->exists();
    }

    private function foreignKeyExists(string $constraintName): bool
    {
        return DB::table('information_schema.referential_constraints')
            ->where('constraint_schema', DB::getDatabaseName())
            ->where('table_name', 'workshop_reviews')
            ->where('constraint_name', $constraintName)
            ->exists();
    }
};
