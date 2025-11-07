<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressionService
{
    private const DEFAULT_MAX_WIDTH = 1920;
    private const DEFAULT_MAX_HEIGHT = 1920;
    private const DEFAULT_QUALITY = 80;

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
        int $quality = self::DEFAULT_QUALITY,
        int $maxWidth = self::DEFAULT_MAX_WIDTH,
        int $maxHeight = self::DEFAULT_MAX_HEIGHT,
        $model = null
    ): ?string {
        try {
            if (!extension_loaded('gd') || !function_exists('imagewebp')) {
                \Log::warning('GD/WebP not available; storing original file without optimization.');
                return $file->store($directory, 'public');
            }

            $mime = $file->getMimeType() ?: '';
            if (!self::isSupportedMime($mime)) {
                throw new \InvalidArgumentException('نوع الملف غير مدعوم');
            }

            $image = self::createImageResource($file);
            if (!$image) {
                throw new \RuntimeException('فشل في قراءة الصورة');
            }

            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            $newDimensions = self::calculateNewDimensions(
                $originalWidth,
                $originalHeight,
                $maxWidth,
                $maxHeight
            );

            $optimizedImage = imagecreatetruecolor(
                $newDimensions['width'],
                $newDimensions['height']
            );

            if (self::supportsAlpha($mime)) {
                imagealphablending($optimizedImage, false);
                imagesavealpha($optimizedImage, true);
                $transparent = imagecolorallocatealpha($optimizedImage, 255, 255, 255, 127);
                imagefill($optimizedImage, 0, 0, $transparent);
            } else {
                $background = imagecolorallocate($optimizedImage, 255, 255, 255);
                imagefill($optimizedImage, 0, 0, $background);
            }

            imagecopyresampled(
                $optimizedImage,
                $image,
                0,
                0,
                0,
                0,
                $newDimensions['width'],
                $newDimensions['height'],
                $originalWidth,
                $originalHeight
            );

            $tempPath = self::writeWebpTempFile($optimizedImage, $quality);

            $filename = Str::uuid() . '.webp';
            $relativePath = trim($directory, '/') . '/' . $filename;

            $stream = fopen($tempPath, 'rb');
            if (!$stream) {
                throw new \RuntimeException('فشل في قراءة الملف المؤقت بعد التحويل إلى WebP.');
            }

            try {
                if (!Storage::disk('public')->put($relativePath, $stream)) {
                    throw new \RuntimeException('تعذر حفظ الصورة بعد التحويل إلى WebP.');
                }
            } finally {
                fclose($stream);
            }

            imagedestroy($image);
            imagedestroy($optimizedImage);
            @unlink($tempPath);

            return $relativePath;
        } catch (\Throwable $e) {
            \Log::error('خطأ في ضغط الصورة: ' . $e->getMessage());
            return null;
        }
    }
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

                $context = stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'header' => [
                            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0',
                            'Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
                            'Accept-Language: en-US,en;q=0.9'
                        ]
                    ]
                ]);

                $imageData = @file_get_contents($imageUrl, false, $context);
                if (!$imageData) {
                    throw new \Exception('فشل في تحميل الصورة من URL');
                }

                $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg';
                $filename = Str::uuid() . '.' . strtolower($extension);
                $relativePath = trim($directory, '/') . '/' . $filename;

                Storage::disk('public')->put($relativePath, $imageData);

                return $relativePath;
            }

            // تحميل الصورة من URL
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => [
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0',
                        'Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
                        'Accept-Language: en-US,en;q=0.9'
                    ]
                ]
            ]);

            $imageData = @file_get_contents($imageUrl, false, $context);
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
