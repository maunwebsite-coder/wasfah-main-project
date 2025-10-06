@extends('layouts.app')

@section('title', 'لوحة التحكم - الإدارة')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-tachometer-alt text-blue-600 ml-2"></i>
                        لوحة التحكم
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">نظرة شاملة ومفصلة على إحصائيات الموقع</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-4 space-x-reverse">
                    <div class="text-sm text-gray-500">
                        آخر تحديث: {{ now()->format('Y-m-d H:i') }}
                    </div>
                    <button onclick="refreshDashboard()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-sync-alt ml-2"></i>
                        تحديث
                    </button>
                </div>
            </div>
        </div>

        <!-- إحصائيات اليوم -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="dashboard-card bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow-lg rounded-lg text-white">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-plus dashboard-icon"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium opacity-90 truncate">مستخدمون اليوم</dt>
                                <dd class="stat-number">{{ number_format($todayUsers) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 overflow-hidden shadow-lg rounded-lg text-white">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-book text-3xl"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium opacity-90 truncate">وصفات اليوم</dt>
                                <dd class="text-2xl font-bold">{{ number_format($todayRecipes) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 overflow-hidden shadow-lg rounded-lg text-white">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-check text-3xl"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium opacity-90 truncate">حجوزات اليوم</dt>
                                <dd class="text-2xl font-bold">{{ number_format($todayBookings) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-orange-500 to-orange-600 overflow-hidden shadow-lg rounded-lg text-white">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-money-bill-wave text-3xl"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium opacity-90 truncate">إيرادات اليوم</dt>
                                <dd class="text-2xl font-bold">{{ number_format($todayRevenue, 2) }} ر.س</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإحصائيات الرئيسية -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- المستخدمون -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-r-4 border-blue-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-3xl text-blue-500"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    إجمالي المستخدمين
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900">
                                    {{ number_format($totalUsers) }}
                                </dd>
                                <dd class="text-sm text-green-600 flex items-center">
                                    <i class="fas fa-arrow-up ml-1"></i>
                                    +{{ $newUsersThisMonth }} هذا الشهر
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الوصفات -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-r-4 border-green-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-book text-3xl text-green-500"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    إجمالي الوصفات
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900">
                                    {{ number_format($totalRecipes) }}
                                </dd>
                                <dd class="text-sm text-green-600 flex items-center">
                                    <i class="fas fa-arrow-up ml-1"></i>
                                    +{{ $newRecipesThisMonth }} هذا الشهر
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الورشات -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-r-4 border-purple-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-graduation-cap text-3xl text-purple-500"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    إجمالي الورشات
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900">
                                    {{ number_format($totalWorkshops) }}
                                </dd>
                                <dd class="text-sm text-purple-600 flex items-center">
                                    <i class="fas fa-check-circle ml-1"></i>
                                    {{ $activeWorkshops }} نشطة
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الحجوزات -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-r-4 border-orange-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-check text-3xl text-orange-500"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    إجمالي الحجوزات
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900">
                                    {{ number_format($totalBookings) }}
                                </dd>
                                <dd class="text-sm text-green-600 flex items-center">
                                    <i class="fas fa-check ml-1"></i>
                                    {{ $confirmedBookings }} مؤكدة
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الرسوم البيانية التفاعلية -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- رسم بياني للإحصائيات الأسبوعية -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line text-blue-500 ml-2"></i>
                        إحصائيات آخر 7 أيام
                    </h3>
                </div>
                <div class="p-6">
                    <canvas id="weeklyChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- رسم بياني للحجوزات -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-pie text-green-500 ml-2"></i>
                        توزيع الحجوزات
                    </h3>
                </div>
                <div class="p-6">
                    <canvas id="bookingsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- إحصائيات الأداء -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-r-4 border-indigo-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-percentage text-3xl text-indigo-500"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">معدل التحويل</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $conversionRate }}%</dd>
                                <dd class="text-sm text-gray-500">من المستخدمين إلى حجوزات</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-r-4 border-pink-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-3xl text-pink-500"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">معدل ملء الورشات</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $workshopFillRate }}%</dd>
                                <dd class="text-sm text-gray-500">من السعة المتاحة</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-r-4 border-teal-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-heart text-3xl text-teal-500"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">معدل الاحتفاظ</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $userRetentionRate }}%</dd>
                                <dd class="text-sm text-gray-500">المستخدمون النشطون</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإيرادات ومعدل النمو -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- الإيرادات -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line text-green-500 ml-2"></i>
                        الإيرادات
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">
                                {{ number_format($totalRevenue, 2) }}
                            </div>
                            <div class="text-sm text-gray-500">ريال إجمالي</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">
                                {{ number_format($monthlyRevenue, 2) }}
                            </div>
                            <div class="text-sm text-gray-500">ريال هذا الشهر</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معدل النمو -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-trending-up text-blue-500 ml-2"></i>
                        معدل النمو الشهري
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold {{ $monthlyGrowth['users'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $monthlyGrowth['users'] }}%
                            </div>
                            <div class="text-sm text-gray-500">المستخدمون</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold {{ $monthlyGrowth['recipes'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $monthlyGrowth['recipes'] }}%
                            </div>
                            <div class="text-sm text-gray-500">الوصفات</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold {{ $monthlyGrowth['workshops'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $monthlyGrowth['workshops'] }}%
                            </div>
                            <div class="text-sm text-gray-500">الورشات</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold {{ $monthlyGrowth['bookings'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $monthlyGrowth['bookings'] }}%
                            </div>
                            <div class="text-sm text-gray-500">الحجوزات</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإحصائيات التفصيلية -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- إحصائيات التفاعل -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-heart text-red-500 ml-2"></i>
                        إحصائيات التفاعل
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($savedRecipes) }}</div>
                            <div class="text-sm text-gray-500">وصفات محفوظة</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($madeRecipes) }}</div>
                            <div class="text-sm text-gray-500">وصفات مطبوخة</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($totalRatings) }}</div>
                            <div class="text-sm text-gray-500">تقييمات</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ number_format($averageRating, 1) }}</div>
                            <div class="text-sm text-gray-500">متوسط التقييم</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات النظام -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-cogs text-gray-500 ml-2"></i>
                        إحصائيات النظام
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($totalTools) }}</div>
                            <div class="text-sm text-gray-500">إجمالي الأدوات</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($activeTools) }}</div>
                            <div class="text-sm text-gray-500">أدوات نشطة</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($totalCategories) }}</div>
                            <div class="text-sm text-gray-500">التصنيفات</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ number_format($adminUsers) }}</div>
                            <div class="text-sm text-gray-500">المديرون</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات الأسبوع الماضي -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-week text-indigo-500 ml-2"></i>
                        إحصائيات الأسبوع الماضي
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($lastWeekUsers) }}</div>
                            <div class="text-sm text-gray-500">مستخدمون جدد</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($lastWeekRecipes) }}</div>
                            <div class="text-sm text-gray-500">وصفات جديدة</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($lastWeekBookings) }}</div>
                            <div class="text-sm text-gray-500">حجوزات جديدة</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- المحتوى الشائع -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- الوصفات الأكثر شعبية -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-fire text-orange-500 ml-2"></i>
                        الوصفات الأكثر شعبية (آخر 30 يوم)
                    </h3>
                </div>
                <div class="p-6">
                    @if($popularRecipes->count() > 0)
                        <div class="space-y-4">
                            @foreach($popularRecipes as $recipe)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $recipe->title }}</h4>
                                        <p class="text-xs text-gray-500">بواسطة {{ $recipe->author }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $recipe->interactions_count }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-book text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">لا توجد وصفات شائعة</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- الورشات الأكثر حجزاً -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-star text-yellow-500 ml-2"></i>
                        الورشات الأكثر حجزاً
                    </h3>
                </div>
                <div class="p-6">
                    @if($popularWorkshops->count() > 0)
                        <div class="space-y-4">
                            @foreach($popularWorkshops as $workshop)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $workshop->title }}</h4>
                                        <p class="text-xs text-gray-500">{{ $workshop->instructor }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $workshop->bookings_count }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-graduation-cap text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">لا توجد ورشات محجوزة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الأنشطة الأخيرة -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- المستخدمون الجدد -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user-plus text-blue-500 ml-2"></i>
                        المستخدمون الجدد
                    </h3>
                </div>
                <div class="p-6">
                    @if($recentUsers->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentUsers as $user)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-users text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">لا يوجد مستخدمون جدد</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- الحجوزات الأخيرة -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-check text-green-500 ml-2"></i>
                        الحجوزات الأخيرة
                    </h3>
                </div>
                <div class="p-6">
                    @if($recentBookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentBookings as $booking)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $booking->workshop->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->user->name }}</p>
                                    </div>
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($booking->status == 'confirmed') bg-green-100 text-green-800
                                            @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $booking->status }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $booking->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-calendar text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">لا توجد حجوزات حديثة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- إحصائيات تفصيلية إضافية -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- إحصائيات الحجوزات -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-bar text-purple-500 ml-2"></i>
                        إحصائيات الحجوزات
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">مؤكدة</span>
                            <span class="text-sm font-medium text-green-600">{{ $confirmedBookings }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">معلقة</span>
                            <span class="text-sm font-medium text-yellow-600">{{ $pendingBookings }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">ملغية</span>
                            <span class="text-sm font-medium text-red-600">{{ $cancelledBookings }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">مكتملة</span>
                            <span class="text-sm font-medium text-blue-600">{{ $completedBookings }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات الورشات -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-graduation-cap text-indigo-500 ml-2"></i>
                        إحصائيات الورشات
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">قادمة</span>
                            <span class="text-sm font-medium text-blue-600">{{ $upcomingWorkshops }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">مكتملة</span>
                            <span class="text-sm font-medium text-green-600">{{ $completedWorkshops }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">مميزة</span>
                            <span class="text-sm font-medium text-yellow-600">{{ $featuredWorkshops }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">السعة</span>
                            <span class="text-sm font-medium text-purple-600">{{ $workshopCapacity }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات التقييمات -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-star text-yellow-500 ml-2"></i>
                        التقييمات
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">إجمالي التقييمات</span>
                            <span class="text-sm font-medium text-blue-600">{{ $totalRatings }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">متوسط التقييم</span>
                            <span class="text-sm font-medium text-green-600">{{ number_format($averageRating, 1) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">تقييمات الورشات</span>
                            <span class="text-sm font-medium text-purple-600">{{ $workshopReviews }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">ورشات عالية التقييم</span>
                            <span class="text-sm font-medium text-yellow-600">{{ $highRatedWorkshops }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات المشاهدة -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-eye text-teal-500 ml-2"></i>
                        المشاهدات
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">إجمالي المشاهدات</span>
                            <span class="text-sm font-medium text-blue-600">{{ $totalViews }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">مشاهدون فريدون</span>
                            <span class="text-sm font-medium text-green-600">{{ $uniqueViewers }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">أكثر ورشة مشاهدة</span>
                            <span class="text-sm font-medium text-purple-600">
                                @if($mostViewedWorkshop)
                                    {{ $mostViewedWorkshop->views_count }}
                                @else
                                    0
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">معدل المشاهدة</span>
                            <span class="text-sm font-medium text-teal-600">
                                @if($totalWorkshops > 0)
                                    {{ number_format($totalViews / $totalWorkshops, 1) }}
                                @else
                                    0
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإجراءات السريعة -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-bolt text-yellow-500 ml-2"></i>
                    إجراءات سريعة
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('admin.recipes.index') }}" class="group flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-book ml-2"></i>
                        إدارة الوصفات
                    </a>
                    <a href="{{ route('admin.workshops.index') }}" class="group flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <i class="fas fa-graduation-cap ml-2"></i>
                        إدارة الورشات
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" class="group flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                        <i class="fas fa-calendar-check ml-2"></i>
                        إدارة الحجوزات
                    </a>
                    <a href="{{ route('admin.bookings.manual') }}" class="group flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة حجز يدوي
                    </a>
                    <a href="{{ route('admin.tools.index') }}" class="group flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                        <i class="fas fa-tools ml-2"></i>
                        إدارة الأدوات
                    </a>
                    <a href="{{ route('home') }}" class="group flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors" target="_blank">
                        <i class="fas fa-external-link-alt ml-2"></i>
                        عرض الموقع
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// رسم بياني للإحصائيات الأسبوعية
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
const weeklyChart = new Chart(weeklyCtx, {
    type: 'line',
    data: {
        labels: [
            @foreach($last7DaysStats as $stat)
                '{{ $stat["day_name"] }}',
            @endforeach
        ],
        datasets: [{
            label: 'المستخدمون',
            data: [
                @foreach($last7DaysStats as $stat)
                    {{ $stat['users'] }},
                @endforeach
            ],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1
        }, {
            label: 'الوصفات',
            data: [
                @foreach($last7DaysStats as $stat)
                    {{ $stat['recipes'] }},
                @endforeach
            ],
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.1
        }, {
            label: 'الحجوزات',
            data: [
                @foreach($last7DaysStats as $stat)
                    {{ $stat['bookings'] }},
                @endforeach
            ],
            borderColor: 'rgb(168, 85, 247)',
            backgroundColor: 'rgba(168, 85, 247, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// رسم بياني للحجوزات
const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
const bookingsChart = new Chart(bookingsCtx, {
    type: 'doughnut',
    data: {
        labels: ['مؤكدة', 'معلقة', 'ملغية', 'مكتملة'],
        datasets: [{
            data: [{{ $confirmedBookings }}, {{ $pendingBookings }}, {{ $cancelledBookings }}, {{ $completedBookings }}],
            backgroundColor: [
                'rgb(34, 197, 94)',
                'rgb(251, 191, 36)',
                'rgb(239, 68, 68)',
                'rgb(59, 130, 246)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// وظيفة تحديث لوحة التحكم
function refreshDashboard() {
    location.reload();
}

// تحديث تلقائي كل 5 دقائق
setInterval(function() {
    // يمكن إضافة AJAX call هنا لتحديث البيانات دون إعادة تحميل الصفحة
}, 300000);
</script>
@endsection
