@extends('layouts.app')

@section('title', 'منطقة الإدمن')

@php
    use App\Models\User;
    use App\Models\Recipe;

    $pendingChefCount = User::query()
        ->where('role', User::ROLE_CHEF)
        ->where('chef_status', User::CHEF_STATUS_PENDING)
        ->count();

    $pendingRecipeCount = Recipe::query()
        ->where('status', Recipe::STATUS_PENDING)
        ->count();
@endphp

@push('styles')
<style>
    .admin-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .admin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        border-color: #f97316;
    }
    
    .admin-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #f97316, #fb923c);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
    }
    
    .admin-icon i {
        font-size: 1.5rem;
        color: white;
    }
    
    .admin-title {
        color: #1f2937;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .admin-description {
        color: #6b7280;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .admin-btn {
        background: linear-gradient(135deg, #f97316, #fb923c);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        width: 100%;
        text-align: center;
    }
    
    .admin-btn:hover {
        background: linear-gradient(135deg, #ea580c, #f97316);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(249, 115, 22, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .stats-card {
        background: linear-gradient(135deg, #fef3e7, #fed7aa);
        border: 1px solid #fb923c;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
    }
    
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: #f97316;
        margin-bottom: 0.5rem;
    }
    
    .stats-label {
        color: #9a3412;
        font-weight: 600;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">
            <i class="fas fa-crown text-orange-500 ml-3"></i>
            منطقة الإدمن
        </h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            مرحباً بك في لوحة التحكم الإدارية. من هنا يمكنك إدارة جميع جوانب الموقع
        </p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="stats-card">
            <div class="stats-number">{{ \App\Models\Recipe::count() }}</div>
            <div class="stats-label">إجمالي الوصفات</div>
        </div>
        <div class="stats-card">
            <div class="stats-number">{{ \App\Models\Workshop::count() }}</div>
            <div class="stats-label">إجمالي الورشات</div>
        </div>
        <div class="stats-card">
            <div class="stats-number">{{ \App\Models\WorkshopBooking::count() }}</div>
            <div class="stats-label">إجمالي الحجوزات</div>
        </div>
        <div class="stats-card">
            <div class="stats-number">{{ \App\Models\User::count() }}</div>
            <div class="stats-label">إجمالي المستخدمين</div>
        </div>
    </div>

    <!-- Admin Actions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- إدارة الوصفات -->
        <div class="bg-white rounded-xl shadow-lg p-6 admin-card">
            <div class="admin-icon">
                <i class="fas fa-utensils"></i>
            </div>
            <h3 class="admin-title">إدارة الوصفات</h3>
            <p class="admin-description">
                إضافة وتعديل وحذف الوصفات، إدارة التصنيفات والمكونات
            </p>
            <div class="mt-6 space-y-3">
                <a href="{{ route('admin.recipes.index') }}" class="admin-btn">
                    <i class="fas fa-list ml-2"></i>
                    عرض جميع الوصفات
                </a>
                <a href="{{ route('admin.recipes.create') }}" class="admin-btn" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة وصفة جديدة
                </a>
            </div>
        </div>

        <!-- طلبات الشيفات الجديدة -->
        <div class="bg-white rounded-xl shadow-lg p-6 admin-card">
            <div class="admin-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <h3 class="admin-title">طلبات الشيفات الجديدة</h3>
            <p class="admin-description">
                مراجعة طلبات الانضمام واعتماد الشيفات الجدد قبل منحهم الصلاحيات
            </p>
            <div class="mt-6 space-y-3">
                <div class="text-sm text-gray-500 text-center">
                    {{ number_format($pendingChefCount) }} طلب قيد المراجعة
                </div>
                <a href="{{ route('admin.chefs.requests') }}" class="admin-btn" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                    <i class="fas fa-list-check ml-2"></i>
                    مراجعة طلبات الشيفات
                </a>
            </div>
        </div>

        <!-- إدارة الورشات -->
        <div class="bg-white rounded-xl shadow-lg p-6 admin-card">
            <div class="admin-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <h3 class="admin-title">إدارة الورشات</h3>
            <p class="admin-description">
                إدارة ورشات العمل، الحجوزات، والمدربين
            </p>
            <div class="mt-6 space-y-3">
                <a href="{{ route('admin.workshops.index') }}" class="admin-btn">
                    <i class="fas fa-list ml-2"></i>
                    عرض جميع الورشات
                </a>
                <a href="{{ route('admin.workshops.create') }}" class="admin-btn" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة ورشة جديدة
                </a>
            </div>
        </div>

        <!-- مراجعة الوصفات الجديدة -->
        <div class="bg-white rounded-xl shadow-lg p-6 admin-card">
            <div class="admin-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <h3 class="admin-title">مراجعة الوصفات الجديدة</h3>
            <p class="admin-description">
                الاطلاع على الوصفات التي رفعها الشيفات واعتمادها قبل النشر
            </p>
            <div class="mt-6 space-y-3">
                <div class="text-sm text-gray-500 text-center">
                    {{ number_format($pendingRecipeCount) }} وصفة تنتظر المراجعة
                </div>
                <a href="{{ route('admin.recipes.index', ['status' => Recipe::STATUS_PENDING]) }}" class="admin-btn" style="background: linear-gradient(135deg, #10b981, #047857);">
                    <i class="fas fa-check-double ml-2"></i>
                    استعراض الوصفات المعلقة
                </a>
            </div>
        </div>

        <!-- إدارة الحجوزات -->
        <div class="bg-white rounded-xl shadow-lg p-6 admin-card">
            <div class="admin-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3 class="admin-title">إدارة الحجوزات</h3>
            <p class="admin-description">
                متابعة وإدارة حجوزات الورشات والمدفوعات
            </p>
            <div class="mt-6 space-y-3">
                <a href="{{ route('admin.bookings.index') }}" class="admin-btn">
                    <i class="fas fa-list ml-2"></i>
                    عرض جميع الحجوزات
                </a>
                <a href="{{ route('admin.bookings.manual') }}" class="admin-btn" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                    <i class="fas fa-user-plus ml-2"></i>
                    إضافة حجز يدوي
                </a>
            </div>
        </div>

        <!-- إدارة الأدوات -->
        <div class="bg-white rounded-xl shadow-lg p-6 admin-card">
            <div class="admin-icon">
                <i class="fas fa-tools"></i>
            </div>
            <h3 class="admin-title">إدارة أدوات الشيف</h3>
            <p class="admin-description">
                إدارة أدوات الطبخ والمعدات المطلوبة للوصفات
            </p>
            <div class="mt-6 space-y-3">
                <a href="{{ route('admin.tools.index') }}" class="admin-btn">
                    <i class="fas fa-list ml-2"></i>
                    عرض جميع الأدوات
                </a>
                <a href="{{ route('admin.tools.create') }}" class="admin-btn" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة أداة جديدة
                </a>
            </div>
        </div>

        <!-- صفحة روابط Wasfah -->
        <div class="bg-white rounded-xl shadow-lg p-6 admin-card">
            <div class="admin-icon" style="background: linear-gradient(135deg, #f97316, #f59e0b);">
                <i class="fas fa-link"></i>
            </div>
            <h3 class="admin-title">صفحة روابط Wasfah</h3>
            <p class="admin-description">
                فتح الصفحة الخاصة بالروابط الموحدة لاستخدامها في البايو ومتابعة تصميمها بسرعة.
            </p>
            <div class="mt-6">
                <a href="https://wasfah.ae/wasfah-links" target="_blank" rel="noopener" class="admin-btn">
                    <i class="fas fa-external-link-alt ml-2"></i>
                    https://wasfah.ae/wasfah-links
                </a>
            </div>
        </div>

        <!-- لوحة التحكم -->
        <div class="bg-white rounded-xl shadow-lg p-6 admin-card">
            <div class="admin-icon">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <h3 class="admin-title">لوحة التحكم</h3>
            <p class="admin-description">
                نظرة عامة على إحصائيات الموقع والأداء
            </p>
            <div class="mt-6">
                <a href="{{ route('admin.dashboard') }}" class="admin-btn">
                    <i class="fas fa-chart-line ml-2"></i>
                    عرض لوحة التحكم
                </a>
            </div>
        </div>

        <!-- إدارة إعدادات الرؤية -->
        <div class="bg-white rounded-xl shadow-lg p-6 admin-card">
            <div class="admin-icon">
                <i class="fas fa-eye"></i>
            </div>
            <h3 class="admin-title">إعدادات الرؤية</h3>
            <p class="admin-description">
                إدارة إعدادات عرض الأقسام في الموقع
            </p>
            <div class="mt-6">
                <a href="{{ route('admin.visibility.index') }}" class="admin-btn">
                    <i class="fas fa-cog ml-2"></i>
                    إدارة الإعدادات
                </a>
            </div>
        </div>

    </div>

    <!-- Quick Actions Section -->
    <div class="mt-12 bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            <i class="fas fa-bolt text-orange-500 ml-2"></i>
            إجراءات سريعة
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.recipes.create') }}" class="bg-white rounded-lg p-4 text-center hover:shadow-lg transition-shadow">
                <i class="fas fa-plus-circle text-2xl text-green-500 mb-2"></i>
                <div class="font-semibold text-gray-800">وصفة جديدة</div>
            </a>

            <a href="{{ route('admin.recipes.index', ['status' => Recipe::STATUS_PENDING]) }}" class="bg-white rounded-lg p-4 text-center hover:shadow-lg transition-shadow">
                <i class="fas fa-clipboard-list text-2xl text-teal-500 mb-2"></i>
                <div class="font-semibold text-gray-800">وصفات تنتظر المراجعة</div>
            </a>

            <a href="{{ route('admin.workshops.create') }}" class="bg-white rounded-lg p-4 text-center hover:shadow-lg transition-shadow">
                <i class="fas fa-chalkboard text-2xl text-blue-500 mb-2"></i>
                <div class="font-semibold text-gray-800">ورشة جديدة</div>
            </a>

            <a href="{{ route('admin.bookings.manual') }}" class="bg-white rounded-lg p-4 text-center hover:shadow-lg transition-shadow">
                <i class="fas fa-user-plus text-2xl text-purple-500 mb-2"></i>
                <div class="font-semibold text-gray-800">حجز يدوي</div>
            </a>

            <a href="{{ route('admin.chefs.requests') }}" class="bg-white rounded-lg p-4 text-center hover:shadow-lg transition-shadow">
                <i class="fas fa-user-check text-2xl text-rose-500 mb-2"></i>
                <div class="font-semibold text-gray-800">طلبات الشيفات</div>
            </a>

            <a href="{{ route('admin.tools.create') }}" class="bg-white rounded-lg p-4 text-center hover:shadow-lg transition-shadow">
                <i class="fas fa-tools text-2xl text-orange-500 mb-2"></i>
                <div class="font-semibold text-gray-800">أداة جديدة</div>
            </a>
        </div>
    </div>

    <!-- Back to Dashboard -->
    <div class="mt-8 text-center">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة إلى لوحة التحكم
        </a>
    </div>
</div>
@endsection
