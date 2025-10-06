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
        Schema::create('workshop_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workshop_id');
            $table->string('ip_address', 45); // IPv6 addresses can be up to 45 characters
            $table->string('user_agent')->nullable();
            $table->timestamp('viewed_at');
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('workshop_id')->references('id')->on('workshops')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate views from same IP
            $table->unique(['workshop_id', 'ip_address']);
            
            // Index for better performance
            $table->index(['workshop_id', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_views');
    }
};
