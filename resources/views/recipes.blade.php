@extends('layouts.app')

@section('title', 'جميع الوصفات - موقع وصفة')

@push('styles')
<style>
    .rating-stars {
        color: #fbbf24;
    }
    .empty-rating {
        color: #d1d5db;
    }
    
    /* Mobile Responsive Improvements */
    @media (max-width: 768px) {
        .recipes-header {
            padding: 2rem 0;
        }
        
        .recipes-title {
            font-size: 2rem;
        }
        
        .recipes-subtitle {
            font-size: 1rem;
        }
        
        .search-sort-section {
            padding: 1rem 0;
        }
        
        .search-form {
            margin-bottom: 1rem;
        }
        
        .search-input {
            font-size: 16px; /* Prevents zoom on iOS */
            padding: 0.75rem 1rem;
        }
        
        .sort-container {
            flex-direction: column;
            gap: 0.5rem;
            align-items: stretch;
        }
        
        .recipes-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .recipe-card {
            margin-bottom: 0;
        }
        
        .recipe-image {
            height: 200px;
        }
        
        .recipe-content {
            padding: 1rem;
        }
        
        .recipe-title {
            font-size: 1.1rem;
            line-height: 1.4;
        }
        
        .recipe-info {
            font-size: 0.8rem;
        }
        
        .recipe-actions {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .recipe-button {
            width: 100%;
            padding: 0.75rem;
            font-size: 0.9rem;
        }
        
        .pagination-container {
            padding: 1rem 0;
        }
        
        .pagination {
            flex-wrap: wrap;
            gap: 0.25rem;
        }
        
        .pagination-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
    }
    
    /* Ultra Simple Pagination */
    .pagination-container {
        margin-top: 1rem;
        text-align: center;
    }
    
    .pagination-info {
        margin-bottom: 0.5rem;
        color: #666;
        font-size: 0.875rem;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.25rem;
    }
    
    nav[role="navigation"] .relative.inline-flex {
        background: #f5f5f5;
        border: 1px solid #ddd;
        color: #333;
        text-decoration: none;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    nav[role="navigation"] .relative.inline-flex span {
        background: #f97316;
        color: white;
    }
    
    nav[role="navigation"] .relative.inline-flex[aria-disabled="true"] {
        background: #f0f0f0;
        color: #999;
    }
    }
    
    /* Mobile */
    @media (max-width: 768px) {
        .pagination {
            gap: 0.125rem;
        }
        
        nav[role="navigation"] .relative.inline-flex {
            padding: 0.375rem 0.5rem;
            font-size: 0.8rem;
        }
    }
    
    @media (max-width: 480px) {
        .recipes-title {
            font-size: 1.75rem;
        }
        
        .recipes-subtitle {
            font-size: 0.9rem;
        }
        
        .search-input {
            padding: 0.625rem 0.875rem;
            font-size: 16px;
        }
        
        .recipe-image {
            height: 180px;
        }
        
        .recipe-content {
            padding: 0.875rem;
        }
        
        .recipe-title {
            font-size: 1rem;
        }
        
        .recipe-info {
            font-size: 0.75rem;
        }
        
        .recipe-button {
            padding: 0.625rem;
            font-size: 0.85rem;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <section class="bg-gradient-to-r from-amber-500 to-orange-600 text-white py-16 recipes-header">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 recipes-title">جميع الوصفات</h1>
                <p class="text-xl text-amber-100 max-w-2xl mx-auto recipes-subtitle">
                    اكتشف مجموعة واسعة من وصفات الحلويات الفاخرة والراقية
                </p>
            </div>
        </div>
    </section>

    <!-- Simple Search and Sort -->
    <section class="bg-white py-6 border-b search-sort-section">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <!-- Search Bar -->
                    <form method="GET" action="{{ route('recipes') }}" class="flex-1 max-w-md search-form">
                        <div class="relative">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="ابحث عن وصفة..." 
                                class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 search-input"
                                dir="rtl"
                            >
                            <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Sort and Count -->
                    <div class="flex items-center gap-4 sort-container">
                        <form method="GET" action="{{ route('recipes') }}" class="flex items-center gap-2">
                            <!-- Preserve existing search parameters -->
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            @if(request('difficulty'))
                                <input type="hidden" name="difficulty" value="{{ request('difficulty') }}">
                            @endif
                            @if(request('prep_time'))
                                <input type="hidden" name="prep_time" value="{{ request('prep_time') }}">
                            @endif
                            
                            <span class="text-gray-600 text-sm">ترتيب:</span>
                            <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>الأحدث</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>التقييم</option>
                            </select>
                        </form>
                        <div class="text-gray-500 text-sm">
                            {{ $recipes->total() }} وصفة
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recipes Grid -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                @if($recipes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 recipes-grid">
                        @foreach($recipes as $recipe)
                            <div class="recipe-card bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-all duration-200">
                                <!-- Image Section -->
                                <div class="relative h-36 overflow-hidden recipe-image">
                                    <img src="{{ $recipe->image_url }}" 
                                         alt="{{ $recipe->title }}" 
                                         class="w-full h-full object-cover"
                                         onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                                    
                                    <!-- Category Badge -->
                                    <div class="absolute top-2 right-2 bg-orange-500 text-white px-2 py-1 rounded text-xs font-medium">
                                        {{ $recipe->category->name ?? 'وصفة' }}
                                    </div>
                                </div>

                                <!-- Content Section -->
                                <div class="p-3 recipe-content">
                                    <!-- Title -->
                                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2 recipe-title">
                                        {{ $recipe->title }}
                                    </h3>

                                    <!-- Recipe Info -->
                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-2 recipe-info">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock ml-1"></i>
                                            <span>{{ (int)$recipe->prep_time }} دقيقة</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-utensils ml-1"></i>
                                            <span>{{ (int)$recipe->servings }} أشخاص</span>
                                        </div>
                                    </div>

                                    <!-- Rating -->
                                    <div class="flex items-center justify-between mb-3 recipe-info">
                                        <div class="flex items-center">
                                            <div class="flex rating-stars text-xs">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= round($recipe->interactions_avg_rating ?? 0) ? '' : 'empty-rating' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-500 mr-1">
                                                {{ number_format($recipe->interactions_avg_rating ?? 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <i class="fas fa-bookmark ml-1"></i>
                                            <span>{{ $recipe->saved_count ?? 0 }}</span>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    @if($recipe->is_registration_closed)
                                        <button class="w-full bg-yellow-400 text-yellow-800 font-medium py-2 px-3 rounded text-sm cursor-not-allowed flex items-center justify-center">
                                            <i class="fas fa-clock ml-1"></i>
                                            انتهت مهلة الحجز
                                        </button>
                                    @else
                                        <a href="{{ route('recipe.show', $recipe->slug) }}" 
                                           class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-3 rounded text-sm transition-colors flex items-center justify-center recipe-button">
                                            <i class="fas fa-eye ml-1"></i>
                                            عرض الوصفة
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        <div class="pagination-info">
                            عرض {{ $recipes->firstItem() }} إلى {{ $recipes->lastItem() }} من {{ $recipes->total() }} نتيجة
                        </div>
                        <div class="pagination">
                            {{ $recipes->links('pagination.custom') }}
                        </div>
                    </div>
                @else
                    <!-- Loading Skeleton -->
                    <div id="recipes-loading-skeleton" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @for($i = 0; $i < 8; $i++)
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden animate-pulse">
                                <!-- Image Skeleton -->
                                <div class="h-36 bg-gray-300"></div>
                                
                                <!-- Content Skeleton -->
                                <div class="p-3">
                                    <!-- Title Skeleton -->
                                    <div class="h-4 bg-gray-300 rounded mb-2"></div>
                                    <div class="h-4 bg-gray-300 rounded w-3/4 mb-3"></div>
                                    
                                    <!-- Info Skeleton -->
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="h-3 bg-gray-300 rounded w-16"></div>
                                        <div class="h-3 bg-gray-300 rounded w-16"></div>
                                    </div>
                                    
                                    <!-- Rating Skeleton -->
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex space-x-1 rtl:space-x-reverse">
                                            <div class="h-3 w-3 bg-gray-300 rounded"></div>
                                            <div class="h-3 w-3 bg-gray-300 rounded"></div>
                                            <div class="h-3 w-3 bg-gray-300 rounded"></div>
                                            <div class="h-3 w-3 bg-gray-300 rounded"></div>
                                            <div class="h-3 w-3 bg-gray-300 rounded"></div>
                                        </div>
                                        <div class="h-3 bg-gray-300 rounded w-8"></div>
                                    </div>
                                    
                                    <!-- Button Skeleton -->
                                    <div class="h-8 bg-gray-300 rounded"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                    
                    <!-- Empty State (Hidden by default) -->
                    <div id="empty-state" class="text-center py-16 hidden">
                        <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-utensils text-4xl text-orange-500"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-700 mb-3">لا توجد وصفات متاحة</h3>
                        <p class="text-gray-500 mb-6">
                            @if(request('search'))
                                لم نجد وصفات تطابق البحث "{{ request('search') }}"
                            @else
                                لا توجد وصفات متاحة حالياً
                            @endif
                        </p>
                        @if(request('search'))
                            <a href="{{ route('recipes') }}" 
                               class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                                <i class="fas fa-refresh ml-2"></i>
                                عرض جميع الوصفات
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus search input
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.focus();
    }

    // Convert Google Drive URLs
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
    
    // Apply URL conversion to all images
    const images = document.querySelectorAll('img[src*="drive.google.com"]');
    images.forEach(function(img) {
        const originalSrc = img.src;
        const convertedSrc = convertGoogleDriveUrl(originalSrc);
        if (convertedSrc !== originalSrc) {
            img.src = convertedSrc;
        }
    });
});
</script>
@endpush
