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
    @stack('styles')
</head>
<body class="font-sans">

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>


    @stack('scripts')
</body>
</html>
