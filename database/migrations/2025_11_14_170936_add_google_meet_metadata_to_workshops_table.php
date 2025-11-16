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
            if (!Schema::hasColumn('workshops', 'meeting_event_id')) {
                $table->string('meeting_event_id', 191)
                    ->nullable()
                    ->after('meeting_code');
            }

            if (!Schema::hasColumn('workshops', 'meeting_calendar_id')) {
                $table->string('meeting_calendar_id', 191)
                    ->nullable()
                    ->after('meeting_event_id');
            }

            if (!Schema::hasColumn('workshops', 'meeting_conference_id')) {
                $table->string('meeting_conference_id', 191)
                    ->nullable()
                    ->after('meeting_calendar_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            if (Schema::hasColumn('workshops', 'meeting_conference_id')) {
                $table->dropColumn('meeting_conference_id');
            }

            if (Schema::hasColumn('workshops', 'meeting_calendar_id')) {
                $table->dropColumn('meeting_calendar_id');
            }

            if (Schema::hasColumn('workshops', 'meeting_event_id')) {
                $table->dropColumn('meeting_event_id');
            }
        });
    }
};
