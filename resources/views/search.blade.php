@extends('layouts.app')

@section('title', 'نتائج البحث - موقع وصفة')

@push('styles')
<style>
    .search-result-card {
        transition: box-shadow 0.3s ease;
    }
    .search-result-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .search-tab {
        transition: all 0.3s ease;
    }
    .search-tab.active {
        background-color: #f97316;
        color: white;
        border-color: #f97316;
    }
    .highlight {
        background-color: #fef3c7;
        padding: 2px 4px;
        border-radius: 4px;
    }
    
    /* Image Display Improvements */
    .result-card-image {
        width: 100%;
        height: 240px;
        object-fit: cover;
        object-position: center;
        transition: transform 0.3s ease;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    
    .search-result-card:hover .result-card-image {
        transform: scale(1.05);
    }
    
    /* Ensure images load properly */
    .result-card-image[src*="drive.google.com"] {
        background-color: #f3f4f6;
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%239ca3af"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>');
        background-repeat: no-repeat;
        background-position: center;
        background-size: 48px;
    }
    
    /* Mobile Responsive Improvements */
    @media (max-width: 768px) {
        .search-header {
            padding: 1rem 0;
        }
        
        .search-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .search-form {
            margin-bottom: 1rem;
        }
        
        .search-input {
            font-size: 16px; /* Prevents zoom on iOS */
            padding: 0.75rem 1rem;
        }
        
        .search-tabs-container {
            flex-direction: column;
            gap: 0.5rem;
            align-items: stretch;
        }
        
        .search-tab {
            text-align: center;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        
        .search-results-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .search-result-card {
            margin-bottom: 0;
        }
        
        .result-card-image {
            height: 200px;
            object-fit: cover;
            object-position: center;
        }
        
        .result-card-content {
            padding: 1rem;
        }
        
        .result-card-title {
            font-size: 1.1rem;
            line-height: 1.4;
        }
        
        .result-card-description {
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .result-card-actions {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .result-card-button {
            width: 100%;
            padding: 0.75rem;
            font-size: 0.9rem;
        }
        
        .rating-modal {
            margin: 1rem;
            max-width: calc(100vw - 2rem);
        }
        
        .star-rating-modal .star {
            font-size: 2rem;
        }
        
    }
    
    @media (max-width: 480px) {
        .search-title {
            font-size: 1.25rem;
        }
        
        .search-input {
            padding: 0.625rem 0.875rem;
            font-size: 16px;
        }
        
        .search-tab {
            padding: 0.625rem 0.875rem;
            font-size: 0.85rem;
        }
        
        .result-card-image {
            height: 180px;
            object-fit: cover;
            object-position: center;
        }
        
        .result-card-content {
            padding: 0.875rem;
        }
        
        .result-card-title {
            font-size: 1rem;
        }
        
        .result-card-description {
            font-size: 0.85rem;
        }
        
        .result-card-info {
            font-size: 0.8rem;
        }
        
        .star-rating-modal .star {
            font-size: 1.75rem;
        }
    }
    
    /* Rating Modal Styles */
    .star-rating-modal {
        unicode-bidi: bidi-override;
        direction: rtl;
        text-align: center;
        display: flex;
        justify-content: center;
        gap: 5px;
        flex-direction: row;
    }
    
    .star-rating-modal input {
        display: none;
    }
    
    .star-rating-modal label {
        display: inline-block;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        padding: 5px;
        position: relative;
    }
    
    .star-rating-modal .star {
        font-size: 2.5rem;
        color: #e5e7eb;
        display: block;
        transition: all 0.2s ease-in-out;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        line-height: 1;
    }
    
    .star-rating-modal input:checked ~ label .star {
        color: #eab308 !important;
        transform: scale(1.1);
        text-shadow: 0 2px 4px rgba(234, 179, 8, 0.3);
    }
    
    .star-rating-modal label:hover .star {
        color: #eab308 !important;
        transform: scale(1.1);
        text-shadow: 0 2px 4px rgba(234, 179, 8, 0.3);
    }
    
    .star-rating-modal label:hover ~ label .star {
        color: #eab308 !important;
        transform: scale(1.1);
        text-shadow: 0 2px 4px rgba(234, 179, 8, 0.3);
    }
    
    .star-rating-modal label:hover + label .star {
        color: #e5e7eb !important;
        transform: scale(1);
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .star-rating-modal label:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Search Header -->
    <section class="bg-white shadow-sm py-8 search-header">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-900 mb-4 search-title">نتائج البحث</h1>
                
                <!-- Search Results Display -->
                <div class="mb-6 search-form">
                    <h2 class="text-2xl font-bold text-gray-800 text-center py-4 border-b border-gray-200">
                        <i class="fas fa-search text-orange-500 ml-2"></i>
                        نتائج البحث عن: 
                        <span class="text-orange-600">"{{ $query }}"</span>
                    </h2>
                </div>
                
                <!-- Search Tabs -->
                <div class="flex flex-wrap items-center justify-center gap-4 mb-6 search-tabs-container">
                    <div class="flex flex-wrap gap-2 w-full md:w-auto">
                        <button type="button" 
                                data-type="all"
                                class="search-tab px-4 py-2 border border-gray-300 rounded-lg {{ $type === 'all' ? 'active' : '' }}">
                            <i class="fas fa-th-large ml-2"></i>
                            الكل (<span id="all-count">{{ $recipes->count() + $workshops->count() }}</span>)
                        </button>
                        <button type="button" 
                                data-type="recipes"
                                class="search-tab px-4 py-2 border border-gray-300 rounded-lg {{ $type === 'recipes' ? 'active' : '' }}">
                            <i class="fas fa-utensils ml-2"></i>
                            الوصفات (<span id="recipes-count">{{ $recipes->count() }}</span>)
                        </button>
                        <button type="button" 
                                data-type="workshops"
                                class="search-tab px-4 py-2 border border-gray-300 rounded-lg {{ $type === 'workshops' ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap ml-2"></i>
                            ورشات العمل (<span id="workshops-count">{{ $workshops->count() }}</span>)
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Search Results -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <!-- Results Container -->
                <div id="search-results-container">
                    @if($query)
                    @if($recipes->count() > 0 || $workshops->count() > 0)
                        <!-- Recipes Results -->
                        @if($recipes->count() > 0 && ($type === 'all' || $type === 'recipes'))
                            <div class="mb-12">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-utensils ml-3 text-orange-500"></i>
                                    الوصفات ({{ $recipes->count() }})
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 search-results-grid">
                                    @foreach($recipes as $recipe)
                                        <div class="search-result-card bg-white rounded-lg shadow-md overflow-hidden">
                                            <div class="relative">
                                                <img src="{{ $recipe->image_url }}" alt="{{ $recipe->title }}" class="result-card-image"
                                                     onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                                                <div class="absolute top-4 right-4 bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                                    {{ $recipe->category->name ?? 'وصفة' }}
                                                </div>
                                            </div>
                                            <div class="p-6 result-card-content">
                                                <h3 class="text-xl font-bold text-gray-900 mb-2 result-card-title">
                                                    {!! str_ireplace($query, '<span class="highlight">' . $query . '</span>', $recipe->title) !!}
                                                </h3>
                                                <p class="text-gray-600 mb-4 line-clamp-2 result-card-description">
                                                    {!! str_ireplace($query, '<span class="highlight">' . $query . '</span>', Str::limit($recipe->description, 100)) !!}
                                                </p>
                                                <div class="flex items-center justify-between mb-4 result-card-info">
                                                    <div class="flex items-center text-sm text-gray-500">
                                                        <i class="fas fa-user ml-1"></i>
                                                        <span>{{ $recipe->author ?? 'غير محدد' }}</span>
                                                    </div>
                                                    <div class="flex items-center text-sm text-gray-500">
                                                        <i class="fas fa-clock ml-1"></i>
                                                        <span>{{ (int)$recipe->prep_time }} دقيقة</span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between mb-4 result-card-info">
                                                    <div class="flex items-center text-sm text-gray-500">
                                                        <i class="fas fa-star text-yellow-400 ml-1"></i>
                                                        <span>{{ number_format($recipe->interactions_avg_rating ?? 0, 1) }}</span>
                                                        <span class="text-gray-400 mr-1">({{ $recipe->interactions_count ?? 0 }})</span>
                                                    </div>
                                                    <div class="flex items-center text-sm text-gray-500">
                                                        <i class="fas fa-bookmark ml-1"></i>
                                                        <span>{{ $recipe->saved_count ?? 0 }} حفظ</span>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2 result-card-actions">
                                                    @if($recipe->is_registration_closed)
                                                        <button class="flex-1 text-center bg-yellow-400 text-yellow-800 font-bold py-2 px-4 rounded-lg cursor-not-allowed">
                                                            <i class="fas fa-clock ml-1"></i>
                                                            انتهت مهلة الحجز
                                                        </button>
                                                    @else
                                                        <a href="{{ route('recipe.show', $recipe->slug) }}" 
                                                           class="flex-1 text-center bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition-colors result-card-button">
                                                            عرض الوصفة
                                                        </a>
                                                    @endif
                                                    <button class="rate-recipe-btn px-3 py-2 border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white rounded-lg transition-colors" 
                                                            data-recipe-id="{{ $recipe->recipe_id }}"
                                                            data-recipe-title="{{ $recipe->title }}">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Workshops Results -->
                        @if($workshops->count() > 0 && ($type === 'all' || $type === 'workshops'))
                            <div class="mb-12">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-graduation-cap ml-3 text-orange-500"></i>
                                    ورشات العمل ({{ $workshops->count() }})
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 search-results-grid">
                                    @foreach($workshops as $workshop)
                                        <div class="search-result-card bg-white rounded-lg shadow-md overflow-hidden">
                                            <div class="relative">
                                                <img src="{{ $workshop->image ? asset('storage/' . $workshop->image) : 'https://placehold.co/600x400/f87171/FFFFFF?text=ورشة' }}" alt="{{ $workshop->title }}" class="result-card-image"
                                                     onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                                                <div class="absolute top-4 right-4 bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                                    {{ $workshop->formatted_price }}
                                                </div>
                                                @if($workshop->is_featured)
                                                    <div class="absolute top-4 left-4 bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                                        مميز
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="p-6 result-card-content">
                                                <h3 class="text-xl font-bold text-gray-900 mb-2 result-card-title">
                                                    {!! str_ireplace($query, '<span class="highlight">' . $query . '</span>', $workshop->title) !!}
                                                </h3>
                                                <p class="text-gray-600 mb-4 line-clamp-2 result-card-description">
                                                    {!! str_ireplace($query, '<span class="highlight">' . $query . '</span>', Str::limit($workshop->description, 100)) !!}
                                                </p>
                                                <div class="flex items-center justify-between mb-4 result-card-info">
                                                    <div class="flex items-center text-sm text-gray-500">
                                                        <i class="fas fa-user ml-1"></i>
                                                        <span>{{ $workshop->instructor }}</span>
                                                    </div>
                                                    <div class="flex items-center text-sm text-gray-500">
                                                        <i class="fas fa-calendar ml-1"></i>
                                                        <span>{{ $workshop->start_date->format('Y-m-d') }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between mb-4 result-card-info">
                                                    <div class="flex items-center text-sm text-gray-500">
                                                        <i class="fas fa-map-marker-alt ml-1"></i>
                                                        <span>{{ $workshop->location }}</span>
                                                    </div>
                                                    <div class="flex items-center text-sm text-gray-500">
                                                        <i class="fas fa-star text-yellow-400 ml-1"></i>
                                                        <span>{{ number_format($workshop->rating ?? 0, 1) }}</span>
                                                        <span class="text-gray-400 mr-1">({{ $workshop->reviews_count ?? 0 }})</span>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2 result-card-actions">
                                                    <a href="{{ route('workshop.show', $workshop->slug) }}" 
                                                       class="flex-1 text-center bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition-colors result-card-button">
                                                        عرض الورشة
                                                    </a>
                                                    <button class="rate-workshop-btn px-3 py-2 border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white rounded-lg transition-colors" 
                                                            data-workshop-id="{{ $workshop->id }}"
                                                            data-workshop-title="{{ $workshop->title }}">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- No Results -->
                        <div class="text-center py-16">
                            <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-2xl font-semibold text-gray-600 mb-2">لم نجد نتائج لـ "{{ $query }}"</h3>
                            <p class="text-gray-500 mb-6">جرب البحث بكلمات مختلفة أو تحقق من الإملاء</p>
                            <div class="flex flex-wrap justify-center gap-4">
                                <a href="{{ route('home') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg transition-colors">
                                    الصفحة الرئيسية
                                </a>
                                <a href="{{ route('workshops') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors">
                                    ورشات العمل
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Loading Skeleton -->
                    <div id="search-loading-skeleton" class="space-y-12">
                        <!-- Recipes Loading Skeleton -->
                        <div>
                            <div class="h-8 bg-gray-300 rounded w-48 mb-6 animate-pulse"></div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @for($i = 0; $i < 6; $i++)
                                    <div class="bg-white rounded-xl shadow-lg overflow-hidden animate-pulse">
                                        <div class="h-48 bg-gray-300"></div>
                                        <div class="p-6">
                                            <div class="h-5 bg-gray-300 rounded mb-2"></div>
                                            <div class="h-5 bg-gray-300 rounded w-3/4 mb-4"></div>
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="h-4 bg-gray-300 rounded w-20"></div>
                                                <div class="h-4 bg-gray-300 rounded w-16"></div>
                                            </div>
                                            <div class="h-8 bg-gray-300 rounded"></div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        
                        <!-- Workshops Loading Skeleton -->
                        <div>
                            <div class="h-8 bg-gray-300 rounded w-48 mb-6 animate-pulse"></div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @for($i = 0; $i < 6; $i++)
                                    <div class="bg-white rounded-xl shadow-lg overflow-hidden animate-pulse">
                                        <div class="h-48 bg-gray-300"></div>
                                        <div class="p-6">
                                            <div class="h-5 bg-gray-300 rounded mb-2"></div>
                                            <div class="h-5 bg-gray-300 rounded w-3/4 mb-4"></div>
                                            <div class="h-3 bg-gray-300 rounded mb-1"></div>
                                            <div class="h-3 bg-gray-300 rounded w-2/3 mb-4"></div>
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="h-4 bg-gray-300 rounded w-20"></div>
                                                <div class="h-4 bg-gray-300 rounded w-16"></div>
                                            </div>
                                            <div class="h-8 bg-gray-300 rounded"></div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                    
                    <!-- Empty Search (Hidden by default) -->
                    <div id="empty-search" class="text-center py-16 hidden">
                        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-2xl font-semibold text-gray-600 mb-2">ابحث عن وصفة أو ورشة</h3>
                        <p class="text-gray-500 mb-6">استخدم مربع البحث أعلاه للعثور على ما تبحث عنه</p>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Rating Modal -->
<div id="rating-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 rating-modal">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">تقييم</h3>
            <button id="close-rating-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="text-center mb-6">
            <h4 id="rating-item-title" class="text-lg font-semibold text-gray-700 mb-2">...</h4>
            <p class="text-sm text-gray-500">كيف تقيم هذا العنصر؟</p>
        </div>
        
        <div class="flex flex-col items-center">
            <div class="star-rating-modal mb-4">
                <input type="radio" id="modal-star5" name="modal-rating" value="5" />
                <label for="modal-star5" title="5 نجوم">
                    <span class="star">★</span>
                </label>
                <input type="radio" id="modal-star4" name="modal-rating" value="4" />
                <label for="modal-star4" title="4 نجوم">
                    <span class="star">★</span>
                </label>
                <input type="radio" id="modal-star3" name="modal-rating" value="3" />
                <label for="modal-star3" title="3 نجوم">
                    <span class="star">★</span>
                </label>
                <input type="radio" id="modal-star2" name="modal-rating" value="2" />
                <label for="modal-star2" title="نجمتان">
                    <span class="star">★</span>
                </label>
                <input type="radio" id="modal-star1" name="modal-rating" value="1" />
                <label for="modal-star1" title="نجمة واحدة">
                    <span class="star">★</span>
                </label>
            </div>
            <p id="modal-rating-text" class="text-center text-gray-500 mb-4">
                الرجاء اختيار تقييم
            </p>
            <button id="submit-modal-rating-btn" class="w-full p-3 rounded-full font-semibold text-white bg-orange-500 hover:bg-orange-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-paper-plane ml-2"></i>
                أرسل التقييم
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // عناصر البحث - تم تحويلها لعرض النتائج فقط
    const resultsContainer = document.getElementById('search-results-container');
    const allCount = document.getElementById('all-count');
    const recipesCount = document.getElementById('recipes-count');
    const workshopsCount = document.getElementById('workshops-count');
    
    let currentType = '{{ $type }}';
    
    // لا نحتاج لتحديث العدادات ديناميكياً - يتم عرضها من الخادم
    console.log('Counts are displayed from server - no dynamic updates needed');
    console.log('Search results display - no input field needed');
    
    // Handle search tabs
    console.log('Setting up search tabs');
    document.querySelectorAll('.search-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const type = this.dataset.type;
            console.log('Search tab clicked:', type);
            currentType = type;
            
            // Update active tab
            document.querySelectorAll('.search-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // تصفية النتائج حسب النوع
            filterResultsByType(type);
        });
    });
    
    // دالة لتصفية النتائج حسب النوع - إعادة تحميل الصفحة
    function filterResultsByType(type) {
        console.log('Filtering results by type:', type);
        
        // إعادة تحميل الصفحة مع النوع الجديد
        const url = new URL(window.location);
        url.searchParams.set('type', type);
        window.location.href = url.toString();
    }
    
    // Advanced filters have been removed
    console.log('Advanced filters removed - no filter functionality needed');

    
    // Rating Modal Functionality
    const ratingModal = document.getElementById('rating-modal');
    const closeRatingModal = document.getElementById('close-rating-modal');
    const ratingItemTitle = document.getElementById('rating-item-title');
    const modalRatingText = document.getElementById('modal-rating-text');
    const submitModalRatingBtn = document.getElementById('submit-modal-rating-btn');
    const modalStarInputs = document.querySelectorAll('input[name="modal-rating"]');
    
    let currentRatingType = null; // 'recipe' or 'workshop'
    let currentItemId = null;
    let currentRating = null;
    
    // التحقق من وجود عناصر Rating Modal
    if (!ratingModal || !closeRatingModal || !ratingItemTitle || !modalRatingText || !submitModalRatingBtn) {
        console.warn('Some rating modal elements are missing');
    }
    
    // Recipe rating buttons
    document.querySelectorAll('.rate-recipe-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentRatingType = 'recipe';
            currentItemId = this.dataset.recipeId;
            if (ratingItemTitle) {
            ratingItemTitle.textContent = this.dataset.recipeTitle;
            }
            openRatingModal();
        });
    });
    
    // Workshop rating buttons
    document.querySelectorAll('.rate-workshop-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentRatingType = 'workshop';
            currentItemId = this.dataset.workshopId;
            if (ratingItemTitle) {
            ratingItemTitle.textContent = this.dataset.workshopTitle;
            }
            openRatingModal();
        });
    });
    
    // Close modal
    if (closeRatingModal) {
    closeRatingModal.addEventListener('click', closeModal);
    }
    if (ratingModal) {
    ratingModal.addEventListener('click', function(e) {
        if (e.target === ratingModal) {
            closeModal();
        }
    });
    }
    
    // Rating change handlers
    if (modalStarInputs && modalStarInputs.length > 0) {
    modalStarInputs.forEach(input => {
        input.addEventListener('change', function() {
            currentRating = parseInt(this.value);
            updateModalRatingText(currentRating);
            updateSubmitButton();
        });
        
        const label = input.nextElementSibling;
        if (label) {
            label.addEventListener('mouseenter', () => {
                highlightModalStars(parseInt(input.value));
            });
        }
    });
    }
    
    // Reset star highlight on mouse leave
    const starRatingModal = document.querySelector('.star-rating-modal');
    if (starRatingModal) {
        starRatingModal.addEventListener('mouseleave', () => {
        resetModalStarHighlight();
    });
    }
    
    // Submit rating
    if (submitModalRatingBtn) {
    submitModalRatingBtn.addEventListener('click', submitRating);
    }
    
    function openRatingModal() {
        if (!ratingModal || !modalRatingText) return;
        
        // Reset modal state
        currentRating = null;
        modalStarInputs.forEach(input => input.checked = false);
        modalRatingText.textContent = 'الرجاء اختيار تقييم';
        updateSubmitButton();
        
        ratingModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal() {
        if (ratingModal) {
        ratingModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        }
    }
    
    function updateModalRatingText(rating) {
        if (!modalRatingText) return;
        
        const ratingTexts = {
            1: 'مقبول',
            2: 'جيد',
            3: 'جيد جداً',
            4: 'ممتاز',
            5: 'رائع جداً'
        };
        
        modalRatingText.textContent = ratingTexts[rating] || 'الرجاء اختيار تقييم';
    }
    
    function updateSubmitButton() {
        if (!submitModalRatingBtn) return;
        
        if (currentRating) {
            submitModalRatingBtn.disabled = false;
            submitModalRatingBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            submitModalRatingBtn.disabled = true;
            submitModalRatingBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
    
    function highlightModalStars(rating) {
        if (!modalStarInputs || modalStarInputs.length === 0) return;
        
        modalStarInputs.forEach(input => {
            const label = input.nextElementSibling;
            const star = label?.querySelector('.star');
            if (star) {
                if (parseInt(input.value) <= rating) {
                    star.style.color = '#eab308';
                    star.style.transform = 'scale(1.1)';
                } else {
                    star.style.color = '#e5e7eb';
                    star.style.transform = 'scale(1)';
                }
            }
        });
    }
    
    function resetModalStarHighlight() {
        if (!modalStarInputs || modalStarInputs.length === 0) return;
        
        modalStarInputs.forEach(input => {
            const label = input.nextElementSibling;
            const star = label?.querySelector('.star');
            if (star) {
                if (input.checked) {
                    star.style.color = '#eab308';
                    star.style.transform = 'scale(1.1)';
                } else {
                    star.style.color = '#e5e7eb';
                    star.style.transform = 'scale(1)';
                }
            }
        });
    }
    
    async function submitRating() {
        if (!currentRating || !currentItemId || !submitModalRatingBtn) {
            showMessage('الرجاء اختيار تقييم قبل الإرسال', 'error');
            return;
        }
        
        // Set loading state
        submitModalRatingBtn.disabled = true;
        submitModalRatingBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري الإرسال...';
        
        try {
            let response;
            if (currentRatingType === 'recipe') {
                response = await fetch('/api/interactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        recipe_id: currentItemId,
                        rating: currentRating
                    })
                });
            } else if (currentRatingType === 'workshop') {
                // For workshops, we'll need to implement a similar API endpoint
                response = await fetch('/api/workshop-reviews', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        workshop_id: currentItemId,
                        rating: currentRating
                    })
                });
            }
            
            const result = await response.json();
            
            if (response.ok) {
                showMessage('تم إرسال التقييم بنجاح!', 'success');
                closeModal();
                // Optionally refresh the page or update the rating display
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                const errorMessage = result.message || 'حدث خطأ أثناء إرسال التقييم';
                throw new Error(errorMessage);
            }
            
        } catch (error) {
            console.error('خطأ في إرسال التقييم:', error);
            
            let errorMessage = 'حدث خطأ أثناء إرسال التقييم. يرجى المحاولة مرة أخرى.';
            
            if (error.message.includes('Unauthenticated') || error.message.includes('401')) {
                errorMessage = 'يجب تسجيل الدخول لتقييم هذا العنصر';
            } else if (error.message.includes('422') || error.message.includes('validation')) {
                errorMessage = 'تقييم غير صالح. يرجى اختيار تقييم من 1 إلى 5 نجوم';
            } else if (error.message.includes('404')) {
                errorMessage = 'العنصر غير موجود';
            }
            
            showMessage(errorMessage, 'error');
        } finally {
            // Reset button state
            if (submitModalRatingBtn) {
            submitModalRatingBtn.disabled = false;
            submitModalRatingBtn.innerHTML = '<i class="fas fa-paper-plane ml-2"></i>أرسل التقييم';
            }
        }
    }
    
    function showMessage(message, type = 'info') {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.rating-message');
        existingMessages.forEach(msg => msg.remove());
        
        // Create message element
        const messageEl = document.createElement('div');
        messageEl.className = `rating-message fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        
        const icon = type === 'success' ? 'fas fa-check-circle' :
                    type === 'error' ? 'fas fa-exclamation-triangle' :
                    'fas fa-info-circle';
        
        messageEl.innerHTML = `
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <i class="${icon}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(messageEl);
        
        // Show message
        setTimeout(() => {
            messageEl.classList.remove('translate-x-full');
        }, 100);
        
        // Hide message
        setTimeout(() => {
            messageEl.classList.add('translate-x-full');
            setTimeout(() => {
                if (messageEl.parentNode) {
                    messageEl.parentNode.removeChild(messageEl);
                }
            }, 300);
        }, 4000);
    }
    
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
    
    // معالجة روابط Google Drive في نتائج البحث
    function convertGoogleDriveUrl(url) {
        if (url.includes('drive.google.com/file/d/')) {
            const match = url.match(/\/file\/d\/([a-zA-Z0-9-_]+)/);
            if (match && match[1]) {
                return `https://lh3.googleusercontent.com/d/${match[1]}`;
            }
        }
        
        if (url.includes('drive.google.com') && url.includes('id=')) {
            const urlParams = new URLSearchParams(new URL(url).search);
            const fileId = urlParams.get('id');
            if (fileId) {
                return `https://lh3.googleusercontent.com/d/${fileId}`;
            }
        }
        
        return url;
    }
    
    // تطبيق التحويل على جميع الصور في نتائج البحث
    const searchImages = document.querySelectorAll('img[src*="drive.google.com"]');
    console.log('Found Google Drive images:', searchImages.length);
    searchImages.forEach(function(img) {
        const originalSrc = img.src;
        const convertedSrc = convertGoogleDriveUrl(originalSrc);
        if (convertedSrc !== originalSrc) {
            console.log('Converting Google Drive image:', originalSrc, 'to:', convertedSrc);
            img.src = convertedSrc;
        }
    });
    
    // إضافة معالجة إضافية للتأكد من أن البحث يعمل
    console.log('Search page JavaScript loaded successfully');
    console.log('Current query:', '{{ $query }}');
    console.log('Current type:', '{{ $type }}');
    
    // عرض النتائج الحالية فقط - لا توجد تصفية ديناميكية
    console.log('Displaying current search results');
    console.log('Current query:', '{{ $query }}');
    console.log('Current type:', '{{ $type }}');
    
    // تم تحويل حقل البحث إلى عرض النتائج فقط
    console.log('Search results display - no click handler needed');
});
</script>
@endpush

