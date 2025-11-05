<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\WorkshopView;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    /**
     * عرض صفحة الورشات
     */
    public function index(Request $request)
    {
        $query = Workshop::active();

        // فلترة حسب الفئة
        if ($request->has('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        // فلترة حسب المستوى
        if ($request->has('level') && $request->level !== 'all') {
            $query->byLevel($request->level);
        }

        // فلترة حسب النوع (أونلاين/أوفلاين)
        if ($request->has('type')) {
            if ($request->type === 'online') {
                $query->online();
            } elseif ($request->type === 'offline') {
                $query->offline();
            }
        }

        // فلترة حسب السعر
        if ($request->has('price_range') && $request->price_range) {
            switch ($request->price_range) {
                case '0-100':
                    $query->where('price', '<=', 100);
                    break;
                case '100-200':
                    $query->whereBetween('price', [100, 200]);
                    break;
                case '200-300':
                    $query->whereBetween('price', [200, 300]);
                    break;
                case '300+':
                    $query->where('price', '>', 300);
                    break;
            }
        }

        // ترتيب النتائج
        $sortBy = $request->get('sort', 'start_date');
        $sortDirection = $request->get('direction', 'asc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortDirection);
                break;
            case 'rating':
                $query->orderBy('rating', $sortDirection);
                break;
            case 'popularity':
                $query->orderBy('bookings_count', 'desc');
                break;
            case 'start_date':
            default:
                $query->orderBy('start_date', $sortDirection);
                break;
        }

        $workshops = $query->withCount(['bookings' => function ($query) {
            $query->where('status', 'confirmed');
        }])->get();

        // جلب الورشة المميزة (الورشة القادمة) مع التعامل مع حال
        // عدم وجود ورشة قادمة مستقبلية ولكن تم تحديد ورشة مميزة
        $featuredWorkshopQuery = Workshop::active()
            ->featured()
            ->withCount(['bookings' => function ($query) {
                $query->where('status', 'confirmed');
            }]);

        $featuredWorkshop = (clone $featuredWorkshopQuery)
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->first();

        if (!$featuredWorkshop) {
            $featuredWorkshop = $featuredWorkshopQuery
                ->orderBy('start_date', 'desc')
                ->first();
        }

        // إحصائيات للفلترة
        $categories = Workshop::active()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        $levels = Workshop::active()
            ->selectRaw('level, COUNT(*) as count')
            ->groupBy('level')
            ->get();

        $bookedWorkshopIds = [];

        if (auth()->check()) {
            $bookedWorkshopIds = auth()->user()
                ->workshopBookings()
                ->pluck('workshop_id')
                ->unique()
                ->values()
                ->all();
        }

        return view('workshops', compact(
            'workshops',
            'featuredWorkshop',
            'categories',
            'levels',
            'bookedWorkshopIds'
        ));
    }

    /**
     * عرض تفاصيل ورشة محددة
     */
    public function show(Workshop $workshop)
    {
        // الحصول على عنوان IP للمستخدم
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();

        // فحص إذا كان هذا IP قد شاهد الورشة من قبل، وإذا لم يشاهد أضف مشاهدة جديدة
        $view = WorkshopView::firstOrCreate(
            [
                'workshop_id' => $workshop->id,
                'ip_address' => $ipAddress,
            ],
            [
                'user_agent' => $userAgent,
                'viewed_at' => now(),
            ]
        );

        // إذا كانت هذه مشاهدة جديدة (تم إنشاؤها للتو)، زد العداد
        if ($view->wasRecentlyCreated) {
            $workshop->increment('views_count');
        }

        // جلب الوصفات المرتبطة بالورشة
        $workshop->load('recipes');

        // ورشات مشابهة
        $relatedWorkshops = Workshop::active()
            ->upcoming()
            ->withCount(['bookings' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->where('id', '!=', $workshop->id)
            ->where('category', $workshop->category)
            ->limit(4)
            ->get();

        $userBooking = null;

        if (auth()->check()) {
            $userBooking = $workshop->bookings()
                ->where('user_id', auth()->id())
                ->latest()
                ->first();
        }

        return view('workshop-details', compact('workshop', 'relatedWorkshops', 'userBooking'));
    }

    /**
     * البحث في الورشات
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('workshops');
        }

        $workshops = Workshop::active()
            ->withCount(['bookings' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('instructor', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%");
            })
            ->orderBy('start_date')
            ->get();

        return view('workshops', compact('workshops'))->with('searchQuery', $query);
    }

    /**
     * API endpoint لجلب الورشات
     */
    public function apiIndex(Request $request)
    {
        $query = Workshop::active()->upcoming();

        // فلترة حسب الفئة
        if ($request->has('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        // فلترة حسب المستوى
        if ($request->has('level') && $request->level !== 'all') {
            $query->byLevel($request->level);
        }

        // فلترة حسب النوع
        if ($request->has('type')) {
            if ($request->type === 'online') {
                $query->online();
            } elseif ($request->type === 'offline') {
                $query->offline();
            }
        }

        // ترتيب النتائج
        $sortBy = $request->get('sort', 'start_date');
        $sortDirection = $request->get('direction', 'asc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortDirection);
                break;
            case 'rating':
                $query->orderBy('rating', $sortDirection);
                break;
            case 'start_date':
            default:
                $query->orderBy('start_date', $sortDirection);
                break;
        }

        $workshops = $query->withCount(['bookings' => function ($query) {
            $query->where('status', 'confirmed');
        }])->paginate($request->get('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => $workshops,
            'categories' => Workshop::active()->upcoming()
                ->selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get(),
            'levels' => Workshop::active()->upcoming()
                ->selectRaw('level, COUNT(*) as count')
                ->groupBy('level')
                ->get(),
        ]);
    }

    /**
     * Test method for debugging workshops
     */
    public function testSimple()
    {
        $query = Workshop::active()->upcoming();

        // جلب الورشة المميزة (الورشة القادمة)
        $featuredWorkshop = Workshop::active()
            ->featured()
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->first();

        $workshops = $query->paginate(12);

        return view('test-workshop-simple', compact('workshops', 'featuredWorkshop'));
    }
}
