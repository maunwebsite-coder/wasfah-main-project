@extends('layouts.app')

@section('title', 'موقع وصفه - دليلك لعالم الحلويات')

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap');
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #fcfcfc;
        }

        /* Hide scrollbar for a cleaner look */
        .swiper-wrapper {
            scrollbar-width: none; /* For Firefox */
        }
        .swiper-wrapper::-webkit-scrollbar {
            display: none; /* For Chrome, Safari, and Opera */
        }

        /* Workshop Cards Styling */
        .workshop-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .workshop-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Workshop Card Image Improvements */
        .workshop-card img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s ease;
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
            }
            
            /* Featured workshop image mobile fix */
            .featured-workshop-image {
                height: 250px;
                object-fit: cover;
                object-position: center;
                min-height: 200px;
                max-height: 300px;
            }
        }
        
        @media (max-width: 480px) {
            .workshop-card img {
                height: 200px;
                object-fit: cover;
                object-position: center;
                min-height: 180px;
                max-height: 220px;
            }
            
            /* Featured workshop image mobile fix */
            .featured-workshop-image {
                height: 220px;
                object-fit: cover;
                object-position: center;
                min-height: 180px;
                max-height: 250px;
            }
        }

        /* Flip Card Styles */
        .card-container {
            perspective: 1000px;
            width: 280px;
            height: 400px;
            cursor: pointer;
            margin: 0;
        }

        .card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }

        .card-container.is-flipped .card-inner {
            transform: rotateY(180deg);
        }

        .card-front,
        .card-back {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            display: flex; /* Ensure content is centered */
            flex-direction: column;
        }

        .card-front { background: #fff; }
        .card-back { background: #fff; transform: rotateY(180deg); }

        /* Swiper Settings */
        .swiper, .swiper-container {
            padding: 0 !important;
            margin: 0 !important;
        }

        .swiper-slide {
            flex: 0 0 auto;
            width: 280px;
            height: 400px;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
        }

        /* Recipe Cards Enhancements - removed hover effects */

        /* Additional card styles for home page */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

    </style>
@endpush

@section('content')
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />

    <!-- قسم المحتوى الرئيسي -->
    <main class="container mx-auto px-4 py-8 mt-6">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- القسم الأيمن (الرئيسي) -->
            <div class="flex-1 bg-white rounded-2xl shadow-lg p-6">
                <img src="{{ asset('image/Brownies.png') }}" alt="صورة تيراميسو إيطالي فاخر" class="w-full h-auto rounded-xl mb-6">
                <span class="bg-gradient-to-r from-amber-100 to-orange-100 text-amber-700 text-sm font-semibold px-4 py-2 rounded-full mb-3 inline-block shadow-sm">عن الموقع</span>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-4 leading-tight">منصّة وصفة – عالم الحلويات الفاخرة والراقية</h1>
                <p class="text-gray-600 leading-relaxed mb-6">اكتشف أسرار صنع أرقى الحلويات العالمية من التيراميسو الإيطالي والشوكولاتة البلجيكية إلى البراونيز الفاخر والبرازنز تيرامايز. وصفات حصرية من أفضل الشيفات العالميين مع تقنيات احترافية ومواد أولية فاخرة.</p>  
            </div>
            
            <!-- القسم الأيسر (الجانبي) -->
            <aside class="w-full md:w-1/3 bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-3">أحدث الوصفات</h2>
                <ul class="space-y-4">
                    @forelse($latestRecipes as $recipe)
                        <x-latest-recipe-item :recipe="$recipe" />
                    @empty
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-utensils text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500">لا توجد وصفات متاحة حالياً</p>
                        </div>
                    @endforelse
                </ul>
            </aside>
        </div>
    </main>

    <!-- قسم الورشة المميزة (الورشة القادمة) -->
    @if($featuredWorkshop)
    @php 
        $featuredIsFull = $featuredWorkshop->bookings_count >= $featuredWorkshop->max_participants; 
        $featuredIsRegistrationClosed = !$featuredWorkshop->is_registration_open;
        $featuredIsCompleted = $featuredWorkshop->is_completed;
    @endphp
    <section class="container mx-auto px-4 py-16">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl overflow-hidden shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <!-- المحتوى النصي -->
                <div class="p-8 lg:p-12 text-white flex flex-col justify-center">
                    <div class="mb-6">
                        <span class="bg-white/20 text-white text-sm font-semibold px-4 py-2 rounded-full inline-block mb-4">
                            الورشة القادمة
                        </span>
                        <h2 class="text-3xl lg:text-4xl font-bold mb-4 leading-tight">
                            {{ $featuredWorkshop->title }}
                        </h2>
                        <p class="text-lg text-amber-100 mb-6 leading-relaxed">
                            {{ $featuredWorkshop->featured_description ?: $featuredWorkshop->description }}
                        </p>
                    </div>
                    
                    <!-- تفاصيل الورشة -->
                    <div class="space-y-3 mb-8">
                        <div class="flex items-center text-amber-100">
                            <i class="fas fa-calendar-alt w-5 text-center ml-3"></i>
                            <span class="font-medium">{{ $featuredWorkshop->start_date->format('d M Y') }}</span>
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
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if($featuredIsCompleted)
                            <button class="bg-gray-400 text-gray-600 font-bold py-4 px-8 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg">
                                <i class="fas fa-check-circle ml-2 text-xl"></i>
                                الورشة مكتملة
                            </button>
                        @elseif($featuredIsFull)
                            <button class="bg-gray-400 text-gray-600 font-bold py-4 px-8 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg">
                                <i class="fas fa-lock ml-2 text-xl"></i>
                                الورشة مكتملة
                            </button>
                        @elseif($featuredIsRegistrationClosed)
                            <button class="bg-yellow-400 text-yellow-800 font-bold py-4 px-8 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg">
                                <i class="fas fa-clock ml-2 text-xl"></i>
                                انتهى التسجيل
                            </button>
                        @else
                            <button onclick="unifiedBooking({{ $featuredWorkshop->id }}, '{{ $featuredWorkshop->title }}', '{{ $featuredWorkshop->formatted_price }}', '{{ $featuredWorkshop->start_date ? $featuredWorkshop->start_date->format('d M, Y') : 'غير محدد' }}', '{{ $featuredWorkshop->instructor }}', '{{ $featuredWorkshop->is_online ? 'ورشة أونلاين' : ($featuredWorkshop->location ?? 'ورشة حضورية') }}', '{{ $featuredWorkshop->registration_deadline ? $featuredWorkshop->registration_deadline->format('d M, Y') : 'غير محدد' }}')" 
                                    class="bg-white text-green-600 hover:bg-green-50 font-bold py-4 px-8 rounded-xl transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl">
                                <i class="fab fa-whatsapp ml-2 text-xl"></i>
                                احجز مقعدك الآن
                            </button>
                        @endif
                        <a href="{{ route('workshop.show', $featuredWorkshop->slug) }}" 
                           class="border-2 border-white text-white hover:bg-white hover:text-amber-600 font-bold py-4 px-8 rounded-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-info-circle ml-2"></i>
                            تفاصيل أكثر
                        </a>
                    </div>
                </div>
                
                <!-- الصورة -->
                <div class="relative h-64 lg:h-auto overflow-hidden">
                    <img src="{{ $featuredWorkshop->image ? asset('storage/' . $featuredWorkshop->image) : 'https://placehold.co/800x600/f87171/FFFFFF?text=ورشة+فاخرة' }}" 
                         alt="{{ $featuredWorkshop->title }}" 
                         class="w-full h-full object-cover featured-workshop-image"
                         loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-l from-transparent to-amber-500/20"></div>
                </div>
            </div>
        </div>
    </section>
    @else
    <!-- رسالة عدم وجود ورشات قادمة -->
    <section class="container mx-auto px-4 py-16">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl overflow-hidden shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <!-- المحتوى النصي -->
                <div class="p-8 lg:p-12 text-white flex flex-col justify-center">
                    <div class="mb-6">
                        <span class="bg-white/20 text-white text-sm font-semibold px-4 py-2 rounded-full inline-block mb-4">
                            الورشة القادمة
                        </span>
                        <h2 class="text-3xl lg:text-4xl font-bold mb-4 leading-tight">
                            لا توجد ورشات قادمة الآن
                        </h2>
                        <p class="text-lg text-amber-100 mb-8 leading-relaxed">
                            نحن نعمل على إعداد ورشات جديدة ومميزة لك. انتظرونا في الورشة القادمة!
                        </p>
                    </div>
                    
                    <!-- أزرار الإجراءات -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('workshops') }}" 
                           class="bg-white text-amber-600 hover:bg-amber-50 font-bold py-4 px-8 rounded-xl transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl">
                            <i class="fas fa-list ml-2 text-xl"></i>
                            تصفح جميع الورشات
                        </a>
                        <a href="{{ route('recipes') }}" 
                           class="border-2 border-white text-white hover:bg-white hover:text-amber-600 font-bold py-4 px-8 rounded-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-utensils ml-2"></i>
                            اكتشف الوصفات
                        </a>
                    </div>
                </div>
                
                <!-- الصورة/الأيقونة -->
                <div class="relative h-64 lg:h-auto overflow-hidden flex items-center justify-center">
                    <div class="w-full h-full flex items-center justify-center">
                        <div class="relative w-56 h-56 bg-white/10 rounded-full flex items-center justify-center">
                            <div class="absolute inset-4 bg-white/20 rounded-full"></div>
                            <div class="absolute inset-12 bg-white/10 rounded-full"></div>
                            <i class="fas fa-calendar-alt text-white text-5xl"></i>
                        </div>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-l from-transparent to-amber-500/20"></div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- قسم الوصفات المميزة -->
    <section class="container mx-auto px-4 py-8">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">ابدأ بحفظ هذه الوصفات</h2>
            <p class="text-gray-600 mt-2">اكتشف وصفات حلويات فاخرة من أفضل الشيفات العالميين</p>
        </div>
        
        @if($featuredRecipes->count() > 0)
            <!-- Swiper Container -->
            <div class="swiper">
                <div class="swiper-wrapper py-4">
                    @foreach($featuredRecipes as $recipe)
                        <div class="swiper-slide">
                            <x-featured-recipe-card :recipe="$recipe" />
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-20 bg-white rounded-2xl shadow-lg">
                <div class="w-24 h-24 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-utensils text-4xl text-amber-500"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-700 mb-3">لا توجد وصفات متاحة حالياً</h3>
                <p class="text-gray-500 text-lg max-w-md mx-auto mb-6">نحن نعمل على إضافة وصفات جديدة ومميزة. تحقق مرة أخرى قريباً!</p>
                <a href="{{ route('recipes') }}" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300">
                    تصفح جميع الوصفات
                </a>
            </div>
        @endif
    </section>

    <!-- قسم لماذا تختار موقع وصفة؟ -->
    <section class="py-16 bg-gradient-to-br from-amber-50 to-orange-50">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">لماذا تختار منصّة وصفة ؟</h2>
            <p class="text-gray-600 mb-12 max-w-3xl mx-auto">نحن نقدم لك تجربة فريدة في عالم الحلويات مع وصفات حصرية وتقنيات احترافية</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 rounded-full h-20 w-20 flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-crown text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">وصفات متنوعة</h3>
                    <p class="text-gray-600">اكتشف أسرار أرقى الحلويات العالمية من التيراميسو الإيطالي والشوكولاتة البلجيكية إلى البراونيز الفاخر</p>
                </div>
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 rounded-full h-20 w-20 flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-gem text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">تقنيات احترافية</h3>
                    <p class="text-gray-600">تعلم من أفضل الشيفات العالميين تقنيات متقدمة لصنع الحلويات الراقية بجودة مطاعم فاخرة</p>
                </div>
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 rounded-full h-20 w-20 flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-award text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">مواد أولية فاخرة</h3>
                    <p class="text-gray-600">نرشدك لأفضل المكونات العالمية من الشوكولاتة البلجيكية إلى القهوة الإيطالية الأصيلة</p>
                </div>
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 rounded-full h-20 w-20 flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-magic text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">ورشات متخصصة</h3>
                    <p class="text-gray-600">انضم لورشات عمل حصرية تتعلم فيها صنع الحلويات الراقية من خبراء عالميين</p>
                </div>
            </div>
        </div>
    </section>

    
    <!-- قسم الورشات المحسن -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-white">
        <div class="container mx-auto px-4">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">ورشات الحلويات الفاخرة</h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto mb-6">انضم إلى ورشاتنا الاحترافية الحصرية وتعلم أسرار صنع أرقى الحلويات العالمية</p>
                <a href="{{ route('workshops') }}" class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-semibold text-lg transition-colors">
                    عرض جميع الورشات
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
            </div>
            
            @if($workshops->count() > 0)
                <!-- Workshops Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($workshops as $workshop)
                        @php 
                            $isFull = $workshop->bookings_count >= $workshop->max_participants; 
                            $isRegistrationClosed = !$workshop->is_registration_open;
                            
                            // Debug information (remove in production)
                            // Uncomment the line below to debug registration status
                            // dd([
                            //     'workshop_id' => $workshop->id,
                            //     'title' => $workshop->title,
                            //     'registration_deadline' => $workshop->registration_deadline,
                            //     'is_upcoming' => $workshop->is_upcoming,
                            //     'is_registration_open' => $workshop->is_registration_open,
                            //     'isRegistrationClosed' => $isRegistrationClosed,
                            //     'now' => now(),
                            // ]);
                            
                            // Temporary debug display (remove in production)
                            // Uncomment the lines below to see debug information
                            // @if($isRegistrationClosed)
                            //     <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-2">
                            //         DEBUG: Registration is closed for {{ $workshop->title }}
                            //     </div>
                            // @endif
                        @endphp
                        <div class="workshop-card bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full">
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
                                    <i class="fas fa-calendar-alt mr-2 rtl:ml-2"></i> {{ $workshop->start_date->format('d M, Y') }}
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
                                        @if($isFull)
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
                                            <button onclick="unifiedBooking({{ $workshop->id }}, '{{ $workshop->title }}', '{{ $workshop->formatted_price }}', '{{ $workshop->start_date->format('d M, Y') }}', '{{ $workshop->instructor }}', '{{ $workshop->is_online ? 'ورشة أونلاين' : ($workshop->location ?? 'ورشة حضورية') }}', '{{ $workshop->registration_deadline->format('d M, Y') }}')" 
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


@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper for recipe cards
    if (document.querySelector('.swiper')) {
        new Swiper('.swiper', {
            slidesPerView: 'auto',
            spaceBetween: 16,
            grabCursor: true,
            navigation: { 
                nextEl: '#nextBtn', 
                prevEl: '#prevBtn' 
            },
        });
    }

    // Cards are now static - no click functionality needed
    // Only save buttons and links are clickable

    // Initialize save buttons using the global function from save-recipe.js
    if (typeof window.SaveRecipe !== 'undefined' && window.SaveRecipe.initializeSaveButtons) {
        window.SaveRecipe.initializeSaveButtons();
    } else {
        // Fallback if save-recipe.js is not loaded
        console.warn('save-recipe.js not loaded, using fallback initialization');
        initializeSaveButtonsFallback();
    }
});

function initializeSaveButtonsFallback() {
    const saveButtons = document.querySelectorAll('.save-recipe-btn');
    
    saveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent card flip
            e.stopImmediatePropagation(); // Prevent other event handlers
            
            const recipeId = this.dataset.recipeId;
            const recipeName = this.dataset.recipeName;
            const isCurrentlySaved = this.dataset.saved === 'true';
            
            // Toggle visual state immediately
            if (isCurrentlySaved) {
                // Change from saved (green) to not saved (orange)
                this.classList.remove('bg-green-500', 'hover:bg-green-600');
                this.classList.add('bg-orange-500', 'hover:bg-orange-600');
                this.querySelector('span').textContent = 'حفظ';
                this.dataset.saved = 'false';
            } else {
                // Change from not saved (orange) to saved (green)
                this.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                this.classList.add('bg-green-500', 'hover:bg-green-600');
                this.querySelector('span').textContent = 'محفوظة';
                this.dataset.saved = 'true';
            }
            
            // Make API call
            fetch('/api/interactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include',
                body: JSON.stringify({
                    recipe_id: recipeId,
                    is_saved: !isCurrentlySaved
                })
            })
            .then(response => {
                if (response.status === 401) {
                    // User not logged in, redirect to login
                    window.location.href = '/login';
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show notification
                    showNotification(
                        isCurrentlySaved ? 'تم إلغاء حفظ الوصفة' : 'تم حفظ الوصفة بنجاح',
                        'success'
                    );
                    
                    // تحديث عداد الحفظ في صفحة الوصفة فورياً (فقط للمستخدمين المسجلين)
                    if (typeof window.SaveRecipe !== 'undefined' && window.SaveRecipe.updateRecipePageSaveCount) {
                        window.SaveRecipe.updateRecipePageSaveCount(!isCurrentlySaved);
                    }
                } else {
                    // Revert visual state on error
                    if (isCurrentlySaved) {
                        // Revert back to saved (green)
                        this.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                        this.classList.add('bg-green-500', 'hover:bg-green-600');
                        this.querySelector('span').textContent = 'محفوظة';
                        this.dataset.saved = 'true';
                    } else {
                        // Revert back to not saved (orange)
                        this.classList.remove('bg-green-500', 'hover:bg-green-600');
                        this.classList.add('bg-orange-500', 'hover:bg-orange-600');
                        this.querySelector('span').textContent = 'حفظ';
                        this.dataset.saved = 'false';
                    }
                    
                    showNotification('حدث خطأ أثناء حفظ الوصفة', 'error');
                }
            })
            .catch(error => {
                // Revert visual state on error
                if (isCurrentlySaved) {
                    // Revert back to saved (green)
                    this.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                    this.classList.add('bg-green-500', 'hover:bg-green-600');
                    this.querySelector('span').textContent = 'محفوظة';
                    this.dataset.saved = 'true';
                } else {
                    // Revert back to not saved (orange)
                    this.classList.remove('bg-green-500', 'hover:bg-green-600');
                    this.classList.add('bg-orange-500', 'hover:bg-orange-600');
                    this.querySelector('span').textContent = 'حفظ';
                    this.dataset.saved = 'false';
                }
                
                showNotification('حدث خطأ أثناء حفظ الوصفة', 'error');
            });
        });
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// الحجز الموحد (يتطلب تسجيل الدخول)
function unifiedBooking(workshopId, title, price, date, instructor, location, deadline) {
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    
    if (isLoggedIn) {
        // المستخدم مسجل دخول - حفظ الحجز + إرسال واتساب
        showBookingConfirmation(workshopId, title, price, date, instructor, location, deadline);
    } else {
        // المستخدم غير مسجل دخول - إظهار modal تسجيل الدخول
        showLoginRequiredModal(title, price, date, instructor, location, deadline);
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
            showCustomAlert('تم حفظ الحجز في النظام وإرسال رسالة الواتساب!', 'success');
            
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

// دالة إظهار modal تسجيل الدخول المطلوب
function showLoginRequiredModal(title, price, date, instructor, location, deadline) {
    // إزالة أي modal سابق
    const existingModal = document.getElementById('login-required-modal');
    if (existingModal) {
        existingModal.remove();
    }

    // إنشاء modal تسجيل الدخول
    const modalHTML = `
        <div id="login-required-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" onclick="closeLoginRequiredModal(event)">
            <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 relative" onclick="event.stopPropagation()">
                <!-- زر الإغلاق في الزاوية العلوية -->
                <button onclick="closeLoginRequiredModal()" class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-full hover:bg-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </button>
                
                <div class="text-center">
                    <!-- الأيقونة -->
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-lock text-amber-600 text-2xl"></i>
                    </div>
                    
                    <!-- العنوان -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">تسجيل الدخول مطلوب</h3>
                    <p class="text-gray-600 mb-6">يجب تسجيل الدخول أولاً لحجز الورشة</p>
                    
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
                    
                    <!-- رسالة إضافية -->
                    <p class="text-sm text-gray-500 mb-6">سجل دخولك أو أنشئ حساب جديد للمتابعة</p>
                    
                    <!-- الأزرار -->
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('login') }}" 
                           class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-xl transition-colors flex items-center justify-center">
                            <i class="fas fa-sign-in-alt ml-2"></i>
                            تسجيل الدخول
                        </a>
                        <a href="{{ route('register') }}" 
                           class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition-colors flex items-center justify-center">
                            <i class="fas fa-user-plus ml-2"></i>
                            إنشاء حساب
                        </a>
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

// دالة إغلاق modal تسجيل الدخول
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

// جعل الدوال متاحة عالمياً
window.confirmBooking = confirmBooking;
window.closeBookingConfirmation = closeBookingConfirmation;
window.showBookingConfirmation = showBookingConfirmation;
window.showLoginRequiredModal = showLoginRequiredModal;
window.closeLoginRequiredModal = closeLoginRequiredModal;
window.unifiedBooking = unifiedBooking;
window.sendWhatsAppMessage = sendWhatsAppMessage;
window.showCustomAlert = showCustomAlert;
window.closeCustomAlert = closeCustomAlert;

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
</script>
@endpush
