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
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone', 64)->nullable()->after('phone');
            }
        });

        $defaultTimezone = config('app.timezone', 'UTC');

        Schema::table('workshops', function (Blueprint $table) use ($defaultTimezone): void {
            if (! Schema::hasColumn('workshops', 'host_timezone')) {
                $table->string('host_timezone', 64)
                    ->nullable()
                    ->default($defaultTimezone)
                    ->after('registration_deadline');
            }
        });

        DB::table('workshops')
            ->whereNull('host_timezone')
            ->update(['host_timezone' => $defaultTimezone]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table): void {
            if (Schema::hasColumn('workshops', 'host_timezone')) {
                $table->dropColumn('host_timezone');
            }
        });

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'timezone')) {
                $table->dropColumn('timezone');
            }
        });
    }
};
