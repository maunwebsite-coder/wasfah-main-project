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
<div class="min-h-screen bg-gray-50">

    <!-- Featured Workshop -->
    @if($featuredWorkshop)
    @php 
        $featuredIsFull = $featuredWorkshop->bookings_count >= $featuredWorkshop->max_participants; 
        $featuredIsRegistrationClosed = !$featuredWorkshop->is_registration_open;
        $featuredIsCompleted = $featuredWorkshop->is_completed;
    @endphp
    <section class="container mx-auto px-4 pt-12 md:pt-16 relative z-20">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl overflow-hidden shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <!-- المحتوى النصي -->
                <div class="p-6 sm:p-8 lg:p-12 text-white flex flex-col justify-center">
                    <div class="mb-6">
                        <span class="bg-white/20 text-white text-sm font-semibold px-4 py-2 rounded-full inline-block mb-4">
                            الورشة القادمة
                        </span>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-4 leading-tight">
                            {{ $featuredWorkshop->title }}
                        </h2>
                        <p class="text-base sm:text-lg text-amber-100 mb-6 leading-relaxed">
                            {{ $featuredWorkshop->featured_description ?: $featuredWorkshop->description }}
                        </p>
                    </div>
                    
                    <!-- تفاصيل الورشة -->
                    <div class="space-y-2 sm:space-y-3 mb-8">
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
                        <div class="flex items-center text-amber-100">
                            <i class="fas fa-users w-5 text-center ml-3"></i>
                            <span class="font-medium">{{ $featuredWorkshop->bookings_count }}/{{ $featuredWorkshop->max_participants }} مشارك</span>
                        </div>
                    </div>
                    
                    <!-- أزرار الإجراءات -->
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                        @if($featuredIsCompleted)
                            <button class="bg-gray-400 text-gray-600 font-bold py-3 px-6 sm:py-4 sm:px-8 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg">
                                <i class="fas fa-check-circle ml-2 text-xl"></i>
                                الورشة مكتملة
                            </button>
                        @elseif($featuredIsFull)
                            <button class="bg-gray-400 text-gray-600 font-bold py-3 px-6 sm:py-4 sm:px-8 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg">
                                <i class="fas fa-lock ml-2 text-xl"></i>
                                الورشة مكتملة
                            </button>
                        @elseif($featuredIsRegistrationClosed)
                            <button class="bg-yellow-400 text-yellow-800 font-bold py-3 px-6 sm:py-4 sm:px-8 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg">
                                <i class="fas fa-clock ml-2 text-xl"></i>
                                انتهى التسجيل
                            </button>
                        @else
                            <button onclick="unifiedBooking({{ $featuredWorkshop->id }}, '{{ $featuredWorkshop->title }}', '{{ $featuredWorkshop->formatted_price }}', '{{ $featuredWorkshop->start_date->format('d/m/Y') }}', '{{ $featuredWorkshop->instructor }}', '{{ $featuredWorkshop->is_online ? 'ورشة أونلاين' : ($featuredWorkshop->location ?? 'ورشة حضورية') }}', '{{ $featuredWorkshop->registration_deadline ? $featuredWorkshop->registration_deadline->format('d/m/Y') : 'غير محدد' }}')" 
                                    class="bg-white text-amber-600 hover:bg-amber-50 font-bold py-3 px-6 sm:py-4 sm:px-8 rounded-xl transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl">
                                <i class="fab fa-whatsapp ml-2 text-xl"></i>
                                التسجيل في الورشة
                            </button>
                        @endif
                        <a href="{{ route('workshop.show', $featuredWorkshop->slug) }}" 
                           class="border-2 border-white text-white hover:bg-white hover:text-amber-600 font-bold py-3 px-6 sm:py-4 sm:px-8 rounded-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-info-circle ml-2"></i>
                            تفاصيل أكثر
                        </a>
                    </div>
                </div>
                
                <!-- الصورة -->
                <div class="relative h-56 sm:h-64 lg:h-auto">
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
    <section class="container mx-auto px-4 pt-12 md:pt-16 relative z-20">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl overflow-hidden shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <!-- المحتوى النصي -->
                <div class="p-6 sm:p-8 lg:p-12 text-white flex flex-col justify-center">
                    <div class="mb-6">
                        <span class="bg-white/20 text-white text-sm font-semibold px-4 py-2 rounded-full inline-block mb-4">
                            الورشة القادمة
                        </span>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-4 leading-tight">
                            لا توجد ورشات قادمة الآن
                        </h2>
                        <p class="text-base sm:text-lg text-amber-100 mb-8 leading-relaxed">
                            نحن نعمل على إعداد ورشات جديدة ومميزة لك. انتظرونا في الورشة القادمة!
                        </p>
                    </div>
                    
                    <!-- أزرار الإجراءات -->
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                        <a href="{{ route('workshops') }}" 
                           class="bg-white text-amber-600 hover:bg-amber-50 font-bold py-3 px-6 sm:py-4 sm:px-8 rounded-xl transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl">
                            <i class="fas fa-list ml-2 text-xl"></i>
                            تصفح جميع الورشات
                        </a>
                        <a href="{{ route('recipes') }}" 
                           class="border-2 border-white text-white hover:bg-white hover:text-amber-600 font-bold py-3 px-6 sm:py-4 sm:px-8 rounded-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-utensils ml-2"></i>
                            اكتشف الوصفات
                        </a>
                    </div>
                </div>
                
                <!-- الصورة/الأيقونة -->
                <div class="relative h-56 sm:h-64 lg:h-auto overflow-hidden flex items-center justify-center">
                    <div class="w-full h-full flex items-center justify-center">
                        <div class="w-40 h-40 sm:w-48 sm:h-48 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-6xl sm:text-8xl text-white/80"></i>
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
                                <div class="flex items-center text-gray-500 text-sm mb-4">
                                    <i class="fas fa-users mr-2 rtl:ml-2"></i> {{ $workshop->bookings_count }}/{{ $workshop->max_participants }} مشارك
                                </div>
                                
                                <!-- الأزرار المثبتة في الأسفل -->
                                <div class="mt-auto pt-4 border-t border-gray-100">
                                    <div class="flex gap-3 items-center">
                                        @if($isCompleted)
                                        <button class="flex-1 bg-gray-400 text-white font-bold py-3 px-4 rounded-full cursor-not-allowed flex items-center justify-center text-sm">
                                            <i class="fas fa-check-circle ml-2"></i>
                                            الورشة مكتملة
                                        </button>
                                        @elseif($isFull)
                                        <button class="flex-1 bg-gray-400 text-white font-bold py-3 px-4 rounded-full cursor-not-allowed flex items-center justify-center text-sm">
                                            <i class="fas fa-lock ml-2"></i>
                                            الورشة مكتملة
                                        </button>
                                        @elseif($isRegistrationClosed)
                                            <button class="flex-1 bg-yellow-400 text-yellow-800 font-bold py-3 px-4 rounded-full cursor-not-allowed flex items-center justify-center text-sm">
                                                <i class="fas fa-clock ml-2"></i>
                                                انتهى التسجيل
                                            </button>
                                        @else
                                            <button onclick="unifiedBooking({{ $workshop->id }}, '{{ $workshop->title }}', '{{ $workshop->formatted_price }}', '{{ $workshop->start_date->format('d/m/Y') }}', '{{ $workshop->instructor }}', '{{ $workshop->is_online ? 'ورشة أونلاين' : ($workshop->location ?? 'ورشة حضورية') }}', '{{ $workshop->registration_deadline ? $workshop->registration_deadline->format('d/m/Y') : 'غير محدد' }}')" 
                                                    class="flex-1 text-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-full transition-colors flex items-center justify-center text-sm">
                                                <i class="fab fa-whatsapp ml-2"></i>
                                                احجز الآن
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
                    <button class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300">
                        اشترك في التنبيهات
                    </button>
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
@vite(['resources/js/workshops.js'])

<script>
document.addEventListener('DOMContentLoaded', function() {
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

<script>
// الحجز الموحد (للمستخدمين المسجلين)
function unifiedBooking(workshopId, title, price, date, instructor, location, deadline) {
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    
    if (isLoggedIn) {
        // المستخدم مسجل دخول - حفظ الحجز + إرسال واتساب
        showBookingConfirmation(workshopId, title, price, date, instructor, location, deadline);
    } else {
        // المستخدم غير مسجل دخول - توجيه لتسجيل الدخول مع معرف الورشة
        showLoginRequiredModal(workshopId);
    }
}

// دالة تأكيد الحجز الجميلة
function showBookingConfirmation(workshopId, title, price, date, instructor, location, deadline) {
    // إزالة أي modal سابق
    const existingModal = document.getElementById('booking-confirmation-modal');
    if (existingModal) {
        existingModal.remove();
    }

    // إنشاء modal التأكيد
    const modalHTML = `
        <div id="booking-confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300">
                <div class="text-center">
                    <!-- الأيقونة -->
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
                    </div>
                    
                    <!-- العنوان -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">تأكيد الحجز</h3>
                    <p class="text-gray-600 mb-6">هل أنت متأكد من حجز هذه الورشة؟</p>
                    
                    <!-- تفاصيل الورشة -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 text-right">
                        <h4 class="font-semibold text-gray-900 mb-2">${title}</h4>
                        <div class="space-y-1 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>التاريخ:</span>
                                <span class="font-medium">${date}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>المدرب:</span>
                                <span class="font-medium">${instructor}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>المكان:</span>
                                <span class="font-medium">${location}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>السعر:</span>
                                <span class="font-medium text-green-600">${price}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- الأزرار -->
                    <div class="flex gap-3">
                        <button onclick="confirmBooking(${workshopId}, '${title}', '${price}', '${date}', '${instructor}', '${location}', '${deadline}')" 
                                class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition-colors flex items-center justify-center">
                            <i class="fas fa-check ml-2"></i>
                            نعم، احجز الآن
                        </button>
                        <button onclick="closeBookingConfirmation()" 
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-xl transition-colors flex items-center justify-center">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // إضافة modal للصفحة
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// دالة تأكيد الحجز
function confirmBooking(workshopId, title, price, date, instructor, location, deadline) {
    closeBookingConfirmation();
    
    // حفظ الحجز في قاعدة البيانات
    fetch('{{ route("bookings.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            workshop_id: workshopId,
            notes: 'حجز موحد - واتساب + قاعدة بيانات'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // إرسال رسالة الواتساب
            sendWhatsAppMessage(title, price, date, instructor, location, deadline);
            
            // إظهار رسالة نجاح
            showCustomAlert('تم حفظ الحجز في النظام وإرسال رسالة الواتساب! يمكنك الآن الدخول إلى حسابك الشخصي لرؤية الورشات المحجوزة.', 'success');
            
            // إعادة تحميل الصفحة
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showCustomAlert('خطأ في حفظ الحجز: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCustomAlert('حدث خطأ أثناء حفظ الحجز', 'error');
    });
}

// دالة إغلاق modal التأكيد
function closeBookingConfirmation() {
    const modal = document.getElementById('booking-confirmation-modal');
    if (modal) {
        modal.remove();
    }
}

// دالة تأكيد الحجز عبر الواتساب (للمستخدمين غير المسجلين)
function showWhatsAppConfirmation(title, price, date, instructor, location, deadline) {
    // إزالة أي modal سابق
    const existingModal = document.getElementById('whatsapp-confirmation-modal');
    if (existingModal) {
        existingModal.remove();
    }

    // إنشاء modal التأكيد الجميل
    const modalHTML = `
        <div id="whatsapp-confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-100">
                <div class="text-center">
                    <!-- الأيقونة -->
                    <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fab fa-whatsapp text-white text-3xl"></i>
                    </div>
                    
                    <!-- العنوان -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">تأكيد الحجز عبر الواتساب</h3>
                    
                    <!-- الرسالة -->
                    <div class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-6">
                        <p class="text-gray-700 text-lg leading-relaxed">
                            سيتم إرسال طلب الحجز عبر الواتساب
                        </p>
                        <p class="text-gray-600 text-sm mt-2">
                            هل تريد المتابعة؟
                        </p>
                    </div>
                    
                    <!-- تفاصيل الورشة -->
                    <div class="bg-gray-50 rounded-2xl p-4 mb-6 text-right">
                        <h4 class="font-semibold text-gray-900 mb-2">${title}</h4>
                        <div class="space-y-1 text-sm text-gray-600">
                            <p><i class="fas fa-calendar-alt text-blue-500 ml-2"></i> ${date}</p>
                            <p><i class="fas fa-user text-purple-500 ml-2"></i> ${instructor}</p>
                            <p><i class="fas fa-map-marker-alt text-red-500 ml-2"></i> ${location}</p>
                            <p><i class="fas fa-tag text-green-500 ml-2"></i> ${price} ريال</p>
                        </div>
                    </div>
                    
                    <!-- الأزرار -->
                    <div class="flex space-x-4 space-x-reverse">
                        <button onclick="closeWhatsAppConfirmation()" 
                                class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </button>
                        <button onclick="confirmWhatsAppBooking('${title}', '${price}', '${date}', '${instructor}', '${location}', '${deadline}')" 
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-green-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-300 shadow-lg">
                            <i class="fab fa-whatsapp ml-2"></i>
                            متابعة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // إضافة تأثير الظهور
    const modal = document.getElementById('whatsapp-confirmation-modal');
    modal.style.opacity = '0';
    modal.style.transform = 'scale(0.9)';
    
    setTimeout(() => {
        modal.style.opacity = '1';
        modal.style.transform = 'scale(1)';
    }, 10);
}

// دالة إغلاق modal الواتساب
function closeWhatsAppConfirmation() {
    const modal = document.getElementById('whatsapp-confirmation-modal');
    if (modal) {
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.9)';
        setTimeout(() => {
            modal.remove();
        }, 200);
    }
}

// دالة تأكيد الحجز عبر الواتساب
function confirmWhatsAppBooking(title, price, date, instructor, location, deadline) {
    closeWhatsAppConfirmation();
    sendWhatsAppMessage(title, price, date, instructor, location, deadline);
}

// جعل الدوال متاحة عالمياً
window.confirmBooking = confirmBooking;
window.closeBookingConfirmation = closeBookingConfirmation;
window.showBookingConfirmation = showBookingConfirmation;
window.unifiedBooking = unifiedBooking;
window.sendWhatsAppMessage = sendWhatsAppMessage;
window.showCustomAlert = showCustomAlert;
window.closeCustomAlert = closeCustomAlert;
window.showWhatsAppConfirmation = showWhatsAppConfirmation;
window.closeWhatsAppConfirmation = closeWhatsAppConfirmation;
window.confirmWhatsAppBooking = confirmWhatsAppBooking;

// إرسال رسالة الواتساب
function sendWhatsAppMessage(title, price, date, instructor, location, deadline) {
    const userName = "{{ auth()->check() ? auth()->user()->name : 'مستخدم' }}";
    const userPhone = "{{ auth()->check() && auth()->user()->phone ? auth()->user()->phone : 'غير محدد' }}";
    const userEmail = "{{ auth()->check() ? auth()->user()->email : 'غير محدد' }}";
    
    const whatsappMessage = `مرحباً! أريد حجز مقعد في الورشة التالية:

🏆 *${title}*

📅 التاريخ: ${date}
👨‍🏫 المدرب: ${instructor}
📍 المكان: ${location}
💰 السعر: ${price}
⏰ آخر موعد للتسجيل: ${deadline}

📋 *معلوماتي الشخصية:*
👤 الاسم: ${userName}
📞 الهاتف: ${userPhone}
📧 البريد الإلكتروني: ${userEmail}

يرجى تأكيد الحجز وتوضيح طريقة الدفع. شكراً!

💡 *ملاحظة:* تم حفظ الحجز في نظامنا تلقائياً.`;

    const encodedMessage = encodeURIComponent(whatsappMessage);
    const whatsappNumber = "962790553680";
    const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;
    window.open(whatsappUrl, '_blank');
}

// دالة عرض التنبيه المخصص
function showCustomAlert(message, type = 'info') {
    // إزالة أي تنبيهات سابقة
    const existingAlert = document.getElementById('custom-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    // تحديد الألوان والأيقونات حسب النوع
    let bgColor, textColor, icon, borderColor;
    switch(type) {
        case 'success':
            bgColor = 'bg-green-50';
            textColor = 'text-green-800';
            icon = 'fas fa-check-circle';
            borderColor = 'border-green-200';
            break;
        case 'error':
            bgColor = 'bg-red-50';
            textColor = 'text-red-800';
            icon = 'fas fa-exclamation-circle';
            borderColor = 'border-red-200';
            break;
        case 'warning':
            bgColor = 'bg-yellow-50';
            textColor = 'text-yellow-800';
            icon = 'fas fa-exclamation-triangle';
            borderColor = 'border-yellow-200';
            break;
        default:
            bgColor = 'bg-blue-50';
            textColor = 'text-blue-800';
            icon = 'fas fa-info-circle';
            borderColor = 'border-blue-200';
    }

    // إنشاء التنبيه
    const alertHTML = `
        <div id="custom-alert" class="fixed top-4 right-4 z-50 max-w-sm w-full mx-4 transform transition-all duration-300 ease-in-out">
            <div class="${bgColor} ${borderColor} border-l-4 rounded-lg shadow-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="${icon} ${textColor} text-xl"></i>
                    </div>
                    <div class="mr-3 flex-1">
                        <p class="${textColor} text-sm font-medium leading-5">
                            ${message}
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <button onclick="closeCustomAlert()" class="${textColor} hover:opacity-75 focus:outline-none">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // إضافة التنبيه للصفحة
    document.body.insertAdjacentHTML('beforeend', alertHTML);

    // إظهار التنبيه مع تأثير
    setTimeout(() => {
        const alert = document.getElementById('custom-alert');
        if (alert) {
            alert.style.transform = 'translateX(0)';
            alert.style.opacity = '1';
        }
    }, 100);

    // إزالة التنبيه تلقائياً بعد 5 ثوان
    setTimeout(() => {
        closeCustomAlert();
    }, 5000);
}

// دالة إغلاق التنبيه
function closeCustomAlert() {
    const alert = document.getElementById('custom-alert');
    if (alert) {
        alert.style.transform = 'translateX(100%)';
        alert.style.opacity = '0';
        setTimeout(() => {
            alert.remove();
        }, 300);
    }
}

// دالة عرض modal تسجيل الدخول المطلوب
function showLoginRequiredModal(workshopId = null) {
    // إزالة أي modal سابق
    const existingModal = document.getElementById('login-required-modal');
    if (existingModal) {
        existingModal.remove();
    }

    // تخزين معرف الورشة للعودة إليها بعد تسجيل الدخول
    if (workshopId) {
        localStorage.setItem('pending_workshop_booking', workshopId);
    }

    // إنشاء modal تسجيل الدخول المطلوب
    const modalHTML = `
        <div id="login-required-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" onclick="closeLoginRequiredModal(event)">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-100 relative" onclick="event.stopPropagation()">
                <!-- زر الإغلاق في الزاوية العلوية -->
                <button onclick="closeLoginRequiredModal()" class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-full hover:bg-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </button>
                
                <div class="text-center">
                    <!-- الأيقونة -->
                    <div class="w-20 h-20 bg-gradient-to-br from-amber-400 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-user-lock text-white text-3xl"></i>
                    </div>
                    
                    <!-- العنوان -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">تسجيل الدخول مطلوب</h3>
                    
                    <!-- الرسالة -->
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-6">
                        <p class="text-gray-700 text-lg leading-relaxed">
                            يجب تسجيل الدخول أولاً لحجز الورشة
                        </p>
                        <p class="text-gray-600 text-sm mt-2">
                            سجل دخولك أو أنشئ حساب جديد للمتابعة
                        </p>
                    </div>
                    
                    <!-- الأزرار -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button onclick="redirectToLoginWithWorkshop()" class="flex-1 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt ml-2"></i>
                            تسجيل الدخول
                        </button>
                        <button onclick="redirectToRegisterWithWorkshop()" class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fas fa-user-plus ml-2"></i>
                            إنشاء حساب
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // إضافة modal للصفحة
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // إضافة مستمع حدث للضغط على مفتاح Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeLoginRequiredModal();
        }
    });
}

// دالة إغلاق modal تسجيل الدخول المطلوب
function closeLoginRequiredModal(event) {
    // إذا كان الحدث من النقر على الخلفية، أغلق الـ modal
    if (event && event.target.id === 'login-required-modal') {
        const modal = document.getElementById('login-required-modal');
        if (modal) {
            modal.remove();
        }
        return;
    }
    
    // إغلاق الـ modal في جميع الحالات الأخرى
    const modal = document.getElementById('login-required-modal');
    if (modal) {
        modal.remove();
    }
}

// دالة التوجيه لتسجيل الدخول مع معرف الورشة
function redirectToLoginWithWorkshop() {
    const workshopId = localStorage.getItem('pending_workshop_booking');
    if (workshopId) {
        window.location.href = `{{ route('login') }}?pending_workshop_booking=${workshopId}`;
    } else {
        window.location.href = '{{ route('login') }}';
    }
}

// دالة التوجيه للتسجيل مع معرف الورشة
function redirectToRegisterWithWorkshop() {
    const workshopId = localStorage.getItem('pending_workshop_booking');
    if (workshopId) {
        window.location.href = `{{ route('register') }}?pending_workshop_booking=${workshopId}`;
    } else {
        window.location.href = '{{ route('register') }}';
    }
}

// جعل الدوال متاحة عالمياً
window.closeLoginRequiredModal = closeLoginRequiredModal;
window.showLoginRequiredModal = showLoginRequiredModal;
window.redirectToLoginWithWorkshop = redirectToLoginWithWorkshop;
window.redirectToRegisterWithWorkshop = redirectToRegisterWithWorkshop;
</script>
@endpush

