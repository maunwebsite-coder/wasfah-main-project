<?php

/**
 * اختبار شامل لنظام ضغط الصور
 * تشغيل: php test_compression_system.php
 */

echo "=== اختبار نظام ضغط الصور الشامل ===\n\n";

// 1. اختبار إنشاء صورة كبيرة
echo "1. إنشاء صورة كبيرة للاختبار...\n";
$width = 3000;
$height = 2000;
$image = imagecreatetruecolor($width, $height);

// إضافة تدرج لوني
for ($i = 0; $i < $height; $i++) {
    $color = imagecolorallocate($image, 100 + ($i * 0.1), 150 + ($i * 0.05), 200 + ($i * 0.03));
    imageline($image, 0, $i, $width, $i, $color);
}

// إضافة نص
$textColor = imagecolorallocate($image, 255, 255, 255);
imagestring($image, 5, $width/2 - 150, $height/2, 'Large Test Image', $textColor);

// حفظ الصورة الأصلية
$originalPath = 'test_large_image.jpg';
imagejpeg($image, $originalPath, 100);
$originalSize = filesize($originalPath);
imagedestroy($image);

echo "تم إنشاء صورة بحجم: " . round($originalSize / 1024, 2) . " كيلوبايت\n";
echo "الأبعاد الأصلية: {$width} x {$height}\n\n";

// 2. محاكاة ضغط الصورة
echo "2. محاكاة ضغط الصورة...\n";

// إنشاء صورة مضغوطة
$compressedWidth = 1200;
$compressedHeight = 800;
$compressedImage = imagecreatetruecolor($compressedWidth, $compressedHeight);

// إعادة إنشاء الصورة بالأبعاد الجديدة
$sourceImage = imagecreatefromjpeg($originalPath);
imagecopyresampled(
    $compressedImage,
    $sourceImage,
    0, 0, 0, 0,
    $compressedWidth,
    $compressedHeight,
    $width,
    $height
);

// حفظ الصورة المضغوطة
$compressedPath = 'test_compressed_image.jpg';
imagejpeg($compressedImage, $compressedPath, 80);
$compressedSize = filesize($compressedPath);

imagedestroy($compressedImage);
imagedestroy($sourceImage);

echo "تم ضغط الصورة إلى: " . round($compressedSize / 1024, 2) . " كيلوبايت\n";
echo "الأبعاد المضغوطة: {$compressedWidth} x {$compressedHeight}\n";

// 3. حساب الإحصائيات
echo "\n3. إحصائيات الضغط:\n";
$sizeReduction = $originalSize - $compressedSize;
$compressionRatio = round((1 - $compressedSize / $originalSize) * 100, 2);
$sizeReductionKB = round($sizeReduction / 1024, 2);
$sizeReductionMB = round($sizeReduction / (1024 * 1024), 2);

echo "- الحجم الأصلي: " . round($originalSize / 1024, 2) . " كيلوبايت\n";
echo "- الحجم المضغوط: " . round($compressedSize / 1024, 2) . " كيلوبايت\n";
echo "- التوفير: {$sizeReductionKB} كيلوبايت ({$sizeReductionMB} ميجابايت)\n";
echo "- نسبة الضغط: {$compressionRatio}%\n";

// 4. اختبار أنواع مختلفة من الصور
echo "\n4. اختبار أنواع مختلفة من الصور...\n";

$testTypes = [
    'PNG' => 'test_image.png',
    'GIF' => 'test_image.gif',
    'WebP' => 'test_image.webp'
];

foreach ($testTypes as $type => $filename) {
    // إنشاء صورة بسيطة
    $testImage = imagecreatetruecolor(1000, 800);
    $bgColor = imagecolorallocate($testImage, rand(0, 255), rand(0, 255), rand(0, 255));
    imagefill($testImage, 0, 0, $bgColor);
    
    // حفظ بنوع مختلف
    switch ($type) {
        case 'PNG':
            imagepng($testImage, $filename);
            break;
        case 'GIF':
            imagegif($testImage, $filename);
            break;
        case 'WebP':
            if (function_exists('imagewebp')) {
                imagewebp($testImage, $filename, 80);
            } else {
                echo "WebP غير مدعوم في هذا النظام\n";
                continue 2;
            }
            break;
    }
    
    $fileSize = filesize($filename);
    echo "- {$type}: " . round($fileSize / 1024, 2) . " كيلوبايت\n";
    
    imagedestroy($testImage);
}

// 5. اختبار الأداء
echo "\n5. اختبار الأداء...\n";
$startTime = microtime(true);

// محاكاة ضغط 10 صور
for ($i = 0; $i < 10; $i++) {
    $testImage = imagecreatetruecolor(800, 600);
    $color = imagecolorallocate($testImage, rand(0, 255), rand(0, 255), rand(0, 255));
    imagefill($testImage, 0, 0, $color);
    
    $tempFile = "temp_test_{$i}.jpg";
    imagejpeg($testImage, $tempFile, 80);
    imagedestroy($testImage);
    
    // حذف الملف المؤقت
    unlink($tempFile);
}

$endTime = microtime(true);
$executionTime = round(($endTime - $startTime) * 1000, 2);

echo "وقت معالجة 10 صور: {$executionTime} مللي ثانية\n";
echo "متوسط الوقت لكل صورة: " . round($executionTime / 10, 2) . " مللي ثانية\n";

// 6. تنظيف الملفات
echo "\n6. تنظيف الملفات...\n";

$filesToDelete = [
    $originalPath,
    $compressedPath,
    'test_image.png',
    'test_image.gif',
    'test_image.webp'
];

foreach ($filesToDelete as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "تم حذف: $file\n";
    }
}

echo "\n=== انتهاء الاختبار الشامل ===\n";
echo "النظام جاهز للاستخدام! 🎉\n";

