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
            if (! Schema::hasColumn('workshops', 'meeting_code')) {
                $table->string('meeting_code', 64)
                    ->nullable()
                    ->after('meeting_link_cipher')
                    ->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            if (Schema::hasColumn('workshops', 'meeting_code')) {
                $table->dropColumn('meeting_code');
            }
        });
    }
};
