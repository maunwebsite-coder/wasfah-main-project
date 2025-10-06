<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_interactions', function (Blueprint $table) {
            // إضافة الـ unique constraint باسم واضح
            $table->unique(['user_id', 'recipe_id'], 'user_interactions_user_id_recipe_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('user_interactions', function (Blueprint $table) {
            // حذف الـ unique باستخدام نفس الاسم
            $table->dropUnique('user_interactions_user_id_recipe_id_unique');
        });
    }
};
