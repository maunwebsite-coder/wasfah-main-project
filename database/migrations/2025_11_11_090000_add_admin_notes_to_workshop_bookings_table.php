<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('workshop_bookings', 'admin_notes')) {
            return;
        }

        Schema::table('workshop_bookings', function (Blueprint $table) {
            $table->text('admin_notes')->nullable()->after('cancellation_reason');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('workshop_bookings', 'admin_notes')) {
            return;
        }

        Schema::table('workshop_bookings', function (Blueprint $table) {
            $table->dropColumn('admin_notes');
        });
    }
};
