<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingRevenueShare;
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
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->validateFilters($request);
        $periodOptions = $this->periodOptions();
        $workshopModeOptions = $this->workshopModeOptions();

        $metrics = Cache::remember(
            $this->cacheKeyFor($filters['period'], $filters['workshop_mode']),
            now()->addMinutes(5),
            fn () => $this->buildMetrics($filters['period'], $filters['workshop_mode'])
        );

        return view('admin.dashboard', array_merge($metrics, [
            'periodOptions' => $periodOptions,
            'workshopModeOptions' => $workshopModeOptions,
            'selectedPeriod' => $filters['period'],
            'selectedWorkshopMode' => $filters['workshop_mode'],
        ]));
    }

    private function buildMetrics(int $periodDays, string $workshopMode): array
    {
        $periodContext = $this->buildPeriodContext($periodDays);
        $filterContext = [
            'period_label' => $periodContext['label'],
            'range' => $periodContext['formatted_range'],
            'workshop_label' => $this->workshopModeOptions()[$workshopMode] ?? $this->workshopModeOptions()['all'],
        ];
        $selectedPeriodSummary = $this->buildPeriodSummary($periodContext, $workshopMode);
        $selectedPeriodStats = $this->getPeriodStats($periodDays, $workshopMode);

        // إحصائيات المستخدمين
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count();
        $adminUsers = User::where(function ($query) {
                                $query->where('is_admin', true)
                                      ->orWhere('role', User::ROLE_ADMIN);
                            })->count();

        // إحصائيات الوصفات
        $totalRecipes = Recipe::count();
        $newRecipesThisMonth = Recipe::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count();
        $mostPopularRecipe = Recipe::approved()->public()
                                  ->withCount('interactions')
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
                                      ->where('payment_status', 'paid')
                                      ->sum('payment_amount');

        $monthlyRevenue = WorkshopBooking::where('status', 'confirmed')
                                        ->where('payment_status', 'paid')
                                        ->whereMonth('workshop_bookings.created_at', now()->month)
                                        ->whereYear('workshop_bookings.created_at', now()->year)
                                        ->sum('payment_amount');

        $currentMonthRange = [now()->copy()->startOfMonth(), now()->copy()->endOfMonth()];
        $shareBaseQuery = BookingRevenueShare::query()
            ->where('status', BookingRevenueShare::STATUS_DISTRIBUTED);

        $platformRevenue = (clone $shareBaseQuery)
            ->where('recipient_type', BookingRevenueShare::TYPE_ADMIN)
            ->sum('amount');

        $platformMonthlyRevenue = (clone $shareBaseQuery)
            ->where('recipient_type', BookingRevenueShare::TYPE_ADMIN)
            ->whereBetween('distributed_at', $currentMonthRange)
            ->sum('amount');

        $chefPayoutTotal = (clone $shareBaseQuery)
            ->where('recipient_type', BookingRevenueShare::TYPE_CHEF)
            ->sum('amount');

        $chefMonthlyPayout = (clone $shareBaseQuery)
            ->where('recipient_type', BookingRevenueShare::TYPE_CHEF)
            ->whereBetween('distributed_at', $currentMonthRange)
            ->sum('amount');

        $partnerPayoutTotal = (clone $shareBaseQuery)
            ->where('recipient_type', BookingRevenueShare::TYPE_PARTNER)
            ->sum('amount');

        $partnerMonthlyPayout = (clone $shareBaseQuery)
            ->where('recipient_type', BookingRevenueShare::TYPE_PARTNER)
            ->whereBetween('distributed_at', $currentMonthRange)
            ->sum('amount');

        // إحصائيات الأسبوع الماضي
        $lastWeekUsers = User::where('created_at', '>=', now()->subWeek())->count();
        $lastWeekRecipes = Recipe::where('created_at', '>=', now()->subWeek())->count();
        $lastWeekBookings = WorkshopBooking::where('created_at', '>=', now()->subWeek())->count();

        // الوصفات الأكثر شعبية (آخر 30 يوم)
        $popularRecipes = Recipe::approved()->public()
                                ->withCount(['interactions' => function($query) {
                                    $query->where('created_at', '>=', now()->subDays(30));
                                }])
                                ->orderBy('interactions_count', 'desc')
                                ->limit(5)
                                ->get();

        // الورشات الأكثر حجزاً
        $popularWorkshopsQuery = Workshop::withCount('bookings')
                                   ->orderBy('bookings_count', 'desc');
        $this->applyWorkshopModeConstraint($popularWorkshopsQuery, $workshopMode);
        $popularWorkshops = $popularWorkshopsQuery
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
        $mostViewedWorkshopQuery = Workshop::withCount('views')
                                    ->orderBy('views_count', 'desc');
        $this->applyWorkshopModeConstraint($mostViewedWorkshopQuery, $workshopMode);
        $mostViewedWorkshop = $mostViewedWorkshopQuery->first();

        // الأنشطة الأخيرة
        $recentUsers = User::latest()->limit(5)->get();
        $recentRecipes = Recipe::latest()->limit(5)->get();
        $recentBookingsQuery = WorkshopBooking::with(['user', 'workshop'])
                                        ->latest();
        $recentBookingsQuery = $this->applyWorkshopModeScope($recentBookingsQuery, $workshopMode);
        $recentBookings = $recentBookingsQuery
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
        $last7DaysStats = $this->getLast7DaysStats($workshopMode);
        $last30DaysStats = $this->getLast30DaysStats($workshopMode);

        return compact(
            'totalUsers', 'newUsersThisMonth', 'adminUsers',
            'totalRecipes', 'newRecipesThisMonth', 'mostPopularRecipe',
            'totalWorkshops', 'activeWorkshops', 'featuredWorkshops',
            'totalBookings', 'confirmedBookings', 'pendingBookings',
            'totalInteractions', 'savedRecipes', 'madeRecipes', 'totalRatings', 'averageRating',
            'totalTools', 'activeTools', 'totalCategories',
            'totalRevenue', 'monthlyRevenue', 'platformRevenue', 'platformMonthlyRevenue',
            'chefPayoutTotal', 'chefMonthlyPayout', 'partnerPayoutTotal', 'partnerMonthlyPayout',
            'lastWeekUsers', 'lastWeekRecipes', 'lastWeekBookings',
            'popularRecipes', 'popularWorkshops', 'activeUsers', 'monthlyGrowth',
            'todayUsers', 'todayRecipes', 'todayBookings', 'todayRevenue',
            'cancelledBookings', 'completedBookings', 'refundedBookings',
            'upcomingWorkshops', 'completedWorkshops', 'workshopCapacity', 'totalParticipants',
            'workshopReviews', 'averageWorkshopRating', 'highRatedWorkshops',
            'totalViews', 'uniqueViewers', 'mostViewedWorkshop',
            'recentUsers', 'recentRecipes', 'recentBookings', 'recentInteractions',
            'conversionRate', 'workshopFillRate', 'userRetentionRate',
            'last7DaysStats', 'last30DaysStats',
            'periodContext', 'filterContext', 'selectedPeriodSummary', 'selectedPeriodStats'
        );
    }

    private function validateFilters(Request $request): array
    {
        $validated = $request->validate([
            'period' => 'nullable|in:7,14,30,60,90',
            'workshop_mode' => 'nullable|in:all,online,in_person,featured',
        ]);

        return [
            'period' => (int) ($validated['period'] ?? 30),
            'workshop_mode' => $validated['workshop_mode'] ?? 'all',
        ];
    }

    private function cacheKeyFor(int $periodDays, string $workshopMode): string
    {
        return sprintf('admin.dashboard.%d.%s', $periodDays, $workshopMode);
    }

    private function periodOptions(): array
    {
        return [
            7 => 'آخر 7 أيام',
            14 => 'آخر 14 يوم',
            30 => 'آخر 30 يوم',
            60 => 'آخر 60 يوم',
            90 => 'آخر 90 يوم',
        ];
    }

    private function workshopModeOptions(): array
    {
        return [
            'all' => 'جميع الورشات',
            'online' => 'ورشات أونلاين فقط',
            'in_person' => 'ورشات حضورية فقط',
            'featured' => 'ورشات مميزة فقط',
        ];
    }

    private function buildPeriodContext(int $periodDays): array
    {
        $periodDays = max(1, $periodDays);
        $end = now()->copy()->endOfDay();
        $start = $end->copy()->subDays($periodDays - 1)->startOfDay();

        return [
            'days' => $periodDays,
            'start' => $start,
            'end' => $end,
            'label' => $this->periodOptions()[$periodDays] ?? __('آخر :days يوم', ['days' => $periodDays]),
            'formatted_range' => sprintf('%s — %s', $start->format('Y-m-d'), $end->format('Y-m-d')),
        ];
    }

    private function buildPeriodSummary(array $periodContext, string $workshopMode): array
    {
        $currentStart = $periodContext['start'];
        $currentEnd = $periodContext['end'];
        $previousStart = $currentStart->copy()->subDays($periodContext['days']);
        $previousEnd = $currentStart->copy()->subDay()->endOfDay();

        $currentUsers = User::whereBetween('created_at', [$currentStart, $currentEnd])->count();
        $previousUsers = User::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $currentRecipes = Recipe::whereBetween('created_at', [$currentStart, $currentEnd])->count();
        $previousRecipes = Recipe::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $currentBookings = $this->applyWorkshopModeScope(
            WorkshopBooking::query()->whereBetween('created_at', [$currentStart, $currentEnd]),
            $workshopMode
        )->count();

        $previousBookings = $this->applyWorkshopModeScope(
            WorkshopBooking::query()->whereBetween('created_at', [$previousStart, $previousEnd]),
            $workshopMode
        )->count();

        $currentRevenue = $this->sumRevenueBetween($currentStart, $currentEnd, $workshopMode);
        $previousRevenue = $this->sumRevenueBetween($previousStart, $previousEnd, $workshopMode);
        $currencyCode = config('finance.default_currency', 'USD');

        $modeLabel = $this->workshopModeOptions()[$workshopMode] ?? $this->workshopModeOptions()['all'];

        return [
            [
                'key' => 'users',
                'label' => 'المستخدمون الجدد',
                'value' => $currentUsers,
                'change' => $this->calculateDeltaBetween($currentUsers, $previousUsers),
                'description' => 'ضمن ' . $periodContext['label'],
                'icon' => 'fa-users',
                'unit' => '',
                'is_currency' => false,
            ],
            [
                'key' => 'recipes',
                'label' => 'الوصفات المنشورة',
                'value' => $currentRecipes,
                'change' => $this->calculateDeltaBetween($currentRecipes, $previousRecipes),
                'description' => 'تمت إضافتها خلال المدة المحددة',
                'icon' => 'fa-book-open',
                'unit' => '',
                'is_currency' => false,
            ],
            [
                'key' => 'bookings',
                'label' => 'الحجوزات',
                'value' => $currentBookings,
                'change' => $this->calculateDeltaBetween($currentBookings, $previousBookings),
                'description' => $modeLabel,
                'icon' => 'fa-calendar-check',
                'unit' => '',
                'is_currency' => false,
            ],
            [
                'key' => 'revenue',
                'label' => 'الإيرادات المؤكدة',
                'value' => $currentRevenue,
                'change' => $this->calculateDeltaBetween($currentRevenue, $previousRevenue),
                'description' => 'صافي المدفوعات المؤكدة',
                'icon' => 'fa-coins',
                'unit' => $currencyCode,
                'is_currency' => true,
            ],
        ];
    }

    private function calculateDeltaBetween(float|int $current, float|int $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }

    private function applyWorkshopModeScope(EloquentBuilder $query, string $workshopMode): EloquentBuilder
    {
        if ($workshopMode === 'all') {
            return $query;
        }

        return $query->whereHas('workshop', function (EloquentBuilder $workshopQuery) use ($workshopMode) {
            $this->applyWorkshopModeConstraint($workshopQuery, $workshopMode);
        });
    }

    private function applyWorkshopModeConstraint($query, string $workshopMode): void
    {
        if ($workshopMode === 'all') {
            return;
        }

        if ($workshopMode === 'online') {
            $query->where('workshops.is_online', true);
        } elseif ($workshopMode === 'in_person') {
            $query->where('workshops.is_online', false);
        } elseif ($workshopMode === 'featured') {
            $query->where('workshops.is_featured', true);
        }
    }

    private function sumRevenueBetween(Carbon $start, Carbon $end, string $workshopMode): float
    {
        $query = DB::table('workshop_bookings')
            ->join('workshops', 'workshop_bookings.workshop_id', '=', 'workshops.id')
            ->where('workshop_bookings.status', 'confirmed')
            ->where('workshop_bookings.payment_status', 'paid')
            ->whereBetween('workshop_bookings.created_at', [$start, $end]);

        $this->applyWorkshopModeConstraint($query, $workshopMode);

        return (float) $query->sum('workshop_bookings.payment_amount');
    }

    private function collectDailyAggregate(string $table, string $dateColumn, Carbon $start, Carbon $end, ?callable $callback = null, string $aggregate = 'count', string $aggregateColumn = '*'): array
    {
        $expression = $aggregate === 'sum'
            ? "SUM($aggregateColumn)"
            : "COUNT($aggregateColumn)";

        $query = DB::table($table)
            ->selectRaw("DATE($dateColumn) as date_value, {$expression} as aggregate_value")
            ->whereBetween($dateColumn, [$start, $end])
            ->groupBy('date_value')
            ->orderBy('date_value');

        if ($callback) {
            $callback($query);
        }

        return $query->pluck('aggregate_value', 'date_value')->all();
    }

    private function getPeriodStats(int $days, string $workshopMode = 'all'): array
    {
        $days = max(1, $days);
        $end = now()->copy()->endOfDay();
        $start = $end->copy()->subDays($days - 1)->startOfDay();

        $userSeries = $this->collectDailyAggregate('users', 'users.created_at', $start, $end);
        $recipeSeries = $this->collectDailyAggregate('recipes', 'recipes.created_at', $start, $end);
        $bookingSeries = $this->collectDailyAggregate(
            'workshop_bookings',
            'workshop_bookings.created_at',
            $start,
            $end,
            function (QueryBuilder $query) use ($workshopMode) {
                $query->join('workshops', 'workshop_bookings.workshop_id', '=', 'workshops.id');
                $this->applyWorkshopModeConstraint($query, $workshopMode);
            }
        );
        $revenueSeries = $this->collectDailyAggregate(
            'workshop_bookings',
            'workshop_bookings.created_at',
            $start,
            $end,
            function (QueryBuilder $query) use ($workshopMode) {
                $query->join('workshops', 'workshop_bookings.workshop_id', '=', 'workshops.id')
                      ->where('workshop_bookings.status', 'confirmed')
                      ->where('workshop_bookings.payment_status', 'paid');
                $this->applyWorkshopModeConstraint($query, $workshopMode);
            },
            'sum',
            'workshop_bookings.payment_amount'
        );

        $stats = [];
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $dateKey = $cursor->format('Y-m-d');

            $stats[] = [
                'date' => $dateKey,
                'label' => $cursor->copy()->locale(app()->getLocale())->translatedFormat('d M'),
                'day_name' => $cursor->copy()->locale(app()->getLocale())->translatedFormat('l'),
                'users' => (int) ($userSeries[$dateKey] ?? 0),
                'recipes' => (int) ($recipeSeries[$dateKey] ?? 0),
                'bookings' => (int) ($bookingSeries[$dateKey] ?? 0),
                'revenue' => (float) ($revenueSeries[$dateKey] ?? 0),
            ];

            $cursor->addDay();
        }

        return $stats;
    }

    private function getLast7DaysStats(string $workshopMode = 'all')
    {
        return $this->getPeriodStats(7, $workshopMode);
    }

    private function getLast30DaysStats(string $workshopMode = 'all')
    {
        return $this->getPeriodStats(30, $workshopMode);
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

}
