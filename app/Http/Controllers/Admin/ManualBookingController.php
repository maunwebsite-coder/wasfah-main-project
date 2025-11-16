<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\WorkshopBooking;
use App\Models\User;
use App\Models\Notification;
use App\Support\NotificationCopy;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ManualBookingController extends Controller
{
    public function index()
    {
        $workshops = Workshop::where('is_active', true)->get();
        $users = User::all();
        
        return view('admin.bookings.manual', compact('workshops', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'workshop_id' => 'required|exists:workshops,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,confirmed,cancelled',
            'payment_status' => 'required|in:pending,paid,refunded',
            'payment_method' => 'nullable|string|max:255',
            'payment_amount' => 'required|numeric|min:0',
            'booking_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $workshop = Workshop::findOrFail($request->workshop_id);

        // التحقق من عدم وجود حجز مكرر
        $existingBooking = WorkshopBooking::where('workshop_id', $workshop->id)
                                         ->where('user_id', $request->user_id)
                                         ->first();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'يوجد حجز سابق لهذا المستخدم في هذه الورشة'
            ], 400);
        }

        // إنشاء الحجز
        $booking = WorkshopBooking::create([
            'workshop_id' => $request->workshop_id,
            'user_id' => $request->user_id,
            'status' => $request->status,
            'booking_date' => Carbon::parse($request->booking_date),
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'payment_amount' => $request->payment_amount,
            'payment_currency' => $workshop->currency,
            'notes' => $request->notes,
            'confirmed_at' => $request->status === 'confirmed' ? now() : null,
        ]);

        // إنشاء إشعار للمستخدم حسب حالة الحجز
        $profileUrl = route('profile');
        $workshop = Workshop::find($request->workshop_id);
        $workshopSlug = $workshop?->slug;
        $bookingShowUrl = route('bookings.show', ['booking' => $booking->id]);
        if ($request->status === 'confirmed') {
            [$title, $message] = NotificationCopy::bookingConfirmed($booking, $workshop);

            Notification::createNotification(
                $request->user_id,
                'workshop_confirmed',
                $title,
                $message,
                [
                    'workshop_id' => $workshop->id, 
                    'booking_id' => $booking->id,
                    'workshop_slug' => $workshopSlug,
                    'profile_url' => $profileUrl,
                    'action_url' => $bookingShowUrl,
                ]
            );
        } elseif ($request->status === 'pending') {
            [$title, $message] = NotificationCopy::bookingPending($booking, $workshop);

            Notification::createNotification(
                $request->user_id,
                'workshop_booking',
                $title,
                $message,
                [
                    'workshop_id' => $workshop->id, 
                    'booking_id' => $booking->id,
                    'workshop_slug' => $workshopSlug,
                    'profile_url' => $profileUrl,
                    'action_url' => $bookingShowUrl,
                ]
            );
        } elseif ($request->status === 'cancelled') {
            [$title, $message] = NotificationCopy::bookingCancelled($booking, null, $workshop);

            Notification::createNotification(
                $request->user_id,
                'workshop_cancelled',
                $title,
                $message,
                [
                    'workshop_id' => $workshop->id, 
                    'booking_id' => $booking->id,
                    'workshop_slug' => $workshopSlug,
                    'profile_url' => $profileUrl,
                    'action_url' => $bookingShowUrl,
                ]
            );
        }

        // سيتم تحديث عدد الحجوزات تلقائياً عبر event listeners

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحجز بنجاح',
            'booking' => $booking->load(['workshop', 'user'])
        ]);
    }

    public function quickAdd(Request $request)
    {
        $request->validate([
            'workshop_id' => 'required|exists:workshops,id',
            'user_name' => 'required|string|max:255',
            'user_phone' => 'required|string|max:20',
            'user_email' => 'required|email|max:255',
            'payment_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $workshop = Workshop::findOrFail($request->workshop_id);

        // إنشاء مستخدم مؤقت أو البحث عن مستخدم موجود
        $user = User::where('email', $request->user_email)->first();
        
        if (!$user) {
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'phone' => $request->user_phone,
                'password' => bcrypt('temp_password_' . time()),
                'is_admin' => false,
                'role' => \App\Models\User::ROLE_CUSTOMER,
                'chef_status' => \App\Models\User::CHEF_STATUS_NEEDS_PROFILE,
            ]);
        }

        // التحقق من عدم وجود حجز مكرر
        $existingBooking = WorkshopBooking::where('workshop_id', $workshop->id)
                                         ->where('user_id', $user->id)
                                         ->first();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'يوجد حجز سابق لهذا المستخدم في هذه الورشة'
            ], 400);
        }

        // إنشاء الحجز
        $booking = WorkshopBooking::create([
            'workshop_id' => $request->workshop_id,
            'user_id' => $user->id,
            'status' => 'pending',
            'booking_date' => now(),
            'payment_status' => 'pending',
            'payment_amount' => $request->payment_amount,
            'payment_currency' => $workshop->currency,
            'notes' => $request->notes . ' (حجز عبر الواتساب)',
        ]);

        // إنشاء إشعار للمستخدم
        $profileUrl = route('profile');
        $workshopSlug = $workshop->slug;
        $bookingShowUrl = route('bookings.show', ['booking' => $booking->id]);
        [$title, $message] = NotificationCopy::bookingPending($booking, $workshop);

        Notification::createNotification(
            $user->id,
            'workshop_booking',
            $title,
            $message,
            [
                'workshop_id' => $workshop->id, 
                'booking_id' => $booking->id,
                'workshop_slug' => $workshopSlug,
                'profile_url' => $profileUrl,
                'action_url' => $bookingShowUrl,
            ]
        );

        // سيتم تحديث عدد الحجوزات تلقائياً عبر event listeners

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحجز بنجاح',
            'booking' => $booking->load(['workshop', 'user'])
        ]);
    }
}
