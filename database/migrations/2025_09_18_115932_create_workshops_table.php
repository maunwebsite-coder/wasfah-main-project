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
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان الورشة
            $table->text('description'); // وصف الورشة
            $table->text('content')->nullable(); // محتوى الورشة التفصيلي
            $table->string('instructor'); // اسم المدرب
            $table->string('instructor_avatar')->nullable(); // صورة المدرب
            $table->text('instructor_bio')->nullable(); // نبذة عن المدرب
            $table->string('category'); // فئة الورشة (طبخ، حلويات، إلخ)
            $table->string('level'); // مستوى الورشة (مبتدئ، متوسط، متقدم)
            $table->integer('duration'); // مدة الورشة بالدقائق
            $table->integer('max_participants')->default(20); // الحد الأقصى للمشاركين
            $table->decimal('price', 8, 2); // سعر الورشة
            $table->string('currency', 3)->default('USD'); // العملة
            $table->string('image')->nullable(); // صورة الورشة
            $table->json('images')->nullable(); // صور إضافية للورشة
            $table->string('location'); // موقع الورشة
            $table->string('address')->nullable(); // العنوان التفصيلي
            $table->decimal('latitude', 10, 8)->nullable(); // خط العرض
            $table->decimal('longitude', 11, 8)->nullable(); // خط الطول
            $table->datetime('start_date'); // تاريخ ووقت بداية الورشة
            $table->datetime('end_date'); // تاريخ ووقت انتهاء الورشة
            $table->datetime('registration_deadline')->nullable(); // آخر موعد للتسجيل
            $table->boolean('is_online')->default(false); // هل الورشة أونلاين؟
            $table->string('meeting_link')->nullable(); // رابط الاجتماع (للورشات الأونلاين)
            $table->text('requirements')->nullable(); // متطلبات الورشة
            $table->text('what_you_will_learn')->nullable(); // ما سيتعلمه المشارك
            $table->text('materials_needed')->nullable(); // المواد المطلوبة
            $table->boolean('is_active')->default(true); // هل الورشة نشطة؟
            $table->boolean('is_featured')->default(false); // هل الورشة مميزة؟
            $table->integer('views_count')->default(0); // عدد المشاهدات
            $table->integer('bookings_count')->default(0); // عدد الحجوزات
            $table->decimal('rating', 3, 2)->default(0); // تقييم الورشة
            $table->integer('reviews_count')->default(0); // عدد التقييمات
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshops');
    }
};
