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
        // إضافة فهارس للوصفات
        Schema::table('recipes', function (Blueprint $table) {
            // فهارس للبحث في النص
            $table->index('title');
            $table->index('description');
            $table->index('author');
            
            // فهارس مركبة للبحث السريع
            $table->index(['title', 'created_at']);
            $table->index(['category_id', 'created_at']);
            
            // فهارس للترتيب
            $table->index('created_at');
        });
        
        // إضافة فهارس للورشات
        Schema::table('workshops', function (Blueprint $table) {
            // فهارس للبحث في النص
            $table->index('title');
            $table->index('description');
            $table->index('instructor');
            $table->index('category');
            
            // فهارس للتصفية
            $table->index('is_active');
            $table->index('is_online');
            $table->index('level');
            $table->index('start_date');
            $table->index('price');
            
            // فهارس مركبة للبحث السريع
            $table->index(['title', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index(['instructor', 'is_active']);
            $table->index(['is_online', 'start_date']);
            
            // فهارس للترتيب
            $table->index(['rating', 'reviews_count']);
            $table->index(['bookings_count', 'created_at']);
        });
        
        // إضافة فهارس للمكونات
        Schema::table('ingredients', function (Blueprint $table) {
            $table->index('name');
        });
        
        // إضافة فهارس للفئات
        Schema::table('categories', function (Blueprint $table) {
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إزالة فهارس الوصفات
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropIndex(['title']);
            $table->dropIndex(['description']);
            $table->dropIndex(['author']);
            $table->dropIndex(['title', 'created_at']);
            $table->dropIndex(['category_id', 'created_at']);
            $table->dropIndex(['created_at']);
        });
        
        // إزالة فهارس الورشات
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropIndex(['title']);
            $table->dropIndex(['description']);
            $table->dropIndex(['instructor']);
            $table->dropIndex(['category']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_online']);
            $table->dropIndex(['level']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['price']);
            $table->dropIndex(['title', 'is_active']);
            $table->dropIndex(['category', 'is_active']);
            $table->dropIndex(['instructor', 'is_active']);
            $table->dropIndex(['is_online', 'start_date']);
            $table->dropIndex(['rating', 'reviews_count']);
            $table->dropIndex(['bookings_count', 'created_at']);
        });
        
        // إزالة فهارس المكونات
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
        
        // إزالة فهارس الفئات
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
};