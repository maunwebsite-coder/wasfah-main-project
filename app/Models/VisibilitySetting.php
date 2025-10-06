<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisibilitySetting extends Model
{
    protected $fillable = [
        'section',
        'is_visible',
        'description'
    ];

    protected $casts = [
        'is_visible' => 'boolean'
    ];

    /**
     * Get the visibility setting by section
     *
     * @param string $section
     * @return static|null
     */
    public static function getBySection($section)
    {
        return static::where('section', $section)->first();
    }

    /**
     * Check if section is visible
     *
     * @param string $section
     * @return bool
     */
    public static function isSectionVisible($section)
    {
        $setting = static::getBySection($section);
        return $setting ? $setting->is_visible : true;
    }
}
