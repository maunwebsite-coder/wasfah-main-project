@extends('layouts.app')

@section('title', 'إدارة الحجوزات')

@php
    $totalBookings = max($stats['total'], 1);

    $statusCards = [
        [
            'label' => 'إجمالي الحجوزات',
            'value' => $stats['total'],
            'formatted' => number_format($stats['total']),
            'icon' => 'fa-calendar-check',
            'gradient' => 'from-sky-500 via-indigo-500 to-purple-500',
            'description' => 'جميع الحجوزات المسجلة في النظام',
            'percentage' => null,
        ],
        [
            'label' => 'قيد المراجعة',
            'value' => $stats['pending'],
            'formatted' => number_format($stats['pending']),
            'icon' => 'fa-hourglass-half',
            'gradient' => 'from-amber-500 to-orange-500',
            'description' => 'حجوزات تنتظر الإجراء',
            'percentage' => round(($stats['pending'] / $totalBookings) * 100, 1),
        ],
        [
            'label' => 'مؤكدة',
            'value' => $stats['confirmed'],
            'formatted' => number_format($stats['confirmed']),
            'icon' => 'fa-check-circle',
            'gradient' => 'from-emerald-500 to-teal-500',
            'description' => 'تم تأكيدها للمشاركين',
            'percentage' => round(($stats['confirmed'] / $totalBookings) * 100, 1),
        ],
        [
            'label' => 'ملغية',
            'value' => $stats['cancelled'],
            'formatted' => number_format($stats['cancelled']),
            'icon' => 'fa-times-circle',
            'gradient' => 'from-rose-500 to-red-500',
            'description' => 'تحتاج تحليل أسباب الإلغاء',
            'percentage' => round(($stats['cancelled'] / $totalBookings) * 100, 1),
        ],
    ];

    $statusDistribution = [
        [
            'label' => 'مؤكدة',
            'icon' => 'fa-check-circle',
            'raw' => $stats['confirmed'],
            'formatted' => number_format($stats['confirmed']),
            'percentage' => round(($stats['confirmed'] / $totalBookings) * 100, 1),
            'color' => 'bg-emerald-500',
        ],
        [
            'label' => 'قيد المراجعة',
            'icon' => 'fa-stopwatch',
            'raw' => $stats['pending'],
            'formatted' => number_format($stats['pending']),
            'percentage' => round(($stats['pending'] / $totalBookings) * 100, 1),
            'color' => 'bg-amber-500',
        ],
        [
            'label' => 'ملغية',
            'icon' => 'fa-times-circle',
            'raw' => $stats['cancelled'],
            'formatted' => number_format($stats['cancelled']),
            'percentage' => round(($stats['cancelled'] / $totalBookings) * 100, 1),
            'color' => 'bg-rose-500',
        ],
    ];

    $paymentBreakdown = [
        [
            'label' => 'مدفوعة',
            'raw' => $stats['paid'],
            'formatted' => number_format($stats['paid']),
            'percentage' => round(($stats['paid'] / $totalBookings) * 100, 1),
            'color' => 'bg-teal-500',
        ],
        [
            'label' => 'غير مدفوعة',
            'raw' => $stats['unpaid'],
            'formatted' => number_format($stats['unpaid']),
            'percentage' => round(($stats['unpaid'] / $totalBookings) * 100, 1),
            'color' => 'bg-rose-500',
        ],
    ];

    $quickStatusFilters = [
        [
            'label' => 'الكل',
            'value' => null,
            'count' => number_format($stats['total']),
            'icon' => 'fa-layer-group',
            'hint' => 'عرض جميع الحجوزات',
        ],
        [
            'label' => 'قيد المراجعة',
            'value' => 'pending',
            'count' => number_format($stats['pending']),
            'icon' => 'fa-hourglass-half',
            'hint' => 'الحجوزات التي لم يتم اتخاذ إجراء بشأنها',
        ],
        [
            'label' => 'مؤكدة',
            'value' => 'confirmed',
            'count' => number_format($stats['confirmed']),
            'icon' => 'fa-check-circle',
            'hint' => 'الحجوزات الجاهزة للورشة',
        ],
        [
            'label' => 'ملغية',
            'value' => 'cancelled',
            'count' => number_format($stats['cancelled']),
            'icon' => 'fa-ban',
            'hint' => 'إلغاءات تحتاج متابعة',
        ],
    ];

    $advancedFiltersActive = request()->hasAny([
        'workshop_type',
        'price_range',
        'payment_method',
        'booking_count',
        'workshop_date_from',
        'workshop_date_to',
    ]);

    $statusMeta = [
        'pending' => [
            'label' => 'قيد المراجعة',
            'class' => 'bg-amber-100 text-amber-700',
        ],
        'confirmed' => [
            'label' => 'مؤكدة',
            'class' => 'bg-emerald-100 text-emerald-700',
        ],
        'cancelled' => [
            'label' => 'ملغية',
            'class' => 'bg-rose-100 text-rose-700',
        ],
    ];

    $paymentMeta = [
        'pending' => [
            'label' => 'بانتظار الدفع',
            'class' => 'bg-amber-100 text-amber-700',
        ],
        'paid' => [
            'label' => 'مدفوعة',
            'class' => 'bg-emerald-100 text-emerald-700',
        ],
        'refunded' => [
            'label' => 'مستردة',
            'class' => 'bg-purple-100 text-purple-700',
        ],
    ];
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
<style>
/* Custom Modal Styles */
#confirmationModal, #alertModal {
    transition: opacity 0.3s ease-in-out;
}

#confirmationModal.show, #alertModal.show {
    opacity: 1;
}

.modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-backdrop {
    backdrop-filter: blur(2px);
}

/* Enhanced button hover effects */
.modal-button {
    transition: all 0.2s ease-in-out;
    position: relative;
    overflow: hidden;
}

.modal-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.modal-button:active {
    transform: translateY(0);
}

/* RTL Support for modals */
.modal-content {
    direction: rtl;
    text-align: right;
}

.modal-content .text-center {
    text-align: center;
}

/* Focus styles for accessibility */
.modal-button:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Loading spinner animation */
.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.quick-filter-scroll {
    display: flex;
    gap: 0.75rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: rgba(59, 130, 246, 0.3) transparent;
}

.quick-filter-scroll::-webkit-scrollbar {
    height: 6px;
}

.quick-filter-scroll::-webkit-scrollbar-thumb {
    background: rgba(59, 130, 246, 0.3);
    border-radius: 9999px;
}

.quick-filter-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.quick-filter-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    border: 1px solid rgba(59, 130, 246, 0.25);
    background: rgba(59, 130, 246, 0.08);
    color: #1e3a8a;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s ease-in-out;
    white-space: nowrap;
}

.quick-filter-chip:hover {
    border-color: rgba(59, 130, 246, 0.4);
    background: rgba(59, 130, 246, 0.12);
}

.quick-filter-chip .chip-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.2);
}

.quick-filter-chip .chip-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.85);
    color: #1e40af;
    font-size: 0.75rem;
    font-weight: 700;
    box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.15);
}

.quick-filter-chip.is-active {
    color: #fff;
    border-color: transparent;
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    box-shadow: 0 18px 35px -20px rgba(79, 70, 229, 0.9);
}

.quick-filter-chip.is-active .chip-count {
    background: rgba(255, 255, 255, 0.22);
    color: #fff;
    box-shadow: none;
}

.insight-progress {
    height: 0.5rem;
    border-radius: 9999px;
    background: #f1f5f9;
    overflow: hidden;
}

.insight-progress span {
    display: block;
    height: 100%;
    border-radius: 9999px;
}

.stat-card-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: var(--stat-icon-size, 3rem);
    height: var(--stat-icon-size, 3rem);
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.22);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
}

.stat-card-highlight {
    position: absolute;
    inset-inline-end: -3rem;
    inset-block-start: -3rem;
    width: 8rem;
    height: 8rem;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    filter: blur(0.5rem);
}

@media (max-width: 640px) {
    .quick-filter-chip {
        padding-inline: 0.75rem;
        font-size: 0.8rem;
    }
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-calendar-check text-blue-600 ml-2"></i>
                        إدارة الحجوزات
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">عرض وإدارة جميع حجوزات الورشات</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-4 space-x-reverse">
                    <div class="text-sm text-gray-500">
                        آخر تحديث: {{ now()->format('Y-m-d H:i') }}
                    </div>
                    <button onclick="refreshBookings()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-sync-alt ml-2"></i>
                        تحديث
                    </button>
                    <a href="{{ route('admin.bookings.manual') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 dashboard-btn">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة حجز يدوي
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dashboard-btn">
                        <i class="fas fa-arrow-right ml-2"></i>
                        العودة للوحة التحكم
                    </a>
                </div>
            </div>
        </div>

        <!-- نظرة عامة سريعة -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
            @foreach($statusCards as $card)
                <div class="relative overflow-hidden rounded-2xl shadow-lg bg-gradient-to-br {{ $card['gradient'] }} text-white">
                    <span class="stat-card-highlight"></span>
                    <div class="relative p-6 flex flex-col gap-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm text-white/70">{{ $card['label'] }}</p>
                                <p class="mt-2 text-3xl font-bold tracking-tight">{{ $card['formatted'] }}</p>
                            </div>
                            <span class="stat-card-icon">
                                <i class="fas {{ $card['icon'] }} text-xl"></i>
                            </span>
                        </div>
                        @if(!is_null($card['percentage']))
                            <div>
                                <div class="flex items-center justify-between text-xs text-white/70 mb-1">
                                    <span>{{ $card['description'] }}</span>
                                    <span>{{ number_format($card['percentage'], 1) }}%</span>
                                </div>
                                <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                                    <div class="h-full bg-white/80 rounded-full" style="width: {{ min($card['percentage'], 100) }}%"></div>
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-white/75">{{ $card['description'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- تحليلات الحالة -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-10">
            <div class="xl:col-span-2 bg-white shadow-lg rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-chart-pie text-blue-500 ml-2"></i>
                            توزيع حالات الحجوزات
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">تابع حالة كل حجز وتعرف على نسب التقدم</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                        {{ now()->format('Y-m-d H:i') }}
                    </span>
                </div>
                <div class="space-y-4">
                    @foreach($statusDistribution as $item)
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="stat-card-icon" style="--stat-icon-size: 2.5rem;">
                                        <i class="fas {{ $item['icon'] }} text-base"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $item['label'] }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($item['percentage'], 1) }}% من الإجمالي</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">{{ $item['formatted'] }}</span>
                            </div>
                            <div class="insight-progress mt-3">
                                <span class="{{ $item['color'] }}" style="width: {{ min($item['percentage'], 100) }}%"></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-wallet text-emerald-500 ml-2"></i>
                    أداء المدفوعات
                </h3>
                <p class="text-sm text-gray-500 mt-2">راقب تحصيل المدفوعات وتابع الحجوزات المؤجلة</p>
                <div class="mt-6 space-y-6">
                    @foreach($paymentBreakdown as $item)
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">{{ $item['label'] }}</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $item['formatted'] }}</span>
                            </div>
                            <div class="insight-progress mt-3">
                                <span class="{{ $item['color'] }}" style="width: {{ min($item['percentage'], 100) }}%"></span>
                            </div>
                            <div class="text-xs text-gray-400 mt-1">{{ number_format($item['percentage'], 1) }}% من إجمالي الحجوزات</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- الفلاتر -->
        <div class="bg-white shadow-xl rounded-2xl mb-8 border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-filter text-blue-500 ml-2"></i>
                        فلترة الحجوزات
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">اختصر الوقت باستعمال الفلاتر الذكية أدناه</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" id="toggleAdvancedFilters" class="inline-flex items-center px-4 py-2 rounded-md border border-blue-200 text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors duration-200" aria-expanded="{{ $advancedFiltersActive ? 'true' : 'false' }}">
                        <i class="fas fa-sliders-h ml-2"></i>
                        <span id="advancedFiltersToggleLabel">{{ $advancedFiltersActive ? 'إخفاء الفلاتر المتقدمة' : 'عرض الفلاتر المتقدمة' }}</span>
                    </button>
                    <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center px-4 py-2 rounded-md border border-gray-200 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors duration-200">
                        <i class="fas fa-redo ml-2"></i>
                        إعادة التعيين
                    </a>
                </div>
            </div>
            <form method="GET" id="bookingFiltersForm" class="px-6 py-6 space-y-6">
                <div class="quick-filter-scroll">
                    @foreach($quickStatusFilters as $filter)
                        @php
                            $isActiveStatus = request()->filled('status') ? request('status') === $filter['value'] : is_null($filter['value']);
                        @endphp
                        <button type="button" class="quick-filter-chip {{ $isActiveStatus ? 'is-active' : '' }}" data-status-value="{{ $filter['value'] ?? '' }}">
                            <span class="chip-icon">
                                <i class="fas {{ $filter['icon'] }} text-sm"></i>
                            </span>
                            <div class="flex flex-col text-right leading-tight">
                                <span class="chip-label">{{ $filter['label'] }}</span>
                                <span class="text-[11px] text-blue-900/60">{{ $filter['hint'] }}</span>
                            </div>
                            <span class="chip-count">{{ $filter['count'] }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <select id="statusSelect" name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>مؤكدة</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">حالة الدفع</label>
                        <select name="payment_status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">جميع حالات الدفع</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                            <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>مستردة</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الورشة</label>
                        <select name="workshop_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">جميع الورشات</option>
                            @foreach($workshops as $workshop)
                                <option value="{{ $workshop->id }}" {{ request('workshop_id') == $workshop->id ? 'selected' : '' }}>
                                    {{ $workshop->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" max="{{ date('Y-m-d') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" max="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div id="advancedFilters" class="border-t border-gray-200 pt-4 {{ $advancedFiltersActive ? '' : 'hidden' }}">
                    <h4 class="text-sm font-medium text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-cogs ml-2"></i>
                        فلاتر متقدمة
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">نوع الورشة</label>
                            <select name="workshop_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">جميع الأنواع</option>
                                <option value="online" {{ request('workshop_type') == 'online' ? 'selected' : '' }}>أونلاين</option>
                                <option value="offline" {{ request('workshop_type') == 'offline' ? 'selected' : '' }}>أوفلاين</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">نطاق السعر</label>
                            <select name="price_range" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">جميع الأسعار</option>
                                <option value="0-50" {{ request('price_range') == '0-50' ? 'selected' : '' }}>0 - 50 ريال</option>
                                <option value="50-100" {{ request('price_range') == '50-100' ? 'selected' : '' }}>50 - 100 ريال</option>
                                <option value="100-200" {{ request('price_range') == '100-200' ? 'selected' : '' }}>100 - 200 ريال</option>
                                <option value="200-500" {{ request('price_range') == '200-500' ? 'selected' : '' }}>200 - 500 ريال</option>
                                <option value="500+" {{ request('price_range') == '500+' ? 'selected' : '' }}>أكثر من 500 ريال</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع</label>
                            <select name="payment_method" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">جميع الطرق</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>
                                        {{ $method }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">عدد الحجوزات</label>
                            <select name="booking_count" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">جميع المستخدمين</option>
                                <option value="single" {{ request('booking_count') == 'single' ? 'selected' : '' }}>حجز واحد</option>
                                <option value="multiple" {{ request('booking_count') == 'multiple' ? 'selected' : '' }}>عدة حجوزات</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الورشة (من)</label>
                            <input type="date" name="workshop_date_from" value="{{ request('workshop_date_from') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الورشة (إلى)</label>
                            <input type="date" name="workshop_date_to" value="{{ request('workshop_date_to') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث بالاسم أو البريد الإلكتروني أو عنوان الورشة..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex gap-2">
                            <select name="sort_by" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>تاريخ الحجز</option>
                                <option value="payment_amount" {{ request('sort_by') == 'payment_amount' ? 'selected' : '' }}>المبلغ</option>
                                <option value="workshop_start_date" {{ request('sort_by') == 'workshop_start_date' ? 'selected' : '' }}>تاريخ الورشة</option>
                            </select>
                            <select name="sort_direction" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>تنازلي</option>
                                <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>تصاعدي</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-search ml-2"></i>
                                بحث
                            </button>
                            <button type="button" id="clearFiltersButton" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                <i class="fas fa-broom ml-2"></i>
                                مسح الحقول
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- مؤشرات الفلاتر النشطة -->
        @if(request()->hasAny(['status', 'payment_status', 'workshop_id', 'date_from', 'date_to', 'search', 'workshop_type', 'price_range', 'payment_method', 'booking_count', 'workshop_date_from', 'workshop_date_to', 'sort_by', 'sort_direction']))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-filter text-blue-600 ml-2"></i>
                    <span class="text-sm font-medium text-blue-900">الفلاتر النشطة:</span>
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-times ml-1"></i>
                    مسح جميع الفلاتر
                </a>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
                @if(request('status'))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        الحالة: {{ request('status') == 'pending' ? 'في الانتظار' : (request('status') == 'confirmed' ? 'مؤكدة' : 'ملغية') }}
                    </span>
                @endif
                @if(request('payment_status'))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        الدفع: {{ request('payment_status') == 'pending' ? 'في الانتظار' : (request('payment_status') == 'paid' ? 'مدفوعة' : 'مستردة') }}
                    </span>
                @endif
                @if(request('workshop_id'))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        الورشة: {{ $workshops->where('id', request('workshop_id'))->first()->title ?? 'غير محدد' }}
                    </span>
                @endif
                @if(request('date_from') || request('date_to'))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        التاريخ: {{ request('date_from') ?: 'بداية' }} - {{ request('date_to') ?: 'نهاية' }}
                    </span>
                @endif
                @if(request('search'))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        البحث: "{{ request('search') }}"
                    </span>
                @endif
                @if(request('workshop_type'))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        النوع: {{ request('workshop_type') == 'online' ? 'أونلاين' : 'أوفلاين' }}
                    </span>
                @endif
                @if(request('price_range'))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                        السعر: {{ request('price_range') == '0-50' ? '0-50 ريال' : (request('price_range') == '50-100' ? '50-100 ريال' : (request('price_range') == '100-200' ? '100-200 ريال' : (request('price_range') == '200-500' ? '200-500 ريال' : 'أكثر من 500 ريال'))) }}
                    </span>
                @endif
                @if(request('payment_method'))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                        الدفع: {{ request('payment_method') }}
                    </span>
                @endif
                @if(request('booking_count'))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        الحجوزات: {{ request('booking_count') == 'single' ? 'واحد فقط' : 'متعددة' }}
                    </span>
                @endif
                @if(request('workshop_date_from') || request('workshop_date_to'))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                        تاريخ الورشة: {{ request('workshop_date_from') ?: 'بداية' }} - {{ request('workshop_date_to') ?: 'نهاية' }}
                    </span>
                @endif
                @if(request('sort_by') && request('sort_by') != 'created_at')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                        الترتيب: {{ request('sort_by') == 'payment_amount' ? 'المبلغ' : 'تاريخ الورشة' }} 
                        ({{ request('sort_direction') == 'desc' ? 'تنازلي' : 'تصاعدي' }})
                    </span>
                @endif
            </div>
        </div>
        @endif

        <!-- جدول الحجوزات -->
        <div class="bg-white shadow-lg overflow-hidden rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-list text-green-500 ml-2"></i>
                        قائمة الحجوزات
                    </h3>
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <div class="text-sm text-gray-500">
                            عرض {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} من أصل {{ $bookings->total() }} حجز
                        </div>
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <button onclick="exportBookings()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-download ml-2"></i>
                                تصدير
                            </button>
                            <button onclick="printBookings()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-print ml-2"></i>
                                طباعة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4">
                
                @if($bookings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="dashboard-table min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <i class="fas fa-user ml-2"></i>
                                        المستخدم
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <i class="fas fa-graduation-cap ml-2"></i>
                                        الورشة
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <i class="fas fa-info-circle ml-2"></i>
                                        الحالة
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <i class="fas fa-credit-card ml-2"></i>
                                        حالة الدفع
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <i class="fas fa-money-bill-wave ml-2"></i>
                                        المبلغ
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <i class="fas fa-calendar ml-2"></i>
                                        تاريخ الحجز
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <i class="fas fa-cogs ml-2"></i>
                                        الإجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bookings as $booking)
                                    <tr class="activity-item hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                                                        <i class="fas fa-user text-white text-lg"></i>
                                                    </div>
                                                </div>
                                                <div class="mr-4">
                                                    <div class="text-sm font-semibold text-gray-900">{{ $booking->user->name }}</div>
                                                    <div class="text-sm text-gray-500 flex items-center">
                                                        <i class="fas fa-envelope text-xs ml-1"></i>
                                                        {{ $booking->user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 mr-3">
                                                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-green-500 to-teal-600 flex items-center justify-center">
                                                        <i class="fas fa-graduation-cap text-white text-sm"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">{{ $booking->workshop->title }}</div>
                                                    <div class="text-sm text-gray-500 flex items-center">
                                                        <i class="fas fa-user-tie text-xs ml-1"></i>
                                                        {{ $booking->workshop->instructor }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($booking->status === 'pending')
                                                <span class="status-badge status-pending inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold">
                                                    <i class="fas fa-clock ml-1"></i>
                                                    في الانتظار
                                                </span>
                                            @elseif($booking->status === 'confirmed')
                                                <span class="status-badge status-confirmed inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold">
                                                    <i class="fas fa-check-circle ml-1"></i>
                                                    مؤكدة
                                                </span>
                                            @else
                                                <span class="status-badge status-cancelled inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold">
                                                    <i class="fas fa-times-circle ml-1"></i>
                                                    ملغية
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($booking->payment_status === 'paid')
                                                <span class="status-badge status-confirmed inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold">
                                                    <i class="fas fa-check ml-1"></i>
                                                    مدفوعة
                                                </span>
                                            @elseif($booking->payment_status === 'refunded')
                                                <span class="status-badge status-cancelled inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold">
                                                    <i class="fas fa-undo ml-1"></i>
                                                    مستردة
                                                </span>
                                            @else
                                                <span class="status-badge status-pending inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold">
                                                    <i class="fas fa-clock ml-1"></i>
                                                    في الانتظار
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ number_format($booking->payment_amount, 2) }} {{ $booking->workshop->currency }}
                                            </div>
                                            @if($booking->payment_method)
                                                <div class="text-xs text-gray-500">
                                                    <i class="fas fa-credit-card ml-1"></i>
                                                    {{ $booking->payment_method }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $booking->created_at->format('Y-m-d') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <i class="fas fa-clock ml-1"></i>
                                                {{ $booking->created_at->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2 space-x-reverse">
                                                <a href="{{ route('admin.bookings.show', $booking) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                                                    <i class="fas fa-eye ml-1"></i>
                                                    عرض
                                                </a>
                                                @if($booking->status === 'pending')
                                                    <button onclick="confirmBooking({{ $booking->id }})" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors duration-200">
                                                        <i class="fas fa-check ml-1"></i>
                                                        تأكيد
                                                    </button>
                                                    <button onclick="cancelBooking({{ $booking->id }})" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 transition-colors duration-200">
                                                        <i class="fas fa-times ml-1"></i>
                                                        إلغاء
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            عرض {{ $bookings->firstItem() ?? 0 }} إلى {{ $bookings->lastItem() ?? 0 }} من أصل {{ $bookings->total() }} نتيجة
                        </div>
                        <div class="flex items-center space-x-2 space-x-reverse">
                            {{ $bookings->appends(request()->query())->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-16">
                        @if(request()->hasAny(['status', 'payment_status', 'workshop_id', 'date_from', 'date_to', 'search', 'workshop_type', 'price_range', 'payment_method', 'booking_count', 'workshop_date_from', 'workshop_date_to', 'sort_by', 'sort_direction']))
                            <div class="mx-auto w-24 h-24 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-search text-blue-500 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">لا توجد نتائج</h3>
                            <p class="text-gray-500 mb-6 max-w-md mx-auto">لم يتم العثور على حجوزات تطابق المعايير المحددة. جرب تعديل الفلاتر أو البحث بكلمات مختلفة.</p>
                            <div class="flex justify-center space-x-4 space-x-reverse">
                                <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dashboard-btn">
                                    <i class="fas fa-times ml-2"></i>
                                    مسح الفلاتر
                                </a>
                                <button onclick="document.getElementById('bookingFiltersForm').reset(); document.getElementById('bookingFiltersForm').submit();" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dashboard-btn">
                                    <i class="fas fa-redo ml-2"></i>
                                    إعادة تعيين
                                </button>
                            </div>
                        @else
                            <div class="mx-auto w-24 h-24 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">لا توجد حجوزات</h3>
                            <p class="text-gray-500 mb-6">لم يتم إنشاء أي حجوزات بعد. ابدأ بإضافة حجز جديد أو انتظر حتى يقوم المستخدمون بالحجز.</p>
                            <a href="{{ route('admin.bookings.manual') }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 dashboard-btn">
                                <i class="fas fa-plus ml-2"></i>
                                إضافة حجز جديد
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden modal-backdrop">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white modal-content">
        <div class="mt-3 text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                <i class="fas fa-question-circle text-blue-600 text-xl"></i>
            </div>
            
            <!-- Title -->
            <h3 class="text-lg font-medium text-gray-900 mb-2" id="modalTitle">
                تأكيد العملية
            </h3>
            
            <!-- Message -->
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="modalMessage">
                    هل أنت متأكد من تأكيد هذا الحجز؟
                </p>
            </div>
            
            <!-- Buttons -->
            <div class="items-center px-4 py-3">
                <div class="flex justify-center space-x-4 space-x-reverse">
                    <button id="modalCancel" class="modal-button px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </button>
                    <button id="modalConfirm" class="modal-button px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200">
                        <i class="fas fa-check ml-2"></i>
                        تأكيد
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Alert Modal -->
<div id="alertModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden modal-backdrop">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white modal-content">
        <div class="mt-3 text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            
            <!-- Title -->
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                تنبيه
            </h3>
            
            <!-- Message -->
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="alertMessage">
                    حدث خطأ أثناء تنفيذ العملية
                </p>
            </div>
            
            <!-- Button -->
            <div class="items-center px-4 py-3">
                <div class="flex justify-center">
                    <button id="alertOk" class="modal-button px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-check ml-2"></i>
                        موافق
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// وظائف إضافية
function refreshBookings() {
    location.reload();
}

function exportBookings() {
    // إضافة معاملات التصدير
    const url = new URL(window.location);
    url.searchParams.set('export', 'excel');
    window.open(url.toString(), '_blank');
}

function printBookings() {
    window.print();
}

// تحسين وظائف الحجز
function confirmBooking(bookingId) {
    showConfirmationModal(
        'تأكيد الحجز',
        'هل أنت متأكد من تأكيد هذا الحجز؟',
        () => {
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;
            
            fetch(`/admin/bookings/${bookingId}/confirm`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showAlertModal(data.message);
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlertModal('حدث خطأ أثناء تأكيد الحجز');
                button.innerHTML = originalContent;
                button.disabled = false;
            });
        }
    );
}

function cancelBooking(bookingId) {
    showCancellationModal(bookingId);
}

// Modal Functions
function showConfirmationModal(title, message, onConfirm) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    const modal = document.getElementById('confirmationModal');
    modal.classList.remove('hidden');
    modal.classList.add('show');
    
    // Clear previous event listeners
    const confirmBtn = document.getElementById('modalConfirm');
    const cancelBtn = document.getElementById('modalCancel');
    
    // Remove existing listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    const newCancelBtn = cancelBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
    
    // Add new listeners
    document.getElementById('modalConfirm').addEventListener('click', () => {
        hideConfirmationModal();
        onConfirm();
    });
    
    document.getElementById('modalCancel').addEventListener('click', hideConfirmationModal);
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            hideConfirmationModal();
        }
    });
}

function hideConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function showAlertModal(message) {
    document.getElementById('alertMessage').textContent = message;
    const modal = document.getElementById('alertModal');
    modal.classList.remove('hidden');
    modal.classList.add('show');
    
    // Clear previous event listeners
    const okBtn = document.getElementById('alertOk');
    const newOkBtn = okBtn.cloneNode(true);
    okBtn.parentNode.replaceChild(newOkBtn, okBtn);
    
    // Add new listener
    document.getElementById('alertOk').addEventListener('click', hideAlertModal);
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            hideAlertModal();
        }
    });
}

function hideAlertModal() {
    const modal = document.getElementById('alertModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function showCancellationModal(bookingId) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 modal-backdrop';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white modal-content">
            <div class="mt-3 text-center">
                <!-- Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                
                <!-- Title -->
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    إلغاء الحجز
                </h3>
                
                <!-- Message -->
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 mb-4">
                        يرجى إدخال سبب الإلغاء:
                    </p>
                    <textarea id="cancellationReason" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                        rows="3" 
                        placeholder="أدخل سبب الإلغاء هنا..."
                        required></textarea>
                    <div id="cancellationError" class="hidden mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle ml-1"></i>
                        يرجى إدخال سبب الإلغاء
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="items-center px-4 py-3">
                    <div class="flex justify-center space-x-4 space-x-reverse">
                        <button id="cancelCancel" class="modal-button px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </button>
                        <button id="cancelConfirm" class="modal-button px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                            <i class="fas fa-check ml-2"></i>
                            تأكيد الإلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Event listeners
    document.getElementById('cancelCancel').addEventListener('click', () => {
        document.body.removeChild(modal);
    });
    
    // Hide error when user starts typing
    document.getElementById('cancellationReason').addEventListener('input', () => {
        document.getElementById('cancellationError').classList.add('hidden');
    });
    
    document.getElementById('cancelConfirm').addEventListener('click', () => {
        const reason = document.getElementById('cancellationReason').value.trim();
        const errorDiv = document.getElementById('cancellationError');
        
        if (reason === '') {
            errorDiv.classList.remove('hidden');
            return;
        } else {
            errorDiv.classList.add('hidden');
        }
        
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        fetch(`/admin/bookings/${bookingId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cancellation_reason: reason
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showAlertModal(data.message);
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlertModal('حدث خطأ أثناء إلغاء الحجز');
            button.innerHTML = originalContent;
            button.disabled = false;
        });
        
        document.body.removeChild(modal);
    });
}

// تحسين وظائف الفلترة
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookingFiltersForm');
    if (!form) {
        return;
    }

    const dateFromInput = form.querySelector('input[name="date_from"]');
    const dateToInput = form.querySelector('input[name="date_to"]');
    const workshopDateFromInput = form.querySelector('input[name="workshop_date_from"]');
    const workshopDateToInput = form.querySelector('input[name="workshop_date_to"]');
    const statusSelect = document.getElementById('statusSelect');
    const quickFilterChips = form.querySelectorAll('.quick-filter-chip');
    const clearFiltersButton = document.getElementById('clearFiltersButton');
    const toggleAdvancedFiltersButton = document.getElementById('toggleAdvancedFilters');
    const advancedFiltersSection = document.getElementById('advancedFilters');
    const advancedFiltersLabel = document.getElementById('advancedFiltersToggleLabel');

    // التحقق من صحة التواريخ الأساسية
    function validateDates() {
        if (!dateFromInput || !dateToInput) {
            return true;
        }

        const dateFrom = dateFromInput.value;
        const dateTo = dateToInput.value;

        if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
            showAlertModal('تاريخ البداية يجب أن يكون قبل تاريخ النهاية');
            dateToInput.value = '';
            return false;
        }
        return true;
    }

    if (dateFromInput && dateToInput) {
        dateFromInput.addEventListener('change', function() {
            if (this.value && dateToInput.value) {
                validateDates();
            }
        });

        dateToInput.addEventListener('change', function() {
            if (this.value && dateFromInput.value) {
                validateDates();
            }
        });
    }

    if (toggleAdvancedFiltersButton && advancedFiltersSection) {
        toggleAdvancedFiltersButton.addEventListener('click', () => {
            const isHidden = advancedFiltersSection.classList.toggle('hidden');
            toggleAdvancedFiltersButton.setAttribute('aria-expanded', (!isHidden).toString());
            if (advancedFiltersLabel) {
                advancedFiltersLabel.textContent = isHidden ? 'عرض الفلاتر المتقدمة' : 'إخفاء الفلاتر المتقدمة';
            }
        });
    }

    const filterInputs = form.querySelectorAll('select, input[type="date"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (validateDates()) {
                form.submit();
            }
        });
    });

    // التحقق من صحة تواريخ الورشة
    function validateWorkshopDates() {
        if (!workshopDateFromInput || !workshopDateToInput) {
            return true;
        }

        const dateFrom = workshopDateFromInput.value;
        const dateTo = workshopDateToInput.value;

        if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
            showAlertModal('تاريخ بداية الورشة يجب أن يكون قبل تاريخ نهاية الورشة');
            workshopDateToInput.value = '';
            return false;
        }
        return true;
    }

    if (workshopDateFromInput && workshopDateToInput) {
        workshopDateFromInput.addEventListener('change', function() {
            if (this.value && workshopDateToInput.value) {
                validateWorkshopDates();
            }
        });

        workshopDateToInput.addEventListener('change', function() {
            if (this.value && workshopDateFromInput.value) {
                validateWorkshopDates();
            }
        });
    }

    // البحث مع تأخير
    const searchInput = form.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 2 || this.value.length === 0) {
                    form.submit();
                }
            }, 500);
        });
    }

    // الفلاتر السريعة للحالة
    quickFilterChips.forEach(chip => {
        chip.addEventListener('click', () => {
            quickFilterChips.forEach(btn => btn.classList.remove('is-active'));
            chip.classList.add('is-active');

            if (statusSelect) {
                statusSelect.value = chip.getAttribute('data-status-value') || '';
                statusSelect.dispatchEvent(new Event('change'));
            } else {
                form.submit();
            }
        });
    });

    if (clearFiltersButton) {
        clearFiltersButton.addEventListener('click', () => {
            form.reset();
            if (statusSelect) {
                statusSelect.value = '';
            }

            quickFilterChips.forEach(btn => btn.classList.remove('is-active'));
            const defaultChip = Array.from(quickFilterChips).find(btn => (btn.getAttribute('data-status-value') || '') === '');
            if (defaultChip) {
                defaultChip.classList.add('is-active');
            }

            if (validateDates()) {
                form.submit();
            }
        });
    }

    // إضافة تأثيرات بصرية للتحميل
    form.addEventListener('submit', function() {
        const submitButton = form.querySelector('button[type="submit"]');
        if (!submitButton) {
            return;
        }

        const originalContent = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري البحث...';
        submitButton.disabled = true;

        document.body.style.opacity = '0.8';
        document.body.style.transition = 'opacity 0.3s ease';

        setTimeout(() => {
            submitButton.innerHTML = originalContent;
            submitButton.disabled = false;
            document.body.style.opacity = '1';
        }, 3000);
    });

    // إضافة تأثيرات hover للبطاقات
    const cards = document.querySelectorAll('.dashboard-card, .bg-white');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.3s ease';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // إضافة تأثيرات للجداول
    const tableRows = document.querySelectorAll('.activity-item');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
            this.style.transition = 'box-shadow 0.3s ease';
        });

        row.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'none';
        });
    });
});
</script>
@endsection
