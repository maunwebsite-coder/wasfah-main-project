@extends('layouts.app')

@section('title', 'ورشات العمل - موقع وصفة')

@push('styles')
<style>
    body {
        background-color: #f9fafb;
    }
    .workshop-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .workshop-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .filter-btn.active {
        background-color: #f97316;
        color: white;
        border-color: #f97316;
    }
    .faq-question {
        transition: background-color 0.3s ease;
    }
    .faq-answer {
        transition: max-height 0.5s ease-in-out, padding 0.5s ease, opacity 0.5s ease;
        max-height: 0;
        overflow: hidden;
        padding-top: 0;
        padding-bottom: 0;
        opacity: 0;
    }
    .faq-item.open .faq-answer {
        max-height: 200px; /* Adjust as needed */
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
        opacity: 1;
    }
    .faq-item.open .faq-icon {
        transform: rotate(180deg);
    }
    .faq-icon {
        transition: transform 0.3s ease;
    }
    
    /* Workshop Card Image Improvements */
    .workshop-card img {
        width: 100%;
        height: 240px;
        object-fit: cover;
        object-position: center;
        transition: transform 0.3s ease;
        aspect-ratio: 16/9;
    }
    
    .workshop-card:hover img {
        transform: scale(1.05);
    }
    
    /* Mobile Responsive Images */
    @media (max-width: 768px) {
        .workshop-card img {
            height: 220px;
            object-fit: cover;
            object-position: center;
            min-height: 200px;
            max-height: 250px;
            aspect-ratio: 4/3;
        }
    }
    
    @media (max-width: 480px) {
        .workshop-card img {
            height: 200px;
            object-fit: cover;
            object-position: center;
            min-height: 180px;
            max-height: 220px;
            aspect-ratio: 4/3;
        }
    }
    
    /* Ensure images load properly */
    .workshop-card img {
        background-color: #f3f4f6;
        background-image: linear-gradient(45deg, #f3f4f6 25%, transparent 25%), 
                         linear-gradient(-45deg, #f3f4f6 25%, transparent 25%), 
                         linear-gradient(45deg, transparent 75%, #f3f4f6 75%), 
                         linear-gradient(-45deg, transparent 75%, #f3f4f6 75%);
        background-size: 20px 20px;
        background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    }
</style>
@endpush

@section('content')
@php
    $showAdminMetrics = auth()->check() && auth()->user()->isAdmin();
@endphp
<div class="min-h-screen bg-gray-50">

    <!-- Featured Workshop -->
    @if($featuredWorkshop)
    @php 
        $featuredIsFull = $featuredWorkshop->bookings_count >= $featuredWorkshop->max_participants; 
        $featuredIsRegistrationClosed = !$featuredWorkshop->is_registration_open;
        $featuredIsCompleted = $featuredWorkshop->is_completed;
        $featuredLocationLabel = $featuredWorkshop->is_online ? 'ورشة أونلاين' : ($featuredWorkshop->location ?? 'ورشة حضورية');
        $featuredDeadlineLabel = $featuredWorkshop->registration_deadline ? $featuredWorkshop->registration_deadline->format('d/m/Y') : 'غير محدد';
        $featuredIsBooked = !empty($bookedWorkshopIds) && in_array($featuredWorkshop->id, $bookedWorkshopIds, true);
    @endphp
    <section class="container mx-auto px-4 pt-10 md:pt-16 relative z-20">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl overflow-hidden shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <!-- المحتوى النصي -->
                <div class="p-5 sm:p-8 lg:p-12 text-white flex flex-col justify-center">
                    <div class="mb-5 sm:mb-6">
                        <span class="bg-white/20 text-white text-sm font-semibold px-3.5 py-1.5 rounded-full inline-block mb-3.5 sm:mb-4">
                            الورشة القادمة
                        </span>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 sm:mb-4 leading-tight">
                            {{ $featuredWorkshop->title }}
                        </h2>
                        <p class="text-base sm:text-lg text-amber-100 mb-5 sm:mb-6 leading-relaxed">
                            {{ $featuredWorkshop->featured_description ?: $featuredWorkshop->description }}
                        </p>
                    </div>
                    
                    <!-- تفاصيل الورشة -->
                    <div class="space-y-1.5 sm:space-y-3 mb-6 sm:mb-8">
                        <div class="flex items-center text-amber-100">
                            <i class="fas fa-calendar-alt w-5 text-center ml-3"></i>
                            <span class="font-medium">{{ $featuredWorkshop->start_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center text-amber-100">
                            <i class="fas {{ $featuredWorkshop->is_online ? 'fa-video' : 'fa-map-marker-alt' }} w-5 text-center ml-3"></i>
                            <span class="font-medium">{{ $featuredWorkshop->is_online ? 'ورشة أونلاين' : ($featuredWorkshop->location ?? 'ورشة حضورية') }}</span>
                        </div>
                        <div class="flex items-center text-amber-100">
                            <i class="fas fa-user w-5 text-center ml-3"></i>
                            <span class="font-medium">مع {{ $featuredWorkshop->instructor }}</span>
                        </div>
                        @if($showAdminMetrics)
                            <div class="flex items-center text-amber-100">
                                <i class="fas fa-users w-5 text-center ml-3"></i>
                                <span class="font-medium">{{ $featuredWorkshop->bookings_count }}/{{ $featuredWorkshop->max_participants }} مشارك</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- أزرار الإجراءات -->
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                        @if($featuredIsCompleted)
                            <button type="button" class="bg-gray-400 text-gray-600 font-bold py-2.5 px-5 sm:py-4 sm:px-8 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg">
                                <i class="fas fa-check-circle ml-2 text-xl"></i>
                                الورشة مكتملة
                            </button>
                        @elseif($featuredIsBooked)
                            <button type="button" class="bg-green-500 text-white font-bold py-2.5 px-5 sm:py-4 sm:px-8 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg" disabled>
                                <i class="fas fa-check ml-2 text-xl booking-button-icon"></i>
                                <span class="booking-button-label">تم الحجز بالفعل</span>
                            </button>
                        @elseif($featuredIsFull)
                            <button type="button" class="bg-gray-400 text-gray-600 font-bold py-2.5 px-5 sm:py-4 sm:px-8 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg">
                                <i class="fas fa-lock ml-2 text-xl"></i>
                                الورشة مكتملة
                            </button>
                        @elseif($featuredIsRegistrationClosed)
                            <button type="button" class="bg-yellow-400 text-yellow-800 font-bold py-2.5 px-5 sm:py-4 sm:px-8 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg">
                                <i class="fas fa-clock ml-2 text-xl"></i>
                                انتهى التسجيل
                            </button>
                        @else
                            <button type="button"
                                    class="js-whatsapp-booking bg-white text-amber-600 hover:bg-amber-50 font-bold py-2.5 px-5 sm:py-4 sm:px-8 rounded-xl transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl"
                                    data-workshop-id="{{ $featuredWorkshop->id }}"
                                    data-title="{{ e($featuredWorkshop->title) }}"
                                    data-price="{{ e($featuredWorkshop->formatted_price) }}"
                                    data-date="{{ e($featuredWorkshop->start_date->format('d/m/Y')) }}"
                                    data-instructor="{{ e($featuredWorkshop->instructor ?? 'غير محدد') }}"
                                    data-location="{{ e($featuredLocationLabel) }}"
                                    data-deadline="{{ e($featuredDeadlineLabel) }}"
                                    data-is-booked="false">
                                <i class="fab fa-whatsapp ml-2 text-xl booking-button-icon"></i>
                                <span class="booking-button-label">التسجيل في الورشة</span>
                            </button>
                        @endif
                        <a href="{{ route('workshop.show', $featuredWorkshop->slug) }}" 
                           class="border-2 border-white text-white hover:bg-white hover:text-amber-600 font-bold py-2.5 px-5 sm:py-4 sm:px-8 rounded-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-info-circle ml-2"></i>
                            تفاصيل أكثر
                        </a>
                    </div>
                </div>
                
                <!-- الصورة -->
                <div class="relative h-48 sm:h-64 lg:h-auto">
                    <img src="{{ $featuredWorkshop->image ? asset('storage/' . $featuredWorkshop->image) : 'https://placehold.co/800x600/f87171/FFFFFF?text=ورشة+فاخرة' }}" 
                         alt="{{ $featuredWorkshop->title }}" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-l from-transparent to-amber-500/20"></div>
                </div>
            </div>
        </div>
    </section>
    @else
    <!-- رسالة عدم وجود ورشات قادمة -->
    <section class="container mx-auto px-4 pt-10 md:pt-16 relative z-20">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl overflow-hidden shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <!-- المحتوى النصي -->
                <div class="p-5 sm:p-8 lg:p-12 text-white flex flex-col justify-center">
                    <div class="mb-5 sm:mb-6">
                        <span class="bg-white/20 text-white text-sm font-semibold px-3.5 py-1.5 rounded-full inline-block mb-3.5 sm:mb-4">
                            الورشة القادمة
                        </span>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 sm:mb-4 leading-tight">
                            لا توجد ورشات قادمة الآن
                        </h2>
                        <p class="text-base sm:text-lg text-amber-100 mb-6 sm:mb-8 leading-relaxed">
                            نحن نعمل على إعداد ورشات جديدة ومميزة لك. انتظرونا في الورشة القادمة!
                        </p>
                    </div>
                    
                    <!-- أزرار الإجراءات -->
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                        <a href="{{ route('workshops') }}" 
                           class="bg-white text-amber-600 hover:bg-amber-50 font-bold py-2.5 px-5 sm:py-4 sm:px-8 rounded-xl transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl">
                            <i class="fas fa-list ml-2 text-xl"></i>
                            تصفح جميع الورشات
                        </a>
                        <a href="{{ route('recipes') }}" 
                           class="border-2 border-white text-white hover:bg-white hover:text-amber-600 font-bold py-2.5 px-5 sm:py-4 sm:px-8 rounded-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-utensils ml-2"></i>
                            اكتشف الوصفات
                        </a>
                    </div>
                </div>
                
                <!-- الصورة/الأيقونة -->
                <div class="relative h-48 sm:h-64 lg:h-auto overflow-hidden flex items-center justify-center">
                    <div class="w-full h-full flex items-center justify-center">
                        <div class="w-36 h-36 sm:w-48 sm:h-48 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-5xl sm:text-8xl text-white/80"></i>
                        </div>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-l from-transparent to-amber-500/20"></div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Filters Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div id="filter-buttons" class="flex flex-wrap justify-center items-center gap-3">
                <button class="filter-btn active font-semibold px-5 py-2 border rounded-full transition-colors duration-300" data-filter="all">الكل</button>
                <button class="filter-btn font-semibold px-5 py-2 border rounded-full transition-colors duration-300" data-filter="online">أونلاين</button>
                <button class="filter-btn font-semibold px-5 py-2 border rounded-full transition-colors duration-300" data-filter="offline">حضور شخصي</button>
                <button class="filter-btn font-semibold px-5 py-2 border rounded-full transition-colors duration-300" data-filter="beginner">مبتدئ</button>
                <button class="filter-btn font-semibold px-5 py-2 border rounded-full transition-colors duration-300" data-filter="advanced">متقدم</button>
            </div>
        </div>
    </section>

    <!-- قسم الورشات المحسن -->
    <section class="py-5 bg-gradient-to-br from-gray-50 to-white">
        <div class="container mx-auto px-4">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">ورشات الحلويات الفاخرة</h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto mb-6">انضم إلى ورشاتنا الاحترافية الحصرية وتعلم أسرار صنع أرقى الحلويات العالمية</p>
            </div>
            
            @if($workshops->count() > 0)
                <!-- Workshops Grid -->
                <div id="workshops-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($workshops as $workshop)
                        @php 
                            $isFull = $workshop->bookings_count >= $workshop->max_participants; 
                            $isRegistrationClosed = !$workshop->is_registration_open;
                            $isCompleted = $workshop->is_completed;
                            $isBooked = !empty($bookedWorkshopIds) && in_array($workshop->id, $bookedWorkshopIds, true);
                            $bookingLocationLabel = $workshop->is_online ? 'ورشة أونلاين' : ($workshop->location ?? 'ورشة حضورية');
                            $bookingDeadlineLabel = $workshop->registration_deadline ? $workshop->registration_deadline->format('d/m/Y') : 'غير محدد';
                            $bookingInstructor = $workshop->instructor ?? 'غير محدد';
                            $bookingButtonStateClasses = $isBooked
                                ? 'bg-green-500 text-white cursor-not-allowed'
                                : 'bg-green-500 hover:bg-green-600 text-white';
                        @endphp
                        <div class="workshop-card bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full" 
                             data-type="{{ $workshop->is_online ? 'online' : 'offline' }}" 
                             data-level="{{ $workshop->level }}" 
                             data-category="{{ $workshop->category }}"
                             data-workshop-id="{{ $workshop->id }}">
                            <div class="relative">
                                <img src="{{ $workshop->image ? asset('storage/' . $workshop->image) : 'https://placehold.co/600x400/f87171/FFFFFF?text=ورشة' }}" 
                                     alt="{{ $workshop->title }}"
                                     onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                                @if($isFull)
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                    <span class="text-white font-bold text-lg bg-red-500 px-4 py-2 rounded-full">اكتمل العدد</span>
                                </div>
                                @elseif($isRegistrationClosed)
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                    <span class="text-white font-bold text-lg bg-yellow-500 px-4 py-2 rounded-full">
                                        <i class="fas fa-clock ml-2"></i>
                                        انتهى التسجيل
                                    </span>
                                </div>
                                @endif
                                <div class="absolute top-4 right-4 bg-orange-500 text-white text-sm font-semibold px-3 py-1 rounded-full">{{ $workshop->price }} {{ $workshop->currency }}</div>
                            </div>
                            
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="mb-2">
                                    <span class="text-sm font-semibold {{ $workshop->is_online ? 'text-blue-600' : 'text-green-600' }}">
                                        {{ $workshop->is_online ? 'اونلاين' : 'حضوري' }}
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $workshop->title }}</h3>
                                <p class="text-gray-600 mb-4">مع {{ $workshop->instructor }}</p>
                                <div class="flex items-center text-gray-500 text-sm mb-4">
                                    <i class="fas fa-calendar-alt mr-2 rtl:ml-2"></i> {{ $workshop->start_date->format('d/m/Y') }}
                                </div>
                                <div class="flex items-center text-gray-500 text-sm mb-4">
                                    <i class="fas fa-map-marker-alt mr-2 rtl:ml-2"></i> {{ $workshop->is_online ? 'اونلاين (مباشر)' : ($workshop->location ?? 'غير محدد') }}
                                </div>
                                @if($showAdminMetrics)
                                    <div class="flex items-center text-gray-500 text-sm mb-4">
                                        <i class="fas fa-users mr-2 rtl:ml-2"></i> {{ $workshop->bookings_count }}/{{ $workshop->max_participants }} مشارك
                                    </div>
                                @endif
                                
                                <!-- الأزرار المثبتة في الأسفل -->
                                <div class="mt-auto pt-4 border-t border-gray-100">
                                    <div class="flex gap-3 items-center">
                                        @if($isCompleted)
                                        <button type="button" class="flex-1 bg-gray-400 text-white font-bold py-3 px-4 rounded-full cursor-not-allowed flex items-center justify-center text-sm">
                                            <i class="fas fa-check-circle ml-2"></i>
                                            الورشة مكتملة
                                        </button>
                                        @elseif($isBooked)
                                        <button type="button" class="flex-1 bg-green-500 text-white font-bold py-3 px-4 rounded-full cursor-not-allowed flex items-center justify-center text-sm" disabled>
                                            <i class="fas fa-check ml-2 booking-button-icon"></i>
                                            <span class="booking-button-label">تم الحجز بالفعل</span>
                                        </button>
                                        @elseif($isFull)
                                        <button type="button" class="flex-1 bg-gray-400 text-white font-bold py-3 px-4 rounded-full cursor-not-allowed flex items-center justify-center text-sm">
                                            <i class="fas fa-lock ml-2"></i>
                                            الورشة مكتملة
                                        </button>
                                        @elseif($isRegistrationClosed)
                                            <button type="button" class="flex-1 bg-yellow-400 text-yellow-800 font-bold py-3 px-4 rounded-full cursor-not-allowed flex items-center justify-center text-sm">
                                                <i class="fas fa-clock ml-2"></i>
                                                انتهى التسجيل
                                            </button>
                                        @else
                                                        <button type="button"
                                                                class="js-whatsapp-booking flex-1 text-center font-bold py-3 px-4 rounded-full transition-colors flex items-center justify-center text-sm {{ $bookingButtonStateClasses }}"
                                                    data-workshop-id="{{ $workshop->id }}"
                                                    data-title="{{ e($workshop->title) }}"
                                                    data-price="{{ e($workshop->formatted_price) }}"
                                                    data-date="{{ e($workshop->start_date->format('d/m/Y')) }}"
                                                    data-instructor="{{ e($bookingInstructor) }}"
                                                    data-location="{{ e($bookingLocationLabel) }}"
                                                    data-deadline="{{ e($bookingDeadlineLabel) }}"
                                                    data-is-booked="false">
                                                <i class="fab fa-whatsapp ml-2 booking-button-icon"></i>
                                                <span class="booking-button-label">احجز الآن</span>
                                            </button>
                                        @endif
                                        <a href="{{ route('workshop.show', $workshop->slug) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-full transition-colors flex items-center justify-center group">
                                            <i class="fas fa-info-circle text-sm ml-2 group-hover:text-orange-500 transition-colors"></i>
                                            <span class="text-sm">تفاصيل</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-20 bg-white rounded-2xl shadow-lg">
                    <div class="w-24 h-24 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-coffee text-4xl text-amber-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-3">لا توجد ورشات متاحة حالياً</h3>
                    <p class="text-gray-500 text-lg max-w-md mx-auto mb-6">نحن نعمل على تحضير ورشات جديدة ومميزة. تحقق مرة أخرى قريباً!</p>
                </div>
            @endif
        </div>
    </section>




    <!-- Why Choose Us? Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-10">لماذا تختار ورشاتنا؟</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="flex flex-col items-center">
                    <div class="bg-orange-100 text-orange-500 rounded-full h-16 w-16 flex items-center justify-center mb-4"><i class="fas fa-user-tie text-3xl"></i></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">طهاة خبراء</h3>
                    <p class="text-gray-600">تعلم من أفضل الطهاة والمختصين في مجالهم.</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-orange-100 text-orange-500 rounded-full h-16 w-16 flex items-center justify-center mb-4"><i class="fas fa-hands-helping text-3xl"></i></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">تطبيق عملي</h3>
                    <p class="text-gray-600">ورشاتنا تفاعلية وتركز على التطبيق العملي المباشر.</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-orange-100 text-orange-500 rounded-full h-16 w-16 flex items-center justify-center mb-4"><i class="fas fa-star text-3xl"></i></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">مكونات عالية الجودة</h3>
                    <p class="text-gray-600">نوفر لك أفضل المكونات الطازجة لضمان أفضل النتائج.</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-orange-100 text-orange-500 rounded-full h-16 w-16 flex items-center justify-center mb-4"><i class="fas fa-certificate text-3xl"></i></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">شهادة إتمام</h3>
                    <p class="text-gray-600">احصل على شهادة تقديرية بعد إتمام كل ورشة عمل.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- FAQ Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12 text-gray-800">أسئلة شائعة</h2>
            <div id="faq-container" class="max-w-4xl mx-auto space-y-4">
                @foreach([
                    ['question' => 'كيف يمكنني التسجيل في ورشة عمل؟', 'answer' => 'يمكنك التسجيل بسهولة عبر النقر على زر "احجز الآن" في بطاقة الورشة والذي سينقلك مباشرة إلى الواتساب لملء البيانات المطلوبة.'],
                    ['question' => 'هل أحتاج إلى خبرة سابقة في الطبخ؟', 'answer' => 'لا، معظم ورشاتنا مصممة للمبتدئين. نحن نقدم ورشات لجميع المستويات، من المبتدئ إلى المتقدم. ستجد مستوى مناسب لك في كل ورشة.'],
                    ['question' => 'ما هي الأدوات المطلوبة للورشات الأونلاين؟', 'answer' => 'للورشات الأونلاين، ستحتاج إلى جهاز كمبيوتر أو هاتف ذكي مع كاميرا، واتصال إنترنت مستقر، والأدوات الأساسية للطبخ في مطبخك. سيتم إرسال قائمة بالمكونات المطلوبة مسبقاً.'],
                ] as $faq)
                <div class="faq-item border border-gray-200 rounded-xl overflow-hidden transition-all duration-300 hover:border-orange-300">
                    <button class="faq-question w-full text-right p-6 bg-white hover:bg-orange-50 flex items-center justify-between focus:outline-none">
                        <span class="font-semibold text-lg text-gray-800">{{ $faq['question'] }}</span>
                        <i class="fas fa-chevron-down faq-icon text-orange-500"></i>
                    </button>
                    <div class="faq-answer px-6 bg-white text-gray-600">
                        <p>{{ $faq['answer'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
@vite(['resources/js/workshops.js', 'resources/js/whatsapp-booking.js'])

<script>
document.addEventListener('DOMContentLoaded', function() {
    WhatsAppBooking.configure({
        isLoggedIn: @json(auth()->check()),
        whatsappNumber: '962790553680',
        bookingEndpoint: @json(route('bookings.store')),
        loginUrl: @json(route('login')),
        registerUrl: @json(route('register')),
        user: {
            name: @json(optional(auth()->user())->name ?? 'مستخدم'),
            phone: @json(optional(auth()->user())->phone ?? 'غير محدد'),
            email: @json(optional(auth()->user())->email ?? 'غير محدد'),
        },
    });
    WhatsAppBooking.initButtons();

    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const workshopsContainer = document.getElementById('workshops-container');
    const workshops = document.querySelectorAll('.workshop-card');
    const noResults = document.getElementById('no-results');

    // Initialize no results message if it doesn't exist
    if (!noResults) {
        const noResultsHTML = `
            <div id="no-results" class="col-span-full text-center py-16 hidden">
                <i class="fas fa-search text-7xl text-gray-300 mb-6"></i>
                <h3 class="text-2xl font-semibold text-gray-700 mb-2">لا توجد نتائج تطابق الفلتر</h3>
                <p class="text-gray-500 max-w-md mx-auto">يرجى تجربة فلتر مختلف.</p>
            </div>
        `;
        workshopsContainer.insertAdjacentHTML('afterend', noResultsHTML);
    }

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter workshops
            let visibleCount = 0;
            workshops.forEach(workshop => {
                const type = workshop.getAttribute('data-type');
                const level = workshop.getAttribute('data-level');
                
                let show = false;
                
                switch(filter) {
                    case 'all':
                        show = true;
                        break;
                    case 'online':
                        show = type === 'online';
                        break;
                    case 'offline':
                        show = type === 'offline';
                        break;
                    case 'beginner':
                        show = level === 'beginner';
                        break;
                    case 'advanced':
                        show = level === 'advanced';
                        break;
                }
                
                if (show) {
                    workshop.style.display = 'flex';
                    visibleCount++;
                } else {
                    workshop.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            const noResultsElement = document.getElementById('no-results');
            if (visibleCount === 0) {
                noResultsElement.classList.remove('hidden');
            } else {
                noResultsElement.classList.add('hidden');
            }
        });
    });
});
</script>

@endpush

