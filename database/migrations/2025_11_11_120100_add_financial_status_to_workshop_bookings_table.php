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
            if (!Schema::hasColumn('workshop_bookings', 'financial_status')) {
                $table->string('financial_status', 20)
                    ->default('pending')
                    ->after('payment_amount')
                    ->index();
            }

            if (!Schema::hasColumn('workshop_bookings', 'financial_split_at')) {
                $table->timestamp('financial_split_at')
                    ->nullable()
                    ->after('financial_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('workshop_bookings', 'financial_split_at')) {
                $table->dropColumn('financial_split_at');
            }

            if (Schema::hasColumn('workshop_bookings', 'financial_status')) {
                $table->dropColumn('financial_status');
            }
        });
    }
};
