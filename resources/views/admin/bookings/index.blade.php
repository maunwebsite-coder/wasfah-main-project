@extends('layouts.app')

@section('title', 'إدارة الحجوزات')

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

        <!-- إحصائيات اليوم -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="dashboard-card bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow-lg rounded-lg text-white">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-check dashboard-icon"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium opacity-90 truncate">إجمالي الحجوزات</dt>
                                <dd class="stat-number">{{ number_format($stats['total']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 overflow-hidden shadow-lg rounded-lg text-white">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-3xl"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium opacity-90 truncate">في الانتظار</dt>
                                <dd class="text-2xl font-bold">{{ number_format($stats['pending']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 overflow-hidden shadow-lg rounded-lg text-white">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium opacity-90 truncate">مؤكدة</dt>
                                <dd class="text-2xl font-bold">{{ number_format($stats['confirmed']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-red-500 to-red-600 overflow-hidden shadow-lg rounded-lg text-white">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-times-circle text-3xl"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium opacity-90 truncate">ملغية</dt>
                                <dd class="text-2xl font-bold">{{ number_format($stats['cancelled']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإحصائيات الرئيسية -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- المدفوعات -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-r-4 border-green-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-credit-card text-3xl text-green-500"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    مدفوعة
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900">
                                    {{ number_format($stats['paid']) }}
                                </dd>
                                <dd class="text-sm text-green-600 flex items-center">
                                    <i class="fas fa-check ml-1"></i>
                                    {{ number_format(($stats['paid'] / max($stats['total'], 1)) * 100, 1) }}% من إجمالي الحجوزات
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- غير المدفوعات -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-r-4 border-orange-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-3xl text-orange-500"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    غير مدفوعة
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900">
                                    {{ number_format($stats['unpaid']) }}
                                </dd>
                                <dd class="text-sm text-orange-600 flex items-center">
                                    <i class="fas fa-clock ml-1"></i>
                                    تحتاج متابعة
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معدل التأكيد -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-r-4 border-purple-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-percentage text-3xl text-purple-500"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">معدل التأكيد</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format(($stats['confirmed'] / max($stats['total'], 1)) * 100, 1) }}%</dd>
                                <dd class="text-sm text-purple-600 flex items-center">
                                    <i class="fas fa-chart-line ml-1"></i>
                                    من إجمالي الحجوزات
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معدل الإلغاء -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg border-r-4 border-red-500">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-ban text-3xl text-red-500"></i>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">معدل الإلغاء</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format(($stats['cancelled'] / max($stats['total'], 1)) * 100, 1) }}%</dd>
                                <dd class="text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-triangle ml-1"></i>
                                    يحتاج تحسين
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الفلاتر -->
        <div class="bg-white shadow-lg rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-filter text-blue-500 ml-2"></i>
                    فلترة الحجوزات
                </h3>
                <p class="mt-1 text-sm text-gray-500">استخدم الفلاتر أدناه للعثور على الحجوزات المحددة</p>
            </div>
            <div class="p-6">
                <form method="GET" id="bookingFiltersForm" class="space-y-6">
                    <!-- الصف الأول - الفلاتر الأساسية -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
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

                    <!-- الصف الثاني - الفلاتر المتقدمة -->
                    <div class="border-t border-gray-200 pt-4">
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
                                <option value="single" {{ request('booking_count') == 'single' ? 'selected' : '' }}>حجز واحد فقط</option>
                                <option value="multiple" {{ request('booking_count') == 'multiple' ? 'selected' : '' }}>حجوزات متعددة</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الورشة</label>
                            <div class="flex gap-2">
                                <input type="date" name="workshop_date_from" value="{{ request('workshop_date_from') }}" placeholder="من" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <input type="date" name="workshop_date_to" value="{{ request('workshop_date_to') }}" placeholder="إلى" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- الصف الثالث - البحث والترتيب والأزرار -->
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
                                <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                <i class="fas fa-times ml-2"></i>
                                مسح
                            </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
    const dateFromInput = document.querySelector('input[name="date_from"]');
    const dateToInput = document.querySelector('input[name="date_to"]');
    
    // التحقق من صحة التواريخ
    function validateDates() {
        const dateFrom = dateFromInput.value;
        const dateTo = dateToInput.value;
        
        if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
            showAlertModal('تاريخ البداية يجب أن يكون قبل تاريخ النهاية');
            dateToInput.value = '';
            return false;
        }
        return true;
    }
    
    // إضافة مستمعي الأحداث
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
    
    // إرسال النموذج عند تغيير الفلاتر
    const filterInputs = form.querySelectorAll('select, input[type="date"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (validateDates()) {
                form.submit();
            }
        });
    });

    // التحقق من صحة تواريخ الورشة
    const workshopDateFromInput = document.querySelector('input[name="workshop_date_from"]');
    const workshopDateToInput = document.querySelector('input[name="workshop_date_to"]');
    
    function validateWorkshopDates() {
        const dateFrom = workshopDateFromInput.value;
        const dateTo = workshopDateToInput.value;
        
        if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
            showAlertModal('تاريخ بداية الورشة يجب أن يكون قبل تاريخ نهاية الورشة');
            workshopDateToInput.value = '';
            return false;
        }
        return true;
    }
    
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
    
    // البحث مع تأخير
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 2 || this.value.length === 0) {
                form.submit();
            }
        }, 500);
    });
    
    // إضافة تأثيرات بصرية للتحميل
    form.addEventListener('submit', function() {
        const submitButton = form.querySelector('button[type="submit"]');
        const originalContent = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري البحث...';
        submitButton.disabled = true;
        
        // إضافة تأثير تحميل للصفحة
        document.body.style.opacity = '0.8';
        document.body.style.transition = 'opacity 0.3s ease';
        
        // إعادة تعيين الزر بعد 3 ثوانٍ كحد أقصى
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
