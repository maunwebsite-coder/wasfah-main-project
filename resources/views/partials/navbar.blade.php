@php
    $primaryLinks = [
        ['route' => 'home', 'icon' => 'fas fa-house', 'label' => 'الرئيسية'],
        ['route' => 'recipes', 'icon' => 'fas fa-utensils', 'label' => 'الوصفات'],
        ['route' => 'workshops', 'icon' => 'fas fa-graduation-cap', 'label' => 'ورشات العمل'],
        ['route' => 'tools', 'icon' => 'fas fa-kitchen-set', 'label' => 'أدوات الشيف'],
    ];
    $authUser = Auth::user();
    $chefLinkData = null;
    if ($authUser) {
        if ($authUser->isChef()) {
            $chefLinkData = [
                'route' => route('chef.dashboard'),
                'icon' => 'fas fa-tachometer-alt',
                'label' => 'لوحة الشيف',
            ];
        } elseif ($authUser->role !== \App\Models\User::ROLE_CHEF) {
            $chefLinkData = [
                'route' => route('onboarding.show'),
                'icon' => 'fas fa-hat-chef',
                'label' => 'التقديم كـ شيف',
            ];
        } elseif ($authUser->needsChefProfile()) {
            if ($authUser->chef_status === \App\Models\User::CHEF_STATUS_PENDING) {
                $chefLinkData = [
                    'route' => route('onboarding.show'),
                    'icon' => 'fas fa-hourglass-half',
                    'label' => 'متابعة حالة الطلب',
                ];
            } else {
                $chefLinkData = [
                    'route' => route('onboarding.show'),
                    'icon' => 'fas fa-clipboard-list',
                    'label' => 'إكمال بيانات الشيف',
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

                <nav class="hidden lg:flex items-center gap-1 text-sm font-medium text-slate-600" aria-label="التصفح الرئيسي">
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
                <button id="mobileSearchBtn" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition-all duration-200 hover:border-orange-300 hover:text-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span class="sr-only">فتح البحث</span>
                </button>

                <a href="{{ route('saved.index') }}" class="relative flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition-all duration-200 hover:border-orange-300 hover:text-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
                    </svg>
                    <span id="mobile-cart-count" class="absolute -top-1 -right-1 bg-orange-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px]">0</span>
                    <span class="sr-only">الأدوات المحفوظة</span>
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
                    <span class="sr-only">القائمة</span>
                </button>
            </div>

            <div class="hidden md:flex flex-1 items-center justify-end gap-5 text-slate-600 header-nav">
                <div class="relative flex-1 max-w-xl group" id="search-container">
                    <input id="search-input"
                           dir="rtl"
                           type="text"
                           placeholder="ابحث عن وصفة، أداة، أو ورشة..."
                           value="{{ old('q', request('q')) }}"
                           autocomplete="off"
                           spellcheck="false"
                           class="w-full rounded-full border border-slate-200 bg-slate-50 pr-12 pl-5 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 shadow-inner transition-all duration-200 focus:border-orange-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-300 group-hover:bg-white">
                    <button id="search-submit" type="button" class="absolute left-2.5 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-transparent bg-white text-slate-500 shadow-sm transition-colors duration-200 hover:text-orange-500 hover:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="sr-only">بدء البحث</span>
                    </button>
                </div>

                <nav class="flex items-center gap-2 text-sm font-medium text-slate-600" aria-label="روابط الحساب">
                    <a href="{{ route('saved.index') }}" class="relative flex items-center gap-2 rounded-full border border-transparent bg-white px-3 py-2 text-slate-500 shadow-sm transition-all duration-200 hover:border-orange-200 hover:bg-orange-50 hover:text-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <i class="fas fa-bookmark text-base"></i>
                        <span class="hidden xl:inline">الأدوات المحفوظة</span>
                        <span id="saved-count" class="absolute -top-1 -right-1 bg-orange-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px]">0</span>
                    </a>

                    @auth
                        <div class="relative notification-container" id="notification-container">
                            <button id="notification-bell" class="relative flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition-all duration-200 hover:border-orange-300 hover:text-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200" onclick="toggleNotificationDropdown()" aria-expanded="false" aria-haspopup="true">
                                <i class="fas fa-bell text-base"></i>
                                <span id="notification-count" data-notification-badge aria-live="polite" aria-atomic="true" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px]" aria-hidden="true">0</span>
                                <span class="sr-only">عرض الإشعارات</span>
                            </button>

                            <div id="notification-dropdown" class="absolute right-0 top-full mt-3 w-80 rounded-2xl border border-slate-100 bg-white/95 shadow-xl ring-1 ring-black/5 z-50 hidden">
                                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                                    <h3 class="text-lg font-semibold text-slate-900">الإشعارات</h3>
                                    <div class="flex items-center gap-2">
                                        <button id="mark-all-read-btn" class="rounded-full bg-orange-500 px-3 py-1 text-xs font-semibold text-white transition hover:bg-orange-600">
                                            تحديد الكل كمقروء
                                        </button>
                                        <a href="{{ route('notifications.index') }}" class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-slate-300">
                                            عرض الكل
                                        </a>
                                    </div>
                                </div>
                                <div id="notification-list" class="max-h-80 overflow-y-auto">
                                    <div class="p-5 text-center text-slate-500">
                                        <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                                        <p>جاري تحميل الإشعارات...</p>
                                    </div>
                                </div>
                                <div class="border-t border-slate-100 bg-slate-50 px-4 py-3">
                                    <a href="{{ route('notifications.index') }}" class="block text-center text-sm font-medium text-orange-600 hover:text-orange-700">
                                        عرض جميع الإشعارات
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div id="user-menu-container" class="relative user-menu-container">
                            <button
                                id="user-menu-button"
                                type="button"
                                class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-2 text-slate-600 shadow-sm transition-all duration-200 hover:border-orange-300 hover:text-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-200"
                                aria-haspopup="true"
                                aria-expanded="false"
                                aria-controls="user-menu-dropdown">
                                <span class="hidden sm:inline font-semibold text-slate-700">{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div
                                id="user-menu-dropdown"
                                class="absolute right-0 mt-2 hidden w-56 rounded-2xl border border-slate-200 bg-white/95 py-2 text-sm text-slate-600 shadow-xl backdrop-blur-md"
                                role="menu"
                                aria-labelledby="user-menu-button">
                                <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2 transition hover:bg-orange-50 hover:text-orange-600" role="menuitem">
                                    <i class="fas fa-user text-orange-500"></i>
                                    <span class="font-semibold">ملفي الشخصي</span>
                                </a>
                                @if($chefLinkData)
                                    <a href="{{ $chefLinkData['route'] }}" class="flex items-center gap-2 px-4 py-2 transition hover:bg-orange-50 hover:text-orange-600" role="menuitem">
                                        <i class="{{ $chefLinkData['icon'] }} text-orange-500"></i>
                                        <span class="font-semibold">{{ $chefLinkData['label'] }}</span>
                                    </a>
                                @endif
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.admin-area') }}" class="flex items-center gap-2 px-4 py-2 transition hover:bg-orange-50 hover:text-orange-600" role="menuitem">
                                        <i class="fas fa-crown text-orange-500"></i>
                                        <span class="font-semibold">منطقة الإدمن</span>
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button id="logout-btn" type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-left transition hover:bg-orange-50 hover:text-orange-600" role="menuitem">
                                        <i class="fas fa-sign-out-alt text-orange-500"></i>
                                        <span class="font-semibold">تسجيل الخروج</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="rounded-full border border-transparent bg-orange-500 px-4 py-2 text-white shadow-sm transition-all duration-200 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-200">تسجيل الدخول</a>
                        <a href="{{ route('register') }}" class="rounded-full border border-orange-200 px-4 py-2 text-orange-600 transition-all duration-200 hover:border-orange-300 hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-200">إنشاء حساب</a>
                    @endguest
                </nav>
            </div>
        </div>
    </div>

    <div id="mobileMenu" class="mobile-menu hidden border-t border-orange-100 bg-white/95 shadow-lg backdrop-blur md:hidden" style="display: none;">
        <nav class="flex flex-col gap-2 p-4 text-slate-700">
            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl bg-orange-50/60 p-3 font-semibold text-orange-600 transition hover:bg-orange-100">
                <i class="fas fa-house text-orange-500"></i>
                <span>الرئيسية</span>
            </a>
            <a href="{{ route('recipes') }}" class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-orange-50">
                <i class="fas fa-utensils text-orange-500"></i>
                <span>الوصفات</span>
            </a>
            <a href="{{ route('tools') }}" class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-orange-50">
                <i class="fas fa-kitchen-set text-orange-500"></i>
                <span>أدوات الشيف</span>
            </a>
            <a href="{{ route('workshops') }}" class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-orange-50">
                <i class="fas fa-graduation-cap text-orange-500"></i>
                <span>ورشات العمل</span>
            </a>
            <a href="{{ route('saved.index') }}" class="relative flex items-center gap-3 rounded-xl p-3 transition hover:bg-orange-50">
                <i class="fas fa-bookmark text-orange-500"></i>
                <span>الأدوات المحفوظة</span>
                <span id="saved-count-mobile" class="absolute left-3 top-3 bg-orange-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px]">0</span>
            </a>
            @auth
                <a href="{{ route('notifications.index') }}" class="relative flex items-center gap-3 rounded-xl p-3 transition hover:bg-orange-50">
                    <i class="fas fa-bell text-orange-500"></i>
                    <span>الإشعارات</span>
                    <span id="mobile-notification-count-menu" data-notification-badge aria-live="polite" aria-atomic="true" class="absolute left-3 top-3 bg-red-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px]" aria-hidden="true">0</span>
                </a>
                <a href="{{ route('profile') }}" class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-orange-50">
                    <i class="fas fa-user text-orange-500"></i>
                    <span>ملفي الشخصي</span>
                </a>
                @if($chefLinkData)
                    <a href="{{ $chefLinkData['route'] }}" class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-orange-50">
                        <i class="{{ $chefLinkData['icon'] }} text-orange-500"></i>
                        <span>{{ $chefLinkData['label'] }}</span>
                    </a>
                @endif
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.admin-area') }}" class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-orange-50">
                        <i class="fas fa-crown text-orange-500"></i>
                        <span>منطقة الإدمن</span>
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button id="logout-btn-mobile" type="submit" class="flex items-center gap-3 rounded-xl p-3 text-left transition hover:bg-orange-50">
                        <i class="fas fa-sign-out-alt text-orange-500"></i>
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            @endauth
            @guest
                <a href="{{ route('login') }}" class="flex items-center gap-3 rounded-xl bg-orange-500 p-3 font-semibold text-white transition hover:bg-orange-600">
                    <i class="fas fa-right-to-bracket"></i>
                    <span>تسجيل الدخول</span>
                </a>
                <a href="{{ route('register') }}" class="flex items-center gap-3 rounded-xl border border-orange-200 p-3 font-semibold text-orange-600 transition hover:border-orange-300 hover:bg-orange-50">
                    <i class="fas fa-user-plus"></i>
                    <span>إنشاء حساب</span>
                </a>
            @endguest
        </nav>
    </div>

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
</header>
