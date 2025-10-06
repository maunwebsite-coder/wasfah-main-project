<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkshopBooking;
use App\Models\Workshop;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkshopBooking::with(['workshop', 'user']);

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب حالة الدفع
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // فلترة حسب الورشة
        if ($request->filled('workshop_id')) {
            $query->where('workshop_id', $request->workshop_id);
        }

        // فلترة حسب التاريخ - تحسين معالجة التواريخ
        if ($request->filled('date_from')) {
            try {
                $dateFrom = Carbon::parse($request->date_from)->startOfDay();
                $query->where('created_at', '>=', $dateFrom);
            } catch (\Exception $e) {
                // تجاهل التاريخ غير الصحيح
            }
        }

        if ($request->filled('date_to')) {
            try {
                $dateTo = Carbon::parse($request->date_to)->endOfDay();
                $query->where('created_at', '<=', $dateTo);
            } catch (\Exception $e) {
                // تجاهل التاريخ غير الصحيح
            }
        }

        // فلترة حسب نوع الورشة (أونلاين/أوفلاين)
        if ($request->filled('workshop_type')) {
            $query->whereHas('workshop', function($q) use ($request) {
                if ($request->workshop_type === 'online') {
                    $q->where('is_online', true);
                } elseif ($request->workshop_type === 'offline') {
                    $q->where('is_online', false);
                }
            });
        }

        // فلترة حسب نطاق السعر
        if ($request->filled('price_range')) {
            $query->whereHas('workshop', function($q) use ($request) {
                switch ($request->price_range) {
                    case '0-50':
                        $q->where('price', '<=', 50);
                        break;
                    case '50-100':
                        $q->whereBetween('price', [50, 100]);
                        break;
                    case '100-200':
                        $q->whereBetween('price', [100, 200]);
                        break;
                    case '200-500':
                        $q->whereBetween('price', [200, 500]);
                        break;
                    case '500+':
                        $q->where('price', '>', 500);
                        break;
                }
            });
        }

        // فلترة حسب تاريخ الورشة
        if ($request->filled('workshop_date_from')) {
            try {
                $workshopDateFrom = Carbon::parse($request->workshop_date_from)->startOfDay();
                $query->whereHas('workshop', function($q) use ($workshopDateFrom) {
                    $q->where('start_date', '>=', $workshopDateFrom);
                });
            } catch (\Exception $e) {
                // تجاهل التاريخ غير الصحيح
            }
        }

        if ($request->filled('workshop_date_to')) {
            try {
                $workshopDateTo = Carbon::parse($request->workshop_date_to)->endOfDay();
                $query->whereHas('workshop', function($q) use ($workshopDateTo) {
                    $q->where('start_date', '<=', $workshopDateTo);
                });
            } catch (\Exception $e) {
                // تجاهل التاريخ غير الصحيح
            }
        }

        // فلترة حسب طريقة الدفع
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // فلترة حسب عدد الحجوزات
        if ($request->filled('booking_count')) {
            if ($request->booking_count === 'single') {
                $query->whereHas('user', function($q) {
                    $q->has('workshopBookings', '=', 1);
                });
            } elseif ($request->booking_count === 'multiple') {
                $query->whereHas('user', function($q) {
                    $q->has('workshopBookings', '>', 1);
                });
            }
        }

        // البحث المحسن
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('workshop', function($workshopQuery) use ($search) {
                    $workshopQuery->where('title', 'like', "%{$search}%")
                                 ->orWhere('instructor', 'like', "%{$search}%");
                });
            });
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        switch ($sortBy) {
            case 'payment_amount':
                $query->orderBy('payment_amount', $sortDirection);
                break;
            case 'workshop_start_date':
                $query->join('workshops', 'workshop_bookings.workshop_id', '=', 'workshops.id')
                      ->orderBy('workshops.start_date', $sortDirection)
                      ->select('workshop_bookings.*');
                break;
            default:
                $query->orderBy('created_at', $sortDirection);
                break;
        }

        $bookings = $query->paginate(20);

        // إحصائيات سريعة - تحسين الأداء مع cache
        $stats = cache()->remember('booking_stats', 300, function () {
            return [
                'total' => WorkshopBooking::count(),
                'pending' => WorkshopBooking::where('status', 'pending')->count(),
                'confirmed' => WorkshopBooking::where('status', 'confirmed')->count(),
                'cancelled' => WorkshopBooking::where('status', 'cancelled')->count(),
                'paid' => WorkshopBooking::where('payment_status', 'paid')->count(),
                'unpaid' => WorkshopBooking::where('payment_status', 'pending')->count(),
            ];
        });

        // قائمة الورشات للفلترة - ترتيب أبجدي
        $workshops = Workshop::select('id', 'title')
                            ->orderBy('title')
                            ->get();

        // طرق الدفع المتاحة
        $paymentMethods = WorkshopBooking::select('payment_method')
                                        ->whereNotNull('payment_method')
                                        ->distinct()
                                        ->pluck('payment_method')
                                        ->filter()
                                        ->values();

        return view('admin.bookings.index', compact('bookings', 'stats', 'workshops', 'paymentMethods'));
    }

    public function show(WorkshopBooking $booking)
    {
        $booking->load(['workshop', 'user']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function confirm(WorkshopBooking $booking)
    {
        if ($booking->status === 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'الحجز مؤكد بالفعل'
            ], 400);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تأكيد حجز ملغي'
            ], 400);
        }

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);

        // إنشاء إشعار للمستخدم عند تأكيد الحجز
        $profileUrl = route('profile');
        Notification::createNotification(
            $booking->user_id,
            'workshop_confirmed',
            'تم تأكيد حجز الورشة',
            "تم تأكيد حجز ورشة '{$booking->workshop->title}' بنجاح! يمكنك الآن الدخول إلى ملفك الشخصي لمتابعة تفاصيل الورشة",
            [
                'workshop_id' => $booking->workshop_id, 
                'booking_id' => $booking->id,
                'profile_url' => $profileUrl
            ]
        );
        
        // إشعار إضافي: ترحيب بالورشة
        Notification::createNotification(
            $booking->user_id,
            'general',
            'مرحباً بك في ورشة ' . $booking->workshop->title,
            "نحن متحمسون لرؤيتك في ورشة '{$booking->workshop->title}'! تأكد من الوصول في الوقت المحدد.",
            [
                'workshop_id' => $booking->workshop_id,
                'workshop_title' => $booking->workshop->title
            ]
        );
        
        // إشعار إضافي: تذكير بالورشة
        Notification::createNotification(
            $booking->user_id,
            'general',
            'تذكير: ورشة ' . $booking->workshop->title,
            "ورشة '{$booking->workshop->title}' مؤكدة! لا تنس مراجعة تفاصيل الورشة في ملفك الشخصي قبل الموعد المحدد.",
            [
                'workshop_id' => $booking->workshop_id,
                'workshop_title' => $booking->workshop->title
            ]
        );

        // مسح cache الإحصائيات
        cache()->forget('booking_stats');

        return response()->json([
            'success' => true,
            'message' => 'تم تأكيد الحجز بنجاح'
        ]);
    }

    public function cancel(WorkshopBooking $booking, Request $request)
    {
        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'الحجز ملغي بالفعل'
            ], 400);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->cancellation_reason ?? 'تم الإلغاء من قبل الإدارة'
        ]);

        // إنشاء إشعار للمستخدم عند إلغاء الحجز
        $profileUrl = route('profile');
        Notification::createNotification(
            $booking->user_id,
            'workshop_cancelled',
            'تم إلغاء حجز الورشة',
            "تم إلغاء حجز ورشة '{$booking->workshop->title}'. يمكنك الدخول إلى ملفك الشخصي لمتابعة تفاصيل الإلغاء: {$profileUrl}",
            [
                'workshop_id' => $booking->workshop_id, 
                'booking_id' => $booking->id,
                'profile_url' => $profileUrl,
                'cancellation_reason' => $request->cancellation_reason ?? 'تم الإلغاء من قبل الإدارة'
            ]
        );
        
        // إشعار إضافي: اعتذار عن الإلغاء
        Notification::createNotification(
            $booking->user_id,
            'general',
            'اعتذار عن إلغاء ورشة ' . $booking->workshop->title,
            "نعتذر عن إلغاء ورشة '{$booking->workshop->title}'. نأمل أن نراك في ورشات أخرى قريباً. تحقق من الورشات المتاحة في موقعنا.",
            [
                'workshop_id' => $booking->workshop_id,
                'workshop_title' => $booking->workshop->title
            ]
        );
        
        // إشعار إضافي: ورشات بديلة
        Notification::createNotification(
            $booking->user_id,
            'general',
            'ورشات بديلة متاحة',
            "نقترح عليك تصفح الورشات الأخرى المتاحة. قد تجد ورشة أخرى تناسب اهتماماتك وتوقيتك.",
            [
                'workshop_id' => $booking->workshop_id,
                'workshop_title' => $booking->workshop->title
            ]
        );

        // سيتم تحديث عدد الحجوزات تلقائياً عبر event listeners

        // مسح cache الإحصائيات
        cache()->forget('booking_stats');

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز بنجاح'
        ]);
    }

    public function updatePayment(WorkshopBooking $booking, Request $request)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,refunded',
            'payment_method' => 'nullable|string|max:255',
        ]);

        $booking->update([
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
        ]);

        // مسح cache الإحصائيات
        cache()->forget('booking_stats');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الدفع بنجاح'
        ]);
    }
}
