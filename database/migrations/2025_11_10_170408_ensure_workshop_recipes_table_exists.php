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
        if (Schema::hasTable('workshop_recipes')) {
            return;
        }

        Schema::create('workshop_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')
                ->constrained('workshops')
                ->onDelete('cascade');
            $table->foreignId('recipe_id')
                ->constrained('recipes', 'recipe_id')
                ->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->unique(['workshop_id', 'recipe_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to roll back because we only create the table when it is missing.
    }
};
