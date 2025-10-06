# منع تكرار الورشات المميزة - Featured Workshop Constraint

## نظرة عامة
تم تطبيق نظام لمنع تكرار الورشات المميزة (is_featured = true) لضمان أن ورشة واحدة فقط يمكن أن تكون مميزة في نفس الوقت.

## التغييرات المطبقة

### 1. قاعدة البيانات
- **Migration**: `2025_09_22_171839_add_featured_workshop_constraint.php`
- **Trigger**: تم إنشاء trigger للتحقق من عدم تكرار الورشات المميزة
- **الوظيفة**: يمنع إدراج أو تحديث ورشة جديدة كمميزة إذا كانت هناك ورشة مميزة أخرى

### 2. النموذج (Model)
- **الملف**: `app/Models/Workshop.php`
- **التحسينات**:
  - إضافة `validationRules()` مع validation مخصص للورشات المميزة
  - إضافة `makeFeatured()` method لجعل ورشة مميزة مع إلغاء تمييز الباقي
  - إضافة `removeFeatured()` method لإلغاء التمييز
  - إضافة `hasOtherFeaturedWorkshop()` method للتحقق من وجود ورشة مميزة أخرى

### 3. المتحكم (Controller)
- **الملف**: `app/Http/Controllers/Admin/WorkshopController.php`
- **التحسينات**:
  - استخدام `Workshop::validationRules()` في دالتي `store()` و `update()`
  - استخدام `makeFeatured()` method في `toggleFeatured()`
  - إضافة `checkFeatured()` method للتحقق من وجود ورشة مميزة عبر AJAX

### 4. الواجهة الأمامية (Frontend)
- **الملفات المحدثة**:
  - `resources/views/admin/workshops/index.blade.php`
  - `resources/views/admin/workshops/create.blade.php`
  - `resources/views/admin/workshops/edit.blade.php`

- **التحسينات**:
  - إضافة JavaScript للتحقق من وجود ورشة مميزة قبل جعل ورشة جديدة مميزة
  - إضافة رسائل تأكيد للمستخدم
  - منع إرسال النموذج إذا رفض المستخدم الاستبدال

### 5. المسارات (Routes)
- **الملف**: `routes/web.php`
- **إضافة**: `GET /admin/workshops/check-featured` للتحقق من وجود ورشة مميزة

## كيفية العمل

### 1. على مستوى قاعدة البيانات
```sql
-- Trigger يمنع إدراج ورشة مميزة جديدة إذا كانت هناك ورشة مميزة موجودة
CREATE TRIGGER check_featured_workshop_before_insert
BEFORE INSERT ON workshops
FOR EACH ROW
BEGIN
    IF NEW.is_featured = 1 THEN
        IF (SELECT COUNT(*) FROM workshops WHERE is_featured = 1) > 0 THEN
            SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "يمكن أن تكون ورشة واحدة فقط مميزة في نفس الوقت";
        END IF;
    END IF;
END
```

### 2. على مستوى التطبيق
```php
// في Workshop Model
public function makeFeatured()
{
    // إلغاء تمييز جميع الورشات الأخرى
    static::where('is_featured', true)->update(['is_featured' => false]);
    
    // جعل هذه الورشة مميزة
    $this->is_featured = true;
    $this->save();
    
    return $this;
}
```

### 3. على مستوى الواجهة
```javascript
// التحقق من وجود ورشة مميزة قبل جعل ورشة جديدة مميزة
if (hasFeatured) {
    if (!confirm('يوجد ورشة مميزة حالياً. هل تريد جعل هذه الورشة هي الورشة المميزة الجديدة؟')) {
        this.checked = false;
    }
}
```

## الميزات

### ✅ الحماية على مستوى قاعدة البيانات
- Trigger يمنع التكرار حتى لو تم تجاوز validation التطبيق

### ✅ Validation على مستوى التطبيق
- Validation rules تمنع التكرار في النماذج
- رسائل خطأ واضحة باللغة العربية

### ✅ تحسين تجربة المستخدم
- رسائل تأكيد قبل الاستبدال
- منع الإرسال إذا رفض المستخدم
- تحقق فوري عند تغيير checkbox

### ✅ سهولة الاستخدام
- استبدال تلقائي للورشة المميزة السابقة
- واجهة بديهية وواضحة

## الاختبار

### 1. اختبار قاعدة البيانات
```sql
-- محاولة إدراج ورشتين مميزتين
INSERT INTO workshops (title, is_featured) VALUES ('ورشة 1', 1);
INSERT INTO workshops (title, is_featured) VALUES ('ورشة 2', 1); -- سيفشل
```

### 2. اختبار التطبيق
- إنشاء ورشة جديدة كمميزة
- محاولة إنشاء ورشة ثانية كمميزة
- التحقق من رسائل الخطأ

### 3. اختبار الواجهة
- فتح صفحة إنشاء ورشة
- تفعيل checkbox "ورشة مميزة"
- التحقق من رسالة التأكيد

## الصيانة

### إزالة القيود (إذا لزم الأمر)
```sql
DROP TRIGGER IF EXISTS check_featured_workshop_before_insert;
DROP TRIGGER IF EXISTS check_featured_workshop_before_update;
```

### تحديث الـ migration
```bash
php artisan migrate:rollback --step=1
```

## الخلاصة
تم تطبيق نظام شامل لمنع تكرار الورشات المميزة على جميع المستويات (قاعدة البيانات، التطبيق، الواجهة) مع ضمان تجربة مستخدم سلسة وآمنة.
