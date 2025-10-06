# نظام إدارة الرؤية - Visibility Management System

## نظرة عامة

نظام إدارة الرؤية هو نظام شامل لإدارة إظهار وإخفاء أجزاء مختلفة من الموقع. يتيح للمديرين التحكم في رؤية المستخدمين لأقسام معينة من الموقع.

## المكونات الرئيسية

### 1. VisibilityHelper.php
الكلاس الرئيسي لإدارة إعدادات الرؤية:
- `getVisibilitySetting($section, $default)` - الحصول على إعداد رؤية قسم معين
- `setVisibilitySetting($section, $isVisible)` - تعيين إعداد رؤية قسم معين
- `isVisible($section)` - التحقق من رؤية قسم معين
- `hideSection($section)` - إخفاء قسم معين
- `showSection($section)` - إظهار قسم معين
- `toggleSection($section)` - تبديل رؤية قسم معين
- `getAllSettings()` - الحصول على جميع الإعدادات
- `clearCache()` - مسح الذاكرة المؤقتة

### 2. BladeVisibilityHelper.php
مساعد Blade للاستخدام في القوالب:
- `isVisible($section)` - التحقق من رؤية قسم في القوالب
- `getVisibilityClass($section)` - الحصول على فئة CSS للرؤية
- `getVisibilityStyle($section)` - الحصول على نمط CSS للرؤية
- `renderSection($section, $content)` - عرض قسم مع التحقق من الرؤية

### 3. VisibilityController.php
تحكم إدارة الرؤية:
- `index()` - عرض صفحة إدارة الرؤية
- `update($section)` - تحديث إعداد قسم معين
- `toggle($section)` - تبديل رؤية قسم معين
- `getConfig()` - الحصول على إعدادات الرؤية للواجهة الأمامية
- `clearCache()` - مسح الذاكرة المؤقتة
- `initializeDefaults()` - تهيئة الإعدادات الافتراضية
- `bulkUpdate()` - تحديث جماعي للإعدادات

## الاستخدام

### في القوالب (Blade)

```blade
<!-- التحقق من رؤية قسم معين -->
@if(\App\Helpers\BladeVisibilityHelper::isVisible('header'))
    <header>محتوى الهيدر</header>
@endif

<!-- استخدام فئة الرؤية -->
<div class="{{ \App\Helpers\BladeVisibilityHelper::getVisibilityClass('navigation') }}">
    محتوى التنقل
</div>

<!-- استخدام نمط الرؤية -->
<div style="{{ \App\Helpers\BladeVisibilityHelper::getVisibilityStyle('footer') }}">
    محتوى الفوتر
</div>

<!-- عرض قسم مع التحقق من الرؤية -->
{!! \App\Helpers\BladeVisibilityHelper::renderSection('sidebar', $sidebarContent) !!}
```

### في JavaScript

```javascript
// التحقق من رؤية قسم
if (window.visibilityManager.isSectionVisible('header')) {
    // القسم مرئي
}

// إخفاء قسم
window.visibilityManager.hideSection('sidebar');

// إظهار قسم
window.visibilityManager.showSection('navigation');

// تبديل رؤية قسم
window.visibilityManager.toggleSectionVisibility('footer');

// تحديث إعدادات متعددة
window.visibilityManager.updateSettings({
    'header': true,
    'footer': false,
    'sidebar': true
});
```

### في PHP

```php
use App\Helpers\VisibilityHelper;

// التحقق من رؤية قسم
if (VisibilityHelper::isVisible('header')) {
    // القسم مرئي
}

// إخفاء قسم
VisibilityHelper::hideSection('sidebar');

// إظهار قسم
VisibilityHelper::showSection('navigation');

// تبديل رؤية قسم
VisibilityHelper::toggleSection('footer');

// الحصول على جميع الإعدادات
$settings = VisibilityHelper::getAllSettings();
```

## الأقسام المدعومة

- `header` - الهيدر
- `navigation` - شريط التنقل
- `footer` - الفوتر
- `sidebar` - الشريط الجانبي
- `search` - البحث
- `recipes` - الوصفات
- `tools` - الأدوات
- `workshops` - الورشات
- `notifications` - الإشعارات
- `profile` - الملف الشخصي
- `admin` - لوحة الإدارة

## المسارات

- `GET /admin/visibility` - صفحة إدارة الرؤية
- `PUT /admin/visibility/{section}` - تحديث إعداد قسم معين
- `POST /admin/visibility/{section}/toggle` - تبديل رؤية قسم معين
- `GET /admin/visibility/config` - الحصول على إعدادات الرؤية
- `POST /admin/visibility/clear-cache` - مسح الذاكرة المؤقتة
- `POST /admin/visibility/initialize-defaults` - تهيئة الإعدادات الافتراضية
- `POST /admin/visibility/bulk-update` - تحديث جماعي

## الملفات المطلوبة

### CSS
```html
<link rel="stylesheet" href="{{ asset('css/visibility-styles.css') }}">
```

### JavaScript
```html
<script src="{{ asset('js/visibility-manager.js') }}"></script>
```

## إعداد قاعدة البيانات

تم إنشاء جدول `visibility_settings` مع الأعمدة التالية:
- `id` - المعرف الفريد
- `section` - اسم القسم
- `is_visible` - حالة الرؤية (true/false)
- `description` - وصف القسم
- `page_name` - اسم الصفحة
- `section_name` - اسم القسم
- `element_key` - مفتاح العنصر
- `created_at` - تاريخ الإنشاء
- `updated_at` - تاريخ التحديث

## الذاكرة المؤقتة

النظام يستخدم الذاكرة المؤقتة لتسريع الوصول للإعدادات. يمكن مسح الذاكرة المؤقتة باستخدام:
- `VisibilityHelper::clearCache()`
- أو من خلال واجهة الإدارة

## الأمان

- جميع المسارات محمية بـ middleware المصادقة والإدارة
- التحقق من صلاحيات المستخدم قبل عرض أقسام الإدارة
- استخدام CSRF tokens في جميع الطلبات

## التخصيص

يمكن إضافة أقسام جديدة من خلال:
1. إضافة القسم إلى قاعدة البيانات
2. تحديث `VisibilityHelper` إذا لزم الأمر
3. إضافة CSS styles للقسم الجديد
4. تحديث JavaScript selectors

## استكشاف الأخطاء

### مشاكل شائعة:
1. **قسم لا يظهر**: تحقق من إعدادات الرؤية في قاعدة البيانات
2. **تغييرات لا تظهر**: امسح الذاكرة المؤقتة
3. **أخطاء JavaScript**: تحقق من تحميل ملف `visibility-manager.js`
4. **مشاكل CSS**: تحقق من تحميل ملف `visibility-styles.css`

### أوامر مفيدة:
```bash
# مسح الذاكرة المؤقتة
php artisan cache:clear

# إعادة تهيئة الإعدادات
php artisan db:seed --class=VisibilitySettingsSeeder

# تشغيل المايجريشن
php artisan migrate
```

## الدعم

للحصول على الدعم أو الإبلاغ عن مشاكل، يرجى التواصل مع فريق التطوير.
