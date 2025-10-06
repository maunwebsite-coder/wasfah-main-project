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
        // إضافة حقول معلومات الضغط للوصفات
        Schema::table('recipes', function (Blueprint $table) {
            $table->boolean('image_compressed')->default(false)->after('image');
            $table->integer('original_image_size')->nullable()->after('image_compressed');
            $table->integer('compressed_image_size')->nullable()->after('original_image_size');
            $table->timestamp('image_compressed_at')->nullable()->after('compressed_image_size');
        });

        // إضافة حقول معلومات الضغط للورشات
        Schema::table('workshops', function (Blueprint $table) {
            $table->boolean('image_compressed')->default(false)->after('image');
            $table->integer('original_image_size')->nullable()->after('image_compressed');
            $table->integer('compressed_image_size')->nullable()->after('original_image_size');
            $table->timestamp('image_compressed_at')->nullable()->after('compressed_image_size');
        });

        // إضافة حقول معلومات الضغط للأدوات
        Schema::table('tools', function (Blueprint $table) {
            $table->boolean('image_compressed')->default(false)->after('image');
            $table->integer('original_image_size')->nullable()->after('image_compressed');
            $table->integer('compressed_image_size')->nullable()->after('original_image_size');
            $table->timestamp('image_compressed_at')->nullable()->after('compressed_image_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['image_compressed', 'original_image_size', 'compressed_image_size', 'image_compressed_at']);
        });

        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn(['image_compressed', 'original_image_size', 'compressed_image_size', 'image_compressed_at']);
        });

        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn(['image_compressed', 'original_image_size', 'compressed_image_size', 'image_compressed_at']);
        });
    }
};