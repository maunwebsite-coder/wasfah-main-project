<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\SavedTool;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SavedController extends Controller
{
    /**
     * Add tool to saved list
     */
    public function add(Request $request): JsonResponse
    {
        // فقط للمستخدمين المسجلين
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ], 401);
        }

        $request->validate([
            'tool_id' => 'required|integer|exists:tools,id'
        ]);

        $userId = auth()->id();

        // Check if already saved
        $existingSaved = SavedTool::forUser($userId)
            ->where('tool_id', $request->tool_id)
            ->first();

        if ($existingSaved) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج محفوظ بالفعل'
            ]);
        }

        // Add to saved
        SavedTool::create([
            'user_id' => $userId,
            'tool_id' => $request->tool_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ المنتج للشراء لاحقاً'
        ]);
    }

    /**
     * Remove tool from saved list
     */
    public function remove(Request $request): JsonResponse
    {
        // فقط للمستخدمين المسجلين
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ], 401);
        }

        $request->validate([
            'tool_id' => 'required|integer|exists:tools,id'
        ]);

        $userId = auth()->id();

        $savedTool = SavedTool::forUser($userId)
            ->where('tool_id', $request->tool_id)
            ->first();

        if (!$savedTool) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير موجود في المحفوظات'
            ]);
        }

        $savedTool->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المنتج من المحفوظات'
        ]);
    }

    /**
     * Get saved tools count
     */
    public function count(): JsonResponse
    {
        // فقط للمستخدمين المسجلين
        if (!auth()->check()) {
            return response()->json(['count' => 0]);
        }

        $userId = auth()->id();
        $count = SavedTool::forUser($userId)->count();

        // Debug log
        \Log::info('Saved count request', [
            'user_id' => $userId,
            'count' => $count,
            'auth_check' => auth()->check()
        ]);

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Get saved tools status
     */
    public function status(): JsonResponse
    {
        // فقط للمستخدمين المسجلين
        if (!auth()->check()) {
            return response()->json([
                'success' => true,
                'saved_tools' => []
            ]);
        }

        $userId = auth()->id();
        $savedTools = SavedTool::forUser($userId)
            ->pluck('tool_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'saved_tools' => $savedTools
        ]);
    }

    /**
     * Show saved tools page
     */
    public function index()
    {
        // فقط للمستخدمين المسجلين
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $userId = auth()->id();
        $savedTools = SavedTool::forUser($userId)
            ->with('tool')
            ->get();

        return view('saved', compact('savedTools'));
    }
}
