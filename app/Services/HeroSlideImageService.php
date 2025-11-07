<?php

namespace App\Services;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroSlideImageService
{
    private const DIRECTORY = 'hero-slides';
    private const MAX_WIDTH = 1920;
    private const MAX_HEIGHT = 1920;
    private const QUALITY = 80;

    private const SUPPORTED_MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
        'image/gif',
        'image/bmp',
    ];

    /**
     * Optimize an uploaded image by resizing and converting to WebP.
     */
    public static function storeOptimized(UploadedFile $file): string
    {
        if (!self::canOptimize($file)) {
            return $file->store(self::DIRECTORY, 'public');
        }

        $tempPath = null;

        try {
            $image = imagecreatefromstring(file_get_contents($file->getPathname()));

            if (!$image) {
                throw new \RuntimeException('Failed to create image resource.');
            }

            $dimensions = self::calculateDimensions(imagesx($image), imagesy($image));
            $canvas = imagecreatetruecolor($dimensions['width'], $dimensions['height']);

            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);

            imagecopyresampled(
                $canvas,
                $image,
                0,
                0,
                0,
                0,
                $dimensions['width'],
                $dimensions['height'],
                imagesx($image),
                imagesy($image)
            );

            $tempPath = tempnam(sys_get_temp_dir(), 'hero-slide-');

            if (!$tempPath || !imagewebp($canvas, $tempPath, self::QUALITY)) {
                throw new \RuntimeException('Failed to write WebP image.');
            }

            $filename = Str::uuid() . '.webp';
            $storedPath = Storage::disk('public')->putFileAs(
                self::DIRECTORY,
                new File($tempPath),
                $filename
            );

            return $storedPath;
        } catch (\Throwable $throwable) {
            Log::warning('HeroSlideImageService fallback to original image.', [
                'message' => $throwable->getMessage(),
            ]);

            return $file->store(self::DIRECTORY, 'public');
        } finally {
            if (isset($image) && is_resource($image)) {
                imagedestroy($image);
            }

            if (isset($canvas) && is_resource($canvas)) {
                imagedestroy($canvas);
            }

            if ($tempPath && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    private static function canOptimize(UploadedFile $file): bool
    {
        return extension_loaded('gd')
            && function_exists('imagewebp')
            && in_array(strtolower($file->getMimeType()), self::SUPPORTED_MIME_TYPES, true);
    }

    private static function calculateDimensions(int $originalWidth, int $originalHeight): array
    {
        if ($originalWidth <= self::MAX_WIDTH && $originalHeight <= self::MAX_HEIGHT) {
            return [
                'width' => $originalWidth,
                'height' => $originalHeight,
            ];
        }

        $widthRatio = self::MAX_WIDTH / $originalWidth;
        $heightRatio = self::MAX_HEIGHT / $originalHeight;
        $ratio = min($widthRatio, $heightRatio);

        return [
            'width' => max(1, (int) round($originalWidth * $ratio)),
            'height' => max(1, (int) round($originalHeight * $ratio)),
        ];
    }
}
