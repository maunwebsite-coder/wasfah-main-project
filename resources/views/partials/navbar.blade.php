@php
    $navCopy = \Illuminate\Support\Facades\Lang::get('navbar');
    $notificationDropdownCopy = $navCopy['notifications_dropdown'] ?? [];
    $currentLocale = $currentLocale ?? app()->getLocale();
    $alternateLocale = $currentLocale === 'ar' ? 'en' : 'ar';
    $showNavbarSearch = $showNavbarSearch ?? true;
    $mobileBannerCopy = $navCopy['mobile_banner'] ?? [];
    $mobileSections = $navCopy['mobile_sections'] ?? [];
    $mobileDescriptionsFallback = $navCopy['mobile_descriptions_default'] ?? 'وصول فوري لأبرز أقسام وصفة';
    $chefLinkLabels = $navCopy['chef_links'] ?? [];
    $primaryLinks = [
        ['route' => 'home', 'icon' => 'fas fa-house', 'label' => $navCopy['links']['home']],
        ['route' => 'recipes', 'icon' => 'fas fa-utensils', 'label' => $navCopy['links']['recipes']],
        ['route' => 'workshops', 'icon' => 'fas fa-graduation-cap', 'label' => $navCopy['links']['workshops']],
        ['route' => 'tools', 'icon' => 'fas fa-kitchen-set', 'label' => $navCopy['links']['tools']],
    ];
    $mobileLinkDescriptions = $navCopy['mobile_descriptions'];
    $accountMenuCopy = $navCopy['account_menu'] ?? [];
    $accountMenuLinks = $accountMenuCopy['links'] ?? [];
    $authUser = Auth::user();
    $guestPlaceholder = $mobileBannerCopy['guest'] ?? 'ضيفنا العزيز';
    $mobileGreeting = __('navbar.mobile_banner.greeting', ['name' => $authUser?->name ?? $guestPlaceholder]);
    $chefLinkData = null;
    if ($authUser) {
        if ($authUser->isChef()) {
            $chefLinkData = [
                'route' => route('chef.dashboard'),
                'icon' => 'fas fa-tachometer-alt',
                'label' => data_get($chefLinkLabels, 'dashboard', 'لوحة الشيف'),
            ];
        } elseif ($authUser->role !== \App\Models\User::ROLE_CHEF) {
            $chefLinkData = [
                'route' => route('onboarding.show'),
                'icon' => 'fas fa-hat-chef',
                'label' => data_get($chefLinkLabels, 'apply', 'التقديم كـ شيف'),
            ];
        } elseif ($authUser->needsChefProfile()) {
            if ($authUser->chef_status === \App\Models\User::CHEF_STATUS_PENDING) {
                $chefLinkData = [
                    'route' => route('onboarding.show'),
                    'icon' => 'fas fa-hourglass-half',
                    'label' => data_get($chefLinkLabels, 'track_request', 'متابعة حالة الطلب'),
                ];
            } else {
                $chefLinkData = [
                    'route' => route('onboarding.show'),
                    'icon' => 'fas fa-clipboard-list',
                    'label' => data_get($chefLinkLabels, 'complete_profile', 'إكمال بيانات الشيف'),
                ];
            }
        }
    }
@endphp

<header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-orange-100 shadow-sm">
    <div class="h-1 w-full bg-gradient-to-l from-orange-500 via-rose-500 to-amber-400 hidden md:block"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 py-3 md:py-4 header-container">
            <div class="flex items-center gap-4 min-w-0">
                <a href="{{ route('home') }}" class="flex items-center gap-3 text-slate-800">
                    <img src="{{ asset('image/logo.png') }}" alt="Logo" class="h-12 w-auto inline header-logo">
                    <span class="hidden md:inline text-xl font-bold tracking-tight"></span>
                </a>

                <nav class="hidden lg:flex items-center gap-1 text-sm font-medium text-slate-600" aria-label="{{ $navCopy['primary_nav_label'] }}">
                    @foreach ($primaryLinks as $link)
                        @php $active = request()->routeIs($link['route'] . '*'); @endphp
                        <a href="{{ route($link['route']) }}"
                           class="flex items-center gap-2 px-4 py-2 rounded-full transition-all duration-200 {{ $active ? 'bg-orange-500/10 text-orange-600 shadow-sm border border-orange-200' : 'hover:text-orange-600 hover:bg-orange-50 border border-transparent' }}">
                            <i class="{{ $link['icon'] }} text-base"></i>
                            <span>{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="flex items-center gap-2 md:hidden mobile-menu-btn">
                @if($showNavbarSearch)
                    <button id="mobileSearchBtn" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition-all duration-200 hover:border-orange-300 hover:text-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="sr-only">{{ $navCopy['search']['open'] }}</span>
                    </button>
                @endif

                <a href="{{ route('saved.index') }}" class="relative flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition-all duration-200 hover:border-orange-300 hover:text-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
                    </svg>
                    <span id="mobile-cart-count" class="absolute -top-1 -right-1 bg-orange-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px]">0</span>
                    <span class="sr-only">{{ $navCopy['saved']['sr'] }}</span>
                </a>

                @auth
                    <a href="{{ route('notifications.index') }}" class="relative flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition-all duration-200 hover:border-orange-300 hover:text-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <i class="fas fa-bell text-base"></i>
                        <span id="mobile-notification-count" data-notification-badge aria-live="polite" aria-atomic="true" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px]" aria-hidden="true">0</span>
                        <span class="sr-only">الإشعارات</span>
                    </a>
                @endauth

                <button id="mobileMenuBtn" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition-all duration-200 hover:border-orange-300 hover:text-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200">
                    <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                    <svg id="close-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="sr-only">{{ $navCopy['menu']['toggle'] }}</span>
                </button>
            </div>

            <div class="hidden md:flex flex-1 items-center justify-end gap-5 text-slate-600 header-nav">
                @if($showNavbarSearch)
                    <div id="search-container" data-expanded="false" class="navbar-search relative flex-1 max-w-xl group">
                        <input id="search-input"
                               dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
                               type="text"
                               placeholder="{{ $navCopy['search']['placeholder'] }}"
                               value="{{ old('q', request('q')) }}"
                               autocomplete="off"
                               spellcheck="false"
                               class="navbar-search-input w-full rounded-full border border-slate-200 bg-slate-50 pr-12 pl-5 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 shadow-inner transition-all duration-200 focus:border-orange-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-300 group-hover:bg-white">
                        <button id="search-submit"
                                type="button"
                                aria-controls="search-input"
                                aria-expanded="false"
                                class="navbar-search-button flex h-11 w-11 items-center justify-center rounded-full border border-transparent bg-white text-slate-500 shadow-sm transition-colors duration-200 hover:text-orange-500 hover:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span class="sr-only">{{ $navCopy['search']['open'] }}</span>
                        </button>
                    </div>
                @endif

                <nav class="flex items-center gap-2 text-sm font-medium text-slate-600" aria-label="{{ $navCopy['account_nav_label'] }}">
                    <a href="{{ route('saved.index') }}" class="relative flex items-center gap-2 rounded-full border border-transparent bg-white px-3 py-2 text-slate-500 shadow-sm transition-all duration-200 hover:border-orange-200 hover:bg-orange-50 hover:text-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <i class="fas fa-bookmark text-base"></i>
                        <span class="hidden xl:inline">{{ $navCopy['saved']['label'] }}</span>
                        <span id="saved-count" class="absolute -top-1 -right-1 bg-orange-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px]">0</span>
                    </a>

                    <a href="{{ route('partnership') }}" class="hidden lg:inline-flex items-center gap-2 rounded-full border border-orange-200 bg-orange-50 px-4 py-2 font-semibold text-orange-600 transition-all duration-200 hover:border-orange-300 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <i class="fas fa-handshake-angle text-base"></i>
                        <span>{{ $navCopy['partnership_cta'] }}</span>
                    </a>

                    @auth
                        <div class="relative notification-container" id="notification-container">
                            <button id="notification-bell" class="relative flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition-all duration-200 hover:border-orange-300 hover:text-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200" onclick="toggleNotificationDropdown()" aria-expanded="false" aria-haspopup="true">
                                <i class="fas fa-bell text-base"></i>
                                <span id="notification-count" data-notification-badge aria-live="polite" aria-atomic="true" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px]" aria-hidden="true">0</span>
                                <span class="sr-only">عرض الإشعارات</span>
                            </button>

                            <div id="notification-dropdown" class="absolute right-0 top-full mt-4 w-[22rem] overflow-hidden rounded-[32px] border border-white/60 bg-white/95 text-sm text-slate-600 shadow-[0_35px_60px_rgba(15,23,42,0.18)] ring-1 ring-black/5 backdrop-blur-xl z-50 hidden">
                                <div class="relative overflow-hidden border-b border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-5 text-white">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="space-y-1">
                                            <p class="text-[11px] font-semibold uppercase tracking-[0.4em] text-white/50">{{ data_get($notificationDropdownCopy, 'title', 'الإشعارات') }}</p>
                                            <p class="text-base font-bold leading-snug">{{ data_get($notificationDropdownCopy, 'subtitle', 'كل تنبيهات حجوزاتك في مكان واحد') }}</p>
                                            <p id="notification-unread-summary" class="text-xs text-white/70">{{ data_get($notificationDropdownCopy, 'unread_summary.zero', 'لا إشعارات جديدة') }}</p>
                                        </div>
                                        <div class="flex flex-col gap-2 text-[11px] font-semibold">
                                            <button id="mark-all-read-btn" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/40 bg-white/10 px-3 py-1 text-white transition hover:bg-white/20">
                                                <i class="fas fa-check-double text-xs"></i>
                                                {{ data_get($notificationDropdownCopy, 'mark_all', 'تحديد الكل كمقروء') }}
                                            </button>
                                            <a href="{{ route('notifications.index') }}" class="inline-flex items-center gap-1 text-white/80 transition hover:text-white">
                                                <span>{{ data_get($notificationDropdownCopy, 'view_all_short', 'عرض الكل') }}</span>
                                                <i class="fas {{ app()->isLocale('ar') ? 'fa-arrow-left' : 'fa-arrow-right' }} text-xs"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div id="notification-list" class="notification-scroll max-h-80 space-y-3 overflow-y-auto bg-white px-4 py-4">
                                    <div class="rounded-2xl border border-dashed border-orange-200/60 bg-orange-50/60 px-5 py-6 text-center text-slate-500 shadow-inner">
                                        <i class="fas fa-spinner fa-spin text-xl mb-2 text-orange-500"></i>
                                        <p>{{ data_get($notificationDropdownCopy, 'loading', 'جاري تحميل الإشعارات...') }}</p>
                                    </div>
                                </div>
                                <div class="border-t border-slate-100 bg-white/90 px-4 py-3">
                                    <a href="{{ route('notifications.index') }}" class="flex items-center justify-center gap-2 text-sm font-semibold text-orange-600 transition hover:text-orange-700">
                                        <span>{{ data_get($notificationDropdownCopy, 'view_all', 'عرض جميع الإشعارات') }}</span>
                                        <i class="fas {{ app()->isLocale('ar') ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div id="user-menu-container" class="relative user-menu-container">
                            <button
                                id="user-menu-button"
                                type="button"
                                class="group flex items-center gap-3 rounded-full border border-orange-100 bg-orange-50/80 px-4 py-2 text-right text-slate-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-orange-200 hover:bg-white focus:outline-none focus:ring-2 focus:ring-orange-200"
                                aria-haspopup="true"
                                aria-expanded="false"
                                aria-controls="user-menu-dropdown">
                                <div class="flex flex-col leading-tight">
                                    <span class="text-[11px] font-medium text-orange-500">{{ data_get($accountMenuCopy, 'greeting', 'أهلاً بك') }}</span>
                                    <span class="text-sm font-semibold">{{ $authUser->name }}</span>
                                </div>
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-orange-500 shadow-inner">
                                    <i class="fas fa-chevron-down text-xs transition"></i>
                                </span>
                            </button>
                            <div
                                id="user-menu-dropdown"
                                class="absolute right-0 mt-3 hidden w-64 rounded-3xl border border-orange-100 bg-white/95 pb-2 text-sm text-slate-600 shadow-2xl backdrop-blur-md"
                                role="menu"
                                aria-labelledby="user-menu-button">
                                <div class="border-b border-slate-100 px-5 pt-5 pb-3">
                                    <p class="text-xs text-slate-400">{{ data_get($accountMenuCopy, 'manage', 'إدارة حسابك') }}</p>
                                    <p class="text-base font-semibold text-slate-800">{{ $authUser->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $authUser->email ?? '' }}</p>
                                </div>
                                <div class="flex flex-col gap-1 px-2 pt-2">
                                    <a href="{{ route('profile') }}" class="flex items-center gap-3 rounded-2xl px-4 py-2 transition hover:bg-orange-50 hover:text-orange-600" role="menuitem">
                                        <i class="fas fa-user text-orange-500"></i>
                                        <span>{{ data_get($accountMenuLinks, 'profile', 'ملفي الشخصي') }}</span>
                                    </a>
                                    <a href="{{ route('bookings.index') }}" class="flex items-center gap-3 rounded-2xl px-4 py-2 transition hover:bg-orange-50 hover:text-orange-600" role="menuitem">
                                        <i class="fas fa-calendar-check text-orange-500"></i>
                                        <span>{{ data_get($accountMenuLinks, 'bookings', 'حجوزاتي') }}</span>
                                    </a>
                                    @if($chefLinkData)
                                        <a href="{{ $chefLinkData['route'] }}" class="flex items-center gap-3 rounded-2xl px-4 py-2 transition hover:bg-orange-50 hover:text-orange-600" role="menuitem">
                                            <i class="{{ $chefLinkData['icon'] }} text-orange-500"></i>
                                            <span>{{ $chefLinkData['label'] }}</span>
                                        </a>
                                    @endif
                                    @if($authUser->isAdmin())
                                        <a href="{{ route('admin.admin-area') }}" class="flex items-center gap-3 rounded-2xl px-4 py-2 transition hover:bg-orange-50 hover:text-orange-600" role="menuitem">
                                            <i class="fas fa-crown text-orange-500"></i>
                                            <span>{{ data_get($accountMenuLinks, 'admin', 'منطقة الإدمن') }}</span>
                                        </a>
                                        <a href="{{ route('admin.finance.dashboard') }}" class="flex items-center gap-3 rounded-2xl px-4 py-2 transition hover:bg-orange-50 hover:text-orange-600" role="menuitem">
                                            <i class="fas fa-file-invoice-dollar text-orange-500"></i>
                                            <span>{{ data_get($accountMenuLinks, 'finance', 'النظام المالي') }}</span>
                                        </a>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-slate-100 pt-2">
                                    @csrf
                                    <button id="logout-btn" type="submit" class="flex w-full items-center gap-3 rounded-2xl px-4 py-2 text-left transition hover:bg-orange-50 hover:text-orange-600" role="menuitem">
                                        <i class="fas fa-sign-out-alt text-orange-500"></i>
                                        <span>{{ data_get($accountMenuLinks, 'logout', 'تسجيل الخروج') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth

                    @if($authUser)
                        @if($authUser->isReferralPartner())
                            <a href="{{ route('referrals.dashboard') }}" class="rounded-full border border-emerald-200 px-4 py-2 text-emerald-600 transition-all duration-200 hover:border-emerald-300 hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                                <i class="fas fa-link ml-2"></i>
                                برنامج الشركاء
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="rounded-full border border-transparent bg-orange-500 px-4 py-2 text-white shadow-sm transition-all duration-200 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-200">تسجيل الدخول</a>
                        <a href="{{ route('register') }}" class="rounded-full border border-orange-200 px-4 py-2 text-orange-600 transition-all duration-200 hover:border-orange-300 hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-200">إنشاء حساب</a>
                    @endif
                </nav>
            </div>
        </div>
    </div>

    <div id="mobileMenu" class="mobile-menu hidden border-t border-orange-100 bg-white/95 shadow-lg backdrop-blur md:hidden" style="display: none;">
        <nav class="flex flex-col gap-6 p-5 text-slate-800" aria-label="{{ $navCopy['mobile_nav_label'] }}">
            <div class="rounded-3xl bg-gradient-to-l from-orange-500 via-rose-500 to-amber-400 p-4 text-white shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 shadow-inner">
                        <i class="fas fa-hat-chef text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-white/80">{{ $mobileGreeting }}</p>
                        <p class="text-lg font-black leading-snug">{{ $mobileBannerCopy['tagline'] ?? 'جاهز لتجربة نكهات جديدة؟' }}</p>
                    </div>
                </div>
                <p class="mt-3 text-sm text-white/90">{{ $mobileBannerCopy['subtitle'] ?? 'تصفح كل ما يهمك من وصفات، أدوات وورش في ثوانٍ.' }}</p>
            </div>

            <section class="space-y-3">
                <div class="flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    <span>{{ data_get($mobileSections, 'quick_nav', 'التصفح السريع') }}</span>
                    <span class="h-px flex-1 bg-slate-200"></span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($primaryLinks as $link)
                        @php $active = request()->routeIs($link['route'] . '*'); @endphp
                        <a href="{{ route($link['route']) }}"
                           class="group flex h-full flex-col justify-between rounded-2xl border p-4 text-sm transition-all duration-200 {{ $active ? 'border-orange-300 bg-orange-50/80 shadow-sm' : 'border-slate-100 bg-white hover:border-orange-200 hover:shadow-md' }}">
                            <div class="flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-orange-100 text-orange-600 shadow-inner">
                                    <i class="{{ $link['icon'] }}"></i>
                                </span>
                                <span class="font-semibold text-slate-900">{{ $link['label'] }}</span>
                            </div>
                            <p class="mt-3 text-xs leading-5 text-slate-500">
                                {{ $mobileLinkDescriptions[$link['route']] ?? $mobileDescriptionsFallback }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-orange-100 bg-orange-50/60 p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-orange-500 shadow-sm">
                        <i class="fas fa-handshake-angle"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-orange-600">{{ data_get($mobileSections, 'partners.title', 'حلول الشركاء') }}</p>
                        <p class="text-xs text-slate-500">{{ data_get($mobileSections, 'partners.subtitle', 'عرض متكامل للتعاون مع وصفة.') }}</p>
                    </div>
                </div>
                <p class="mt-3 text-sm text-slate-600">{{ data_get($mobileSections, 'partners.description', 'اطلع على الأرقام، النماذج، وخطوات الشراكة في ملف واحد.') }}</p>
                <a href="{{ route('partnership') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-2xl bg-orange-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-orange-600">
                    {{ data_get($mobileSections, 'partners.cta', 'اكتشف ملف الشراكات') }}
                    <i class="fas fa-arrow-left ml-2 text-xs"></i>
                </a>
            </section>

            <section class="rounded-2xl border border-slate-100 bg-white/90 p-4 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-base font-semibold text-slate-900">{{ data_get($mobileSections, 'tool_hub.title', 'مركز أدواتك') }}</p>
                    <span class="text-xs font-medium text-slate-400">{{ data_get($mobileSections, 'tool_hub.subtitle', 'تابع نشاطك بسرعة') }}</span>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <a href="{{ route('saved.index') }}" class="relative flex h-full flex-col gap-2 rounded-2xl border border-slate-100 bg-slate-50/60 p-4 transition hover:border-orange-200 hover:bg-orange-50">
                        <div class="flex items-center gap-2 text-slate-900">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-orange-500 shadow-sm">
                                <i class="fas fa-bookmark"></i>
                            </span>
                            <div>
                                <p class="font-semibold">{{ data_get($mobileSections, 'tool_hub.saved.title', $navCopy['saved']['label']) }}</p>
                                <p class="text-xs text-slate-500">{{ data_get($mobileSections, 'tool_hub.saved.subtitle', 'الوصفات والأدوات التي أحببتها') }}</p>
                            </div>
                        </div>
                        <span id="saved-count-mobile" class="absolute left-3 top-3 hidden h-5 min-w-[20px] rounded-full bg-orange-500 px-2 text-center text-[10px] font-bold leading-5 text-white">0</span>
                        <span class="mt-auto text-[11px] font-semibold text-orange-500">{{ data_get($mobileSections, 'tool_hub.saved.cta', 'عرض الكل') }}</span>
                    </a>
                    <a href="{{ route('notifications.index') }}" class="relative flex h-full flex-col gap-2 rounded-2xl border border-slate-100 p-4 transition hover:border-orange-200 hover:bg-orange-50">
                        <div class="flex items-center gap-2 text-slate-900">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-orange-100 text-orange-600 shadow-inner">
                                <i class="fas fa-bell"></i>
                            </span>
                            <div>
                                <p class="font-semibold">{{ data_get($mobileSections, 'tool_hub.notifications.title', 'الإشعارات') }}</p>
                                <p class="text-xs text-slate-500">{{ data_get($mobileSections, 'tool_hub.notifications.subtitle', 'تنبيهات الورش والوصفات') }}</p>
                            </div>
                        </div>
                        <span id="mobile-notification-count-menu" data-notification-badge aria-live="polite" aria-atomic="true" class="absolute left-3 top-3 hidden h-5 min-w-[20px] rounded-full bg-red-500 px-2 text-center text-[10px] font-bold leading-5 text-white" aria-hidden="true">0</span>
                        <span class="mt-auto text-[11px] font-semibold text-orange-500">{{ data_get($mobileSections, 'tool_hub.notifications.cta', 'عرض السجل') }}</span>
                    </a>
                </div>
            </section>

            @auth
                <section class="space-y-3">
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                        <span>{{ data_get($mobileSections, 'profile.title', 'ملفي') }}</span>
                        <span class="h-px flex-1 bg-slate-200"></span>
                    </div>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('profile') }}" class="flex items-center justify-between rounded-2xl border border-slate-100 bg-white/90 p-4 text-sm font-semibold text-slate-800 transition hover:border-orange-200 hover:bg-orange-50">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
                                    <i class="fas fa-user"></i>
                                </span>
                                <div>
                                    <p>{{ data_get($mobileSections, 'profile.profile_card.title', 'ملفي الشخصي') }}</p>
                                    <p class="text-xs font-normal text-slate-500">{{ data_get($mobileSections, 'profile.profile_card.subtitle', 'إدارة بيانات الحساب والعنوان') }}</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-left text-slate-300"></i>
                        </a>
                        <a href="{{ route('bookings.index') }}" class="flex items-center justify-between rounded-2xl border border-orange-100 bg-white p-4 text-sm font-semibold text-slate-800 transition hover:border-orange-200 hover:bg-orange-50">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                                    <i class="fas fa-calendar-check"></i>
                                </span>
                                <div>
                                    <p>{{ data_get($mobileSections, 'profile.bookings_card.title', 'حجوزاتي') }}</p>
                                    <p class="text-xs font-normal text-slate-500">{{ data_get($mobileSections, 'profile.bookings_card.subtitle', 'تابع كل طلبات الورش وحالتها') }}</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-left text-orange-300"></i>
                        </a>
                        @if($chefLinkData)
                            <a href="{{ $chefLinkData['route'] }}" class="flex items-center justify-between rounded-2xl border border-orange-200 bg-orange-50/80 p-4 text-sm font-semibold text-orange-700 transition hover:bg-orange-100">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-orange-500 shadow-sm">
                                        <i class="{{ $chefLinkData['icon'] }}"></i>
                                    </span>
                                    <div>
                                        <p>{{ $chefLinkData['label'] }}</p>
                                        <p class="text-xs font-normal text-orange-600/80">{{ data_get($mobileSections, 'profile.chef_card.subtitle', 'تابع الأداء وجداول الورش') }}</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-left text-orange-400"></i>
                            </a>
                        @endif
                        @if($authUser?->isAdmin())
                            <a href="{{ route('admin.admin-area') }}" class="flex items-center justify-between rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-700 transition hover:bg-amber-100">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-amber-500 shadow-sm">
                                        <i class="fas fa-crown"></i>
                                    </span>
                                    <div>
                                        <p>{{ data_get($mobileSections, 'profile.admin_card.title', 'منطقة الإدمن') }}</p>
                                        <p class="text-xs font-normal text-amber-700/80">{{ data_get($mobileSections, 'profile.admin_card.subtitle', 'إدارة المحتوى والمنصة') }}</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-left text-amber-400"></i>
                            </a>
                            <a href="{{ route('admin.finance.dashboard') }}" class="flex items-center justify-between rounded-2xl border border-indigo-200 bg-indigo-50 p-4 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-indigo-500 shadow-sm">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </span>
                                    <div>
                                        <p>النظام المالي</p>
                                        <p class="text-xs font-normal text-indigo-700/80">تقارير المدفوعات والفواتير</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-left text-indigo-400"></i>
                            </a>
                        @endif
                        @if($authUser?->isReferralPartner())
                            <a href="{{ route('referrals.dashboard') }}" class="flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-emerald-500 shadow-sm">
                                        <i class="fas fa-link"></i>
                                    </span>
                                    <div>
                                        <p>{{ data_get($mobileSections, 'profile.partner_card.title', 'برنامج الشركاء') }}</p>
                                        <p class="text-xs font-normal text-emerald-700/80">{{ data_get($mobileSections, 'profile.partner_card.subtitle', 'تتبع النقرات والعمولات') }}</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-left text-emerald-400"></i>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button id="logout-btn-mobile" type="submit" class="flex w-full items-center justify-between rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-rose-500 shadow-sm">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </span>
                                    <span>تسجيل الخروج</span>
                                </div>
                                <i class="fas fa-chevron-left text-rose-400"></i>
                            </button>
                        </form>
                    </div>
                </section>
            @endauth

            @guest
                <section class="space-y-3">
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                        <span>الانضمام</span>
                        <span class="h-px flex-1 bg-slate-200"></span>
                    </div>
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('login') }}" class="flex items-center justify-between rounded-2xl bg-gradient-to-l from-orange-500 to-rose-500 p-4 text-white shadow-lg transition hover:opacity-95">
                            <div>
                                <p class="text-base font-semibold">تسجيل الدخول</p>
                                <p class="text-sm text-white/80">تابع أدواتك وحجوزاتك بسهولة</p>
                            </div>
                            <i class="fas fa-arrow-left text-white/80"></i>
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center justify-between rounded-2xl border border-orange-200 bg-white/90 p-4 text-sm font-semibold text-orange-600 transition hover:bg-orange-50">
                            <div>
                                <p>إنشاء حساب</p>
                                <p class="text-xs font-normal text-orange-500">ابدأ رحلتك مع مجتمع وصْفة</p>
                            </div>
                            <i class="fas fa-chevron-left text-orange-400"></i>
                        </a>
                    </div>
                </section>
            @endguest
        </nav>
    </div>

    @if($showNavbarSearch)
        <div id="mobileSearchModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white/95 p-6 shadow-2xl backdrop-blur">
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-slate-900">البحث</h3>
                    <button id="closeMobileSearchModal" class="text-slate-400 transition hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                        <span class="sr-only">إغلاق</span>
                    </button>
                </div>
                <div class="relative" id="mobile-search-container">
                    <input id="mobile-search-input"
                           dir="rtl"
                           type="text"
                           placeholder="ابحث عن وصفة، أداة، أو ورشة..."
                           autocomplete="off"
                           spellcheck="false"
                           class="w-full rounded-2xl border border-slate-200 bg-slate-50 pr-12 pl-5 py-3 text-lg text-slate-700 placeholder:text-slate-400 transition-all duration-200 focus:border-orange-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <button id="mobile-search-submit" class="absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-transparent bg-white text-slate-500 shadow-sm transition hover:text-orange-500 hover:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="sr-only">بدء البحث</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</header>
