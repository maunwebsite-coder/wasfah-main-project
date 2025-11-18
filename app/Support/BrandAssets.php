<?php

namespace App\Support;

use Illuminate\Support\Facades\App;

class BrandAssets
{
    /**
     * Determine the base path (without extension) for the logo that matches the locale.
     */
    public static function logoBase(?string $locale = null): string
    {
        $locale ??= App::getLocale();

        if ($locale === 'en' && self::hasLocalizedLogo('image/logo en')) {
            return 'image/logo en';
        }

        return 'image/logo';
    }

    /**
     * Get the absolute URL for the logo, preferring the requested extension when available.
     */
    public static function logoAsset(?string $preferredExtension = 'png', ?string $locale = null): string
    {
        $locale ??= App::getLocale();
        $preferredExtension = self::normalizeExtension($preferredExtension);

        $extensions = array_values(array_unique(array_filter([
            $preferredExtension,
            'webp',
            'avif',
            'png',
            'jpg',
            'jpeg',
        ])));

        $bases = array_values(array_unique(array_filter([
            self::logoBase($locale),
            $locale === 'en' ? 'image/logo' : null,
        ])));

        foreach ($bases as $base) {
            foreach ($extensions as $extension) {
                $relativePath = "{$base}.{$extension}";

                if (file_exists(public_path($relativePath))) {
                    return asset($relativePath);
                }
            }
        }

        // Fall back to the default base without checking extensions.
        return asset('image/logo.png');
    }

    protected static function normalizeExtension(?string $extension): ?string
    {
        if ($extension === null) {
            return null;
        }

        $extension = ltrim(strtolower($extension), '.');

        return $extension !== '' ? $extension : null;
    }

    protected static function hasLocalizedLogo(string $baseWithoutExtension): bool
    {
        foreach (['png', 'webp', 'avif', 'jpg', 'jpeg'] as $extension) {
            if (file_exists(public_path("{$baseWithoutExtension}.{$extension}"))) {
                return true;
            }
        }

        return false;
    }
}
