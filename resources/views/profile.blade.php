@extends('layouts.app')

@section('title', 'الملف الشخصي - موقع وصفة')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-8 rtl:space-x-reverse">
                <!-- Profile Picture -->
                <div class="relative">
                    <div class="w-32 h-32 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="صورة الملف الشخصي" class="w-full h-full rounded-full object-cover">
                        @else
                            {{ substr($user->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="absolute -bottom-2 -right-2 bg-green-500 w-8 h-8 rounded-full border-4 border-white flex items-center justify-center">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                </div>
                
                <!-- User Info -->
                <div class="flex-1 text-center md:text-right">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $user->name }}</h1>
                    <p class="text-gray-600 mb-4">{{ $user->email }}</p>
                    @if($user->phone)
                        <p class="text-gray-600 mb-4">
                            <i class="fas fa-phone ml-2"></i>
                            {{ $user->phone }}
                        </p>
                    @endif
                    <div class="flex items-center justify-center md:justify-start space-x-4 rtl:space-x-reverse text-sm text-gray-500">
                        <span>
                            <i class="fas fa-calendar-alt ml-1"></i>
                            عضو منذ {{ $user->created_at->format('M Y') }}
                        </span>
                        @if($user->is_admin)
                            <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full font-medium">
                                <i class="fas fa-crown ml-1"></i>
                                مدير
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Edit Profile Button -->
                <button id="editProfileBtn" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center space-x-2 rtl:space-x-reverse">
                    <i class="fas fa-edit"></i>
                    <span>تعديل الملف الشخصي</span>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bookmark text-orange-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $stats['saved_recipes_count'] }}</h3>
                <p class="text-gray-600">وصفة محفوظة</p>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $stats['made_recipes_count'] }}</h3>
                <p class="text-gray-600">وصفة مصنوعة</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-graduation-cap text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $stats['booked_workshops_count'] }}</h3>
                <p class="text-gray-600">ورشة محجوزة</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $stats['reviews_count'] }}</h3>
                <p class="text-gray-600">تقييم</p>
            </div>
        </div>

        <!-- Content Tabs -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 rtl:space-x-reverse px-8">
                <button class="tab-btn active py-4 px-2 border-b-2 border-orange-500 text-orange-600 font-medium" data-tab="workshops">
                        <i class="fas fa-graduation-cap ml-2"></i>
                        الورشات المحجوزة
                    </button>
                    <button class="tab-btn py-4 px-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium" data-tab="saved">
                        <i class="fas fa-bookmark ml-2"></i>
                        الوصفات المحفوظة
                    </button>

                    <button class="tab-btn py-4 px-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium" data-tab="made">
                        <i class="fas fa-check-circle ml-2"></i>
                        الوصفات المصنوعة
                    </button>

 

                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-8">
                <!-- Saved Recipes Tab -->
                <div id="saved-tab" class="tab-content hidden">
                    @if($savedRecipes->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($savedRecipes as $recipe)
                                <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 group recipe-card">
                                    <div class="relative overflow-hidden">
                                        @if($recipe->image || $recipe->image_url)
                                            <img src="{{ $recipe->image ? Storage::disk('public')->url($recipe->image) : $recipe->image_url }}" 
                                                 alt="{{ $recipe->title }}" 
                                                 class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-300"
                                                 onerror="this.style.display='none'; this.parentElement.querySelector('.placeholder-fallback').style.display='flex'; this.parentElement.querySelector('.placeholder-fallback').classList.add('show');"
                                                 loading="lazy">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        @endif
                                        @if(!$recipe->image && !$recipe->image_url)
                                            <div class="w-full h-56 placeholder-gradient-orange flex items-center justify-center relative overflow-hidden placeholder-fallback">
                                                <!-- Background Pattern -->
                                                <div class="absolute inset-0 bg-gradient-to-br from-orange-300/30 to-orange-700/30"></div>
                                                <div class="absolute inset-0 opacity-20">
                                                    <div class="absolute top-4 left-4 w-8 h-8 bg-white/30 rounded-full"></div>
                                                    <div class="absolute top-8 right-8 w-6 h-6 bg-white/20 rounded-full"></div>
                                                    <div class="absolute bottom-6 left-8 w-4 h-4 bg-white/25 rounded-full"></div>
                                                    <div class="absolute bottom-4 right-4 w-10 h-10 bg-white/15 rounded-full"></div>
                                                </div>
                                                
                                                <!-- Main Content -->
                                                <div class="relative z-10 text-center">
                                                    <div class="bg-white/20 backdrop-blur-sm rounded-full p-4 mb-3 inline-block">
                                                        <svg class="w-12 h-12 text-white drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.2-1.1-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z"/>
                                                        </svg>
                                                    </div>
                                                    <p class="text-white text-sm font-medium drop-shadow-md mb-1">وصفة لذيذة</p>
                                                    <p class="text-white/80 text-xs drop-shadow-sm">{{ Str::limit($recipe->title, 20) }}</p>
                                                </div>
                                                
                                                <!-- Decorative Elements -->
                                                <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm rounded-full p-2">
                                                    <i class="fas fa-bookmark text-white text-lg"></i>
                                                </div>
                                                <div class="absolute bottom-4 left-4 bg-white/15 backdrop-blur-sm rounded-lg px-2 py-1">
                                                    <i class="fas fa-heart text-white text-sm"></i>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="absolute top-3 left-3 bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium shadow-lg">
                                            <i class="fas fa-clock ml-1"></i>
                                            {{ $recipe->prep_time }} دقيقة
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <h3 class="font-bold text-gray-800 mb-2 text-lg group-hover:text-orange-600 transition-colors duration-200">{{ $recipe->title }}</h3>
                                        <p class="text-gray-600 text-sm mb-4 leading-relaxed">{{ Str::limit($recipe->description, 100) }}</p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <div class="flex items-center text-yellow-500">
                                                    <i class="fas fa-star text-sm"></i>
                                                    <span class="text-gray-600 text-sm mr-1">4.5</span>
                                                </div>
                                                <span class="text-gray-400">•</span>
                                                <span class="text-gray-500 text-sm">{{ $recipe->category->name ?? 'عام' }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <button class="remove-recipe-btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2 rtl:space-x-reverse"
                                                        data-recipe-id="{{ $recipe->recipe_id }}"
                                                        data-recipe-name="{{ $recipe->title }}"
                                                        title="إزالة من المحفوظات">
                                                    <i class="fas fa-trash ml-1"></i>
                                                    <span>إزالة</span>
                                                </button>
                                                <span></span>
                                                <a href="{{ route('recipe.show', $recipe->slug) }}" 
                                                   class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2 rtl:space-x-reverse group btn-animated">
                                                    <span>عرض الوصفة</span>
                                                    <i class="fas fa-arrow-left group-hover:translate-x-1 transition-transform duration-200"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-bookmark text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-xl font-medium text-gray-500 mb-2">لا توجد وصفات محفوظة</h3>
                            <p class="text-gray-400">ابدأ بحفظ الوصفات المفضلة لديك</p>
                        </div>
                    @endif
                </div>

                <!-- Made Recipes Tab -->
                <div id="made-tab" class="tab-content hidden">
                    @if($madeRecipes->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($madeRecipes as $recipe)
                                <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 group recipe-card">
                                    <div class="relative overflow-hidden">
                                        @if($recipe->image || $recipe->image_url)
                                            <img src="{{ $recipe->image ? Storage::disk('public')->url($recipe->image) : $recipe->image_url }}" 
                                                 alt="{{ $recipe->title }}" 
                                                 class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-300"
                                                 onerror="this.style.display='none'; this.parentElement.querySelector('.placeholder-fallback').style.display='flex'; this.parentElement.querySelector('.placeholder-fallback').classList.add('show');"
                                                 loading="lazy">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        @endif
                                        @if(!$recipe->image && !$recipe->image_url)
                                            <div class="w-full h-56 placeholder-gradient-green flex items-center justify-center relative overflow-hidden placeholder-fallback">
                                                <!-- Background Pattern -->
                                                <div class="absolute inset-0 bg-gradient-to-br from-green-300/30 to-green-700/30"></div>
                                                <div class="absolute inset-0 opacity-20">
                                                    <div class="absolute top-6 left-6 w-6 h-6 bg-white/25 rounded-full"></div>
                                                    <div class="absolute top-4 right-6 w-8 h-8 bg-white/20 rounded-full"></div>
                                                    <div class="absolute bottom-8 left-4 w-5 h-5 bg-white/30 rounded-full"></div>
                                                    <div class="absolute bottom-6 right-8 w-7 h-7 bg-white/15 rounded-full"></div>
                                                </div>
                                                
                                                <!-- Main Content -->
                                                <div class="relative z-10 text-center">
                                                    <div class="bg-white/20 backdrop-blur-sm rounded-full p-4 mb-3 inline-block">
                                                        <svg class="w-12 h-12 text-white drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                                        </svg>
                                                    </div>
                                                    <p class="text-white text-sm font-medium drop-shadow-md mb-1">وصفة مكتملة</p>
                                                    <p class="text-white/80 text-xs drop-shadow-sm">{{ Str::limit($recipe->title, 20) }}</p>
                                                </div>
                                                
                                                <!-- Decorative Elements -->
                                                <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm rounded-full p-2">
                                                    <i class="fas fa-check text-white text-lg"></i>
                                                </div>
                                                <div class="absolute bottom-4 left-4 bg-white/15 backdrop-blur-sm rounded-lg px-2 py-1">
                                                    <i class="fas fa-star text-white text-sm"></i>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="absolute top-3 left-3 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium shadow-lg">
                                            <i class="fas fa-clock ml-1"></i>
                                            {{ $recipe->prep_time }} دقيقة
                                        </div>
                                        <div class="absolute top-3 right-3 bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium shadow-lg">
                                            <i class="fas fa-check ml-1"></i>
                                            مكتملة
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <h3 class="font-bold text-gray-800 mb-2 text-lg group-hover:text-green-600 transition-colors duration-200">{{ $recipe->title }}</h3>
                                        <p class="text-gray-600 text-sm mb-4 leading-relaxed">{{ Str::limit($recipe->description, 100) }}</p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <div class="flex items-center text-yellow-500">
                                                    <i class="fas fa-star text-sm"></i>
                                                    <span class="text-gray-600 text-sm mr-1">4.5</span>
                                                </div>
                                                <span class="text-gray-400">•</span>
                                                <span class="text-gray-500 text-sm">{{ $recipe->category->name ?? 'عام' }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <button class="remove-made-recipe-btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2 rtl:space-x-reverse"
                                                        data-recipe-id="{{ $recipe->recipe_id }}"
                                                        data-recipe-name="{{ $recipe->title }}"
                                                        title="إزالة من المصنوعة">
                                                    <i class="fas fa-trash ml-1"></i>
                                                    <span>إزالة</span>
                                                </button>
                                                <span></span>
                                                <a href="{{ route('recipe.show', $recipe->slug) }}" 
                                                   class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2 rtl:space-x-reverse group btn-animated">
                                                    <span>عرض الوصفة</span>
                                                    <i class="fas fa-arrow-left group-hover:translate-x-1 transition-transform duration-200"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-check-circle text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-xl font-medium text-gray-500 mb-2">لا توجد وصفات مصنوعة</h3>
                            <p class="text-gray-400">ابدأ بصنع الوصفات وشاركنا تجربتك</p>
                        </div>
                    @endif
                </div>

                <!-- Booked Workshops Tab -->
                <div id="workshops-tab" class="tab-content">
                    @if($bookedWorkshops->count() > 0)
                        <!-- إحصائيات الورشات -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $stats['confirmed_workshops_count'] }}</div>
                                <div class="text-sm text-green-700">ورشات مؤكدة</div>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_workshops_count'] }}</div>
                                <div class="text-sm text-yellow-700">ورشات معلقة</div>
                            </div>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-red-600">{{ $stats['cancelled_workshops_count'] }}</div>
                                <div class="text-sm text-red-700">ورشات ملغية</div>
                            </div>
                        </div>

                        <!-- قائمة الورشات -->
                        <div class="space-y-4">
                            @foreach($bookedWorkshops as $booking)
                                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                                    <div class="flex items-start space-x-4 rtl:space-x-reverse">
                                        @if($booking->workshop->image)
                                            <img src="{{ $booking->workshop->image ? asset('storage/' . $booking->workshop->image) : 'https://placehold.co/80x80/f87171/FFFFFF?text=ورشة' }}" alt="{{ $booking->workshop->title }}" class="w-20 h-20 rounded-lg object-cover">
                                        @else
                                            <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-graduation-cap text-white text-2xl"></i>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between mb-2">
                                                <h3 class="font-bold text-gray-800 text-lg">{{ $booking->workshop->title }}</h3>
                                                @if($booking->status === 'confirmed')
                                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                                        <i class="fas fa-check-circle ml-1"></i>
                                                        مؤكد
                                                    </span>
                                                @elseif($booking->status === 'pending')
                                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                                        <i class="fas fa-clock ml-1"></i>
                                                        في الانتظار
                                                    </span>
                                                @else
                                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                                                        <i class="fas fa-times-circle ml-1"></i>
                                                        ملغي
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <p class="text-gray-600 text-sm mb-3">{{ Str::limit($booking->workshop->description ?? 'لا يوجد وصف', 120) }}</p>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                <div class="space-y-2">
                                                    <div class="flex items-center text-blue-600">
                                                        <i class="fas fa-calendar ml-2"></i>
                                                        <span class="font-medium">تاريخ الورشة:</span>
                                                        <span class="mr-2">{{ $booking->workshop->workshop_date ? $booking->workshop->workshop_date->format('Y-m-d') : 'غير محدد' }}</span>
                                                    </div>
                                                    <div class="flex items-center text-green-600">
                                                        <i class="fas fa-play-circle ml-2"></i>
                                                        <span class="font-medium">تاريخ البداية:</span>
                                                        <span class="mr-2">{{ $booking->workshop->start_date ? $booking->workshop->start_date->format('m/d/Y g:i A') : 'غير محدد' }}</span>
                                                    </div>
                                                    <div class="flex items-center text-red-600">
                                                        <i class="fas fa-stop-circle ml-2"></i>
                                                        <span class="font-medium">تاريخ النهاية:</span>
                                                        <span class="mr-2">{{ $booking->workshop->end_date ? $booking->workshop->end_date->format('m/d/Y g:i A') : 'غير محدد' }}</span>
                                                    </div>
                                                    <div class="flex items-center text-gray-600">
                                                        <i class="fas fa-map-marker-alt ml-2"></i>
                                                        <span class="font-medium">المكان:</span>
                                                        <span class="mr-2">{{ $booking->workshop->location ?? 'غير محدد' }}</span>
                                                    </div>
                                                    @if($booking->workshop->is_online && $booking->workshop->meeting_link && $booking->status === 'confirmed')
                                                        <div class="flex items-center text-purple-600">
                                                            <i class="fas fa-video ml-2"></i>
                                                            <span class="font-medium">رابط الاجتماع:</span>
                                                            <a href="{{ $booking->workshop->meeting_link }}" 
                                                               target="_blank" 
                                                               class="mr-2 text-purple-600 hover:text-purple-800 underline font-medium">
                                                                انضم للورشة
                                                            </a>
                                                        </div>
                                                    @elseif($booking->workshop->is_online && $booking->status !== 'confirmed')
                                                        <div class="flex items-center text-amber-600">
                                                            <i class="fas fa-clock ml-2"></i>
                                                            <span class="font-medium">رابط الاجتماع:</span>
                                                            <span class="mr-2 text-amber-600 font-medium">
                                                                سوف يظهر رابط الورشة بعد تأكيد الحجز من قبل فريق وصفة
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <div class="space-y-2">
                                                    <div class="flex items-center text-gray-600">
                                                        <i class="fas fa-calendar-plus ml-2"></i>
                                                        <span class="font-medium">تاريخ الحجز:</span>
                                                        <span class="mr-2">{{ $booking->created_at->format('Y-m-d H:i') }}</span>
                                                    </div>
                                                    @if($booking->status === 'confirmed' && $booking->confirmed_at)
                                                        <div class="flex items-center text-green-600">
                                                            <i class="fas fa-check-double ml-2"></i>
                                                            <span class="font-medium">تم التأكيد:</span>
                                                            <span class="mr-2">{{ $booking->confirmed_at ? $booking->confirmed_at->format('Y-m-d H:i') : 'غير محدد' }}</span>
                                                        </div>
                                                    @endif
                                                    @if($booking->status === 'cancelled' && $booking->cancelled_at)
                                                        <div class="flex items-center text-red-600">
                                                            <i class="fas fa-ban ml-2"></i>
                                                            <span class="font-medium">تم الإلغاء:</span>
                                                            <span class="mr-2">{{ $booking->cancelled_at ? $booking->cancelled_at->format('Y-m-d H:i') : 'غير محدد' }}</span>
                                                        </div>
                                                    @endif
                                                    @if($booking->cancellation_reason)
                                                        <div class="flex items-start text-red-600">
                                                            <i class="fas fa-exclamation-triangle ml-2 mt-1"></i>
                                                            <div>
                                                                <span class="font-medium">سبب الإلغاء:</span>
                                                                <p class="text-sm mr-2">{{ $booking->cancellation_reason }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            @if($booking->workshop->is_online && $booking->workshop->meeting_link && $booking->status === 'confirmed')
                                                <div class="mt-4 p-4 bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center">
                                                            <div class="bg-purple-100 p-2 rounded-full ml-3">
                                                                <i class="fas fa-video text-purple-600"></i>
                                                            </div>
                                                            <div>
                                                                <h4 class="font-bold text-purple-800">ورشة أونلاين</h4>
                                                                <p class="text-sm text-purple-600">انضم للورشة عبر الرابط أدناه</p>
                                                            </div>
                                                        </div>
                                                        <a href="{{ $booking->workshop->meeting_link }}" 
                                                           target="_blank" 
                                                           class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center space-x-2 rtl:space-x-reverse">
                                                            <i class="fas fa-external-link-alt"></i>
                                                            <span>انضم الآن</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            @elseif($booking->workshop->is_online && $booking->status !== 'confirmed')
                                                <div class="mt-4 p-4 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-lg">
                                                    <div class="flex items-center">
                                                        <div class="bg-amber-100 p-2 rounded-full ml-3">
                                                            <i class="fas fa-clock text-amber-600"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-bold text-amber-800">ورشة أونلاين</h4>
                                                            <p class="text-sm text-amber-600">سوف يظهر رابط الورشة بعد تأكيد الحجز من قبل فريق وصفة</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($booking->notes)
                                                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                                    <div class="flex items-start">
                                                        <i class="fas fa-sticky-note text-gray-500 ml-2 mt-1"></i>
                                                        <div>
                                                            <span class="font-medium text-gray-700">ملاحظات:</span>
                                                            <p class="text-sm text-gray-600 mt-1">{{ $booking->notes }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-graduation-cap text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-xl font-medium text-gray-500 mb-2">لا توجد ورشات محجوزة</h3>
                            <p class="text-gray-400">احجز ورشة عمل لتعلم مهارات جديدة</p>
                            <a href="{{ route('workshops') }}" class="inline-flex items-center px-6 py-3 mt-4 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                                <i class="fas fa-graduation-cap ml-2"></i>
                                تصفح الورشات
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Reviews Tab -->
                <div id="reviews-tab" class="tab-content hidden">
                    @if($workshopReviews->count() > 0)
                        <div class="space-y-6">
                            @foreach($workshopReviews as $review)
                                <div class="bg-gray-50 rounded-xl p-6 hover:shadow-lg transition-shadow">
                                    <div class="flex items-start space-x-4 rtl:space-x-reverse">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <h3 class="font-bold text-gray-800">{{ $review->workshop->title }}</h3>
                                                <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                            <p class="text-gray-600 mb-3">{{ $review->comment }}</p>
                                            <div class="text-sm text-gray-500">
                                                <i class="fas fa-calendar ml-1"></i>
                                                {{ $review->created_at->format('Y-m-d') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-star text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-xl font-medium text-gray-500 mb-2">لا توجد تقييمات</h3>
                            <p class="text-gray-400">شارك تقييمك للورشات التي حضرتها</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">تعديل الملف الشخصي</h2>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الاسم</label>
                    <input type="text" name="name" value="{{ $user->name }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ $user->email }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                    <input type="text" name="phone" value="{{ $user->phone }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
            </div>
            
            <div class="flex space-x-4 rtl:space-x-reverse mt-8">
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-medium transition-colors">
                    حفظ التغييرات
                </button>
                <button type="button" id="cancelEdit" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="removeConfirmationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-8 shadow-2xl transform scale-95 transition-all duration-300">
        <div class="text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            
            <!-- Title -->
            <h3 class="text-xl font-bold text-gray-900 mb-2">تأكيد الإزالة</h3>
            
            <!-- Message -->
            <p class="text-gray-600 mb-6" id="confirmationMessage">
                هل أنت متأكد من إزالة هذه الوصفة من المحفوظات؟
            </p>
            
            <!-- Recipe Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6" id="recipeInfo">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <div class="flex-shrink-0">
                        <i class="fas fa-bookmark text-orange-500 text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate" id="recipeName">اسم الوصفة</p>
                        <p class="text-sm text-gray-500">سيتم إزالتها من قائمة المحفوظات</p>
                    </div>
                </div>
            </div>
            
            <!-- Buttons -->
            <div class="flex space-x-3 rtl:space-x-reverse">
                <button id="cancelRemove" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-4 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </button>
                <button id="confirmRemove" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-trash ml-2"></i>
                    إزالة
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal for Made Recipes -->
<div id="removeMadeConfirmationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-8 shadow-2xl transform scale-95 transition-all duration-300">
        <div class="text-center">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            
            <!-- Title -->
            <h3 class="text-xl font-bold text-gray-900 mb-2">تأكيد الإزالة</h3>
            
            <!-- Message -->
            <p class="text-gray-600 mb-6" id="madeConfirmationMessage">
                هل أنت متأكد من إزالة هذه الوصفة من قائمة المصنوعة؟
            </p>
            
            <!-- Recipe Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6" id="madeRecipeInfo">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate" id="madeRecipeName">اسم الوصفة</p>
                        <p class="text-sm text-gray-500">سيتم إزالتها من قائمة الوصفات المصنوعة</p>
                    </div>
                </div>
            </div>
            
            <!-- Buttons -->
            <div class="flex space-x-3 rtl:space-x-reverse">
                <button id="cancelMadeRemove" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-4 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </button>
                <button id="confirmMadeRemove" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-trash ml-2"></i>
                    إزالة
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all tabs
            tabBtns.forEach(b => {
                b.classList.remove('active', 'border-orange-500', 'text-orange-600');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Add active class to clicked tab
            this.classList.add('active', 'border-orange-500', 'text-orange-600');
            this.classList.remove('border-transparent', 'text-gray-500');
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show target tab content
            document.getElementById(targetTab + '-tab').classList.remove('hidden');
        });
    });
    
    // Edit profile modal
    const editProfileBtn = document.getElementById('editProfileBtn');
    const editProfileModal = document.getElementById('editProfileModal');
    const closeModal = document.getElementById('closeModal');
    const cancelEdit = document.getElementById('cancelEdit');
    
    editProfileBtn.addEventListener('click', function() {
        editProfileModal.classList.remove('hidden');
    });
    
    closeModal.addEventListener('click', function() {
        editProfileModal.classList.add('hidden');
    });
    
    cancelEdit.addEventListener('click', function() {
        editProfileModal.classList.add('hidden');
    });
    
    // Close modal when clicking outside
    editProfileModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
    
    // تهيئة أزرار إزالة الوصفات المحفوظة
    initializeRemoveButtons();
    
    // تهيئة أزرار إزالة الوصفات المصنوعة
    initializeRemoveMadeButtons();
    
    // تهيئة modal التأكيد للوصفات المحفوظة
    initializeConfirmationModal();
    
    // تهيئة modal التأكيد للوصفات المصنوعة
    initializeMadeConfirmationModal();
});

/**
 * يعرض رسالة toast للمستخدم
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white font-semibold transform translate-x-full transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' :
        'bg-blue-500'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

/**
 * يحصل على CSRF token
 */
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

/**
 * يتعامل مع عملية إزالة الوصفة من المحفوظات
 */
async function handleRemoveRecipe(button, recipeId) {
    const recipeName = button.dataset.recipeName || 'الوصفة';
    
    // التحقق من تسجيل الدخول
    const userId = document.body.dataset.userId || document.querySelector('[data-user-id]')?.dataset.userId;
    if (!userId || userId === 'null' || userId === '') {
        showToast('يجب تسجيل الدخول لإزالة الوصفة', 'warning');
        window.location.href = '/login';
        return;
    }

    // تعطيل الزر أثناء التحميل
    button.disabled = true;
    button.classList.add('opacity-70');
    
    // تغيير النص والأيقونة أثناء التحميل
    const span = button.querySelector('span');
    const icon = button.querySelector('i');
    const originalText = span.textContent;
    const originalIcon = icon.className;
    
    span.textContent = 'جاري الإزالة...';
    icon.className = 'fas fa-spinner fa-spin ml-1';

    try {
        // تأكد من تهيئة كوكي CSRF
        await fetch('/sanctum/csrf-cookie', { credentials: 'include' });

        const csrf = getCsrfToken();
        const res = await fetch('/api/interactions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'include',
            body: JSON.stringify({
                recipe_id: recipeId,
                is_saved: false, // إزالة من المحفوظات
            }),
        });

        if (!res.ok) {
            if (res.status === 401) {
                showToast('يجب تسجيل الدخول لإزالة الوصفة', 'warning');
                window.location.href = '/login';
                return;
            } else if (res.status === 419) {
                window.location.reload();
                return;
            } else {
                const txt = await res.text();
                console.error('Error body:', txt);
                showToast('حدث خطأ أثناء محاولة إزالة الوصفة. يرجى المحاولة مرة أخرى.', 'error');
                return;
            }
        }

        // تحليل الاستجابة
        const responseData = await res.json();
        console.log('Remove request successful:', responseData);
        
        // إزالة الكارت من الواجهة
        const cardContainer = button.closest('.recipe-card');
        if (cardContainer) {
            // إضافة تأثير بصري للإزالة
            cardContainer.style.transition = 'all 0.3s ease';
            cardContainer.style.transform = 'scale(0.95)';
            cardContainer.style.opacity = '0.5';
            
            setTimeout(() => {
                cardContainer.remove();
                
                // التحقق من وجود وصفات أخرى
                const remainingCards = document.querySelectorAll('.recipe-card');
                if (remainingCards.length === 0) {
                    // إظهار رسالة عدم وجود وصفات
                    const tabContent = document.getElementById('saved-tab');
                    if (tabContent) {
                        tabContent.innerHTML = `
                            <div class="text-center py-12">
                                <i class="fas fa-bookmark text-gray-300 text-6xl mb-4"></i>
                                <h3 class="text-xl font-medium text-gray-500 mb-2">لا توجد وصفات محفوظة</h3>
                                <p class="text-gray-400">ابدأ بحفظ الوصفات المفضلة لديك</p>
                            </div>
                        `;
                    }
                }
            }, 300);
        }
        
        // إظهار رسالة نجاح
        showToast(`تم إزالة "${recipeName}" من المحفوظات بنجاح!`, 'success');

    } catch (err) {
        console.error(err);
        showToast('حدث خطأ أثناء محاولة إزالة الوصفة.', 'error');
        
        // إعادة النص والأيقونة الأصليين
        span.textContent = originalText;
        icon.className = originalIcon;
        
    } finally {
        // إعادة تفعيل الزر
        button.disabled = false;
        button.classList.remove('opacity-70');
    }
}

/**
 * يهيئ جميع أزرار إزالة الوصفة في الصفحة
 */
function initializeRemoveButtons() {
    console.log('Initializing remove buttons...');
    
    // البحث عن جميع أزرار إزالة الوصفة
    const removeButtons = document.querySelectorAll('.remove-recipe-btn');
    console.log('Found remove buttons:', removeButtons.length);
    
    removeButtons.forEach((button, index) => {
        console.log(`Initializing button ${index + 1}:`, button);
        
        // منع إضافة مستمعين متعددين
        if (button.dataset.initialized === 'true') {
            console.log('Button already initialized, skipping...');
            return;
        }
        
        // البحث عن معرف الوصفة في العنصر
        const recipeId = button.dataset.recipeId;
        console.log('Recipe ID:', recipeId);
        
        if (!recipeId) {
            console.log('No recipe ID found, skipping...');
            return;
        }

        // إضافة مستمع النقر
        button.addEventListener('click', async (e) => {
            console.log('Remove button clicked!');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // إظهار modal التأكيد
            showConfirmationModal(button, recipeId);
        });

        // تحديد أن الزر تم تهيئته
        button.dataset.initialized = 'true';
        console.log('Button initialized successfully');
    });
}

/**
 * إظهار modal التأكيد
 */
function showConfirmationModal(button, recipeId) {
    const modal = document.getElementById('removeConfirmationModal');
    const recipeName = button.dataset.recipeName || 'الوصفة';
    const recipeNameElement = document.getElementById('recipeName');
    const confirmationMessage = document.getElementById('confirmationMessage');
    
    // تحديث معلومات الوصفة
    recipeNameElement.textContent = recipeName;
    confirmationMessage.textContent = `هل أنت متأكد من إزالة "${recipeName}" من المحفوظات؟`;
    
    // إظهار الـ modal مع تأثير
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.bg-white').classList.remove('scale-95');
        modal.querySelector('.bg-white').classList.add('scale-100');
    }, 10);
    
    // حفظ البيانات للاستخدام لاحقاً
    modal.dataset.currentButton = button.outerHTML;
    modal.dataset.currentRecipeId = recipeId;
}

/**
 * تهيئة modal التأكيد
 */
function initializeConfirmationModal() {
    const modal = document.getElementById('removeConfirmationModal');
    const cancelBtn = document.getElementById('cancelRemove');
    const confirmBtn = document.getElementById('confirmRemove');
    
    // إلغاء الإزالة
    cancelBtn.addEventListener('click', () => {
        hideConfirmationModal();
    });
    
    // تأكيد الإزالة
    confirmBtn.addEventListener('click', async () => {
        const recipeId = modal.dataset.currentRecipeId;
        const buttonHtml = modal.dataset.currentButton;
        
        if (recipeId) {
            // إنشاء عنصر زر مؤقت للاستخدام
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = buttonHtml;
            const button = tempDiv.firstElementChild;
            
            hideConfirmationModal();
            await handleRemoveRecipe(button, parseInt(recipeId));
        }
    });
    
    // إغلاق عند النقر خارج الـ modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            hideConfirmationModal();
        }
    });
}

/**
 * إخفاء modal التأكيد
 */
function hideConfirmationModal() {
    const modal = document.getElementById('removeConfirmationModal');
    const modalContent = modal.querySelector('.bg-white');
    
    // تأثير الإغلاق
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

/**
 * يهيئ جميع أزرار إزالة الوصفات المصنوعة في الصفحة
 */
function initializeRemoveMadeButtons() {
    console.log('Initializing remove made buttons...');
    
    // البحث عن جميع أزرار إزالة الوصفات المصنوعة
    const removeMadeButtons = document.querySelectorAll('.remove-made-recipe-btn');
    console.log('Found remove made buttons:', removeMadeButtons.length);
    
    removeMadeButtons.forEach((button, index) => {
        console.log(`Initializing made button ${index + 1}:`, button);
        
        // منع إضافة مستمعين متعددين
        if (button.dataset.initialized === 'true') {
            console.log('Made button already initialized, skipping...');
            return;
        }
        
        // البحث عن معرف الوصفة في العنصر
        const recipeId = button.dataset.recipeId;
        console.log('Made Recipe ID:', recipeId);
        
        if (!recipeId) {
            console.log('No made recipe ID found, skipping...');
            return;
        }

        // إضافة مستمع النقر
        button.addEventListener('click', async (e) => {
            console.log('Remove made button clicked!');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // إظهار modal التأكيد للوصفات المصنوعة
            showMadeConfirmationModal(button, recipeId);
        });

        // تحديد أن الزر تم تهيئته
        button.dataset.initialized = 'true';
        console.log('Made button initialized successfully');
    });
}

/**
 * يتعامل مع عملية إزالة الوصفة من المصنوعة
 */
async function handleRemoveMadeRecipe(button, recipeId) {
    const recipeName = button.dataset.recipeName || 'الوصفة';
    
    // التحقق من تسجيل الدخول
    const userId = document.body.dataset.userId || document.querySelector('[data-user-id]')?.dataset.userId;
    if (!userId || userId === 'null' || userId === '') {
        showToast('يجب تسجيل الدخول لإزالة الوصفة', 'warning');
        window.location.href = '/login';
        return;
    }

    // تعطيل الزر أثناء التحميل
    button.disabled = true;
    button.classList.add('opacity-70');
    
    // تغيير النص والأيقونة أثناء التحميل
    const span = button.querySelector('span');
    const icon = button.querySelector('i');
    const originalText = span.textContent;
    const originalIcon = icon.className;
    
    span.textContent = 'جاري الإزالة...';
    icon.className = 'fas fa-spinner fa-spin ml-1';

    try {
        // تأكد من تهيئة كوكي CSRF
        await fetch('/sanctum/csrf-cookie', { credentials: 'include' });

        const csrf = getCsrfToken();
        const res = await fetch('/api/interactions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'include',
            body: JSON.stringify({
                recipe_id: recipeId,
                is_made: false, // إزالة من المصنوعة
            }),
        });

        if (!res.ok) {
            if (res.status === 401) {
                showToast('يجب تسجيل الدخول لإزالة الوصفة', 'warning');
                window.location.href = '/login';
                return;
            } else if (res.status === 419) {
                window.location.reload();
                return;
            } else {
                const txt = await res.text();
                console.error('Error body:', txt);
                showToast('حدث خطأ أثناء محاولة إزالة الوصفة. يرجى المحاولة مرة أخرى.', 'error');
                return;
            }
        }

        // تحليل الاستجابة
        const responseData = await res.json();
        console.log('Remove made request successful:', responseData);
        
        // إزالة الكارت من الواجهة
        const cardContainer = button.closest('.recipe-card');
        if (cardContainer) {
            // إضافة تأثير بصري للإزالة
            cardContainer.style.transition = 'all 0.3s ease';
            cardContainer.style.transform = 'scale(0.95)';
            cardContainer.style.opacity = '0.5';
            
            setTimeout(() => {
                cardContainer.remove();
                
                // التحقق من وجود وصفات أخرى
                const remainingCards = document.querySelectorAll('#made-tab .recipe-card');
                if (remainingCards.length === 0) {
                    // إظهار رسالة عدم وجود وصفات
                    const tabContent = document.getElementById('made-tab');
                    if (tabContent) {
                        tabContent.innerHTML = `
                            <div class="text-center py-12">
                                <i class="fas fa-check-circle text-gray-300 text-6xl mb-4"></i>
                                <h3 class="text-xl font-medium text-gray-500 mb-2">لا توجد وصفات مصنوعة</h3>
                                <p class="text-gray-400">ابدأ بصنع الوصفات وشاركنا تجربتك</p>
                            </div>
                        `;
                    }
                }
            }, 300);
        }
        
        // إظهار رسالة نجاح
        showToast(`تم إزالة "${recipeName}" من المصنوعة بنجاح!`, 'success');

    } catch (err) {
        console.error(err);
        showToast('حدث خطأ أثناء محاولة إزالة الوصفة.', 'error');
        
        // إعادة النص والأيقونة الأصليين
        span.textContent = originalText;
        icon.className = originalIcon;
        
    } finally {
        // إعادة تفعيل الزر
        button.disabled = false;
        button.classList.remove('opacity-70');
    }
}

/**
 * إظهار modal التأكيد للوصفات المصنوعة
 */
function showMadeConfirmationModal(button, recipeId) {
    const modal = document.getElementById('removeMadeConfirmationModal');
    const recipeName = button.dataset.recipeName || 'الوصفة';
    const recipeNameElement = document.getElementById('madeRecipeName');
    const confirmationMessage = document.getElementById('madeConfirmationMessage');
    
    // تحديث معلومات الوصفة
    recipeNameElement.textContent = recipeName;
    confirmationMessage.textContent = `هل أنت متأكد من إزالة "${recipeName}" من قائمة المصنوعة؟`;
    
    // إظهار الـ modal مع تأثير
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.bg-white').classList.remove('scale-95');
        modal.querySelector('.bg-white').classList.add('scale-100');
    }, 10);
    
    // حفظ البيانات للاستخدام لاحقاً
    modal.dataset.currentButton = button.outerHTML;
    modal.dataset.currentRecipeId = recipeId;
}

/**
 * تهيئة modal التأكيد للوصفات المصنوعة
 */
function initializeMadeConfirmationModal() {
    const modal = document.getElementById('removeMadeConfirmationModal');
    const cancelBtn = document.getElementById('cancelMadeRemove');
    const confirmBtn = document.getElementById('confirmMadeRemove');
    
    // إلغاء الإزالة
    cancelBtn.addEventListener('click', () => {
        hideMadeConfirmationModal();
    });
    
    // تأكيد الإزالة
    confirmBtn.addEventListener('click', async () => {
        const recipeId = modal.dataset.currentRecipeId;
        const buttonHtml = modal.dataset.currentButton;
        
        if (recipeId) {
            // إنشاء عنصر زر مؤقت للاستخدام
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = buttonHtml;
            const button = tempDiv.firstElementChild;
            
            hideMadeConfirmationModal();
            await handleRemoveMadeRecipe(button, parseInt(recipeId));
        }
    });
    
    // إغلاق عند النقر خارج الـ modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            hideMadeConfirmationModal();
        }
    });
}

/**
 * إخفاء modal التأكيد للوصفات المصنوعة
 */
function hideMadeConfirmationModal() {
    const modal = document.getElementById('removeMadeConfirmationModal');
    const modalContent = modal.querySelector('.bg-white');
    
    // تأثير الإغلاق
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
</script>
@endpush

@push('styles')
<style>
.tab-btn.active {
    border-bottom-color: #f97316 !important;
    color: #ea580c !important;
}

/* Enhanced recipe card animations */
.recipe-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.recipe-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Image overlay effects */
.recipe-image-overlay {
    background: linear-gradient(45deg, rgba(249, 115, 22, 0.1), rgba(16, 185, 129, 0.1));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.recipe-card:hover .recipe-image-overlay {
    opacity: 1;
}

/* Custom scrollbar for better UX */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(45deg, #f97316, #ea580c);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(45deg, #ea580c, #c2410c);
}

/* Enhanced button animations */
.btn-animated {
    position: relative;
    overflow: hidden;
}

.btn-animated::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-animated:hover::before {
    left: 100%;
}

/* Recipe card image loading animation */
.recipe-image {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Enhanced placeholder gradients */
.placeholder-gradient-orange {
    background: linear-gradient(135deg, #fed7aa 0%, #fdba74 25%, #fb923c 50%, #f97316 75%, #ea580c 100%);
    background-size: 400% 400%;
    animation: gradientShift 4s ease infinite;
    position: relative;
    overflow: hidden;
}

.placeholder-gradient-green {
    background: linear-gradient(135deg, #bbf7d0 0%, #86efac 25%, #4ade80 50%, #22c55e 75%, #16a34a 100%);
    background-size: 400% 400%;
    animation: gradientShift 4s ease infinite;
    position: relative;
    overflow: hidden;
}

/* Add floating animation to decorative circles */
.placeholder-gradient-orange .absolute,
.placeholder-gradient-green .absolute {
    animation: float 6s ease-in-out infinite;
}

.placeholder-gradient-orange .absolute:nth-child(2) {
    animation-delay: -1s;
}

.placeholder-gradient-orange .absolute:nth-child(3) {
    animation-delay: -2s;
}

.placeholder-gradient-orange .absolute:nth-child(4) {
    animation-delay: -3s;
}

.placeholder-gradient-green .absolute:nth-child(2) {
    animation-delay: -1.5s;
}

.placeholder-gradient-green .absolute:nth-child(3) {
    animation-delay: -2.5s;
}

.placeholder-gradient-green .absolute:nth-child(4) {
    animation-delay: -3.5s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) scale(1);
    }
    50% {
        transform: translateY(-10px) scale(1.1);
    }
}

@keyframes gradientShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* RTL support improvements */
[dir="rtl"] .recipe-card {
    text-align: right;
}

[dir="rtl"] .recipe-card .flex {
    flex-direction: row-reverse;
}

/* Placeholder fallback styling */
.placeholder-fallback {
    display: none;
}

.placeholder-fallback.show {
    display: flex !important;
}

/* Image loading states */
.recipe-image {
    transition: opacity 0.3s ease;
}

.recipe-image.loading {
    opacity: 0.7;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .recipe-card:hover {
        transform: translateY(-4px) scale(1.01);
    }
    
    .recipe-card .p-5 {
        padding: 1rem;
    }
}

/* Custom Confirmation Modal Styles */
#removeConfirmationModal .bg-white {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#removeConfirmationModal .bg-white.scale-95 {
    transform: scale(0.95);
    opacity: 0.8;
}

#removeConfirmationModal .bg-white.scale-100 {
    transform: scale(1);
    opacity: 1;
}

/* Remove button hover effects */
.remove-recipe-btn {
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.remove-recipe-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.remove-recipe-btn:active {
    transform: translateY(0);
}

/* Confirmation modal animations */
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

#removeConfirmationModal.show {
    animation: modalSlideIn 0.3s ease-out;
}

/* Made recipes remove button styles */
.remove-made-recipe-btn {
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.remove-made-recipe-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.remove-made-recipe-btn:active {
    transform: translateY(0);
}

/* Made recipes confirmation modal styles */
#removeMadeConfirmationModal .bg-white {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#removeMadeConfirmationModal .bg-white.scale-95 {
    transform: scale(0.95);
    opacity: 0.8;
}

#removeMadeConfirmationModal .bg-white.scale-100 {
    transform: scale(1);
    opacity: 1;
}
</style>
@endpush
@endsection

