<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\WorkshopBooking;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorkshopBookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'workshop_id' => 'required|exists:workshops,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $workshop = Workshop::findOrFail($request->workshop_id);

        // التحقق من أن المستخدم مسجل دخول
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً'
            ], 401);
        }

        // التحقق من أن الورشة نشطة ومتاحة للحجز
        if (!$workshop->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'الورشة غير متاحة للحجز'
            ], 400);
        }

        if ($workshop->is_fully_booked) {
            return response()->json([
                'success' => false,
                'message' => 'الورشة مكتملة العدد'
            ], 400);
        }

        if (!$workshop->is_registration_open) {
            return response()->json([
                'success' => false,
                'message' => 'انتهى موعد التسجيل'
            ], 400);
        }

        // التحقق من عدم وجود حجز سابق لنفس المستخدم
        $existingBooking = WorkshopBooking::where('workshop_id', $workshop->id)
                                         ->where('user_id', Auth::id())
                                         ->first();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'لديك حجز سابق في هذه الورشة'
            ], 400);
        }

        // إنشاء الحجز
        $booking = WorkshopBooking::create([
            'workshop_id' => $workshop->id,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'booking_date' => now(),
            'payment_status' => 'pending',
            'payment_amount' => $workshop->price,
            'notes' => $request->notes,
        ]);

        // إنشاء إشعار للمستخدم
        Notification::createNotification(
            Auth::id(),
            'workshop_booking',
            'تم إرسال طلب حجز الورشة',
            "تم إرسال طلب حجز ورشة '{$workshop->title}' بنجاح. يرجى التحقق من ملفك الشخصي لمتابعة حالة الحجز والورشات المحجوزة.",
            ['workshop_id' => $workshop->id, 'booking_id' => $booking->id]
        );

        // سيتم تحديث عدد الحجوزات تلقائياً عبر event listeners

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال طلب الحجز بنجاح. يمكنك الآن الدخول إلى حسابك الشخصي لرؤية الورشات المحجوزة.',
            'booking' => $booking
        ]);
    }

    public function index()
    {
        $bookings = WorkshopBooking::with(['workshop', 'user'])
                                  ->where('user_id', Auth::id())
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function show(WorkshopBooking $booking)
    {
        // التحقق من أن الحجز يخص المستخدم الحالي
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        return view('bookings.show', compact('booking'));
    }

    public function cancel(WorkshopBooking $booking)
    {
        // التحقق من أن الحجز يخص المستخدم الحالي
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // التحقق من إمكانية الإلغاء
        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'الحجز ملغي بالفعل'
            ], 400);
        }

        if ($booking->status === 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إلغاء حجز مؤكد'
            ], 400);
        }

        // إلغاء الحجز
        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'تم الإلغاء من قبل المستخدم'
        ]);

        // سيتم تحديث عدد الحجوزات تلقائياً عبر event listeners

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز بنجاح'
        ]);
    }
}
