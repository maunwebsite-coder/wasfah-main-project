<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'موقع وصفة')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Swiper.js for slider -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css"/>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js" defer></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap');
    body {
        font-family: 'Tajawal', sans-serif;
        background-color: #f8f8f8;
    }
    .swiper-wrapper {
        scrollbar-width: none; /* For Firefox */
    }
    .swiper-wrapper::-webkit-scrollbar {
        display: none; /* For Chrome, Safari, and Opera */
    }
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
    }
    .card-front { background: #fff; }
    .card-back { background: #fff; transform: rotateY(180deg); }
    .swiper, .swiper-container { padding: 0 !important; margin: 0 !important; }
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
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans" data-user-id="@auth{{ Auth::id() }}@endauth">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('image/logo.png') }}" alt="Logo" class="h-12 w-auto inline">
                </a>
            </div>
            <div class="flex items-center space-x-4 rtl:space-x-reverse md:hidden">
                <button id="mobileMenuBtn" class="text-gray-600 hover:text-orange-500 transition-colors focus:outline-none">
                    <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    <svg id="close-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="hidden md:flex items-center md:space-x-4 rtl:space-x-reverse text-gray-600">
                <div class="relative w-auto md:w-64">
                    <input dir="rtl" type="text" placeholder="ابحث عن وصفة" class="w-full pl-4 pr-10 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <nav class="flex items-center space-x-4 rtl:space-x-reverse text-gray-600">
                    @auth
                        <div id="user-menu-container" class="relative user-menu-container">
                            <button
                                id="user-menu-button"
                                type="button"
                                class="flex items-center gap-2 rounded-full border border-orange-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:border-orange-300 hover:text-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-200"
                                aria-haspopup="true"
                                aria-expanded="false"
                                aria-controls="user-menu-dropdown">
                                <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div
                                id="user-menu-dropdown"
                                class="absolute right-0 mt-2 hidden w-56 rounded-xl border border-slate-200 bg-white/95 py-2 text-sm text-slate-600 shadow-xl backdrop-blur"
                                role="menu"
                                aria-labelledby="user-menu-button">
                                <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2 transition hover:bg-orange-50 hover:text-orange-600" role="menuitem">
                                    <i class="fas fa-user text-orange-500"></i>
                                    <span class="font-semibold">ملفي الشخصي</span>
                                </a>
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
                        <a href="{{ route('login') }}" class="hover:text-orange-500 transition-colors">تسجيل الدخول</a>
                    @endguest
                    <a href="#" class="hover:text-orange-500 transition-colors">تواصل معنا</a>
                </nav>
            </div>
        </div>
        <div id="mobileMenu" class="hidden bg-white border-t shadow-md md:hidden">
            <nav class="flex flex-col space-y-2 p-4 text-gray-600">
                @auth
                    <a href="#" class="-m-3 flex items-center p-3 focus:outline-offset-2"><span class="ml-4 rtl:mr-4 text-base font-semibold text-gray-900">{{ Auth::user()->name }}</span></a>
                    <div class="mt-2 space-y-2">
                        <a href="#" class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">ملفي الشخصي</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="#" id="logout-btn-mobile" class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50" onclick="event.preventDefault(); this.closest('form').submit();">تسجيل الخروج</a>
                        </form>
                    </div>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="hover:text-orange-500 transition-colors">تسجيل الدخول</a>
                @endguest
                <a href="#" class="hover:text-orange-500 transition-colors">تواصل معنا</a>
            </nav>
        </div>
    </header>

    <!-- Main Navigation Bar -->
    <!-- <nav class="bg-white border-t border-gray-200 shadow-sm ">
        <div class="container mx-auto px-4 py-3">
            <ul class="flex justify-center space-x-8 rtl:space-x-reverse text-gray-700 font-semibold">
                <li><a href="{{ route('home') }}" class="hover:text-orange-500 transition-colors">الرئيسية</a></li>
                <li><a href="#" class="hover:text-orange-500 transition-colors">ورشات</a></li>
                <li><a href="#" class="hover:text-orange-500 transition-colors">ادوات الشيف</a></li>
                <li><a href="#" class="hover:text-orange-500 transition-colors">أصناف الحلويات</a></li>
            </ul>
        </div>
    </nav> -->

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-8">
                <div class="col-span-1 md:col-span-2 lg:col-span-2">
                    <div class="flex items-center mb-4"><span class="text-2xl font-bold text-gray-800 ml-2">وصفة</span></div>
                    <p class="text-gray-500 mb-2">تابعنا</p>
                    <div class="flex space-x-4 text-gray-500">
                        <a href="https://www.instagram.com/wasfah.jo/" target="_blank"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/instagram.svg" alt="Instagram" class="h-8 w-8" /></a>
                    </div>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 mb-4">أصناف</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li><a href="#" class="hover:text-orange-500 transition-colors">براونيز</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">تيرامياسو</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">شوكولوته</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">سان سبستيان</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">كيك</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 mb-4">روابط سريعة</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li><a href="#" class="hover:text-orange-500 transition-colors">عن وصفة</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 mb-4">المزيد</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li><a href="#" class="hover:text-orange-500 transition-colors">نصائح الخبز</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">الإعلان</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">اتصل بنا</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-300 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center text-gray-500">
                <div class="flex items-center mb-4 md:mb-0">
                    <img src="{{ asset('image/logo.png') }}" alt="شعار وصفة" class="h-8 w-auto">
                    <span class="ml-4 text-sm">موقع وصفه هو جزء من شركة وصفة الاردن</span>
                </div>
            </div>
        </div>
    </footer>

    @vite(['resources/js/script.js', 'resources/js/header.js'])
    @stack('scripts')
</body>
</html>
