<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // المفتاح الأساسي
    protected $primaryKey = 'category_id';

    // Laravel بافتراضه بيستخدم auto increment و integer، فممكن نثبتهم
    public $incrementing = true;
    protected $keyType = 'int';

    // الأعمدة اللي مسموح تتعبى
    protected $fillable = ['name'];

    // العلاقة مع الوصفات
    public function recipes()
    {
        // لازم تحدد المفتاحين (foreign key و local key) عشان ما يدور على id
        return $this->hasMany(Recipe::class, 'category_id', 'category_id');
    }
}
