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
        Schema::create('chef_link_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chef_link_page_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('url');
            $table->string('icon')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['chef_link_page_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chef_link_items');
    }
};
