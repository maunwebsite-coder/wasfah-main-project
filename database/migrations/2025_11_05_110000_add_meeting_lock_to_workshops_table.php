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
            if (!Schema::hasColumn('workshops', 'meeting_locked_at')) {
                $table->timestamp('meeting_locked_at')
                    ->nullable()
                    ->after('meeting_started_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            if (Schema::hasColumn('workshops', 'meeting_locked_at')) {
                $table->dropColumn('meeting_locked_at');
            }
        });
    }
};

