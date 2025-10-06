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
        Schema::create('workshop_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshops')->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained('recipes', 'recipe_id')->onDelete('cascade');
            $table->integer('order')->default(0); // ترتيب الوصفة في الورشة
            $table->timestamps();
            
            // فهرس فريد لمنع تكرار نفس الوصفة في نفس الورشة
            $table->unique(['workshop_id', 'recipe_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_recipes');
    }
};
