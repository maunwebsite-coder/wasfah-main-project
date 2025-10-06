<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInteraction extends Model
{
    use HasFactory;

    // المفتاح الأساسي (لأنه اسمه interaction_id مش id)
    protected $primaryKey = 'interaction_id';

    // الحقول المسموح تعمل عليها mass assignment
    protected $fillable = [
        'user_id',
        'recipe_id',
        'is_saved',
        'is_made',
        'rating',
    ];

    // تحديد القيم الافتراضية
    protected $attributes = [
        'is_saved' => false,
        'is_made' => false,
    ];

    // تحديد نوع البيانات
    protected $casts = [
        'is_saved' => 'boolean',
        'is_made' => 'boolean',
        'rating' => 'integer',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * العلاقة مع الوصفة
     */
    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id', 'recipe_id');
    }

    /**
     * دالة مخصصة لتخزين أو تحديث التفاعل
     */
    public static function storeOrUpdate($userId, $recipeId, $data)
    {
        return self::updateOrCreate(
            [
                'user_id'   => $userId,
                'recipe_id' => $recipeId,
            ],
            $data
        );
    }


    

}
