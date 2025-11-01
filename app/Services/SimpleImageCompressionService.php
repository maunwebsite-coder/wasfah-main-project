<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SimpleImageCompressionService
{
    /**
     * ضغط بسيط للصور بدون GD Extension
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param int $quality
     * @return string|null
     */
    public static function compressAndStore(
        UploadedFile $file,
        string $directory = 'images',
        int $quality = 80
    ): ?string {
        try {
            \Log::info('SimpleImageCompressionService: Starting image processing', [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_mime' => $file->getMimeType(),
                'directory' => $directory
            ]);

            // التحقق من نوع الملف
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                \Log::error('SimpleImageCompressionService: Unsupported file type', [
                    'mime_type' => $file->getMimeType(),
                    'allowed_types' => $allowedTypes
                ]);
                throw new \InvalidArgumentException('نوع الملف غير مدعوم');
            }

            // إنشاء اسم فريد للملف
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
            
            \Log::info('SimpleImageCompressionService: Generated filename', [
                'filename' => $filename,
                'extension' => $extension
            ]);
            
            // حفظ الصورة مباشرة بدون ضغط
            $storedPath = Storage::disk('public')->putFileAs(
                $directory,
                $file,
                $filename
            );

            \Log::info("SimpleImageCompressionService: تم حفظ الصورة بدون ضغط", [
                'stored_path' => $storedPath,
                'full_path' => Storage::disk('public')->path($storedPath)
            ]);
            return $storedPath;

        } catch (\Exception $e) {
            \Log::error('SimpleImageCompressionService: خطأ في حفظ الصورة', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * ضغط صورة من URL بدون GD Extension
     *
     * @param string $imageUrl
     * @param string $directory
     * @return string|null
     */
    public static function compressFromUrl(
        string $imageUrl,
        string $directory = 'images'
    ): ?string {
        try {
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

            // تحميل الصورة من URL
            $imageData = @file_get_contents($imageUrl, false, $context);
            if (!$imageData) {
                throw new \Exception('فشل في تحميل الصورة من URL');
            }

            // إنشاء اسم فريد للملف
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg';
            $filename = Str::uuid() . '.' . strtolower($extension);
            
            // حفظ الصورة مباشرة
            $relativePath = trim($directory, '/') . '/' . $filename;
            Storage::disk('public')->put($relativePath, $imageData);

            \Log::info("تم حفظ الصورة من URL بدون ضغط: $relativePath");
            return $relativePath;

        } catch (\Exception $e) {
            \Log::error('خطأ في حفظ الصورة من URL: ' . $e->getMessage());
            return null;
        }
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
            \Log::error('خطأ في حذف الصورة: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * الحصول على معلومات الصورة
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

            $fileSize = filesize($fullPath);

            return [
                'file_size' => $fileSize,
                'file_size_mb' => round($fileSize / 1024 / 1024, 2),
                'compression_note' => 'تم الحفظ بدون ضغط - PHP GD غير متوفر'
            ];
        } catch (\Exception $e) {
            \Log::error('خطأ في الحصول على معلومات الصورة: ' . $e->getMessage());
            return null;
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
            'gd_available' => extension_loaded('gd'),
            'fallback_mode' => true,
            'note' => 'النظام يعمل في وضع الحفظ المباشر بدون ضغط'
        ];
    }
}
