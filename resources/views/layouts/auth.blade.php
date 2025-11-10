@php
    $currentLocale = $currentLocale ?? app()->getLocale();
    $isRtl = $isRtl ?? ($currentLocale === 'ar');
    $alternateLocale = $currentLocale === 'ar' ? 'en' : 'ar';
    $languageCopy = \Illuminate\Support\Facades\Lang::get('navbar.language');
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'موقع وصفة')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap');
    body {
        font-family: 'Tajawal', sans-serif;
        background: radial-gradient(circle at top right, rgba(249, 115, 22, 0.18), transparent 50%),
            radial-gradient(circle at bottom left, rgba(251, 146, 60, 0.15), transparent 40%),
            #fdeee2;
    }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script>
        window.__APP_LOCALE = "{{ $currentLocale }}";
        window.__CONTENT_TRANSLATIONS = @json($globalContentTranslations ?? []);
    </script>
    @stack('styles')
</head>
<body class="font-sans relative">

    <div class="fixed top-6 {{ $isRtl ? 'left-6' : 'right-6' }} z-50">
        <form method="POST" action="{{ route('locale.switch') }}">
            @csrf
            <input type="hidden" name="locale" value="{{ $alternateLocale }}">
            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-full border border-white/60 bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-700 shadow-lg shadow-slate-200/70 transition hover:border-orange-200 hover:text-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-200"
                aria-label="{{ data_get($languageCopy, 'switch_to.' . $alternateLocale, 'Switch language') }}"
            >
                <span>{{ data_get($languageCopy, 'short.' . $alternateLocale, strtoupper($alternateLocale)) }}</span>
                <span class="sr-only">{{ data_get($languageCopy, 'switch_to.' . $alternateLocale, 'Switch language') }}</span>
            </button>
        </form>
    </div>

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>


    @stack('scripts')
</body>
</html>
