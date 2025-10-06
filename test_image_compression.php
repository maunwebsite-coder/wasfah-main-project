<?php

/**
 * ملف اختبار نظام ضغط الصور
 * يمكن تشغيله عبر: php test_image_compression.php
 */

require_once 'vendor/autoload.php';

use App\Services\ImageCompressionService;

echo "=== اختبار نظام ضغط الصور ===\n\n";

// اختبار إنشاء صورة بسيطة
echo "1. إنشاء صورة اختبار...\n";

// إنشاء صورة بسيطة للاختبار
$width = 2000;
$height = 1500;
$image = imagecreatetruecolor($width, $height);

// إضافة لون خلفية
$bgColor = imagecolorallocate($image, 100, 150, 200);
imagefill($image, 0, 0, $bgColor);

// إضافة نص
$textColor = imagecolorallocate($image, 255, 255, 255);
imagestring($image, 5, $width/2 - 100, $height/2, 'Test Image', $textColor);

// حفظ الصورة
$testImagePath = 'test_image.jpg';
imagejpeg($image, $testImagePath, 100);
imagedestroy($image);

echo "تم إنشاء صورة اختبار بحجم: " . filesize($testImagePath) . " بايت\n";

// اختبار ضغط الصورة
echo "\n2. اختبار ضغط الصورة...\n";

try {
    // إنشاء UploadedFile وهمي
    $uploadedFile = new \Illuminate\Http\UploadedFile(
        $testImagePath,
        'test_image.jpg',
        'image/jpeg',
        null,
        true
    );

    // ضغط الصورة
    $compressedPath = ImageCompressionService::compressAndStore(
        $uploadedFile,
        'test',
        80,
        1200,
        1200
    );

    if ($compressedPath) {
        echo "تم ضغط الصورة بنجاح!\n";
        echo "المسار المضغوط: $compressedPath\n";
        
        // الحصول على معلومات الصورة المضغوطة
        $info = ImageCompressionService::getImageInfo($compressedPath);
        if ($info) {
            echo "معلومات الصورة المضغوطة:\n";
            echo "- الأبعاد: {$info['width']} x {$info['height']}\n";
            echo "- الحجم: {$info['file_size_mb']} ميجابايت\n";
            echo "- النوع: {$info['mime_type']}\n";
        }
        
        // حساب نسبة الضغط
        $originalSize = filesize($testImagePath);
        $compressedSize = $info['file_size'];
        $compressionRatio = round((1 - $compressedSize / $originalSize) * 100, 2);
        
        echo "\nنسبة الضغط: $compressionRatio%\n";
        echo "توفير في الحجم: " . round(($originalSize - $compressedSize) / 1024, 2) . " كيلوبايت\n";
        
    } else {
        echo "فشل في ضغط الصورة!\n";
    }

} catch (Exception $e) {
    echo "خطأ في الاختبار: " . $e->getMessage() . "\n";
}

// تنظيف الملفات
echo "\n3. تنظيف الملفات...\n";

if (file_exists($testImagePath)) {
    unlink($testImagePath);
    echo "تم حذف صورة الاختبار الأصلية\n";
}

// حذف الصورة المضغوطة إذا كانت موجودة
if (isset($compressedPath) && $compressedPath) {
    ImageCompressionService::deleteCompressedImage($compressedPath);
    echo "تم حذف الصورة المضغوطة\n";
}

echo "\n=== انتهاء الاختبار ===\n";

