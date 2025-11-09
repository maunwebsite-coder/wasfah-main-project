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
        if (!Schema::hasTable('workshop_bookings')) {
            $this->createFullTable();
            return;
        }

        $this->addColumnIfMissing('public_code', function (Blueprint $table) {
            $table->string('public_code', 16)
                ->nullable()
                ->after('id');
        });

        $this->addColumnIfMissing('first_joined_at', function (Blueprint $table) {
            $table->timestamp('first_joined_at')
                ->nullable()
                ->after('confirmed_at');
        });

        $this->addColumnIfMissing('join_device_token', function (Blueprint $table) {
            $table->string('join_device_token', 128)
                ->nullable()
                ->after('first_joined_at');
        });

        $this->addColumnIfMissing('join_device_fingerprint', function (Blueprint $table) {
            $table->string('join_device_fingerprint', 128)
                ->nullable()
                ->after('join_device_token');
        });

        $this->addColumnIfMissing('join_device_ip', function (Blueprint $table) {
            $table->string('join_device_ip', 45)
                ->nullable()
                ->after('join_device_fingerprint');
        });

        $this->addColumnIfMissing('join_device_user_agent', function (Blueprint $table) {
            $table->text('join_device_user_agent')
                ->nullable()
                ->after('join_device_ip');
        });

        $this->addColumnIfMissing('admin_notes', function (Blueprint $table) {
            $table->text('admin_notes')
                ->nullable()
                ->after('cancellation_reason');
        });

        $this->ensureIndexes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank – this migration only repairs missing schema.
    }

    private function createFullTable(): void
    {
        Schema::create('workshop_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('public_code', 16)->nullable();
            $table->unsignedBigInteger('workshop_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->dateTime('booking_date');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->decimal('payment_amount', 8, 2);
            $table->text('notes')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->timestamp('first_joined_at')->nullable();
            $table->string('join_device_token', 128)->nullable();
            $table->string('join_device_fingerprint', 128)->nullable();
            $table->string('join_device_ip', 45)->nullable();
            $table->text('join_device_user_agent')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        $this->ensureIndexes();
    }

    private function addColumnIfMissing(string $column, callable $callback): void
    {
        if (!Schema::hasColumn('workshop_bookings', $column)) {
            Schema::table('workshop_bookings', function (Blueprint $table) use ($callback) {
                $callback($table);
            });
        }
    }

    private function ensureIndexes(): void
    {
        $this->addIndexIfMissing('workshop_bookings_public_code_unique', function (Blueprint $table) {
            $table->unique('public_code', 'workshop_bookings_public_code_unique');
        });

        $this->addIndexIfMissing('workshop_bookings_workshop_status_index', function (Blueprint $table) {
            $table->index(['workshop_id', 'status'], 'workshop_bookings_workshop_status_index');
        });

        $this->addIndexIfMissing('workshop_bookings_user_status_index', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'workshop_bookings_user_status_index');
        });

        $this->addIndexIfMissing('workshop_bookings_workshop_user_unique', function (Blueprint $table) {
            $table->unique(['workshop_id', 'user_id'], 'workshop_bookings_workshop_user_unique');
        });
    }

    private function addIndexIfMissing(string $indexName, callable $callback): void
    {
        if ($this->indexMissing($indexName)) {
            Schema::table('workshop_bookings', function (Blueprint $table) use ($callback) {
                $callback($table);
            });
        }
    }

    private function indexMissing(string $indexName): bool
    {
        return !DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'workshop_bookings')
            ->where('index_name', $indexName)
            ->exists();
    }
};
