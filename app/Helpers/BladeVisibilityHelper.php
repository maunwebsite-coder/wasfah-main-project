<?php

namespace App\Helpers;

use App\Helpers\VisibilityHelper;

class BladeVisibilityHelper
{
    /**
     * Check if a section should be visible
     *
     * @param string $section
     * @return bool
     */
    public static function isVisible($section)
    {
        return VisibilityHelper::isVisible($section);
    }

    /**
     * Get visibility class for a section
     *
     * @param string $section
     * @param string $visibleClass
     * @param string $hiddenClass
     * @return string
     */
    public static function getVisibilityClass($section, $visibleClass = 'visibility-visible', $hiddenClass = 'visibility-hidden')
    {
        return self::isVisible($section) ? $visibleClass : $hiddenClass;
    }

    /**
     * Get visibility style for a section
     *
     * @param string $section
     * @return string
     */
    public static function getVisibilityStyle($section)
    {
        return self::isVisible($section) ? '' : 'display: none;';
    }

    /**
     * Get visibility attributes for a section
     *
     * @param string $section
     * @return string
     */
    public static function getVisibilityAttributes($section)
    {
        $isVisible = self::isVisible($section);
        $class = $isVisible ? 'visibility-visible' : 'visibility-hidden';
        $style = $isVisible ? '' : 'display: none;';
        
        return "class=\"{$class}\" style=\"{$style}\" data-section=\"{$section}\"";
    }

    /**
     * Check if user can see admin sections
     *
     * @return bool
     */
    public static function canSeeAdmin()
    {
        return VisibilityHelper::canSeeAdminSections();
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    public static function isAuthenticated()
    {
        return VisibilityHelper::canSeeUserSections();
    }

    /**
     * Get all visibility settings for frontend
     *
     * @return array
     */
    public static function getFrontendConfig()
    {
        return VisibilityHelper::getFrontendConfig();
    }

    /**
     * Render visibility wrapper for a section
     *
     * @param string $section
     * @param string $content
     * @param string $wrapperTag
     * @param array $attributes
     * @return string
     */
    public static function renderSection($section, $content, $wrapperTag = 'div', $attributes = [])
    {
        if (!self::isVisible($section)) {
            return '';
        }

        $defaultAttributes = [
            'data-section' => $section,
            'class' => 'visibility-visible'
        ];

        $attributes = array_merge($defaultAttributes, $attributes);
        
        $attributeString = '';
        foreach ($attributes as $key => $value) {
            $attributeString .= " {$key}=\"{$value}\"";
        }

        return "<{$wrapperTag}{$attributeString}>{$content}</{$wrapperTag}>";
    }

    /**
     * Get conditional visibility class
     *
     * @param string $section
     * @param string $baseClass
     * @return string
     */
    public static function getConditionalClass($section, $baseClass = '')
    {
        $visibilityClass = self::getVisibilityClass($section);
        return trim($baseClass . ' ' . $visibilityClass);
    }

    /**
     * Check if multiple sections are visible
     *
     * @param array $sections
     * @return bool
     */
    public static function areVisible($sections)
    {
        foreach ($sections as $section) {
            if (!self::isVisible($section)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if any section is visible
     *
     * @param array $sections
     * @return bool
     */
    public static function anyVisible($sections)
    {
        foreach ($sections as $section) {
            if (self::isVisible($section)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get visibility data attributes for JavaScript
     *
     * @param string $section
     * @return string
     */
    public static function getDataAttributes($section)
    {
        $isVisible = self::isVisible($section);
        return "data-section=\"{$section}\" data-visible=\"" . ($isVisible ? 'true' : 'false') . "\"";
    }
}
