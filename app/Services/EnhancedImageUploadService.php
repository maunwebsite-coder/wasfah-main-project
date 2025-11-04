<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EnhancedImageUploadService
{
    /**
     * رفع صورة مع معالجة محسنة للأخطاء
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param int $quality
     * @param int $maxWidth
     * @param int $maxHeight
     * @return array
     */
    public static function uploadImage(
        UploadedFile $file,
        string $directory = 'images',
        int $quality = 85,
        int $maxWidth = 1200,
        int $maxHeight = 1200
    ): array {
        try {
            // التحقق من صحة الملف
            if (!$file->isValid()) {
                return [
                    'success' => false,
                    'error' => 'الملف المرفوع غير صالح',
                    'error_code' => $file->getError(),
                    'error_message' => $file->getErrorMessage()
                ];
            }

            // التحقق من نوع الملف
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return [
                    'success' => false,
                    'error' => 'نوع الملف غير مدعوم. يرجى اختيار صورة بصيغة JPG, PNG, GIF أو WebP.',
                    'mime_type' => $file->getMimeType()
                ];
            }

            // التحقق من حجم الملف (2MB)
            $maxSize = 2 * 1024 * 1024; // 2MB
            if ($file->getSize() > $maxSize) {
                return [
                    'success' => false,
                    'error' => 'حجم الصورة كبير جداً. الحد الأقصى المسموح هو 2 ميجابايت.',
                    'file_size' => $file->getSize(),
                    'max_size' => $maxSize
                ];
            }

            // إنشاء اسم فريد للملف
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
            $filePath = $directory . '/' . $filename;

            // محاولة ضغط الصورة إذا كان GD متوفراً
            if (extension_loaded('gd')) {
                $result = self::compressAndStore($file, $directory, $filename, $quality, $maxWidth, $maxHeight);
                if ($result['success']) {
                    return [
                        'success' => true,
                        'path' => $result['path'],
                        'compressed' => true,
                        'original_size' => $file->getSize(),
                        'compressed_size' => $result['compressed_size']
                    ];
                } else {
                    // إذا فشل الضغط، احفظ الملف مباشرة
                    Log::warning('Image compression failed, saving without compression', [
                        'error' => $result['error']
                    ]);
                }
            }

            // حفظ الملف مباشرة بدون ضغط
            $storedPath = Storage::disk('public')->putFileAs($directory, $file, $filename);
            
            if ($storedPath) {
                return [
                    'success' => true,
                    'path' => $storedPath,
                    'compressed' => false,
                    'original_size' => $file->getSize()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'فشل في حفظ الملف'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Image upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize()
            ]);

            return [
                'success' => false,
                'error' => 'حدث خطأ غير متوقع أثناء رفع الصورة',
                'exception' => $e->getMessage()
            ];
        }
    }

    /**
     * ضغط وحفظ الصورة
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $filename
     * @param int $quality
     * @param int $maxWidth
     * @param int $maxHeight
     * @return array
     */
    private static function compressAndStore(
        UploadedFile $file,
        string $directory,
        string $filename,
        int $quality,
        int $maxWidth,
        int $maxHeight
    ): array {
        try {
            // قراءة الصورة
            $imageData = file_get_contents($file->getPathname());
            $image = imagecreatefromstring($imageData);

            if (!$image) {
                return [
                    'success' => false,
                    'error' => 'فشل في قراءة الصورة'
                ];
            }

            // الحصول على الأبعاد الأصلية
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // حساب الأبعاد الجديدة مع الحفاظ على النسبة
            $newDimensions = self::calculateNewDimensions(
                $originalWidth,
                $originalHeight,
                $maxWidth,
                $maxHeight
            );

            // إنشاء صورة جديدة بالأبعاد المحدثة
            $compressedImage = imagecreatetruecolor(
                $newDimensions['width'],
                $newDimensions['height']
            );

            // الحفاظ على الشفافية للصور PNG
            if ($file->getMimeType() === 'image/png') {
                imagealphablending($compressedImage, false);
                imagesavealpha($compressedImage, true);
                $transparent = imagecolorallocatealpha($compressedImage, 255, 255, 255, 127);
                imagefill($compressedImage, 0, 0, $transparent);
            }

            // تغيير حجم الصورة
            imagecopyresampled(
                $compressedImage,
                $image,
                0, 0, 0, 0,
                $newDimensions['width'],
                $newDimensions['height'],
                $originalWidth,
                $originalHeight
            );

            // إنشاء ملف مؤقت
            $tempPath = tempnam(sys_get_temp_dir(), 'compressed_');
            
            // حفظ الصورة المضغوطة
            switch ($file->getMimeType()) {
                case 'image/jpeg':
                case 'image/jpg':
                    imagejpeg($compressedImage, $tempPath, $quality);
                    break;
                case 'image/png':
                    imagepng($compressedImage, $tempPath, 9 - (int)($quality / 10));
                    break;
                case 'image/gif':
                    imagegif($compressedImage, $tempPath);
                    break;
                case 'image/webp':
                    imagewebp($compressedImage, $tempPath, $quality);
                    break;
            }

            // رفع الملف المضغوط إلى التخزين
            $storedPath = Storage::disk('public')->putFileAs(
                $directory,
                new \Illuminate\Http\File($tempPath),
                $filename
            );

            // تنظيف الذاكرة
            imagedestroy($image);
            imagedestroy($compressedImage);
            unlink($tempPath);

            if ($storedPath) {
                return [
                    'success' => true,
                    'path' => $storedPath,
                    'compressed_size' => filesize($tempPath)
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'فشل في حفظ الصورة المضغوطة'
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'فشل في ضغط الصورة: ' . $e->getMessage()
            ];
        }
    }

    /**
     * حساب الأبعاد الجديدة مع الحفاظ على النسبة
     *
     * @param int $originalWidth
     * @param int $originalHeight
     * @param int $maxWidth
     * @param int $maxHeight
     * @return array
     */
    private static function calculateNewDimensions(
        int $originalWidth,
        int $originalHeight,
        int $maxWidth,
        int $maxHeight
    ): array {
        // إذا كانت الصورة أصغر من الحد الأقصى، لا نحتاج لتغيير الحجم
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return [
                'width' => $originalWidth,
                'height' => $originalHeight
            ];
        }

        // حساب النسبة
        $widthRatio = $maxWidth / $originalWidth;
        $heightRatio = $maxHeight / $originalHeight;
        $ratio = min($widthRatio, $heightRatio);

        return [
            'width' => (int)($originalWidth * $ratio),
            'height' => (int)($originalHeight * $ratio)
        ];
    }

    /**
     * حذف الصورة
     *
     * @param string $imagePath
     * @return bool
     */
    public static function deleteImage(string $imagePath): bool
    {
        try {
            if (Storage::disk('public')->exists($imagePath)) {
                return Storage::disk('public')->delete($imagePath);
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete image', [
                'path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * التحقق من إمكانيات النظام
     *
     * @return array
     */
    public static function checkSystemCapabilities(): array
    {
        return [
            'gd_loaded' => extension_loaded('gd'),
            'gd_version' => extension_loaded('gd') ? gd_info()['GD Version'] : null,
            'supported_formats' => [
                'jpeg' => function_exists('imagecreatefromjpeg'),
                'png' => function_exists('imagecreatefrompng'),
                'gif' => function_exists('imagecreatefromgif'),
                'webp' => function_exists('imagecreatefromwebp'),
            ],
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];
    }
}
