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
            $table->timestamp('host_first_joined_at')
                ->nullable()
                ->after('meeting_locked_at');

            $table->string('host_join_device_token', 128)
                ->nullable()
                ->after('host_first_joined_at');

            $table->string('host_join_device_fingerprint', 128)
                ->nullable()
                ->after('host_join_device_token');

            $table->string('host_join_device_ip', 45)
                ->nullable()
                ->after('host_join_device_fingerprint');

            $table->text('host_join_device_user_agent')
                ->nullable()
                ->after('host_join_device_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn([
                'host_first_joined_at',
                'host_join_device_token',
                'host_join_device_fingerprint',
                'host_join_device_ip',
                'host_join_device_user_agent',
            ]);
        });
    }
};
