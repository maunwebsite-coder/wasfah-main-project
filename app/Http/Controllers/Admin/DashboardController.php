<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Workshop;
use App\Models\UserInteraction;
use App\Models\WorkshopBooking;
use App\Models\WorkshopReview;
use App\Models\WorkshopView;
use App\Models\Category;
use App\Models\Tool;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // إحصائيات المستخدمين
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count();
        $adminUsers = User::where('is_admin', true)->count();

        // إحصائيات الوصفات
        $totalRecipes = Recipe::count();
        $newRecipesThisMonth = Recipe::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count();
        $mostPopularRecipe = Recipe::withCount('interactions')
                                  ->orderBy('interactions_count', 'desc')
                                  ->first();

        // إحصائيات الورشات
        $totalWorkshops = Workshop::count();
        $activeWorkshops = Workshop::where('is_active', true)->count();
        $featuredWorkshops = Workshop::where('is_featured', true)->count();
        $totalBookings = WorkshopBooking::count();
        $confirmedBookings = WorkshopBooking::where('status', 'confirmed')->count();
        $pendingBookings = WorkshopBooking::where('status', 'pending')->count();

        // إحصائيات التفاعلات
        $totalInteractions = UserInteraction::count();
        $savedRecipes = UserInteraction::where('is_saved', true)->count();
        $madeRecipes = UserInteraction::where('is_made', true)->count();
        $totalRatings = UserInteraction::whereNotNull('rating')->count();
        $averageRating = UserInteraction::whereNotNull('rating')->avg('rating');

        // إحصائيات الأدوات
        $totalTools = Tool::count();
        $activeTools = Tool::where('is_active', true)->count();

        // إحصائيات التصنيفات
        $totalCategories = Category::count();

        // إحصائيات الإيرادات (من الورشات)
        $totalRevenue = WorkshopBooking::where('status', 'confirmed')
                                      ->join('workshops', 'workshop_bookings.workshop_id', '=', 'workshops.id')
                                      ->sum('workshops.price');

        $monthlyRevenue = WorkshopBooking::where('status', 'confirmed')
                                        ->whereMonth('workshop_bookings.created_at', now()->month)
                                        ->whereYear('workshop_bookings.created_at', now()->year)
                                        ->join('workshops', 'workshop_bookings.workshop_id', '=', 'workshops.id')
                                        ->sum('workshops.price');

        // إحصائيات الأسبوع الماضي
        $lastWeekUsers = User::where('created_at', '>=', now()->subWeek())->count();
        $lastWeekRecipes = Recipe::where('created_at', '>=', now()->subWeek())->count();
        $lastWeekBookings = WorkshopBooking::where('created_at', '>=', now()->subWeek())->count();

        // الوصفات الأكثر شعبية (آخر 30 يوم)
        $popularRecipes = Recipe::withCount(['interactions' => function($query) {
                                    $query->where('created_at', '>=', now()->subDays(30));
                                }])
                                ->orderBy('interactions_count', 'desc')
                                ->limit(5)
                                ->get();

        // الورشات الأكثر حجزاً
        $popularWorkshops = Workshop::withCount('bookings')
                                   ->orderBy('bookings_count', 'desc')
                                   ->limit(5)
                                   ->get();

        // المستخدمون النشطون (آخر 30 يوم)
        $activeUsers = User::whereHas('interactions', function($query) {
                            $query->where('created_at', '>=', now()->subDays(30));
                        })
                        ->count();

        // إحصائيات النمو الشهرية
        $monthlyGrowth = [
            'users' => $this->calculateGrowthRate(User::class, 'users'),
            'recipes' => $this->calculateGrowthRate(Recipe::class, 'recipes'),
            'workshops' => $this->calculateGrowthRate(Workshop::class, 'workshops'),
            'bookings' => $this->calculateGrowthRate(WorkshopBooking::class, 'bookings'),
        ];

        // إحصائيات إضافية جديدة
        $todayUsers = User::whereDate('created_at', today())->count();
        $todayRecipes = Recipe::whereDate('created_at', today())->count();
        $todayBookings = WorkshopBooking::whereDate('created_at', today())->count();
        $todayRevenue = WorkshopBooking::where('status', 'confirmed')
                                      ->whereDate('workshop_bookings.created_at', today())
                                      ->join('workshops', 'workshop_bookings.workshop_id', '=', 'workshops.id')
                                      ->sum('workshops.price') ?? 0;

        // إحصائيات الحجوزات التفصيلية
        $cancelledBookings = WorkshopBooking::where('status', 'cancelled')->count();
        $completedBookings = WorkshopBooking::where('status', 'completed')->count();
        $refundedBookings = WorkshopBooking::where('status', 'refunded')->count();

        // إحصائيات الورشات التفصيلية
        $upcomingWorkshops = Workshop::where('start_date', '>', now())->count();
        $completedWorkshops = Workshop::where('end_date', '<', now())->count();
        $workshopCapacity = Workshop::sum('max_participants') ?? 0;
        $totalParticipants = WorkshopBooking::where('status', 'confirmed')->count();

        // إحصائيات التقييمات
        $workshopReviews = WorkshopReview::count();
        $averageWorkshopRating = WorkshopReview::avg('rating') ?? 0;
        $highRatedWorkshops = Workshop::whereIn('id', function ($subquery) {
            $subquery->select('workshop_id')
                ->from('workshop_reviews')
                ->groupBy('workshop_id')
                ->havingRaw('AVG(rating) >= 4.5');
        })->count();

        // إحصائيات البحث والاستخدام
        $totalViews = WorkshopView::count();
        $uniqueViewers = WorkshopView::distinct('ip_address')->count(); // استخدام IP بدلاً من user_id
        $mostViewedWorkshop = Workshop::withCount('views')
                                    ->orderBy('views_count', 'desc')
                                    ->first();

        // الأنشطة الأخيرة
        $recentUsers = User::latest()->limit(5)->get();
        $recentRecipes = Recipe::latest()->limit(5)->get();
        $recentBookings = WorkshopBooking::with(['user', 'workshop'])
                                        ->latest()
                                        ->limit(5)
                                        ->get();
        $recentInteractions = UserInteraction::with(['user', 'recipe'])
                                            ->latest()
                                            ->limit(10)
                                            ->get();

        // إحصائيات الأداء
        $conversionRate = $totalUsers > 0 ? round(($totalBookings / $totalUsers) * 100, 2) : 0;
        $workshopFillRate = $workshopCapacity > 0 ? round(($totalParticipants / $workshopCapacity) * 100, 2) : 0;
        $userRetentionRate = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0;

        // إحصائيات الأيام
        $last7DaysStats = $this->getLast7DaysStats();
        $last30DaysStats = $this->getLast30DaysStats();

        return view('admin.dashboard', compact(
            'totalUsers', 'newUsersThisMonth', 'adminUsers',
            'totalRecipes', 'newRecipesThisMonth', 'mostPopularRecipe',
            'totalWorkshops', 'activeWorkshops', 'featuredWorkshops',
            'totalBookings', 'confirmedBookings', 'pendingBookings',
            'totalInteractions', 'savedRecipes', 'madeRecipes', 'totalRatings', 'averageRating',
            'totalTools', 'activeTools', 'totalCategories',
            'totalRevenue', 'monthlyRevenue',
            'lastWeekUsers', 'lastWeekRecipes', 'lastWeekBookings',
            'popularRecipes', 'popularWorkshops', 'activeUsers', 'monthlyGrowth',
            'todayUsers', 'todayRecipes', 'todayBookings', 'todayRevenue',
            'cancelledBookings', 'completedBookings', 'refundedBookings',
            'upcomingWorkshops', 'completedWorkshops', 'workshopCapacity', 'totalParticipants',
            'workshopReviews', 'averageWorkshopRating', 'highRatedWorkshops',
            'totalViews', 'uniqueViewers', 'mostViewedWorkshop',
            'recentUsers', 'recentRecipes', 'recentBookings', 'recentInteractions',
            'conversionRate', 'workshopFillRate', 'userRetentionRate',
            'last7DaysStats', 'last30DaysStats'
        ));
    }

    private function calculateGrowthRate($model, $type)
    {
        $currentMonth = $model::whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year)
                             ->count();
        
        $lastMonth = $model::whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year)
                          ->count();

        if ($lastMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 2);
    }

    private function getLast7DaysStats()
    {
        $stats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $stats[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->format('l'),
                'users' => User::whereDate('created_at', $date)->count(),
                'recipes' => Recipe::whereDate('created_at', $date)->count(),
                'bookings' => WorkshopBooking::whereDate('created_at', $date)->count(),
                'revenue' => WorkshopBooking::where('status', 'confirmed')
                                          ->whereDate('workshop_bookings.created_at', $date)
                                          ->join('workshops', 'workshop_bookings.workshop_id', '=', 'workshops.id')
                                          ->sum('workshops.price') ?? 0
            ];
        }
        return $stats;
    }

    private function getLast30DaysStats()
    {
        $stats = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $stats[] = [
                'date' => $date->format('Y-m-d'),
                'users' => User::whereDate('created_at', $date)->count(),
                'recipes' => Recipe::whereDate('created_at', $date)->count(),
                'bookings' => WorkshopBooking::whereDate('created_at', $date)->count(),
                'revenue' => WorkshopBooking::where('status', 'confirmed')
                                          ->whereDate('workshop_bookings.created_at', $date)
                                          ->join('workshops', 'workshop_bookings.workshop_id', '=', 'workshops.id')
                                          ->sum('workshops.price') ?? 0
            ];
        }
        return $stats;
    }
}
