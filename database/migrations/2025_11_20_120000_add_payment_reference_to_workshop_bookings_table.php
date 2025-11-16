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
            if (! Schema::hasColumn('workshop_bookings', 'payment_reference')) {
                $table->string('payment_reference', 191)
                    ->nullable()
                    ->after('payment_method');
            }

            if (! Schema::hasColumn('workshop_bookings', 'payment_payload')) {
                $table->json('payment_payload')
                    ->nullable()
                    ->after('payment_reference');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('workshop_bookings', 'payment_payload')) {
                $table->dropColumn('payment_payload');
            }

            if (Schema::hasColumn('workshop_bookings', 'payment_reference')) {
                $table->dropColumn('payment_reference');
            }
        });
    }
};
