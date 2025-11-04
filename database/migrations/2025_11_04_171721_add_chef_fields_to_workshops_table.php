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
            if (!Schema::hasColumn('workshops', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('workshops', 'meeting_provider')) {
                $table->string('meeting_provider', 50)
                    ->default('manual')
                    ->after('meeting_link');
            }

            if (!Schema::hasColumn('workshops', 'jitsi_room')) {
                $table->string('jitsi_room')
                    ->nullable()
                    ->after('meeting_provider');
            }

            if (!Schema::hasColumn('workshops', 'jitsi_passcode')) {
                $table->string('jitsi_passcode', 20)
                    ->nullable()
                    ->after('jitsi_room');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            if (Schema::hasColumn('workshops', 'jitsi_passcode')) {
                $table->dropColumn('jitsi_passcode');
            }

            if (Schema::hasColumn('workshops', 'jitsi_room')) {
                $table->dropColumn('jitsi_room');
            }

            if (Schema::hasColumn('workshops', 'meeting_provider')) {
                $table->dropColumn('meeting_provider');
            }

            if (Schema::hasColumn('workshops', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
