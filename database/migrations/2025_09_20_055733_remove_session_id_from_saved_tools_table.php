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
        Schema::table('saved_tools', function (Blueprint $table) {
            // جعل user_id غير nullable
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_tools', function (Blueprint $table) {
            // إضافة session_id column مرة أخرى
            $table->string('session_id')->nullable();
            
            // إضافة unique constraint للـ session_id
            $table->unique(['session_id', 'tool_id']);
            
            // جعل user_id nullable مرة أخرى
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }
};
