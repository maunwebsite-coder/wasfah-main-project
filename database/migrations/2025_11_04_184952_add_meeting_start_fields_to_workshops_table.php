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
        Schema::table('workshops', function (Blueprint $table) {
            if (!Schema::hasColumn('workshops', 'meeting_started_at')) {
                $table->timestamp('meeting_started_at')->nullable()->after('meeting_provider');
            }

            if (!Schema::hasColumn('workshops', 'meeting_started_by')) {
                $table->foreignId('meeting_started_by')
                    ->nullable()
                    ->after('meeting_started_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            if (Schema::hasColumn('workshops', 'meeting_started_by')) {
                $table->dropConstrainedForeignId('meeting_started_by');
            }

            if (Schema::hasColumn('workshops', 'meeting_started_at')) {
                $table->dropColumn('meeting_started_at');
            }
        });
    }
};
