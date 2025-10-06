<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisibilitySetting;
use App\Helpers\VisibilityHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VisibilityController extends Controller
{
    /**
     * Display a listing of visibility settings
     */
    public function index()
    {
        $settings = VisibilityHelper::getAllSettings();
        return view('admin.visibility.index', compact('settings'));
    }

    /**
     * Update visibility setting
     */
    public function update(Request $request, $section)
    {
        $request->validate([
            'is_visible' => 'required|boolean'
        ]);

        $isVisible = VisibilityHelper::setVisibilitySetting($section, $request->boolean('is_visible'));

        return response()->json([
            'success' => true,
            'section' => $section,
            'is_visible' => $isVisible,
            'message' => $isVisible ? 'Section is now visible' : 'Section is now hidden'
        ]);
    }

    /**
     * Toggle visibility setting
     */
    public function toggle($section)
    {
        $isVisible = VisibilityHelper::toggleSection($section);

        return response()->json([
            'success' => true,
            'section' => $section,
            'is_visible' => $isVisible,
            'message' => $isVisible ? 'Section is now visible' : 'Section is now hidden'
        ]);
    }

    /**
     * Get visibility configuration for frontend
     */
    public function getConfig()
    {
        return response()->json(VisibilityHelper::getFrontendConfig());
    }

    /**
     * Clear visibility cache
     */
    public function clearCache()
    {
        VisibilityHelper::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Visibility cache cleared successfully'
        ]);
    }

    /**
     * Initialize default settings
     */
    public function initializeDefaults()
    {
        VisibilityHelper::initializeDefaults();

        return response()->json([
            'success' => true,
            'message' => 'Default visibility settings initialized'
        ]);
    }

    /**
     * Bulk update visibility settings
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'boolean'
        ]);

        $result = VisibilityHelper::setMultipleVisibility($request->settings);

        return response()->json([
            'success' => true,
            'settings' => $result,
            'message' => 'Visibility settings updated successfully'
        ]);
    }
}
