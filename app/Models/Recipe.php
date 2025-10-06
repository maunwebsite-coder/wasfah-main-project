<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    // المفتاح الأساسي (لأنك مستخدم recipe_id مش id)
    protected $primaryKey = 'recipe_id';

    // لو المفتاح الأساسي مش auto-increment لازم تحدد كمان
    public $incrementing = true;

    // لو نوع المفتاح مش int (مثلاً uuid) تحدد
    protected $keyType = 'int';

    // الحقول القابلة للـ mass assignment
    protected $fillable = [
        'title',
        'slug',
        'description',
        'author',
        'image_url',
        'image',
        'image_2',
        'image_3',
        'image_4',
        'image_5',
        'video_url',
        'category_id',
        'prep_time',
        'cook_time',
        'servings',
        'difficulty',
        'steps',
        'tools',
        'registration_deadline',
    ];

    // ✅ أضف هذا الجزء للتعامل مع حقل Steps كمصفوفة
    protected $casts = [
        'steps' => 'array',
        'tools' => 'array',
        'registration_deadline' => 'datetime',
        'prep_time' => 'integer',
        'cook_time' => 'integer',
        'servings' => 'integer',
    ];

    /**
     * العلاقات
     */

    // العلاقة مع التصنيف
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    // العلاقة مع المكونات
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class, 'recipe_id', 'recipe_id');
    }

    // author هو اسم المستخدم المخزن مباشرة في الجدول
    // لا نحتاج علاقة مع جدول المستخدمين

    // العلاقة مع التفاعلات (تقييمات، إعجابات...)
    public function interactions()
    {
        return $this->hasMany(UserInteraction::class, 'recipe_id', 'recipe_id');
    }

    // العلاقة مع الورشات (many-to-many)
    public function workshops()
    {
        return $this->belongsToMany(Workshop::class, 'workshop_recipes', 'recipe_id', 'workshop_id')
                    ->withPivot('order')
                    ->orderBy('workshop_recipes.order');
    }

    /**
     * الحصول على رابط الصورة
     */
    public function getImageUrlAttribute()
    {
        // إذا كان هناك رابط صورة خارجي (مثل Unsplash)
        if (isset($this->attributes['image_url']) && $this->attributes['image_url']) {
            // تحقق إذا كان الرابط خارجي (يبدأ بـ http)
            if (str_starts_with($this->attributes['image_url'], 'http')) {
                return $this->attributes['image_url'];
            }
            // إذا كان الرابط محلي، أضف storage path
            return \Storage::disk('public')->url($this->attributes['image_url']);
        }
        
        // إذا كان هناك ملف صورة محفوظ محلياً
        if (isset($this->attributes['image']) && $this->attributes['image']) {
            return \Storage::disk('public')->url($this->attributes['image']);
        }
        
        return null;
    }

    /**
     * الحصول على جميع الصور
     */
    public function getAllImages()
    {
        $images = [];
        
        // إضافة الصورة الرئيسية
        if ($this->image) {
            $images[] = \Storage::disk('public')->url($this->image);
        }
        
        // إضافة الصور الإضافية
        for ($i = 2; $i <= 5; $i++) {
            $imageField = "image_{$i}";
            if ($this->$imageField) {
                $images[] = \Storage::disk('public')->url($this->$imageField);
            }
        }
        
        return $images;
    }

    /**
     * الحصول على رابط صورة محددة
     */
    public function getImageUrl($imageNumber = 1)
    {
        if ($imageNumber == 1) {
            return $this->getImageUrlAttribute();
        }
        
        $imageField = "image_{$imageNumber}";
        if (isset($this->attributes[$imageField]) && $this->attributes[$imageField]) {
            return \Storage::disk('public')->url($this->attributes[$imageField]);
        }
        
        return null;
    }

    /**
     * التحقق من انتهاء مهلة الحجز
     */
    public function getIsRegistrationOpenAttribute()
    {
        if (!$this->registration_deadline) {
            return true; // إذا لم يتم تحديد موعد انتهاء، الوصفة متاحة دائماً
        }
        
        return now() < $this->registration_deadline;
    }

    /**
     * التحقق من انتهاء مهلة الحجز (للاستخدام في Blade)
     */
    public function getIsRegistrationClosedAttribute()
    {
        return !$this->is_registration_open;
    }

    /**
     * Generate slug from title
     */
    public function generateSlug()
    {
        $slug = \Str::slug($this->title, '-', 'ar');
        
        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;
        
        while (static::where('slug', $slug)->where('recipe_id', '!=', $this->recipe_id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($recipe) {
            if (empty($recipe->slug)) {
                $recipe->slug = $recipe->generateSlug();
            }
        });
        
        static::updating(function ($recipe) {
            if ($recipe->isDirty('title') && empty($recipe->slug)) {
                $recipe->slug = $recipe->generateSlug();
            }
        });
    }

    /**
     * Get route key name for model binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
