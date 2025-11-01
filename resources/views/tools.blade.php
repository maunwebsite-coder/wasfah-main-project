@extends('layouts.app')

@section('title', 'أدوات الشيف الاحترافية - موقع وصفة')

@push('styles')
<style>
    .tools-hero {
        position: relative;
        overflow: hidden;
    }
    .tools-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0));
        pointer-events: none;
    }
    .tool-card {
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        border-radius: 1.125rem;
        border: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #ffffff 0%, #fff7ed 100%);
        box-shadow: 0 18px 28px rgba(15, 23, 42, 0.08);
        transition: transform 0.35s ease, box-shadow 0.35s ease;
        overflow: hidden;
    }
    .tool-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 26px 40px rgba(15, 23, 42, 0.12);
    }
    .tool-card__media {
        position: relative;
        aspect-ratio: 4 / 3;
        min-height: 210px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tool-slider {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    .tool-slider .swiper-wrapper {
        height: 100%;
    }
    .tool-slider .swiper-slide {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        height: 100%;
    }
    .tool-card__image {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        transition: transform 0.35s ease;
    }
    .tool-card:hover .tool-card__image {
        transform: scale(1.05);
    }
    .tool-card__badge {
        align-self: flex-end;
        margin: 0.9rem 1.25rem 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(249, 115, 22, 0.92);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.2);
    }
    .tool-card__content {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        flex-grow: 1;
        padding: 1.25rem;
        background: #fff;
    }
    .tool-card__title {
        color: #0f172a;
        font-weight: 700;
        line-height: 1.4;
        font-size: 1rem;
    }
    .tool-card__title span {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .tool-card__price {
        color: #f97316;
        font-weight: 700;
        font-size: 1.125rem;
    }
    .tool-card .rating-stars {
        color: #fbbf24;
        font-size: 0.875rem;
    }
    .tool-card .empty-rating {
        color: #d1d5db;
    }
    .tool-card__meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        row-gap: 0.4rem;
        gap: 0.5rem;
        color: #475569;
        font-size: 0.75rem;
    }
    .tool-card__meta > * {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .tool-card__actions {
        margin-top: auto;
        display: grid;
        gap: 0.5rem;
    }
    .tool-card__actions a,
    .tool-card__actions button {
        border-radius: 0.875rem;
        font-weight: 600;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .tool-card__actions a:active,
    .tool-card__actions button:active {
        transform: scale(0.97);
    }
    .tool-card__actions .amazon-btn {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
    }
    .tool-card__actions .amazon-btn:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.28);
    }
    .tool-slider .swiper-button-next,
    .tool-slider .swiper-button-prev {
        width: 28px;
        height: 28px;
        border-radius: 9999px;
        background: rgba(15, 23, 42, 0.4);
        color: #fff;
        top: 50%;
        transform: translateY(-50%);
    }
    .tool-slider .swiper-button-next::after,
    .tool-slider .swiper-button-prev::after {
        font-size: 12px;
        font-weight: bold;
    }
    .tool-slider .swiper-pagination-bullet {
        background-color: rgba(249, 115, 22, 0.85);
        opacity: 1;
        width: 6px;
        height: 6px;
    }
    .tool-slider .swiper-pagination-bullet-active {
        background-color: #f97316;
    }
    .tool-slider .swiper-button-disabled {
        opacity: 0.35 !important;
    }
    .tools-grid-wrapper {
        position: relative;
        z-index: 0;
    }
    .tools-grid-wrapper::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top, rgba(253, 230, 138, 0.35), transparent 65%);
        z-index: -1;
        opacity: 0.7;
    }
    .tools-filter {
        background: #fff;
        border-radius: 1.75rem;
        border: 1px solid rgba(249, 115, 22, 0.2);
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.12);
    }
    .category-filter-btn {
        transition: all 0.3s ease;
    }
    .category-filter-btn.active {
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.25);
    }
    .save-for-later-btn:disabled {
        opacity: 0.85;
        cursor: not-allowed;
    }
    #tools-grid {
        gap: 2rem;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    @media (max-width: 1024px) {
        .tool-card {
            border-radius: 1.25rem;
        }
        .tool-card__content {
            padding: 1rem 1.15rem 1.25rem;
        }
        #tools-grid {
            gap: 1.5rem;
        }
    }
    @media (max-width: 640px) {
        .tool-card {
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
        }
        .tool-card__media {
            aspect-ratio: 1 / 1;
            min-height: 200px;
        }
        .tool-card__title {
            font-size: 0.9rem;
        }
        .tool-card__price {
            font-size: 1rem;
        }
        .tools-filter {
            border-radius: 1.25rem;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.15);
        }
        #tools-grid {
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <section class="tools-hero relative bg-gradient-to-r from-amber-500 via-orange-500 to-orange-600 text-white py-20">
        <div class="container mx-auto px-4 text-center relative z-10">
            <h1 class="text-4xl sm:text-5xl font-extrabold mb-4">أدوات الشيف الاحترافية</h1>
            <p class="text-base sm:text-lg max-w-3xl mx-auto leading-relaxed">
                اكتشف مجموعة مختارة بعناية من أدوات الحلويات الموثوقة من أمازون لتطوير مهاراتك في المطبخ.
            </p>
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-4xl mx-auto text-sm">
                <div class="bg-white/15 border border-white/25 rounded-2xl px-5 py-4 shadow-lg backdrop-blur flex items-center justify-center">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-white/20">
                            <i class="fas fa-star text-lg"></i>
                        </span>
                        <span class="leading-6 text-right">ترشيحات مبنية على أعلى تقييمات أمازون</span>
                    </div>
                </div>
                <div class="bg-white/15 border border-white/25 rounded-2xl px-5 py-4 shadow-lg backdrop-blur flex items-center justify-center">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-white/20">
                            <i class="fas fa-book-open text-lg"></i>
                        </span>
                        <span class="leading-6 text-right">ملخصات سريعة لأهم المزايا قبل الشراء</span>
                    </div>
                </div>
                <div class="bg-white/15 border border-white/25 rounded-2xl px-5 py-4 shadow-lg backdrop-blur flex items-center justify-center">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-white/20">
                            <i class="fas fa-sync-alt text-lg"></i>
                        </span>
                        <span class="leading-6 text-right">تحديثات مستمرة لأحدث الإصدارات والعروض</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Filter -->
    <section class="relative -mt-12 lg:-mt-16">
        <div class="container mx-auto px-4">
            <div class="tools-filter max-w-6xl mx-auto overflow-hidden">
                <div class="px-6 sm:px-8 lg:px-10 py-8">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <a href="{{ route('saved.index') }}" 
                           class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-all text-sm sm:text-base">
                            <i class="fas fa-bookmark text-base"></i>
                            <span>عرض الأدوات المحفوظة</span>
                            <span id="saved-count-badge" class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full hidden">0</span>
                        </a>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <i class="fas fa-info-circle text-orange-500"></i>
                            <span>احفظ الأدوات التي تعجبك للرجوع إليها لاحقاً.</span>
                        </div>
                    </div>
                    
                    <!-- Category Filters -->
                    <div class="flex flex-wrap justify-center gap-3 sm:gap-4">
                        <button class="category-filter-btn px-4 sm:px-6 py-2.5 sm:py-3 bg-orange-500 text-white rounded-xl font-semibold transition-all active text-xs sm:text-sm" data-category="all">
                            جميع الفئات
                        </button>
                        @foreach($categories as $categoryName => $categoryTools)
                            <button class="category-filter-btn px-4 sm:px-6 py-2.5 sm:py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-orange-500 hover:text-white transition-all text-xs sm:text-sm" data-category="{{ $categoryName }}">
                                {{ $categoryName }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tools Grid -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="tools-grid-wrapper max-w-7xl mx-auto">
                <div id="tools-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($tools as $tool)
                        <div class="tool-card group" data-category="{{ $tool->category }}">
                            <!-- Image Section -->
                            @php
                                $galleryUrls = $tool->gallery_image_urls;
                                if (empty($galleryUrls)) {
                                    $galleryUrls = [$tool->image_url];
                                }
                            @endphp
                            <div class="tool-card__badge">{{ $tool->category }}</div>
                            
                            <div class="tool-card__media">
                                <div class="tool-slider swiper tool-swiper" data-tool-id="{{ $tool->id }}">
                                    <div class="swiper-wrapper">
                                        @foreach($galleryUrls as $image)
                                            <div class="swiper-slide">
                                                <img src="{{ $image }}"
                                                     alt="{{ $tool->name }}"
                                                     class="tool-card__image"
                                                     onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper-pagination"></div>
                                    <div class="swiper-button-prev"></div>
                                    <div class="swiper-button-next"></div>
                                </div>
                            </div>

                            <!-- Content Section -->
                            <div class="tool-card__content">
                                <h3 class="tool-card__title text-sm sm:text-base">
                                    <a href="{{ route('tools.show', $tool) }}" class="block hover:text-orange-500 transition-colors">
                                        <span>{{ $tool->name }}</span>
                                    </a>
                                </h3>

                                <div class="tool-card__meta justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="flex rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= round($tool->rating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                        <span class="text-xs font-semibold text-slate-600">
                                        {{ $tool->rating }}
                                    </span>
                                    </div>
                                    <span class="text-xs text-slate-400 hidden sm:inline-flex">
                                        ({{ rand(10, 2000) }})
                                    </span>
                                </div>

                                <!-- Price -->
                                <div class="tool-card__price text-sm sm:text-lg">
                                    {{ number_format($tool->price, 2) }} درهم إماراتي
                                </div>


                                <!-- Action Buttons -->
                                <div class="tool-card__actions">
                                    <a href="{{ route('tools.show', $tool) }}"
                                       class="w-full flex items-center justify-center gap-2 py-2.5 sm:py-3 px-4 text-xs sm:text-sm font-semibold text-orange-600 border border-orange-200 hover:bg-orange-50 rounded-xl transition-colors duration-300">
                                        <i class="fas fa-info-circle text-base"></i>
                                        <span>عرض التفاصيل الكاملة</span>
                                    </a>
                                    @if($tool->amazon_url)
                                    <a href="{{ $tool->amazon_url }}" 
                                       target="_blank"
                                       class="amazon-btn w-full flex items-center justify-center gap-2 py-2.5 sm:py-3 px-4 text-xs sm:text-sm">
                                        <i class="fab fa-amazon text-base"></i>
                                        <span>متابعة الشراء من Amazon</span>
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                    @endif
                                    
                                    @if($tool->amazon_url || $tool->affiliate_url)
                                    <button class="save-for-later-btn w-full bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold py-2.5 sm:py-3 px-4 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 transition-all duration-300"
                                            data-tool-id="{{ $tool->id }}"
                                            data-tool-name="{{ $tool->name }}"
                                            data-tool-price="{{ $tool->price }}">
                                        <i class="fas fa-bookmark text-base"></i>
                                        <span class="btn-text">حفظ للشراء لاحقاً</span>
                                        <i class="fas fa-spinner fa-spin hidden loading-icon text-sm"></i>
                                    </button>
                                    @else
                                    <div class="w-full bg-slate-100 text-slate-500 font-semibold py-2.5 px-4 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2">
                                        <i class="fas fa-exclamation-circle text-base"></i>
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
    <section class="py-16 bg-gradient-to-br from-amber-50 via-orange-50/70 to-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">لماذا نعرض أدوات الشيف الموصى بها؟</h2>
            <p class="text-gray-600 mb-12 max-w-3xl mx-auto">
                نعمل كمسوّقين بالعمولة مع أمازون، ونختار بعناية أدوات الحلويات الأعلى تقييماً لمساعدتك في العثور على المنتجات المناسبة دون عناء.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 rounded-full h-20 w-20 flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-check-circle text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">ترشيحات موثوقة</h3>
                    <p class="text-gray-600 text-center">نراجع تقييمات وآراء العملاء على أمازون لنرشح المنتجات الأعلى جودة.</p>
                </div>
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 rounded-full h-20 w-20 flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-book-reader text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">معلومات واضحة</h3>
                    <p class="text-gray-600 text-center">نلخّص أهم المواصفات لتتخذ قرار الشراء بثقة وفي وقت قصير.</p>
                </div>
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 rounded-full h-20 w-20 flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-sync text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">تحديثات مستمرة</h3>
                    <p class="text-gray-600 text-center">نحدّث القوائم باستمرار بحسب توفر المنتجات وأفضل العروض.</p>
                </div>
                <div class="flex flex-col items-center bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600 rounded-full h-20 w-20 flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-shopping-bag text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">شراء مباشر من أمازون</h3>
                    <p class="text-gray-600 text-center">إتمام الطلب يتم على موقع أمازون. نحن لا نوفر البيع أو الشحن بأنفسنا.</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper !== 'undefined') {
        document.querySelectorAll('.tool-swiper').forEach(function (el) {
            const slidesCount = el.querySelectorAll('.swiper-slide').length;
            const paginationEl = el.querySelector('.swiper-pagination');
            const nextEl = el.querySelector('.swiper-button-next');
            const prevEl = el.querySelector('.swiper-button-prev');

            const options = {
                slidesPerView: 1,
                spaceBetween: 8,
                loop: slidesCount > 1
            };

            if (slidesCount > 1 && paginationEl) {
                options.pagination = {
                    el: paginationEl,
                    clickable: true
                };
            } else if (paginationEl) {
                paginationEl.classList.add('hidden');
            }

            if (slidesCount > 1 && nextEl && prevEl) {
                options.navigation = {
                    nextEl,
                    prevEl
                };
            } else {
                if (nextEl) {
                    nextEl.classList.add('hidden');
                }
                if (prevEl) {
                    prevEl.classList.add('hidden');
                }
            }

            if (slidesCount > 1) {
                options.autoplay = {
                    delay: 4000,
                    disableOnInteraction: false
                };
            }

            new Swiper(el, options);
        });
    }

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
    const defaultSaveClasses = ['bg-gradient-to-r', 'from-orange-500', 'to-amber-500', 'hover:from-orange-600', 'hover:to-amber-600'];
    const savedStateClasses = ['bg-green-500', 'hover:bg-green-600', 'saved'];

    function applySavedButtonState(button) {
        button.classList.remove(...defaultSaveClasses, 'bg-red-500');
        button.classList.add(...savedStateClasses);
    }

    function applyDefaultButtonState(button) {
        button.classList.remove(...savedStateClasses, 'bg-red-500');
        defaultSaveClasses.forEach(cls => {
            if (!button.classList.contains(cls)) {
                button.classList.add(cls);
            }
        });
    }

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
                    card.style.removeProperty('display');
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
                            applySavedButtonState(btn);
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
                    applySavedButtonState(this);
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
                    this.classList.remove(...defaultSaveClasses, ...savedStateClasses);
                    this.classList.add('bg-red-500');
                    
                    showToast(data.message || 'حدث خطأ أثناء حفظ المنتج', 'error');
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        this.disabled = false;
                        this.querySelector('.btn-text').textContent = 'حفظ للشراء لاحقاً';
                        this.querySelector('.loading-icon').classList.add('hidden');
                        this.classList.remove('bg-red-500');
                        applyDefaultButtonState(this);
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
                applyDefaultButtonState(button);
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
