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
        Schema::table('notifications', function (Blueprint $table) {
            // فهارس محسنة للأداء
            $table->index(['user_id', 'is_read', 'created_at'], 'notifications_user_read_created_idx');
            $table->index(['user_id', 'created_at'], 'notifications_user_created_idx');
            $table->index(['is_read', 'created_at'], 'notifications_read_created_idx');
            $table->index('type', 'notifications_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_read_created_idx');
            $table->dropIndex('notifications_user_created_idx');
            $table->dropIndex('notifications_read_created_idx');
            $table->dropIndex('notifications_type_idx');
        });
    }
};