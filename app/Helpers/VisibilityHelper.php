<?php

namespace App\Helpers;

use App\Models\VisibilitySetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class VisibilityHelper
{
    /**
     * Get visibility setting for a specific section
     *
     * @param string $section
     * @param mixed $default
     * @return mixed
     */
    public static function getVisibilitySetting($section, $default = true)
    {
        $cacheKey = "visibility_setting_{$section}";
        
        return Cache::remember($cacheKey, 3600, function () use ($section, $default) {
            $setting = VisibilitySetting::where('section', $section)->first();
            return $setting ? $setting->is_visible : $default;
        });
    }

    /**
     * Set visibility setting for a specific section
     *
     * @param string $section
     * @param bool $isVisible
     * @return bool
     */
    public static function setVisibilitySetting($section, $isVisible = true)
    {
        $setting = VisibilitySetting::updateOrCreate(
            ['section' => $section],
            ['is_visible' => $isVisible]
        );

        // Clear cache
        Cache::forget("visibility_setting_{$section}");
        
        return $setting->is_visible;
    }

    /**
     * Check if a section should be visible
     *
     * @param string $section
     * @return bool
     */
    public static function isVisible($section)
    {
        return self::getVisibilitySetting($section, true);
    }

    /**
     * Hide a section
     *
     * @param string $section
     * @return bool
     */
    public static function hideSection($section)
    {
        return self::setVisibilitySetting($section, false);
    }

    /**
     * Show a section
     *
     * @param string $section
     * @return bool
     */
    public static function showSection($section)
    {
        return self::setVisibilitySetting($section, true);
    }

    /**
     * Toggle visibility of a section
     *
     * @param string $section
     * @return bool
     */
    public static function toggleSection($section)
    {
        $currentVisibility = self::getVisibilitySetting($section, true);
        return self::setVisibilitySetting($section, !$currentVisibility);
    }

    /**
     * Get all visibility settings
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllSettings()
    {
        return Cache::remember('all_visibility_settings', 3600, function () {
            return VisibilitySetting::all();
        });
    }

    /**
     * Clear all visibility cache
     *
     * @return void
     */
    public static function clearCache()
    {
        $settings = self::getAllSettings();
        foreach ($settings as $setting) {
            Cache::forget("visibility_setting_{$setting->section}");
        }
        Cache::forget('all_visibility_settings');
    }

    /**
     * Check if user can see admin sections
     *
     * @return bool
     */
    public static function canSeeAdminSections()
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->is_admin ?? false;
    }

    /**
     * Check if user can see user-specific sections
     *
     * @return bool
     */
    public static function canSeeUserSections()
    {
        return Auth::check();
    }

    /**
     * Get visibility status for multiple sections
     *
     * @param array $sections
     * @return array
     */
    public static function getMultipleVisibility($sections)
    {
        $result = [];
        foreach ($sections as $section) {
            $result[$section] = self::isVisible($section);
        }
        return $result;
    }

    /**
     * Set visibility for multiple sections
     *
     * @param array $settings
     * @return array
     */
    public static function setMultipleVisibility($settings)
    {
        $result = [];
        foreach ($settings as $section => $isVisible) {
            $result[$section] = self::setVisibilitySetting($section, $isVisible);
        }
        return $result;
    }

    /**
     * Get sections that should be hidden for current user
     *
     * @return array
     */
    public static function getHiddenSections()
    {
        $allSettings = self::getAllSettings();
        $hiddenSections = [];

        foreach ($allSettings as $setting) {
            if (!$setting->is_visible) {
                $hiddenSections[] = $setting->section;
            }
        }

        return $hiddenSections;
    }

    /**
     * Get sections that should be visible for current user
     *
     * @return array
     */
    public static function getVisibleSections()
    {
        $allSettings = self::getAllSettings();
        $visibleSections = [];

        foreach ($allSettings as $setting) {
            if ($setting->is_visible) {
                $visibleSections[] = $setting->section;
            }
        }

        return $visibleSections;
    }

    /**
     * Check if a specific page/section should be accessible
     *
     * @param string $section
     * @return bool
     */
    public static function isAccessible($section)
    {
        // Check basic visibility
        if (!self::isVisible($section)) {
            return false;
        }

        // Check user permissions for specific sections
        switch ($section) {
            case 'admin':
            case 'dashboard':
                return self::canSeeAdminSections();
            case 'profile':
            case 'bookings':
                return self::canSeeUserSections();
            default:
                return true;
        }
    }

    /**
     * Get visibility configuration for frontend
     *
     * @return array
     */
    public static function getFrontendConfig()
    {
        return [
            'sections' => self::getMultipleVisibility([
                'header',
                'navigation',
                'footer',
                'sidebar',
                'search',
                'recipes',
                'tools',
                'workshops',
                'notifications',
                'profile',
                'admin'
            ]),
            'user' => [
                'is_authenticated' => Auth::check(),
                'is_admin' => self::canSeeAdminSections(),
                'can_see_user_sections' => self::canSeeUserSections()
            ]
        ];
    }

    /**
     * Initialize default visibility settings
     *
     * @return void
     */
    public static function initializeDefaults()
    {
        $defaultSections = [
            'header' => true,
            'navigation' => true,
            'footer' => true,
            'sidebar' => true,
            'search' => true,
            'recipes' => true,
            'tools' => true,
            'workshops' => true,
            'notifications' => true,
            'profile' => true,
            'admin' => false
        ];

        foreach ($defaultSections as $section => $isVisible) {
            if (!VisibilitySetting::where('section', $section)->exists()) {
                VisibilitySetting::create([
                    'section' => $section,
                    'is_visible' => $isVisible
                ]);
            }
        }
    }
}
