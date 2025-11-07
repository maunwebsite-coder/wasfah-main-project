<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroSlideMediaService
{
    private const DIRECTORY = 'hero-slides';
    private const MAX_WIDTH = 1920;
    private const QUALITY = 80;

    /**
     * Store uploaded hero media.
     */
    public static function store(UploadedFile $file): string
    {
        if (self::isVideo($file)) {
            return $file->store(self::DIRECTORY, 'public');
        }

        if (!self::canProcessImage($file)) {
            return $file->store(self::DIRECTORY, 'public');
        }

        $image = null;
        $resized = null;
        $tempPath = null;

        try {
            $image = self::createImageResource($file);
            if (!$image) {
                throw new \RuntimeException('Unable to create image resource.');
            }

            $resized = self::resizeImage($image, self::MAX_WIDTH, self::supportsAlpha($file));

            if (!function_exists('imagewebp')) {
                throw new \RuntimeException('imagewebp function is not available.');
            }

            $tempBase = tempnam(sys_get_temp_dir(), 'hero_slide_');
            if ($tempBase === false) {
                throw new \RuntimeException('Unable to create temp file.');
            }

            $tempPath = $tempBase . '.webp';
            if (!@rename($tempBase, $tempPath)) {
                $tempPath = $tempBase;
            }

            if (!imagewebp($resized, $tempPath, self::QUALITY)) {
                throw new \RuntimeException('Failed to encode image as WebP.');
            }

            $filename = Str::uuid() . '.webp';
            $storagePath = self::DIRECTORY . '/' . $filename;

            $stream = fopen($tempPath, 'rb');
            if (!$stream) {
                throw new \RuntimeException('Unable to open temp WebP file.');
            }

            try {
                $stored = Storage::disk('public')->put($storagePath, $stream);
            } finally {
                fclose($stream);
            }

            if (!$stored) {
                throw new \RuntimeException('Failed to store processed hero slide media.');
            }

            return $storagePath;
        } catch (\Throwable $exception) {
            Log::warning('Hero slide media conversion failed; storing original file instead.', [
                'message' => $exception->getMessage(),
                'mime' => $file->getMimeType(),
            ]);

            return $file->store(self::DIRECTORY, 'public');
        } finally {
            if (is_resource($image) || $image instanceof \GdImage) {
                imagedestroy($image);
            }

            if (($resized instanceof \GdImage || is_resource($resized)) && $resized !== $image) {
                imagedestroy($resized);
            }

            if ($tempPath && is_file($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    /**
     * Determine whether the uploaded file should be treated as a video.
     */
    protected static function isVideo(UploadedFile $file): bool
    {
        $mime = $file->getMimeType() ?: '';
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: '');

        return $mime === 'video/webm' || $extension === 'webm';
    }

    /**
     * Ensure we can process this upload as an image.
     */
    protected static function canProcessImage(UploadedFile $file): bool
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        $mime = $file->getMimeType() ?: '';
        if (!str_starts_with($mime, 'image/')) {
            return false;
        }

        if ($mime === 'image/svg+xml') {
            return false;
        }

        return true;
    }

    /**
     * Detect whether the image supports alpha (PNG/GIF/WebP).
     */
    protected static function supportsAlpha(UploadedFile $file): bool
    {
        $mime = $file->getMimeType() ?: '';

        return in_array($mime, ['image/png', 'image/gif', 'image/webp'], true);
    }

    /**
     * Create a GD resource from the uploaded image.
     */
    protected static function createImageResource(UploadedFile $file)
    {
        $path = $file->getRealPath();
        if (!$path || !is_readable($path)) {
            return null;
        }

        $mime = $file->getMimeType() ?: '';

        return match ($mime) {
            'image/jpeg', 'image/jpg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($path) : null,
            'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($path) : null,
            'image/gif' => function_exists('imagecreatefromgif') ? @imagecreatefromgif($path) : null,
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            'image/bmp', 'image/x-ms-bmp' => function_exists('imagecreatefrombmp') ? @imagecreatefrombmp($path) : null,
            default => @imagecreatefromstring(file_get_contents($path)),
        };
    }

    /**
     * Resize the image to the desired max width while keeping the aspect ratio.
     */
    protected static function resizeImage($image, int $maxWidth, bool $preserveAlpha)
    {
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        if ($originalWidth <= $maxWidth) {
            $targetWidth = $originalWidth;
            $targetHeight = $originalHeight;
        } else {
            $ratio = $maxWidth / max(1, $originalWidth);
            $targetWidth = $maxWidth;
            $targetHeight = max(1, (int) round($originalHeight * $ratio));
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($preserveAlpha) {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
            imagefill($canvas, 0, 0, $transparent);
        } else {
            $background = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $background);
        }

        imagecopyresampled(
            $canvas,
            $image,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $originalWidth,
            $originalHeight
        );

        return $canvas;
    }
}
