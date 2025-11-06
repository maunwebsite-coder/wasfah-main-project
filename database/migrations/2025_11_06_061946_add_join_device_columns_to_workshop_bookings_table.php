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
        Schema::table('workshop_bookings', function (Blueprint $table) {
            $table->timestamp('first_joined_at')
                ->nullable()
                ->after('confirmed_at');

            $table->string('join_device_token', 128)
                ->nullable()
                ->after('first_joined_at');

            $table->string('join_device_fingerprint', 128)
                ->nullable()
                ->after('join_device_token');

            $table->string('join_device_ip', 45)
                ->nullable()
                ->after('join_device_fingerprint');

            $table->text('join_device_user_agent')
                ->nullable()
                ->after('join_device_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'first_joined_at',
                'join_device_token',
                'join_device_fingerprint',
                'join_device_ip',
                'join_device_user_agent',
            ]);
        });
    }
};
