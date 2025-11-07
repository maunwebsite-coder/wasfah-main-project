<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hero_slides')) {
            Schema::create('hero_slides', function (Blueprint $table) {
                $table->id();
                $table->string('badge')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('image_alt')->nullable();
                $table->string('desktop_image_path')->nullable();
                $table->string('mobile_image_path')->nullable();
                $table->json('features')->nullable();
                $table->json('actions')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hero_slides')) {
            Schema::dropIfExists('hero_slides');
        }
    }
};
