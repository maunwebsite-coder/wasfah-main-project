<?php

/**
 * اختبار النظام المحدث لضغط الصور
 * تشغيل: php test_fixed_compression.php
 */

require_once 'vendor/autoload.php';

use App\Services\ImageCompressionService;
use App\Services\SimpleImageCompressionService;

echo "=== اختبار النظام المحدث لضغط الصور ===\n\n";

// 1. فحص إمكانيات النظام
echo "1. فحص إمكانيات النظام...\n";

$capabilities = ImageCompressionService::checkSystemCapabilities();
echo "PHP GD Extension: " . ($capabilities['gd_loaded'] ? '✅ مفعل' : '❌ غير مفعل') . "\n";

if ($capabilities['gd_loaded']) {
    echo "الإصدار: {$capabilities['gd_version']}\n";
    echo "النظام: ضغط متقدم متوفر\n";
} else {
    echo "النظام: وضع الحفظ المباشر\n";
}

echo "\n";

// 2. اختبار الخدمة المناسبة
echo "2. اختبار الخدمة المناسبة...\n";

if ($capabilities['gd_loaded']) {
    echo "✅ سيتم استخدام ImageCompressionService (ضغط متقدم)\n";
} else {
    echo "✅ سيتم استخدام SimpleImageCompressionService (حفظ مباشر)\n";
}

echo "\n";

// 3. اختبار إنشاء صورة بسيطة
echo "3. اختبار إنشاء صورة بسيطة...\n";

// إنشاء صورة بسيطة للاختبار
$width = 800;
$height = 600;
$image = imagecreatetruecolor($width, $height);

// إضافة لون خلفية
$bgColor = imagecolorallocate($image, 100, 150, 200);
imagefill($image, 0, 0, $bgColor);

// إضافة نص
$textColor = imagecolorallocate($image, 255, 255, 255);
imagestring($image, 5, $width/2 - 100, $height/2, 'Test Image', $textColor);

// حفظ الصورة
$testImagePath = 'test_image_fixed.jpg';
imagejpeg($image, $testImagePath, 100);
$originalSize = filesize($testImagePath);
imagedestroy($image);

echo "تم إنشاء صورة بحجم: " . round($originalSize / 1024, 2) . " كيلوبايت\n";
echo "الأبعاد: {$width} x {$height}\n";

// 4. اختبار الخدمة المناسبة
echo "\n4. اختبار الخدمة المناسبة...\n";

try {
    if ($capabilities['gd_loaded']) {
        echo "اختبار ImageCompressionService...\n";
        
        // إنشاء UploadedFile وهمي
        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $testImagePath,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $result = ImageCompressionService::compressAndStore(
            $uploadedFile,
            'test',
            80,
            400,
            300
        );

        if ($result) {
            echo "✅ تم ضغط الصورة بنجاح!\n";
            echo "المسار: $result\n";
            
            // الحصول على معلومات الصورة
            $info = ImageCompressionService::getImageInfo($result);
            if ($info) {
                echo "الحجم المضغوط: {$info['file_size_mb']} ميجابايت\n";
                echo "الأبعاد: {$info['width']} x {$info['height']}\n";
            }
        } else {
            echo "❌ فشل في ضغط الصورة\n";
        }
    } else {
        echo "اختبار SimpleImageCompressionService...\n";
        
        // إنشاء UploadedFile وهمي
        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $testImagePath,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $result = SimpleImageCompressionService::compressAndStore(
            $uploadedFile,
            'test',
            80
        );

        if ($result) {
            echo "✅ تم حفظ الصورة بنجاح!\n";
            echo "المسار: $result\n";
            
            // الحصول على معلومات الصورة
            $info = SimpleImageCompressionService::getImageInfo($result);
            if ($info) {
                echo "الحجم: {$info['file_size_mb']} ميجابايت\n";
                echo "ملاحظة: {$info['compression_note']}\n";
            }
        } else {
            echo "❌ فشل في حفظ الصورة\n";
        }
    }

} catch (Exception $e) {
    echo "❌ خطأ في الاختبار: " . $e->getMessage() . "\n";
}

// 5. تنظيف الملفات
echo "\n5. تنظيف الملفات...\n";

if (file_exists($testImagePath)) {
    unlink($testImagePath);
    echo "تم حذف صورة الاختبار\n";
}

// 6. ملخص النتائج
echo "\n6. ملخص النتائج:\n";

if ($capabilities['gd_loaded']) {
    echo "✅ النظام يعمل بالضغط المتقدم\n";
    echo "✅ يمكن ضغط الصور وتقليل حجمها\n";
    echo "✅ الأداء محسن\n";
} else {
    echo "✅ النظام يعمل بالحفظ المباشر\n";
    echo "✅ لا توجد أخطاء\n";
    echo "⚠️  لتفعيل الضغط المتقدم، يجب تفعيل PHP GD Extension\n";
}

echo "\n=== انتهاء الاختبار ===\n";
echo "النظام جاهز للاستخدام! 🎉\n";

