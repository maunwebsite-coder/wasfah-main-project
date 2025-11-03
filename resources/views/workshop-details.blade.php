@extends('layouts.app')

@section('title', $workshop->title . ' - موقع وصفة')

@push('styles')
<style>
    .workshop-hero {
        background: linear-gradient(to right, #f59e0b, #ea580c);
        color: white;
        border-radius: 1.5rem;
        margin: 2rem auto 0 auto;
        max-width: 1200px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    /* Add margin to prevent edge sticking */
    .workshop-hero-container {
        padding: 0 1rem;
    }
    
    /* Mobile Responsive Improvements */
    @media (max-width: 768px) {
        .workshop-hero-container {
            padding: 0 1.5rem;
        }
        
        .workshop-hero {
            margin: 1rem auto 0 auto;
            border-radius: 1rem;
        }
        
        .workshop-hero-content {
            padding: 1.5rem;
        }
        
        .workshop-hero-title {
            font-size: 1.75rem;
            line-height: 1.3;
        }
        
        .workshop-hero-description {
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .workshop-hero-details {
            font-size: 0.9rem;
        }
        
        .workshop-image {
            height: 250px;
            object-fit: cover;
            object-position: center;
        }
        
        .content-card {
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .sidebar-card {
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .booking-section {
            position: static !important;
            top: auto !important;
        }
        
        .info-item {
            padding: 0.5rem 0;
        }
        
        .info-icon {
            width: 2rem;
            height: 2rem;
            font-size: 1rem;
        }
        
        /* Additional mobile improvements */
        .workshop-details-section {
            padding: 2rem 0;
        }
        
        .workshop-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .workshop-main-content {
            order: 1;
        }
        
        .workshop-sidebar {
            order: 2;
        }
        
        .workshop-content-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .workshop-content-text {
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .booking-button {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
        }
        
        .instructor-card {
            margin-bottom: 1rem;
        }
        
        .instructor-avatar {
            width: 4rem;
            height: 4rem;
        }
        
        .instructor-name {
            font-size: 1.1rem;
        }
        
        .instructor-bio {
            font-size: 0.9rem;
        }
        
        .related-workshops-section {
            padding: 2rem 0;
        }
        
        .related-workshops-title {
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
        }
        
        .related-workshops-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .related-workshop-card {
            margin-bottom: 1rem;
        }
        
        .related-workshop-image {
            height: 180px;
        }
        
        .related-workshop-content {
            padding: 1rem;
        }
        
        .related-workshop-title {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .related-workshop-instructor {
            font-size: 0.9rem;
        }
        
        .related-workshop-price {
            font-size: 1.1rem;
        }
        
        .related-workshop-button {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
    }
    
    @media (max-width: 480px) {
        .workshop-hero-container {
            padding: 0 1rem;
        }
        
        .workshop-hero {
            margin: 0.75rem auto 0 auto;
            border-radius: 0.75rem;
        }
        
        .workshop-hero-content {
            padding: 1rem;
        }
        
        .workshop-hero-title {
            font-size: 1.5rem;
        }
        
        .workshop-hero-description {
            font-size: 0.9rem;
        }
        
        .workshop-hero-details {
            font-size: 0.85rem;
        }
        
        .workshop-image {
            height: 220px;
            object-fit: cover;
            object-position: center;
        }
        
        .content-card {
            padding: 1rem;
        }
        
        .sidebar-card {
            padding: 1rem;
        }
        
        .workshop-details-section {
            padding: 1.5rem 0;
        }
        
        .workshop-content-title {
            font-size: 1.25rem;
        }
        
        .workshop-content-text {
            font-size: 0.9rem;
        }
        
        .booking-button {
            padding: 0.875rem;
            font-size: 0.95rem;
        }
        
        .instructor-avatar {
            width: 3.5rem;
            height: 3.5rem;
        }
        
        .instructor-name {
            font-size: 1rem;
        }
        
        .instructor-bio {
            font-size: 0.85rem;
        }
        
        .related-workshops-title {
            font-size: 1.5rem;
        }
        
        .related-workshop-image {
            height: 160px;
        }
        
        .related-workshop-content {
            padding: 0.875rem;
        }
        
        .related-workshop-title {
            font-size: 0.95rem;
        }
        
        .related-workshop-instructor {
            font-size: 0.85rem;
        }
        
        .related-workshop-price {
            font-size: 1rem;
        }
        
        .related-workshop-button {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
    }
    
    .workshop-image {
        transition: all 0.3s ease;
        border-radius: 0;
        box-shadow: none;
        border: none;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
    
    .workshop-image:hover {
        transform: none;
        box-shadow: none;
        border-color: transparent;
    }
    
    /* Mobile Image Improvements */
    @media (max-width: 768px) {
        .workshop-image {
            min-height: 250px;
            max-height: 300px;
            object-fit: cover;
            object-position: center;
            width: 100%;
        }
        
        .workshop-hero .relative {
            height: 250px !important;
        }
    }
    
    @media (max-width: 480px) {
        .workshop-image {
            min-height: 220px;
            max-height: 280px;
            object-fit: cover;
            object-position: center;
            width: 100%;
        }
        
        .workshop-hero .relative {
            height: 220px !important;
        }
    }
    
    /* Ensure images maintain aspect ratio */
    .workshop-image {
        aspect-ratio: 16/9;
    }
    
    @media (max-width: 768px) {
        .workshop-image {
            aspect-ratio: 4/3;
        }
    }
    
    .instructor-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .instructor-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border-color: #f97316;
    }
    
    .related-workshop {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .related-workshop:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        border-color: #f97316;
    }
    
    .booking-btn {
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .booking-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .booking-btn:hover::before {
        left: 100%;
    }
    
    .booking-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(37, 211, 102, 0.4);
        background: linear-gradient(135deg, #128C7E 0%, #075E54 100%);
    }
    
    .booking-btn:active {
        transform: translateY(0);
        box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
    }
    
    .prose ul > li::before {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    }
    
    
    .content-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }
    
    .content-card:hover {
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    
    .sidebar-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }
    
    .sidebar-card:hover {
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }
    
    .info-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }
    
    .info-item:hover {
        background: #f8fafc;
        padding-right: 1rem;
        border-radius: 0.5rem;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 1rem;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        font-size: 1.1rem;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
    }
    
</style>
@endpush

@section('content')
<div class="min-h-screen" style="background-color: #f3f4f6;">
    <!-- Workshop Hero Section -->
    <section class="workshop-hero-container">
        <div class="workshop-hero">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
            <!-- Workshop Info -->
            <div class="p-8 lg:p-12 text-white flex flex-col justify-center workshop-hero-content">
                <div class="mb-6">
                    <span class="bg-white/20 text-white text-sm font-semibold px-4 py-2 rounded-full inline-block mb-4">
                        @if($workshop->is_featured)
                            <i class="fas fa-star text-yellow-300 mr-2"></i> ورشة مميزة
                        @else
                            ورشة عمل
                        @endif
                    </span>
                    <h1 class="text-3xl lg:text-4xl font-bold mb-4 leading-tight workshop-hero-title">
                        {{ $workshop->title }}
                    </h1>
                    <p class="text-lg text-amber-100 mb-6 leading-relaxed workshop-hero-description">
                        {{ $workshop->description }}
                    </p>
                </div>
                
                <!-- Workshop Details -->
                <div class="space-y-3 mb-8 workshop-hero-details">
                    <div class="flex items-center text-amber-100">
                        <i class="fas fa-calendar-alt w-5 text-center ml-3"></i>
                        <span class="font-medium">{{ $workshop->start_date ? $workshop->start_date->format('d/m/Y') : 'غير محدد' }}</span>
                    </div>
                    <div class="flex items-center text-amber-100">
                        <i class="fas fa-play-circle w-5 text-center ml-3"></i>
                        <span class="font-medium">البداية: {{ $workshop->start_date ? $workshop->start_date->format('m/d/Y g:i A') : 'غير محدد' }}</span>
                    </div>
                    <div class="flex items-center text-amber-100">
                        <i class="fas fa-stop-circle w-5 text-center ml-3"></i>
                        <span class="font-medium">النهاية: {{ $workshop->end_date ? $workshop->end_date->format('m/d/Y g:i A') : 'غير محدد' }}</span>
                    </div>
                    <div class="flex items-center text-amber-100">
                        <i class="fas {{ $workshop->is_online ? 'fa-video' : 'fa-map-marker-alt' }} w-5 text-center ml-3"></i>
                        <span class="font-medium">{{ $workshop->is_online ? 'ورشة أونلاين' : ($workshop->location ?? 'ورشة حضورية') }}</span>
                    </div>
                    <div class="flex items-center text-amber-100">
                        <i class="fas fa-user w-5 text-center ml-3"></i>
                        <span class="font-medium">مع {{ $workshop->instructor }}</span>
                    </div>
                    <div class="flex items-center text-amber-100">
                        <i class="fas fa-clock w-5 text-center ml-3"></i>
                        <span class="font-medium">{{ $workshop->formatted_duration }}</span>
                    </div>
                    <div class="flex items-center text-amber-100">
                        <i class="fas fa-users w-5 text-center ml-3"></i>
                        <span class="font-medium">{{ $workshop->bookings_count }}/{{ $workshop->max_participants }} مشارك</span>
                    </div>
                </div>
                
                <!-- Price and Rating -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 pt-6 border-t border-amber-200/30">
                    <div class="text-3xl font-bold text-white">
                        {{ $workshop->formatted_price }}
                    </div>
                    <div class="flex items-center text-white">
                        <div class="flex items-center mr-3">
                            <i class="fas fa-star text-yellow-300 mr-1"></i>
                            <span class="font-bold text-xl text-white">{{ (float)$workshop->rating }}</span>
                        </div>
                        <span class="text-amber-100 font-medium">({{ (int)$workshop->reviews_count }} تقييم)</span>
                    </div>
                </div>
            </div>
            
            <!-- Workshop Image -->
            <div class="relative h-64 lg:h-auto overflow-hidden">
                <img src="{{ $workshop->image ? asset('storage/' . $workshop->image) : 'https://placehold.co/800x600/f87171/FFFFFF?text=ورشة+فاخرة' }}" 
                     alt="{{ $workshop->title }}" 
                     class="workshop-image w-full h-full object-cover"
                     onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';"
                     loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-l from-transparent to-amber-500/20"></div>
                @if($workshop->is_fully_booked)
                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                        <span class="text-white text-2xl font-bold bg-red-500 px-6 py-3 rounded-full">
                            اكتمل العدد
                        </span>
                    </div>
                @endif
            </div>
        </div>
        </div>
    </section>

    <!-- Workshop Details -->
    <section class="py-16 workshop-details-section">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 workshop-grid">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-8 workshop-main-content">
                        <!-- About Workshop -->
                        <div class="content-card p-8 sm:p-10">
                            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-right flex items-center workshop-content-title">
                                <div class="info-icon ml-4">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                حول الورشة
                            </h2>
                            <div class="prose prose-lg max-w-none text-gray-700 text-right leading-relaxed workshop-content-text">
                                {!! $workshop->content !!}
                            </div>
                        </div>

                        @if($workshop->what_you_will_learn)
                            <div class="content-card p-8 sm:p-10">
                                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-right flex items-center workshop-content-title">
                                    <div class="info-icon ml-4">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    ماذا ستتعلم
                                </h2>
                                <div class="prose prose-lg max-w-none text-gray-700 text-right leading-relaxed workshop-content-text">
                                    {!! $workshop->what_you_will_learn !!}
                                </div>
                            </div>
                        @endif

                        @if($workshop->requirements)
                            <div class="content-card p-8 sm:p-10">
                                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-right flex items-center workshop-content-title">
                                    <div class="info-icon ml-4">
                                        <i class="fas fa-list-check"></i>
                                    </div>
                                    المتطلبات
                                </h2>
                                <div class="prose prose-lg max-w-none text-gray-700 text-right leading-relaxed workshop-content-text">
                                    {!! $workshop->requirements !!}
                                </div>
                            </div>
                        @endif

                        @if($workshop->materials_needed)
                            <div class="content-card p-8 sm:p-10">
                                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-right flex items-center workshop-content-title">
                                    <div class="info-icon ml-4">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    المواد المطلوبة
                                </h2>
                                <div class="prose prose-lg max-w-none text-gray-700 text-right leading-relaxed workshop-content-text">
                                    {!! $workshop->materials_needed !!}
                                </div>
                            </div>
                        @endif

                        @if($workshop->recipes && $workshop->recipes->count() > 0)
                            <div class="content-card p-8 sm:p-10">
                                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-right flex items-center workshop-content-title">
                                    <div class="info-icon ml-4">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    وصفات الورشة
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($workshop->recipes as $recipe)
                                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                                            <a href="{{ route('recipe.show', $recipe->slug) }}" class="block group">
                                                <div class="relative overflow-hidden">
                                                    <img src="{{ $recipe->image_url ?: 'https://placehold.co/400x300/f87171/FFFFFF?text=وصفة' }}" 
                                                         alt="{{ $recipe->title }}" 
                                                         class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                                                         onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                                    <div class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                        <div class="bg-white/90 backdrop-blur-sm rounded-full p-2">
                                                            <i class="fas fa-arrow-left text-orange-500"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="p-6">
                                                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors line-clamp-2">
                                                        {{ $recipe->title }}
                                                    </h3>
                                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                                        {{ Str::limit($recipe->description, 100) }}
                                                    </p>
                                                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                                        <div class="flex items-center">
                                                            <i class="fas fa-clock text-orange-500 ml-1"></i>
                                                            <span>{{ $recipe->prep_time + $recipe->cook_time }} دقيقة</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <i class="fas fa-users text-orange-500 ml-1"></i>
                                                            <span>{{ $recipe->servings }} حصة</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <i class="fas fa-signal text-orange-500 ml-1"></i>
                                                            <span>{{ $recipe->difficulty === 'easy' ? 'سهل' : ($recipe->difficulty === 'medium' ? 'متوسط' : 'صعب') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center text-orange-500">
                                                            <i class="fas fa-user text-sm ml-1"></i>
                                                            <span class="text-sm font-medium">{{ $recipe->author }}</span>
                                                        </div>
                                                        <span class="text-orange-500 font-semibold">
                                                            <i class="fas fa-arrow-left text-xs ml-1"></i>
                                                            عرض الوصفة
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-8 workshop-sidebar">
                        <div class="sidebar-card p-8 booking-section">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-right flex items-center">
                                <div class="info-icon ml-4">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                احجز مقعدك الآن
                            </h3>
                            
                            <div class="space-y-6 mb-8">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">السعر</p>
                                        <p class="text-2xl font-bold text-orange-500">{{ $workshop->formatted_price }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">آخر موعد للتسجيل</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ $workshop->registration_deadline ? $workshop->registration_deadline->format('d/m/Y') : 'غير محدد' }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">عدد المشاركين</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ $workshop->bookings_count }}/{{ $workshop->max_participants }} مشارك</p>
                                    </div>
                                </div>
                            </div>

                            @if($workshop->is_completed)
                                <button class="w-full bg-gray-300 text-gray-500 font-bold py-4 px-6 rounded-xl cursor-not-allowed text-lg booking-button">
                                    <i class="fas fa-check-circle ml-2"></i>
                                    الورشة مكتملة
                                </button>
                            @elseif($workshop->is_fully_booked)
                                <button class="w-full bg-gray-300 text-gray-500 font-bold py-4 px-6 rounded-xl cursor-not-allowed text-lg booking-button">
                                    <i class="fas fa-times-circle ml-2"></i>
                                    الورشة مكتملة
                                </button>
                            @elseif(!$workshop->is_registration_open)
                                <button class="w-full bg-yellow-400 text-yellow-800 font-bold py-4 px-6 rounded-xl cursor-not-allowed text-lg booking-button">
                                    <i class="fas fa-clock ml-2"></i>
                                    انتهى التسجيل
                                </button>
                            @else
                                <button id="unifiedBookingBtn" class="booking-btn w-full text-white font-bold py-4 px-6 rounded-xl text-lg booking-button bg-green-500 hover:bg-green-600 transition-all duration-300 transform hover:scale-105">
                                    <i class="fab fa-whatsapp ml-2"></i>
                                    احجز الآن
                                </button>
                            @endif
                        </div>

                        <div class="instructor-card bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-right">عن المدرب</h3>
                            
                            <div class="text-center">
                                <img src="{{ $workshop->instructor_avatar }}" alt="{{ $workshop->instructor }}" 
                                     class="w-20 h-20 rounded-full mx-auto mb-4 object-cover border-2 border-orange-500 instructor-avatar">
                                <h4 class="text-xl font-bold text-gray-900 mb-2 instructor-name">{{ $workshop->instructor }}</h4>
                                @if($workshop->instructor_bio)
                                    <p class="text-gray-600 text-sm leading-relaxed bg-gray-50 p-4 rounded-lg instructor-bio">
                                        {{ $workshop->instructor_bio }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="sidebar-card p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-right flex items-center">
                                <div class="info-icon ml-4">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                تفاصيل إضافية
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-play-circle"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">تاريخ البداية</p>
                                        <p class="font-semibold text-gray-900">{{ $workshop->start_date ? $workshop->start_date->format('m/d/Y g:i A') : 'غير محدد' }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-stop-circle"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">تاريخ النهاية</p>
                                        <p class="font-semibold text-gray-900">{{ $workshop->end_date ? $workshop->end_date->format('m/d/Y g:i A') : 'غير محدد' }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">الفئة</p>
                                        <p class="font-semibold text-gray-900">{{ $workshop->category }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-signal"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">المستوى</p>
                                        <p class="font-semibold text-gray-900">{{ $workshop->level === 'beginner' ? 'مبتدئ' : 'متقدم' }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">المشاهدات</p>
                                        <p class="font-semibold text-gray-900">{{ $workshop->views_count }}</p>
                                    </div>
                                </div>
                                @if($workshop->address)
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-500">العنوان</p>
                                            <p class="font-semibold text-gray-900">{{ $workshop->address }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($relatedWorkshops->count() > 0)
        <section class="py-20 related-workshops-section" style="background-color: #f3f4f6;">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-bold text-gray-900 mb-4 related-workshops-title">قد تعجبك أيضاً</h2>
                        <div class="w-24 h-1 bg-gradient-to-r from-orange-500 to-orange-600 mx-auto rounded-full"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 related-workshops-grid">
                        @foreach($relatedWorkshops as $related)
                             @php 
                                $isFull = $related->bookings_count >= $related->max_participants; 
                            @endphp
                            <div class="related-workshop bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col {{ $isFull ? 'opacity-70' : '' }} related-workshop-card">
                                <a href="{{ route('workshop.show', $related->slug) }}" class="block group">
                                    <div class="relative overflow-hidden">
                                        <img src="{{ $related->image ? asset('storage/' . $related->image) : 'https://placehold.co/600x400/f87171/FFFFFF?text=ورشة' }}" 
                                             alt="{{ $related->title }}" 
                                             class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300 related-workshop-image"
                                             onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        @if($isFull)
                                            <span class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">مكتمل</span>
                                        @elseif($related->is_online)
                                            <span class="absolute top-3 left-3 bg-orange-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">أونلاين</span>
                                        @else
                                            <span class="absolute top-3 left-3 bg-orange-600 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">حضوري</span>
                                        @endif
                                        <div class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <div class="bg-white/90 backdrop-blur-sm rounded-full p-2">
                                                <i class="fas fa-arrow-left text-orange-500"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="p-6 flex flex-col flex-grow related-workshop-content">
                                    <h3 class="text-lg font-bold text-gray-900 mb-3 hover:text-orange-600 transition-colors line-clamp-2 related-workshop-title">
                                        <a href="{{ route('workshop.show', $related->slug) }}">{{ $related->title }}</a>
                                    </h3>
                                    <div class="flex items-center text-sm text-gray-500 mb-4">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-orange-500 text-xs"></i>
                                        </div>
                                        <span class="related-workshop-instructor">{{ $related->instructor }}</span>
                                    </div>
                                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                                        <div class="text-xl font-bold text-orange-500 related-workshop-price">
                                            {{ $related->price }} <span class="text-sm font-medium text-gray-500">{{ $related->currency }}</span>
                                        </div>
                                        <a href="{{ route('workshop.show', $related->slug) }}" 
                                           class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold text-sm transition-colors duration-300 related-workshop-button">
                                            التفاصيل <i class="fas fa-arrow-left mr-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // الزر الموحد للحجز
    const unifiedBookingBtn = document.getElementById('unifiedBookingBtn');
    if (unifiedBookingBtn) {
        unifiedBookingBtn.addEventListener('click', function() {
            // التحقق من حالة تسجيل الدخول
            const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
            
            if (isLoggedIn) {
                // المستخدم مسجل دخول - حفظ الحجز + إرسال واتساب
                unifiedBooking();
            } else {
                // المستخدم غير مسجل دخول - توجيه لتسجيل الدخول مع معرف الورشة
                showLoginRequiredModal({{ $workshop->id }});
            }
        });
    }

    // الحجز الموحد (للمستخدمين المسجلين)
    function unifiedBooking() {
        showBookingConfirmation();
    }

    // دالة تأكيد الحجز الجميلة
    function showBookingConfirmation() {
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
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $workshop->title }}</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>التاريخ:</span>
                                    <span class="font-medium">{{ $workshop->start_date ? $workshop->start_date->format('d/m/Y') : 'غير محدد' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>تاريخ البداية:</span>
                                    <span class="font-medium">{{ $workshop->start_date ? $workshop->start_date->format('m/d/Y g:i A') : 'غير محدد' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>تاريخ النهاية:</span>
                                    <span class="font-medium">{{ $workshop->end_date ? $workshop->end_date->format('m/d/Y g:i A') : 'غير محدد' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>المدرب:</span>
                                    <span class="font-medium">{{ $workshop->instructor }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>المكان:</span>
                                    <span class="font-medium">{{ $workshop->is_online ? 'ورشة أونلاين' : ($workshop->location ?? 'ورشة حضورية') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>السعر:</span>
                                    <span class="font-medium text-green-600">{{ $workshop->formatted_price }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- الأزرار -->
                        <div class="flex gap-3">
                            <button onclick="confirmBooking()" 
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
    function confirmBooking() {
        closeBookingConfirmation();
        
        // حفظ الحجز في قاعدة البيانات
        fetch('{{ route("bookings.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                workshop_id: {{ $workshop->id }},
                notes: 'حجز موحد - واتساب + قاعدة بيانات'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // إرسال رسالة الواتساب
                sendWhatsAppMessage();
                
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
    function showWhatsAppConfirmation() {
        // إزالة أي modal سابق
        const existingModal = document.getElementById('whatsapp-confirmation-modal');
        if (existingModal) {
            existingModal.remove();
        }

        // الحصول على تفاصيل الورشة
        const workshopTitle = "{{ $workshop->title }}";
        const workshopPrice = "{{ $workshop->formatted_price }}";
        const workshopDate = "{{ $workshop->start_date ? $workshop->start_date->format('d/m/Y') : 'غير محدد' }}";
        const workshopInstructor = "{{ $workshop->instructor_name }}";
        const workshopLocation = "{{ $workshop->location }}";
        const workshopDeadline = "{{ $workshop->registration_deadline ? $workshop->registration_deadline->format('d/m/Y') : 'غير محدد' }}";

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
                            <h4 class="font-semibold text-gray-900 mb-2">${workshopTitle}</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <p><i class="fas fa-calendar-alt text-blue-500 ml-2"></i> ${workshopDate}</p>
                                <p><i class="fas fa-user text-purple-500 ml-2"></i> ${workshopInstructor}</p>
                                <p><i class="fas fa-map-marker-alt text-red-500 ml-2"></i> ${workshopLocation}</p>
                                <p><i class="fas fa-tag text-green-500 ml-2"></i> ${workshopPrice} ريال</p>
                            </div>
                        </div>
                        
                        <!-- الأزرار -->
                        <div class="flex space-x-4 space-x-reverse">
                            <button onclick="closeWhatsAppConfirmation()" 
                                    class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                <i class="fas fa-times ml-2"></i>
                                إلغاء
                            </button>
                            <button onclick="confirmWhatsAppBooking()" 
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
    function confirmWhatsAppBooking() {
        closeWhatsAppConfirmation();
        sendWhatsAppMessage();
    }

    // جعل الدوال متاحة عالمياً
    window.confirmBooking = confirmBooking;
    window.closeBookingConfirmation = closeBookingConfirmation;
    window.showBookingConfirmation = showBookingConfirmation;
    window.unifiedBooking = unifiedBooking;
    window.whatsappOnlyBooking = whatsappOnlyBooking;
    window.sendWhatsAppMessage = sendWhatsAppMessage;
    window.showCustomAlert = showCustomAlert;
    window.closeCustomAlert = closeCustomAlert;
    window.showWhatsAppConfirmation = showWhatsAppConfirmation;
    window.closeWhatsAppConfirmation = closeWhatsAppConfirmation;
    window.confirmWhatsAppBooking = confirmWhatsAppBooking;

    // الحجز عبر الواتساب فقط (للمستخدمين غير المسجلين)
    function whatsappOnlyBooking() {
        showWhatsAppConfirmation();
    }

    // إرسال رسالة الواتساب
    function sendWhatsAppMessage() {
        // إنشاء رسالة الواتساب مع تفاصيل الورشة
        const workshopTitle = "{{ $workshop->title }}";
        const workshopPrice = "{{ $workshop->formatted_price }}";
        const workshopDate = "{{ $workshop->start_date ? $workshop->start_date->format('d/m/Y') : 'غير محدد' }}";
        const workshopStartDate = "{{ $workshop->start_date ? $workshop->start_date->format('m/d/Y g:i A') : 'غير محدد' }}";
        const workshopEndDate = "{{ $workshop->end_date ? $workshop->end_date->format('m/d/Y g:i A') : 'غير محدد' }}";
        const workshopInstructor = "{{ $workshop->instructor }}";
        const workshopLocation = "{{ $workshop->is_online ? 'ورشة أونلاين' : ($workshop->location ?? 'ورشة حضورية') }}";
        const registrationDeadline = "{{ $workshop->registration_deadline ? $workshop->registration_deadline->format('d/m/Y') : 'غير محدد' }}";
        
        // معلومات المستخدم
        const userName = "{{ auth()->check() ? auth()->user()->name : 'مستخدم' }}";
        const userPhone = "{{ auth()->check() && auth()->user()->phone ? auth()->user()->phone : 'غير محدد' }}";
        const userEmail = "{{ auth()->check() ? auth()->user()->email : 'غير محدد' }}";
        
        // إنشاء رسالة الواتساب
        const whatsappMessage = `مرحباً! أريد حجز مقعد في الورشة التالية:

🏆 *${workshopTitle}*

📅 التاريخ: ${workshopDate}
🕐 تاريخ البداية: ${workshopStartDate}
🕐 تاريخ النهاية: ${workshopEndDate}
👨‍🏫 المدرب: ${workshopInstructor}
📍 المكان: ${workshopLocation}
💰 السعر: ${workshopPrice}
⏰ آخر موعد للتسجيل: ${registrationDeadline}

📋 *معلوماتي الشخصية:*
👤 الاسم: ${userName}
📞 الهاتف: ${userPhone}
📧 البريد الإلكتروني: ${userEmail}

يرجى تأكيد الحجز وتوضيح طريقة الدفع. شكراً!

💡 *ملاحظة:* تم حفظ الحجز في نظامنا تلقائياً.`;

        // تشفير الرسالة للواتساب
        const encodedMessage = encodeURIComponent(whatsappMessage);
        
        // رقم الواتساب
        const whatsappNumber = "962790553680";
        
        // فتح الواتساب
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
});
</script>
@endpush

