<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressionService
{
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
        ];
    }
    /**
     * ضغط الصورة وحفظها
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param int $quality
     * @param int $maxWidth
     * @param int $maxHeight
     * @param object|null $model
     * @return string|null
     */
    public static function compressAndStore(
        UploadedFile $file,
        string $directory = 'images',
        int $quality = 80,
        int $maxWidth = 1200,
        int $maxHeight = 1200,
        $model = null
    ): ?string {
        try {
            // التحقق من وجود PHP GD extension
            if (!extension_loaded('gd')) {
                \Log::warning('PHP GD extension غير متوفر، سيتم حفظ الصورة بدون ضغط');
                return $file->store($directory, 'public');
            }

            // التحقق من نوع الملف
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                throw new \InvalidArgumentException('نوع الملف غير مدعوم');
            }

            // قراءة الصورة
            $imageData = file_get_contents($file->getPathname());
            $image = imagecreatefromstring($imageData);

            if (!$image) {
                throw new \Exception('فشل في قراءة الصورة');
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

            // إنشاء اسم فريد للملف
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
            $filePath = $directory . '/' . $filename;

            // حفظ الصورة المضغوطة
            $tempPath = tempnam(sys_get_temp_dir(), 'compressed_');
            
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

            // تسجيل معلومات الضغط في النموذج إذا كان متوفراً
            if ($model && method_exists($model, 'update')) {
                $originalSize = $file->getSize();
                $compressedSize = filesize($tempPath);
                
                $model->update([
                    'image_compressed' => true,
                    'original_image_size' => $originalSize,
                    'compressed_image_size' => $compressedSize,
                    'image_compressed_at' => now(),
                ]);
            }

            // تنظيف الذاكرة
            imagedestroy($image);
            imagedestroy($compressedImage);
            unlink($tempPath);

            return $storedPath;

        } catch (\Exception $e) {
            \Log::error('خطأ في ضغط الصورة: ' . $e->getMessage());
            return null;
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
     * ضغط صورة من URL
     *
     * @param string $imageUrl
     * @param string $directory
     * @param int $quality
     * @param int $maxWidth
     * @param int $maxHeight
     * @return string|null
     */
    public static function compressFromUrl(
        string $imageUrl,
        string $directory = 'images',
        int $quality = 80,
        int $maxWidth = 1200,
        int $maxHeight = 1200
    ): ?string {
        try {
            // التحقق من وجود PHP GD extension
            if (!extension_loaded('gd')) {
                \Log::warning('PHP GD extension غير متوفر، سيتم حفظ الصورة بدون ضغط');
                // تحميل الصورة وحفظها مباشرة
                $imageData = file_get_contents($imageUrl);
                if (!$imageData) {
                    throw new \Exception('فشل في تحميل الصورة من URL');
                }
                $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
                $extension = pathinfo($filename, PATHINFO_EXTENSION) ?: 'jpg';
                $filename = Str::uuid() . '.' . $extension;
                return Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($imageUrl), $filename);
            }

            // تحميل الصورة من URL
            $imageData = file_get_contents($imageUrl);
            if (!$imageData) {
                throw new \Exception('فشل في تحميل الصورة من URL');
            }

            // إنشاء ملف مؤقت
            $tempFile = tempnam(sys_get_temp_dir(), 'downloaded_image_');
            file_put_contents($tempFile, $imageData);

            // إنشاء UploadedFile وهمي
            $uploadedFile = new UploadedFile(
                $tempFile,
                basename($imageUrl),
                mime_content_type($tempFile),
                null,
                true
            );

            // ضغط الصورة
            $result = self::compressAndStore($uploadedFile, $directory, $quality, $maxWidth, $maxHeight);

            // حذف الملف المؤقت
            unlink($tempFile);

            return $result;

        } catch (\Exception $e) {
            \Log::error('خطأ في ضغط الصورة من URL: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * حذف الصورة المضغوطة
     *
     * @param string $imagePath
     * @return bool
     */
    public static function deleteCompressedImage(string $imagePath): bool
    {
        try {
            if (Storage::disk('public')->exists($imagePath)) {
                return Storage::disk('public')->delete($imagePath);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('خطأ في حذف الصورة: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * الحصول على معلومات الصورة المضغوطة
     *
     * @param string $imagePath
     * @return array|null
     */
    public static function getImageInfo(string $imagePath): ?array
    {
        try {
            $fullPath = Storage::disk('public')->path($imagePath);
            
            if (!file_exists($fullPath)) {
                return null;
            }

            $imageSize = getimagesize($fullPath);
            $fileSize = filesize($fullPath);

            return [
                'width' => $imageSize[0],
                'height' => $imageSize[1],
                'mime_type' => $imageSize['mime'],
                'file_size' => $fileSize,
                'file_size_mb' => round($fileSize / 1024 / 1024, 2)
            ];
        } catch (\Exception $e) {
            \Log::error('خطأ في الحصول على معلومات الصورة: ' . $e->getMessage());
            return null;
        }
    }
}
