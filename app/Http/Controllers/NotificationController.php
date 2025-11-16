<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    // صفحة الإشعارات الرئيسية
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Auth::user()->unreadNotificationsCount();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    // API للحصول على الإشعارات (للعرض في القائمة المنسدلة) - مع التخزين المؤقت
    public function api()
    {
        $userId = Auth::id();
        $cacheKey = "notifications_api_{$userId}";
        
        // محاولة الحصول على البيانات من التخزين المؤقت
        $cachedData = Cache::get($cacheKey);
        if ($cachedData) {
            return response()->json($cachedData);
        }

        // جلب البيانات من قاعدة البيانات مع تحسينات إضافية
        $notifications = Auth::user()->notifications()
            ->select('id', 'type', 'title', 'message', 'data', 'is_read', 'created_at') // تحديد الأعمدة المطلوبة فقط
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // حساب العدد غير المقروء بشكل محسن
        $unreadCount = Auth::user()->unreadNotifications()->count();

        $data = [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'timestamp' => now()->timestamp // إضافة timestamp للتحقق من صحة البيانات
        ];

        // حفظ البيانات في التخزين المؤقت لمدة 30 ثانية
        Cache::put($cacheKey, $data, 30);

        return response()->json($data);
    }

    // تحديد إشعار كمقروء
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // مسح التخزين المؤقت
        $this->clearNotificationCache();

        return response()->json(['success' => true]);
    }

    // تحديد جميع الإشعارات كمقروءة
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        $unreadCount = 0;

        // مسح التخزين المؤقت
        $this->clearNotificationCache();

        return response()->json([
            'success' => true,
            'unreadCount' => $unreadCount,
        ]);
    }

    // حذف إشعار
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        // مسح التخزين المؤقت
        $this->clearNotificationCache();

        return response()->json(['success' => true]);
    }

    // حذف جميع الإشعارات المقروءة
    public function clearRead()
    {
        Auth::user()->notifications()
            ->where('is_read', true)
            ->delete();

        // مسح التخزين المؤقت
        $this->clearNotificationCache();

        return response()->json(['success' => true]);
    }

    /**
     * مسح التخزين المؤقت للإشعارات
     */
    private function clearNotificationCache()
    {
        $userId = Auth::id();
        $cacheKey = "notifications_api_{$userId}";
        Cache::forget($cacheKey);
    }
}
