@extends('layouts.app')

@section('title', 'أدوات الشيف الاحترافية - موقع وصفة')

@push('styles')
<style>
    .tool-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        max-width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .tool-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transform: translateY(-4px);
    }
    .rating-stars {
        color: #fbbf24;
    }
    .empty-rating {
        color: #d1d5db;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* تحسينات للهواتف */
    @media (max-width: 640px) {
        .tool-card {
            margin-bottom: 0.75rem;
            min-height: 280px;
        }
        
        .tool-card .p-3 {
            padding: 0.75rem;
        }
        
        .tool-card h3 {
            font-size: 0.75rem;
            line-height: 1.2rem;
            min-height: 3rem;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .tool-card .text-sm {
            font-size: 0.875rem;
        }
        
        .tool-card .text-lg {
            font-size: 1rem;
        }
        
        .tool-card .text-xl {
            font-size: 1.125rem;
        }
        
        .tool-card button {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }
        
        .tool-card .rating-stars {
            font-size: 0.625rem;
        }
    }
    
    /* تحسينات للشاشات المتوسطة */
    @media (min-width: 641px) and (max-width: 1024px) {
        .tool-card {
            margin-bottom: 1.25rem;
        }
    }
    
    /* تحسين الأزرار */
    .add-to-cart-btn:active {
        transform: scale(0.98);
    }
    
    /* تحسين أزرار Amazon */
    .tool-card a[href*="amazon"]:active {
        transform: scale(0.98);
    }
    
    .tool-card a[href*="amazon"]:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    
    /* تحسين الشارات */
    .category-badge {
        backdrop-filter: blur(10px);
        background: rgba(249, 115, 22, 0.9);
    }
    
    /* ضمان التناسق */
    .tool-card .p-4 {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    .tool-card h3 {
        min-height: 3.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }
    
    .tool-card .mt-auto {
        margin-top: auto;
    }
    
    
    /* ضمان التناسق على الهواتف */
    @media (max-width: 640px) {
        #tools-grid {
            gap: 0.75rem;
        }
        
        .tool-card {
            width: 100%;
            max-width: 100%;
        }
        
        .tool-card img {
            max-height: 120px;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <section class="bg-gradient-to-r from-amber-500 to-orange-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-extrabold mb-4">أدوات الشيف الاحترافية</h1>
            <p class="text-lg max-w-2xl mx-auto">
                اكتشف أفضل أدوات الشيف المحترفين لصنع أرقى الحلويات في منزلك
            </p>
        </div>
    </section>

    <!-- Categories Filter -->
    <section class="bg-white py-8 shadow-sm">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <!-- Cart Actions -->
                <div class="flex justify-center mb-6 px-4">
                    <a href="{{ route('saved.index') }}" 
                       class="px-4 sm:px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-all flex items-center justify-center text-sm sm:text-base">
                        <i class="fas fa-bookmark ml-2"></i>
                        عرض الأدوات المحفوظة
                        <span id="saved-count-badge" class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full mr-2 hidden">0</span>
                    </a>
                </div>
                
                <!-- Category Filters -->
                <div class="flex flex-wrap gap-2 sm:gap-3 justify-center px-4">
                    <button class="category-filter-btn px-3 sm:px-6 py-2 sm:py-3 bg-orange-500 text-white rounded-lg font-semibold transition-all active text-xs sm:text-sm" data-category="all">
                        جميع الفئات
                    </button>
                    @foreach($categories as $categoryName => $categoryTools)
                        <button class="category-filter-btn px-3 sm:px-6 py-2 sm:py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-orange-500 hover:text-white transition-all text-xs sm:text-sm" data-category="{{ $categoryName }}">
                            {{ $categoryName }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Tools Grid -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                <div id="tools-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
                    @foreach($tools as $tool)
                        <div class="tool-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group flex flex-col h-full" data-category="{{ $tool->category }}">
                            <!-- Image Section -->
                            <div class="relative bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-3 sm:p-4 flex-shrink-0" style="height: 140px;">
                                <img src="{{ $tool->image_url }}" 
                                     alt="{{ $tool->name }}" 
                                     class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300"
                                     onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                                
                                <!-- Category Badge -->
                                <div class="absolute top-2 right-2 bg-orange-500 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                    {{ $tool->category }}
                                </div>
                            </div>

                            <!-- Content Section -->
                <div class="p-3 sm:p-4 flex flex-col flex-grow">
                                <!-- Brand/Title -->
                    <h3 class="text-xs sm:text-sm font-bold text-gray-900 mb-2 line-clamp-4 leading-tight min-h-[3rem] sm:min-h-[3.5rem]">
                                    {{ $tool->name }}
                                </h3>

                                <!-- Rating -->
                    <div class="flex items-center mb-2 sm:mb-3">
                        <div class="flex rating-stars text-xs">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= round($tool->rating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                        <span class="text-xs text-gray-600 mr-2 font-medium">
                                        {{ $tool->rating }}
                                    </span>
                        <span class="text-xs text-gray-500 hidden sm:inline">
                                        ({{ rand(10, 2000) }})
                                    </span>
                                </div>

                                <!-- Price -->
                    <div class="text-sm sm:text-lg font-bold text-orange-600 mb-2 sm:mb-3">
                        {{ number_format($tool->price, 2) }} درهم إماراتي
                                </div>


                    <!-- Action Buttons - Always at bottom -->
                    <div class="w-full mt-auto space-y-2">
                                    @if($tool->amazon_url)
                                    <a href="{{ $tool->amazon_url }}" 
                                       target="_blank"
                                       class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 sm:py-2.5 px-3 sm:px-4 rounded-lg text-xs sm:text-sm flex items-center justify-center transition-all duration-300 hover:shadow-lg active:scale-95 group">
                                        <i class="fab fa-amazon ml-1 sm:ml-2 group-hover:scale-110 transition-transform duration-300"></i>
                                        <span>متابعة الشراء من Amazon</span>
                                        <i class="fas fa-external-link-alt mr-1 sm:mr-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                                    </a>
                                    @endif
                                    
                                    @if($tool->amazon_url || $tool->affiliate_url)
                                    <button class="save-for-later-btn w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 sm:py-2.5 px-3 sm:px-4 rounded-lg text-xs sm:text-sm flex items-center justify-center transition-all duration-300 hover:shadow-lg active:scale-95"
                                            data-tool-id="{{ $tool->id }}"
                                            data-tool-name="{{ $tool->name }}"
                                            data-tool-price="{{ $tool->price }}">
                                        <i class="fas fa-bookmark ml-1 sm:ml-2"></i>
                                        <span class="btn-text">حفظ للشراء لاحقاً</span>
                                        <i class="fas fa-spinner fa-spin ml-1 sm:ml-2 hidden loading-icon"></i>
                                    </button>
                                    @else
                                    <div class="w-full bg-gray-300 text-gray-600 font-semibold py-2 sm:py-2.5 px-3 sm:px-4 rounded-lg text-xs sm:text-sm flex items-center justify-center">
                                        <i class="fas fa-exclamation-circle ml-1 sm:ml-2"></i>
                                        غير متوفر
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Our Tools Section -->
    <section class="py-16 bg-gradient-to-br from-amber-50 to-orange-50">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">لماذا تختار أدوات الشيف الموصى بها؟</h2>
            <p class="text-gray-600 mb-12 max-w-3xl mx-auto">نحن نختار بعناية أفضل أدوات الشيف المحترفين التي تساعدك في صنع حلويات احترافية</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 rounded-full h-20 w-20 flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-star text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">جودة عالية</h3>
                    <p class="text-gray-600 text-center">جميع الأدوات مختارة بعناية من أفضل العلامات التجارية</p>
                </div>
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 rounded-full h-20 w-20 flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-shipping-fast text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">شحن سريع</h3>
                    <p class="text-gray-600 text-center">توصيل سريع وآمن لجميع أنحاء المملكة</p>
                </div>
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 rounded-full h-20 w-20 flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-shield-alt text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">ضمان الجودة</h3>
                    <p class="text-gray-600 text-center">ضمان شامل على جميع المنتجات مع خدمة عملاء متميزة</p>
                </div>
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 rounded-full h-20 w-20 flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-tools text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">أدوات الشيف المحترفين</h3>
                    <p class="text-gray-600 text-center">نفس الأدوات التي يستخدمها الشيفات المحترفون في أفضل المطاعم</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load saved status for all tools
    loadSavedStatus();
    
    // Load saved count for the badge
    loadSavedCount();
    
    // جعل الدالة متاحة عالمياً
    window.updateSavedCountUI = updateSavedCountUI;
    window.loadSavedCount = loadSavedCount;
    
    // Category Filter Functionality
    const categoryButtons = document.querySelectorAll('.category-filter-btn');
    const toolCards = document.querySelectorAll('.tool-card');

    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.dataset.category;
            
            // Update active button
            categoryButtons.forEach(btn => btn.classList.remove('active', 'bg-orange-500', 'text-white'));
            categoryButtons.forEach(btn => btn.classList.add('bg-gray-100', 'text-gray-700'));
            this.classList.add('active', 'bg-orange-500', 'text-white');
            this.classList.remove('bg-gray-100', 'text-gray-700');

            // Filter tools
            toolCards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.5s ease-in-out';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Load saved status for all tools
    function loadSavedStatus() {
        fetch('/saved/status')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update each tool's saved status
                    document.querySelectorAll('.save-for-later-btn').forEach(btn => {
                        const toolId = btn.dataset.toolId;
                        const isSaved = data.saved_tools.includes(parseInt(toolId));
                        
                        if (isSaved) {
                            btn.querySelector('.btn-text').textContent = 'محفوظ';
                            btn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                            btn.classList.add('bg-green-500', 'hover:bg-green-600', 'saved');
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error loading saved status:', error);
            });
    }

    // Update saved count UI
    function updateSavedCountUI(count) {
        // تحديث العداد في الهيدر
        const savedCountEl = document.getElementById('saved-count');
        if (savedCountEl) {
            if (count > 0) {
                savedCountEl.textContent = count;
                savedCountEl.classList.remove('hidden');
                console.log('Updated navbar counter to:', count);
            } else {
                savedCountEl.classList.add('hidden');
                console.log('Hidden navbar counter');
            }
        }
        
        // تحديث العداد في صفحة الأدوات
        const savedCountBadge = document.getElementById('saved-count-badge');
        if (savedCountBadge) {
            if (count > 0) {
                savedCountBadge.textContent = count;
                savedCountBadge.classList.remove('hidden');
            } else {
                savedCountBadge.classList.add('hidden');
            }
        }
    }

    // Load saved count for the badge
    function loadSavedCount() {
        console.log('Loading saved count from tools page...');
        fetch('/saved/count')
            .then(response => {
                console.log('Tools page response status:', response.status);
                if (response.status === 401) {
                    console.log('User not authenticated in tools page');
                    updateSavedCountUI(0);
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data) {
                    console.log('Tools page saved count data:', data);
                    updateSavedCountUI(data.count);
                    
                    // تحديث فوري للعداد في الهاتف المحمول
                    if (window.updateSavedCountUI) {
                        window.updateSavedCountUI(data.count);
                        console.log('Updated mobile saved count from tools page:', data.count);
                    }
                    
                    // تحديث فوري للعداد في الهاتف المحمول (السلة)
                    const mobileCartCountEl = document.getElementById('mobile-cart-count');
                    if (mobileCartCountEl) {
                        if (data.count > 0) {
                            mobileCartCountEl.textContent = data.count;
                            mobileCartCountEl.classList.remove('hidden');
                            console.log('Updated mobile cart count (saved tools) from tools page:', data.count);
                        } else {
                            mobileCartCountEl.classList.add('hidden');
                            console.log('Hidden mobile cart count (saved tools) from tools page');
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error loading saved count from tools page:', error);
                updateSavedCountUI(0);
            });
    }



    // Save for Later Functionality
    document.querySelectorAll('.save-for-later-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const toolId = this.dataset.toolId;
            const toolName = this.dataset.toolName;
            const toolPrice = this.dataset.toolPrice;
            
            // Check if item is already saved
            if (this.classList.contains('saved')) {
                // Item is saved, remove it
                removeFromSaved(this, toolId);
                return;
            }
            
            // Show loading state
            this.disabled = true;
            this.querySelector('.btn-text').textContent = 'جاري الحفظ...';
            this.querySelector('.loading-icon').classList.remove('hidden');
            
            // Save for later
            fetch('/saved/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    tool_id: toolId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success state - Item is now saved
                    this.querySelector('.btn-text').textContent = 'محفوظ';
                    this.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                    this.classList.add('bg-green-500', 'hover:bg-green-600');
                    this.classList.add('saved');
                    this.disabled = false;
                    
                    // Update saved count in header - تحديث فوري للعداد
                    if (window.updateSavedCountUI) {
                        const currentCount = parseInt(document.getElementById('saved-count')?.textContent || '0');
                        window.updateSavedCountUI(currentCount + 1);
                        console.log('Updated counter after adding item:', currentCount + 1);
                    } else if (window.loadSavedCount) {
                        window.loadSavedCount();
                    }
                    
                    // تحديث فوري للعداد في الهاتف المحمول (السلة)
                    const mobileCartCountEl = document.getElementById('mobile-cart-count');
                    if (mobileCartCountEl) {
                        const currentCartCount = parseInt(mobileCartCountEl.textContent || '0');
                        mobileCartCountEl.textContent = currentCartCount + 1;
                        mobileCartCountEl.classList.remove('hidden');
                        console.log('Updated mobile cart count (saved tools) after adding item:', currentCartCount + 1);
                    }
                    
                    // تحديث فوري للعداد في الهاتف المحمول (الأدوات المحفوظة)
                    const mobileSavedCountEl = document.getElementById('saved-count-mobile');
                    if (mobileSavedCountEl) {
                        const currentMobileCount = parseInt(mobileSavedCountEl.textContent || '0');
                        mobileSavedCountEl.textContent = currentMobileCount + 1;
                        mobileSavedCountEl.classList.remove('hidden');
                        console.log('Updated mobile saved count after adding item:', currentMobileCount + 1);
                    }
                    
                    // Show success animation on the button
                    this.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 200);
                    
                    // Show toast notification
                    showToast('تم حفظ المنتج للشراء لاحقاً!', 'success');
                    
                    // Hide loading icon
                    this.querySelector('.loading-icon').classList.add('hidden');
                } else {
                    // Show error state
                    this.querySelector('.btn-text').textContent = 'خطأ في الحفظ';
                    this.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                    this.classList.add('bg-red-500');
                    
                    showToast(data.message || 'حدث خطأ أثناء حفظ المنتج', 'error');
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        this.disabled = false;
                        this.querySelector('.btn-text').textContent = 'حفظ للشراء لاحقاً';
                        this.querySelector('.loading-icon').classList.add('hidden');
                        this.classList.remove('bg-red-500');
                        this.classList.add('bg-orange-500', 'hover:bg-orange-600');
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.disabled = false;
                this.querySelector('.btn-text').textContent = 'حفظ للشراء لاحقاً';
                this.querySelector('.loading-icon').classList.add('hidden');
                showToast('حدث خطأ أثناء حفظ المنتج', 'error');
            });
        });
    });

    // Remove from saved function
    function removeFromSaved(button, toolId) {
        // Show loading state
        button.disabled = true;
        button.querySelector('.btn-text').textContent = 'جاري الحذف...';
        button.querySelector('.loading-icon').classList.remove('hidden');
        
        // Remove from saved
        fetch('/saved/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                tool_id: toolId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success state - Item removed
                button.querySelector('.btn-text').textContent = 'حفظ للشراء لاحقاً';
                button.classList.remove('bg-green-500', 'hover:bg-green-600', 'saved');
                button.classList.add('bg-orange-500', 'hover:bg-orange-600');
                button.disabled = false;
                
                // Update saved count - تحديث فوري للعداد
                if (window.updateSavedCountUI) {
                    const currentCount = parseInt(document.getElementById('saved-count')?.textContent || '0');
                    window.updateSavedCountUI(Math.max(0, currentCount - 1));
                    console.log('Updated counter after removing item:', Math.max(0, currentCount - 1));
                } else if (window.loadSavedCount) {
                    window.loadSavedCount();
                }
                
                // تحديث فوري للعداد في الهاتف المحمول (السلة)
                const mobileCartCountEl = document.getElementById('mobile-cart-count');
                if (mobileCartCountEl) {
                    const currentCartCount = parseInt(mobileCartCountEl.textContent || '0');
                    const newCartCount = Math.max(0, currentCartCount - 1);
                    mobileCartCountEl.textContent = newCartCount;
                    if (newCartCount === 0) {
                        mobileCartCountEl.classList.add('hidden');
                    } else {
                        mobileCartCountEl.classList.remove('hidden');
                    }
                    console.log('Updated mobile cart count (saved tools) after removing item:', newCartCount);
                }
                
                // تحديث فوري للعداد في الهاتف المحمول (الأدوات المحفوظة)
                const mobileSavedCountEl = document.getElementById('saved-count-mobile');
                if (mobileSavedCountEl) {
                    const currentMobileCount = parseInt(mobileSavedCountEl.textContent || '0');
                    const newCount = Math.max(0, currentMobileCount - 1);
                    mobileSavedCountEl.textContent = newCount;
                    if (newCount === 0) {
                        mobileSavedCountEl.classList.add('hidden');
                    } else {
                        mobileSavedCountEl.classList.remove('hidden');
                    }
                    console.log('Updated mobile saved count after removing item:', newCount);
                }
                
                // Show success animation
                button.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    button.style.transform = 'scale(1)';
                }, 200);
                
                // Show success message
                showToast('تم حذف المنتج من المحفوظات!', 'success');
                
                // Hide loading icon
                button.querySelector('.loading-icon').classList.add('hidden');
            } else {
                throw new Error(data.message || 'حدث خطأ أثناء حذف المنتج');
            }
        })
        .catch(error => {
            console.error('Error removing from saved:', error);
            button.disabled = false;
            button.querySelector('.btn-text').textContent = 'محفوظ';
            button.querySelector('.loading-icon').classList.add('hidden');
            showToast('حدث خطأ أثناء حذف المنتج من المحفوظات', 'error');
        });
    }


    // Toast notification function
    function showToast(message, type = 'info') {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.toast');
        existingToasts.forEach(toast => toast.remove());
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium transition-all duration-300 transform translate-x-full`;
        
        // Set color based on type
        if (type === 'success') {
            toast.classList.add('bg-green-500');
        } else if (type === 'error') {
            toast.classList.add('bg-red-500');
        } else if (type === 'warning') {
            toast.classList.add('bg-yellow-500');
        } else {
            toast.classList.add('bg-blue-500');
        }
        
        toast.textContent = message;
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .cart-quantity {
            transition: all 0.3s ease;
            min-width: 20px;
            text-align: center;
        }
        
        .cart-quantity.animate-pulse {
            animation: pulse 1s ease-in-out;
        }
        
        .add-to-cart-btn:disabled {
            cursor: not-allowed;
            opacity: 0.9;
        }
        
        .add-to-cart-btn.bg-blue-500:hover {
            background-color: #3b82f6 !important;
        }
    `;
    document.head.appendChild(style);
});
</script>
@endpush
