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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('policies_accepted_at')
                ->nullable()
                ->after('referral_admin_notes');

            $table->string('policies_accepted_ip', 45)
                ->nullable()
                ->after('policies_accepted_at');

            $table->string('policies_version', 20)
                ->nullable()
                ->after('policies_accepted_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'policies_accepted_at',
                'policies_accepted_ip',
                'policies_version',
            ]);
        });
    }
};

