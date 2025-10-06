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
    
    /* Mobile Responsive Improvements */
    @media (max-width: 768px) {
        .header-container {
            padding: 0.75rem 1rem;
        }
        
        .header-logo {
            height: 2.5rem;
        }
        
        .header-nav {
            display: none;
        }
        
        .mobile-menu-btn {
            display: block;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .mobile-menu-btn:hover {
            background-color: #fef3c7;
            transform: scale(1.05);
        }
        
        .mobile-menu-btn:active {
            transform: scale(0.95);
        }
        
        /* Mobile cart counter styling */
        #mobile-cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #f97316;
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            border-radius: 50%;
            height: 20px;
            width: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            animation: pulse 2s infinite;
        }
        
        #mobile-cart-count.hidden {
            display: none !important;
        }
        
        @keyframes pulse {
            0%, 100% { 
                transform: scale(1);
                opacity: 1;
            }
            50% { 
                transform: scale(1.1);
                opacity: 0.8;
            }
        }
        
        .mobile-menu {
            padding: 1rem;
            display: none;
            visibility: hidden;
            opacity: 0;
            transition: all 0.3s ease-in-out;
            transform: translateY(-10px);
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-top: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 50;
        }
        
        .mobile-menu.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            transform: translateY(0);
        }
        
        .mobile-menu.hidden {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            transform: translateY(-10px);
        }
        
        /* إضافة تحسينات إضافية للقائمة المحمولة */
        .mobile-menu {
            transition: all 0.3s ease-in-out;
        }
        
        .mobile-menu.show {
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .mobile-menu-item {
            padding: 0.75rem;
            font-size: 1rem;
            transition: all 0.2s ease;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            position: relative;
            overflow: hidden;
        }
        
        .mobile-menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(249, 115, 22, 0.1), transparent);
            transition: width 0.3s ease;
        }
        
        .mobile-menu-item:hover::before {
            width: 100%;
        }
        
        .mobile-menu-item:hover {
            background-color: #fef3c7;
            transform: translateX(-2px);
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.2);
        }
        
        .mobile-menu-item:active {
            transform: translateX(-1px) scale(0.98);
        }
        
        /* تحسينات إضافية للقائمة الجانبية */
        .mobile-menu {
            max-height: 80vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .mobile-menu::-webkit-scrollbar {
            width: 4px;
        }
        
        .mobile-menu::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .mobile-menu::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        
        .mobile-menu::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu nav {
            padding: 0.5rem 0;
        }
        
        .mobile-menu nav a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
            position: relative;
            z-index: 1;
        }
        
        .mobile-menu nav a i {
            transition: transform 0.2s ease;
        }
        
        .mobile-menu nav a:hover i {
            transform: scale(1.1);
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > * + * {
            margin-top: 0.5rem;
        }
        
        .mobile-menu .space-y-2 > * {
            margin-bottom: 0.5rem;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:last-child {
            margin-bottom: 0;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:first-child {
            margin-top: 0;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:not(:first-child) {
            margin-top: 0.5rem;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:not(:last-child) {
            margin-bottom: 0.5rem;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:not(:first-child):not(:last-child) {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:only-child {
            margin-top: 0;
            margin-bottom: 0;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:first-child:last-child {
            margin-top: 0;
            margin-bottom: 0;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:nth-child(odd) {
            background-color: rgba(249, 115, 22, 0.02);
        }
        
        .mobile-menu .space-y-2 > *:nth-child(even) {
            background-color: rgba(249, 115, 22, 0.01);
        }
        
        .mobile-menu .space-y-2 > *:hover {
            background-color: #fef3c7 !important;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:active {
            background-color: #fed7aa !important;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:focus {
            outline: 2px solid #f97316;
            outline-offset: 2px;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:focus-visible {
            outline: 2px solid #f97316;
            outline-offset: 2px;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:focus-within {
            outline: 2px solid #f97316;
            outline-offset: 2px;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:target {
            outline: 2px solid #f97316;
            outline-offset: 2px;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:target-within {
            outline: 2px solid #f97316;
            outline-offset: 2px;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:visited {
            color: #6b7280;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:link {
            color: #374151;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:any-link {
            color: #374151;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:local-link {
            color: #374151;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:scope {
            color: #374151;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:current {
            color: #374151;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:past {
            color: #374151;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:future {
            color: #374151;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:playing {
            color: #374151;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:paused {
            color: #374151;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:seeking {
            color: #374151;
        }
        
        /* تحسينات للقائمة الجانبية في الهاتف المحمول */
        .mobile-menu .space-y-2 > *:buffering {
            color: #374151;
        }
        
        .mobile-search-container {
            margin-bottom: 1rem;
        }
        
        .mobile-search-input {
            font-size: 16px; /* Prevents zoom on iOS */
            padding: 0.75rem 1rem;
        }
        
        /* Mobile Search Modal Styles */
        #mobileSearchModal {
            backdrop-filter: blur(4px);
        }
        
        #mobileSearchModal .bg-white {
            animation: slideInUp 0.3s ease-out;
        }
        
        @keyframes slideInUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        #mobileSearchModal.hidden {
            animation: fadeOut 0.2s ease-in;
        }
        
        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
        
        .user-menu-container {
            position: relative;
        }
        
        .dropdown-menu {
            right: 0;
            left: auto;
            min-width: 200px;
            z-index: 9999;
            position: absolute;
            top: 100%;
            margin-top: 0.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        
        .footer-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            text-align: center;
        }
        
        .footer-bottom {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
    }
    
    @media (max-width: 480px) {
        .header-container {
            padding: 0.5rem 0.75rem;
        }
        
        .header-logo {
            height: 2rem;
        }
        
        .mobile-menu {
            padding: 0.75rem;
        }
        
        .mobile-menu-item {
            padding: 0.625rem;
            font-size: 0.9rem;
        }
        
        .mobile-search-input {
            padding: 0.625rem 0.875rem;
            font-size: 16px;
        }
        
        .dropdown-menu {
            min-width: 180px;
            font-size: 0.9rem;
        }
    }
    
    /* Simple Dropdown CSS */
    .user-menu-container {
        position: relative;
    }
    
    #dropdown-menu {
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: 5px;
        min-width: 200px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 10000;
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
    }
    
    #dropdown-menu.show {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    #dropdown-menu a {
        display: block;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        border-bottom: 1px solid #eee;
    }
    
    #dropdown-menu a:last-child {
        border-bottom: none;
    }
    
    #dropdown-menu a:hover {
        background-color: #f5f5f5;
    }
    
    /* Additional Mobile Improvements */
    @media (max-width: 768px) {
        /* Touch-friendly buttons */
        .btn-touch {
            min-height: 44px;
            min-width: 44px;
            padding: 0.75rem 1rem;
        }
        
        /* Better spacing for mobile */
        .mobile-spacing {
            margin: 0.5rem 0;
        }
        
        /* Improved text readability */
        .mobile-text {
            line-height: 1.6;
        }
        
        /* Better form inputs */
        .mobile-input {
            font-size: 16px; /* Prevents zoom on iOS */
            padding: 0.875rem 1rem;
            border-radius: 0.5rem;
        }
        
        /* Improved card spacing */
        .mobile-card {
            margin-bottom: 1rem;
        }
        
        /* Better modal positioning */
        .mobile-modal {
            margin: 1rem;
            max-width: calc(100vw - 2rem);
        }
        
        /* Improved navigation */
        .mobile-nav {
            padding: 0.75rem 1rem;
        }
        
        /* Better image handling */
        .mobile-image {
            width: 100%;
            height: auto;
            object-fit: cover;
        }
        
        /* Improved button groups */
        .mobile-button-group {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .mobile-button-group .btn {
            width: 100%;
        }
        
        /* Better list spacing */
        .mobile-list {
            padding: 0.5rem 0;
        }
        
        .mobile-list-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        /* Improved grid layouts */
        .mobile-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        /* Better text sizing */
        .mobile-h1 {
            font-size: 1.75rem;
            line-height: 1.3;
        }
        
        .mobile-h2 {
            font-size: 1.5rem;
            line-height: 1.3;
        }
        
        .mobile-h3 {
            font-size: 1.25rem;
            line-height: 1.4;
        }
        
        .mobile-body {
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .mobile-small {
            font-size: 0.875rem;
            line-height: 1.5;
        }
    }
    
    /* Touch improvements */
    @media (hover: none) and (pointer: coarse) {
        /* Remove hover effects on touch devices */
        .hover\:scale-105:hover {
            transform: none;
        }
        
        .hover\:shadow-lg:hover {
            box-shadow: none;
        }
        
        /* Add active states for touch */
        .btn:active {
            transform: scale(0.98);
        }
        
        .card:active {
            transform: scale(0.99);
        }
    }
    
    /* High DPI display improvements */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .mobile-image {
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }
    }
    /* رسالة تسجيل الخروج */
    .logout-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        z-index: 9999;
        transform: translateX(400px);
        transition: transform 0.3s ease-in-out;
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 350px;
    }
    .logout-toast.show {
        transform: translateX(0);
    }
    .logout-toast .icon {
        font-size: 20px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    /* رسالة نجاح تسجيل الدخول/التسجيل */
    .auth-success-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(249, 115, 22, 0.3);
        z-index: 9999;
        transform: translateX(400px);
        transition: transform 0.3s ease-in-out;
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        max-width: 400px;
    }
    .auth-success-toast.show {
        transform: translateX(0);
    }
    .auth-success-toast .icon {
        font-size: 20px;
        animation: pulse 2s infinite;
    }
    
    /* Cart count animation */
    #cart-count {
        transition: all 0.3s ease;
    }
    
    #cart-count.animate-pulse {
        animation: pulse 1s ease-in-out;
    }
    
    /* رسالة إعلامية */
    .info-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        z-index: 9999;
        transform: translateX(400px);
        transition: transform 0.3s ease-in-out;
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 350px;
    }
    .info-toast.show {
        transform: translateX(0);
    }
    .info-toast .icon {
        font-size: 20px;
        animation: pulse 2s infinite;
    }
    
    /* رسالة خطأ */
    .error-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
        z-index: 9999;
        transform: translateX(400px);
        transition: transform 0.3s ease-in-out;
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 350px;
    }
    .error-toast.show {
        transform: translateX(0);
    }
    .error-toast .icon {
        font-size: 20px;
        animation: pulse 2s infinite;
    }
    .swiper-wrapper {
        scrollbar-width: none; /* For Firefox */
    }
    .swiper-wrapper::-webkit-scrollbar {
        display: none; /* For Chrome, Safari, and Opera */
    }
    /* Recipe card flip styles */
    .card-container {
        perspective: 1000px;
        width: 280px;
        height: 400px;
        margin: 0;
        position: relative;
        user-select: none;
        pointer-events: auto;
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
        display: flex;
        flex-direction: column;
    }
    .card-front { 
        background: #fff; 
    }
    .card-back { 
        background: #fff; 
        transform: rotateY(180deg); 
    }
    
    /* Smooth transitions for better UX */
    .card-container * {
        pointer-events: none;
    }
    .card-container a,
    .card-container button {
        pointer-events: auto;
    }
    
    /* Ensure card container is clickable */
    .card-container {
        pointer-events: auto !important;
    }
    
    /* Make sure card inner is also clickable */
    .card-inner {
        pointer-events: auto !important;
    }
    
    /* Make sure card front and back are clickable */
    .card-front,
    .card-back {
        pointer-events: auto !important;
    }
    
    /* Ensure all card elements are clickable */
    .card-container * {
        pointer-events: none;
    }
    .card-container a,
    .card-container button {
        pointer-events: auto;
    }
    .card-container {
        pointer-events: auto !important;
    }
    
    /* Cards are now static - no hover effects needed */
    
    /* Save button on card front styling */
    .card-front .save-recipe-btn {
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .card-front .save-recipe-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    
    .card-front .save-recipe-btn.bg-orange-500\/80 {
        background-color: rgba(249, 115, 22, 0.8) !important;
    }
    
    .card-front .save-recipe-btn.bg-orange-500\/80:hover {
        background-color: rgba(234, 88, 12, 0.9) !important;
    }
    
    /* Line clamp utilities */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
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
    
    <!-- Simple dropdown fix -->
    <script>
        function toggleUserMenu() {
            const dropdown = document.getElementById('dropdown-menu');
            if (dropdown) {
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                } else {
                    dropdown.classList.add('show');
                }
            }
        }
        window.toggleUserMenu = toggleUserMenu;
    </script>
    
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/search-enhancements.css') }}">
</head>
<body class="bg-gray-100 font-sans" data-user-id="@auth{{ Auth::id() }}@endauth">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between header-container">
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('image/logo.png') }}" alt="Logo" class="h-12 w-auto inline header-logo">
                </a>
            </div>
            <div class="flex items-center space-x-4 rtl:space-x-reverse md:hidden mobile-menu-btn">
                <!-- Mobile Search Icon -->
                <button id="mobileSearchBtn" class="text-gray-600 hover:text-orange-500 transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                
                <!-- Mobile Cart Icon with Counter -->
                <a href="{{ route('saved.index') }}" class="relative text-gray-600 hover:text-orange-500 transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
                    </svg>
                    <span id="mobile-cart-count" class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px] z-10">0</span>
                </a>
                
                @auth
                <!-- Mobile Notification Bell Icon -->
                <a href="/notifications" class="relative text-gray-600 hover:text-orange-500 transition-colors focus:outline-none">
                    <i class="fas fa-bell text-xl"></i>
                    <span id="mobile-notification-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px] z-10">0</span>
                </a>
                @endauth
                
                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" class="text-gray-600 hover:text-orange-500 transition-colors focus:outline-none">
                    <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    <svg id="close-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="hidden md:flex items-center md:space-x-4 rtl:space-x-reverse text-gray-600 header-nav">
                <div class="relative w-auto md:w-64" id="search-container">
                    <input id="search-input" 
                           dir="rtl" 
                           type="text" 
                           placeholder="ابحث عن وصفة أو ورشة..." 
                           autocomplete="off"
                           spellcheck="false"
                           class="w-full pl-4 pr-10 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all duration-200">
                    <button id="search-submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
                <nav class="flex items-center space-x-4 rtl:space-x-reverse text-gray-600">
                    <a href="{{ route('recipes') }}" class="hover:text-orange-500 transition-colors font-medium">
                        <i class="fas fa-utensils ml-1"></i>
                        الوصفات
                    </a>
                    <a href="{{ route('tools') }}" class="hover:text-orange-500 transition-colors font-medium">
                        <i class="fas fa-kitchen-set ml-1"></i>
                        أدوات الشيف
                    </a>
                    <a href="{{ route('workshops') }}" class="hover:text-orange-500 transition-colors font-medium">
                        <i class="fas fa-graduation-cap ml-1"></i>
                        ورشات العمل
                    </a>
                    <a href="{{ route('saved.index') }}" class="hover:text-orange-500 transition-colors font-medium relative">
                        <i class="fas fa-bookmark ml-1"></i>
                        الأدوات المحفوظة
                        <span id="saved-count" class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px] z-10">0</span>
                    </a>
                    @auth
                        <!-- Notification Bell Icon -->
                        <div class="relative notification-container" id="notification-container">
                            <button id="notification-bell" class="relative text-gray-600 hover:text-orange-500 transition-colors focus:outline-none" onclick="toggleNotificationDropdown()">
                                <i class="fas fa-bell text-xl"></i>
                                <span id="notification-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px] z-10">0</span>
                            </button>
                            
                            <!-- Notification Dropdown -->
                            <div id="notification-dropdown" class="absolute right-0 top-full mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden">
                                <div class="p-4 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900">الإشعارات</h3>
                                        <div class="flex space-x-2 rtl:space-x-reverse">
                                            <button id="mark-all-read-btn" class="text-xs bg-orange-500 hover:bg-orange-600 text-white px-2 py-1 rounded transition-colors">
                                                تحديد الكل كمقروء
                                            </button>
                                            <a href="/notifications" class="text-xs bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded transition-colors">
                                                عرض الكل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="notification-list" class="max-h-80 overflow-y-auto">
                                    <!-- Notifications will be loaded here -->
                                    <div class="p-4 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                                        <p>جاري تحميل الإشعارات...</p>
                                    </div>
                                </div>
                                
                                <div class="p-3 border-t border-gray-200 bg-gray-50">
                                    <a href="/notifications" class="block text-center text-sm text-orange-600 hover:text-orange-700 font-medium">
                                        عرض جميع الإشعارات
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endauth
                    @auth
                        <div class="relative user-menu-container" id="user-menu-container">
                            <button id="user-menu-button" class="flex items-center space-x-2 rtl:space-x-reverse focus:outline-none" onclick="toggleUserMenu()">
                                <span class="font-semibold text-gray-700 hidden sm:inline">{{ Auth::user()->name }}</span>
                                <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                            <div id="dropdown-menu">
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">ملفي الشخصي</a>
                                @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.admin-area') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-crown ml-2 text-orange-500"></i>
                                    <span class="font-semibold">منطقة الإدمن</span>
                                </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="#" id="logout-btn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="event.preventDefault(); this.closest('form').submit();">تسجيل الخروج</a>
                                </form>
                            </div>
                        </div>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="hover:text-orange-500 transition-colors">تسجيل الدخول</a>
                    @endguest
                </nav>
            </div>
        </div>
        <div id="mobileMenu" class="hidden bg-white border-t shadow-md md:hidden mobile-menu" style="display: none; position: relative; z-index: 50;">
            <nav class="flex flex-col space-y-2 p-4 text-gray-600">
                <a href="{{ route('recipes') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors mobile-menu-item">
                    <i class="fas fa-utensils ml-3 text-orange-500"></i>
                    <span class="font-medium">الوصفات</span>
                </a>
                <a href="{{ route('tools') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors mobile-menu-item">
                    <i class="fas fa-kitchen-set ml-3 text-orange-500"></i>
                    <span class="font-medium">أدوات الشيف</span>
                </a>
                <a href="{{ route('workshops') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors mobile-menu-item">
                    <i class="fas fa-graduation-cap ml-3 text-orange-500"></i>
                    <span class="font-medium">ورشات العمل</span>
                </a>
                <a href="{{ route('saved.index') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors mobile-menu-item relative">
                    <i class="fas fa-bookmark ml-3 text-orange-500"></i>
                    <span class="font-medium">الأدوات المحفوظة</span>
                    <span id="saved-count-mobile" class="absolute left-3 top-3 bg-orange-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px] z-10">0</span>
                </a>
                @auth
                <a href="/notifications" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors mobile-menu-item relative">
                    <i class="fas fa-bell ml-3 text-orange-500"></i>
                    <span class="font-medium">الإشعارات</span>
                    <span id="mobile-notification-count-menu" class="absolute left-3 top-3 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden min-w-[20px] z-10">0</span>
                </a>
                @endauth
                @auth
                    <a href="#" class="-m-3 flex items-center p-3 focus:outline-offset-2"><span class="ml-4 rtl:mr-4 text-base font-semibold text-gray-900">{{ Auth::user()->name }}</span></a>
                    <div class="mt-2 space-y-2">
                        <a href="{{ route('profile') }}" class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">ملفي الشخصي</a>
                        @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.admin-area') }}" class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                            <i class="fas fa-crown ml-2 text-orange-500"></i>
                            <span class="font-bold">منطقة الإدمن</span>
                        </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="#" id="logout-btn-mobile" class="block rounded-lg py-2 pl-6 pr-3 text-sm font-semibold leading-7 text-gray-900 hover:bg-gray-50" onclick="event.preventDefault(); this.closest('form').submit();">تسجيل الخروج</a>
                        </form>
                    </div>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="hover:text-orange-500 transition-colors">تسجيل الدخول</a>
                @endguest
            </nav>
        </div>
        
        <!-- Mobile Search Modal -->
        <div id="mobileSearchModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">البحث</h3>
                    <button id="closeMobileSearchModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="relative">
                    <input id="mobile-search-input" 
                           dir="rtl" 
                           type="text" 
                           placeholder="ابحث عن وصفة أو ورشة..." 
                           autocomplete="off"
                           spellcheck="false"
                           class="w-full pl-4 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all duration-200 text-lg">
                    <button id="mobile-search-submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </div>
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

    <!-- Load Cart Count Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
            // Load cart count on page load
        loadCartCount();
        
        // Load saved count for mobile menu
        loadSavedCountMobile();
        
        // Load saved count for desktop menu
        loadSavedCountDesktop();
        
        // Load mobile cart count
        loadMobileCartCount();
    });
    
    function loadCartCount() {
        fetch('/cart/count')
            .then(response => response.json())
            .then(data => {
                const cartCountEl = document.getElementById('cart-count');
                if (cartCountEl) {
                    const oldCount = parseInt(cartCountEl.textContent) || 0;
                    const newCount = data.count;
                    
                    cartCountEl.textContent = newCount;
                    
                    if (newCount > 0) {
                        cartCountEl.classList.remove('hidden');
                        
                        // Adjust width based on number of digits
                        if (newCount > 9) {
                            cartCountEl.classList.remove('h-5', 'w-5');
                            cartCountEl.classList.add('h-6', 'w-6', 'px-1');
                        } else {
                            cartCountEl.classList.remove('h-6', 'w-6', 'px-1');
                            cartCountEl.classList.add('h-5', 'w-5');
                        }
                        
                        // Add animation if count increased
                        if (newCount > oldCount) {
                            cartCountEl.classList.add('animate-pulse');
                            setTimeout(() => {
                                cartCountEl.classList.remove('animate-pulse');
                            }, 1000);
                        }
                    } else {
                        cartCountEl.classList.add('hidden');
                    }
                }
            })
            .catch(error => {
                console.error('Error loading cart count:', error);
            });
    }
    
    function loadSavedCountMobile() {
        fetch('/saved/count')
            .then(response => response.json())
            .then(data => {
                const savedCountEl = document.getElementById('saved-count-mobile');
                if (savedCountEl) {
                    const count = data.count || 0;
                    savedCountEl.textContent = count;
                    
                    if (count > 0) {
                        savedCountEl.classList.remove('hidden');
                    } else {
                        savedCountEl.classList.add('hidden');
                    }
                }
            })
            .catch(error => {
                console.error('Error loading saved count for mobile:', error);
            });
    }
    
    function loadSavedCountDesktop() {
        fetch('/saved/count')
            .then(response => response.json())
            .then(data => {
                const savedCountEl = document.getElementById('saved-count');
                if (savedCountEl) {
                    const count = data.count || 0;
                    savedCountEl.textContent = count;
                    
                    if (count > 0) {
                        savedCountEl.classList.remove('hidden');
                    } else {
                        savedCountEl.classList.add('hidden');
                    }
                }
            })
            .catch(error => {
                console.error('Error loading saved count for desktop:', error);
            });
    }
    
    function loadMobileCartCount() {
        fetch('/saved/count')
            .then(response => response.json())
            .then(data => {
                const mobileCartCountEl = document.getElementById('mobile-cart-count');
                if (mobileCartCountEl) {
                    const count = data.count || 0;
                    mobileCartCountEl.textContent = count;
                    
                    if (count > 0) {
                        mobileCartCountEl.classList.remove('hidden');
                        
                        // Add pulse animation for attention
                        mobileCartCountEl.classList.add('animate-pulse');
                        setTimeout(() => {
                            mobileCartCountEl.classList.remove('animate-pulse');
                        }, 3000);
                    } else {
                        mobileCartCountEl.classList.add('hidden');
                    }
                }
            })
            .catch(error => {
                console.error('Error loading mobile cart count:', error);
            });
    }
    
    // Make function globally available
    window.loadCartCount = loadCartCount;
    window.loadSavedCountMobile = loadSavedCountMobile;
    window.loadSavedCountDesktop = loadSavedCountDesktop;
    window.loadMobileCartCount = loadMobileCartCount;
    </script>

    <!-- Footer -->
    <footer class="bg-gray-100 py-8">
        <div class="container mx-auto px-4">
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 footer-content">
                <!-- Brand & Social -->
                <div>
                    <div class="flex items-center mb-4">
                        <img src="{{ asset('image/logo.png') }}" alt="شعار وصفة" class="h-10 w-auto">
                    </div>
                    <p class="text-gray-600 mb-4">منصّة وصفة للحلويات الفاخرة والراقية</p>
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <a href="https://www.instagram.com/wasfah.jo/" target="_blank" class="text-gray-600 hover:text-orange-500 transition-colors">
                            <i class="fab fa-instagram text-2xl"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="font-bold text-gray-800 mb-4">روابط سريعة</h3>
                    <ul class="space-y-3 text-gray-600">
                        <li><a href="{{ route('recipes') }}" class="hover:text-orange-500 transition-colors">جميع الوصفات</a></li>
                        <li><a href="{{ route('tools') }}" class="hover:text-orange-500 transition-colors">أدوات الشيف</a></li>
                        <li><a href="{{ route('search') }}" class="hover:text-orange-500 transition-colors">البحث</a></li>
                        <li><a href="{{ route('workshops') }}" class="hover:text-orange-500 transition-colors">ورشات العمل</a></li>
                        <li><a href="{{ route('baking-tips') }}" class="hover:text-orange-500 transition-colors">نصائح الحلويات</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="font-bold text-gray-800 mb-4">تواصل معنا</h3>
                    <div class="space-y-3 text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-orange-500 ml-2"></i>
                            <span>wasfah99@gmail.com</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-orange-500 ml-2"></i>
                            <span>+962 6 123 4567</span>
                        </div>
                        <div class="space-y-2 mt-4">
                            <a href="{{ route('about') }}" class="block hover:text-orange-500 transition-colors">من نحن</a>
                            <a href="{{ route('contact') }}" class="block hover:text-orange-500 transition-colors">اتصل بنا</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Bar -->
            <div class="border-t border-gray-300 pt-6 flex flex-col md:flex-row justify-between items-center text-gray-500 footer-bottom">
                <div class="text-sm mb-4 md:mb-0">
                    <span>&copy; 2024 وصفة. جميع الحقوق محفوظة.</span>
                </div>
                <div class="text-sm">
                    <span>موقع وصفه هو جزء من شركة وصفة الاردن</span>
                </div>
            </div>
        </div>
    </footer>

    @vite(['resources/js/header.js', 'resources/js/mobile-menu.js', 'resources/js/save-recipe.js', 'resources/js/script.js', 'resources/js/search.js', 'resources/js/recipe.js', 'resources/js/recipe-save-button.js', 'resources/js/notification-manager.js'])
    
    <!-- Notification System JavaScript -->
    <script>
        // Notification dropdown functions
        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notification-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
                
                // Load notifications when dropdown is opened
                if (!dropdown.classList.contains('hidden')) {
                    loadNotificationDropdown();
                }
            }
        }

        // Close notification dropdown when clicking outside
        document.addEventListener('click', (event) => {
            const notificationContainer = document.getElementById('notification-container');
            const notificationDropdown = document.getElementById('notification-dropdown');
            
            if (notificationContainer && notificationDropdown && 
                !notificationContainer.contains(event.target)) {
                notificationDropdown.classList.add('hidden');
            }
        });

        // Load notifications in dropdown
        function loadNotificationDropdown() {
            const notificationList = document.getElementById('notification-list');
            if (!notificationList) return;

            // Show loading state
            notificationList.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                    <p>جاري تحميل الإشعارات...</p>
                </div>
            `;

            // Load notifications using NotificationManager
            if (window.NotificationManager) {
                window.NotificationManager.getNotifications((data, error) => {
                    if (error) {
                        console.error('Error loading notifications:', error);
                        notificationList.innerHTML = `
                            <div class="p-4 text-center text-red-500">
                                <i class="fas fa-exclamation-circle text-xl mb-2"></i>
                                <p>حدث خطأ في تحميل الإشعارات</p>
                            </div>
                        `;
                        return;
                    }

                    const notifications = data?.notifications || [];
                    if (notifications.length === 0) {
                        notificationList.innerHTML = `
                            <div class="p-4 text-center text-gray-500">
                                <i class="fas fa-bell-slash text-xl mb-2"></i>
                                <p>لا توجد إشعارات</p>
                            </div>
                        `;
                    } else {
                        let notificationsHTML = '';
                        notifications.slice(0, 5).forEach(notification => {
                            const isRead = notification.is_read;
                            const timeAgo = new Date(notification.created_at).toLocaleDateString('ar-SA', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            
                            notificationsHTML += `
                                <div class="p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer ${isRead ? 'opacity-75' : 'bg-orange-50'}" 
                                     onclick="markNotificationAsRead(${notification.id})">
                                    <div class="flex items-start space-x-3 rtl:space-x-reverse">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 ${notification.type === 'workshop_booking' ? 'bg-blue-100' : (notification.type === 'workshop_confirmed' ? 'bg-green-100' : (notification.type === 'workshop_cancelled' ? 'bg-red-100' : 'bg-orange-100'))} rounded-full flex items-center justify-center">
                                                <i class="fas fa-${notification.type === 'workshop_booking' ? 'calendar-plus' : (notification.type === 'workshop_confirmed' ? 'check-circle' : (notification.type === 'workshop_cancelled' ? 'times-circle' : 'info-circle'))} text-xs ${notification.type === 'workshop_booking' ? 'text-blue-600' : (notification.type === 'workshop_confirmed' ? 'text-green-600' : (notification.type === 'workshop_cancelled' ? 'text-red-600' : 'text-orange-600'))}"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 ${isRead ? '' : 'font-bold'}">${notification.title}</p>
                                            <p class="text-xs text-gray-500 mt-1">${notification.message}</p>
                                            <p class="text-xs text-gray-400 mt-1">${timeAgo}</p>
                                        </div>
                                        ${!isRead ? '<div class="w-2 h-2 bg-orange-500 rounded-full flex-shrink-0 mt-2"></div>' : ''}
                                    </div>
                                </div>
                            `;
                        });
                        
                        notificationList.innerHTML = notificationsHTML;
                    }
                });
            }
        }

        // Mark notification as read
        function markNotificationAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update notification count
                    updateNotificationCounts();
                    // Reload dropdown
                    loadNotificationDropdown();
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }

        // Update notification counts
        function updateNotificationCounts() {
            if (window.NotificationManager) {
                window.NotificationManager.getNotifications((data, error) => {
                    if (error) {
                        console.error('Error updating notification counts:', error);
                        return;
                    }

                    const count = data?.unreadCount || 0;
                    
                    // Update desktop notification count
                    const desktopCount = document.getElementById('notification-count');
                    if (desktopCount) {
                        desktopCount.textContent = count;
                        if (count > 0) {
                            desktopCount.classList.remove('hidden');
                        } else {
                            desktopCount.classList.add('hidden');
                        }
                    }

                    // Update mobile notification counts
                    const mobileCount = document.getElementById('mobile-notification-count');
                    const mobileMenuCount = document.getElementById('mobile-notification-count-menu');
                    
                    [mobileCount, mobileMenuCount].forEach(element => {
                        if (element) {
                            element.textContent = count;
                            if (count > 0) {
                                element.classList.remove('hidden');
                            } else {
                                element.classList.add('hidden');
                            }
                        }
                    });
                });
            }
        }

        // Mark all notifications as read
        document.addEventListener('click', (event) => {
            if (event.target.id === 'mark-all-read-btn') {
                fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationCounts();
                        loadNotificationDropdown();
                    }
                })
                .catch(error => {
                    console.error('Error marking all as read:', error);
                });
            }
        });

        // Initialize notification system
        document.addEventListener('DOMContentLoaded', function() {
            // Load notification counts on page load
            updateNotificationCounts();
            
            // Update notification counts every 30 seconds
            setInterval(updateNotificationCounts, 30000);
        });
    </script>
    
    <script>
        // Simple dropdown function (backup)
        function toggleUserMenuBackup() {
            const dropdownMenu = document.getElementById('dropdown-menu');
            if (dropdownMenu) {
                if (dropdownMenu.style.display === 'block') {
                    dropdownMenu.style.display = 'none';
                } else {
                    dropdownMenu.style.display = 'block';
                }
            }
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdownMenu = document.getElementById('dropdown-menu');
            const userMenuButton = document.getElementById('user-menu-button');
            
            if (dropdownMenu && userMenuButton && 
                !userMenuButton.contains(e.target) && 
                !dropdownMenu.contains(e.target)) {
                dropdownMenu.style.display = 'none';
                console.log('Dropdown closed - clicked outside');
            }
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM Content Loaded - Initializing mobile menu...');
            
            // Simple dropdown setup
            const userMenuButton = document.getElementById('user-menu-button');
            const dropdownMenu = document.getElementById('dropdown-menu');
            
            if (userMenuButton && dropdownMenu) {
                // Ensure dropdown starts hidden
                dropdownMenu.style.display = 'none';
                
                // Add click handler
                userMenuButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (dropdownMenu.classList.contains('show')) {
                        dropdownMenu.classList.remove('show');
                    } else {
                        dropdownMenu.classList.add('show');
                    }
                });
            }
            
            // Mobile Menu Setup - Simple and Direct
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');
            
            console.log('Mobile menu elements:', {
                mobileMenuBtn: !!mobileMenuBtn,
                mobileMenu: !!mobileMenu,
                menuIcon: !!menuIcon,
                closeIcon: !!closeIcon
            });
            
            if (mobileMenuBtn && mobileMenu && menuIcon && closeIcon) {
                console.log('All mobile menu elements found, setting up click handler...');
                
                // Direct click handler - no complex functions
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Mobile menu button clicked!');
                    
                    // Check current state
                    const isHidden = mobileMenu.classList.contains('hidden') || !mobileMenu.classList.contains('show');
                    console.log('Current state - isHidden:', isHidden);
                    
                    if (isHidden) {
                        // Show menu
                        console.log('Opening mobile menu...');
                        mobileMenu.classList.remove('hidden');
                        mobileMenu.classList.add('show');
                        mobileMenu.style.display = 'block';
                        menuIcon.classList.add('hidden');
                        closeIcon.classList.remove('hidden');
                        console.log('Mobile menu opened successfully');
                    } else {
                        // Hide menu
                        console.log('Closing mobile menu...');
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('show');
                        mobileMenu.style.display = 'none';
                        menuIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                        console.log('Mobile menu closed successfully');
                    }
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!mobileMenuBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
                        if (mobileMenu.classList.contains('show')) {
                            console.log('Closing menu - clicked outside');
                            mobileMenu.classList.add('hidden');
                            mobileMenu.classList.remove('show');
                            mobileMenu.style.display = 'none';
                            menuIcon.classList.remove('hidden');
                            closeIcon.classList.add('hidden');
                        }
                    }
                });
                
                console.log('Mobile menu setup completed successfully!');
            } else {
                console.error('Mobile menu elements not found!', {
                    mobileMenuBtn: mobileMenuBtn,
                    mobileMenu: mobileMenu,
                    menuIcon: menuIcon,
                    closeIcon: closeIcon
                });
            }
            
            // Test function for debugging
            window.testMobileMenu = function() {
                console.log('Testing mobile menu...');
                const mobileMenu = document.getElementById('mobileMenu');
                const menuIcon = document.getElementById('menu-icon');
                const closeIcon = document.getElementById('close-icon');
                
                if (mobileMenu && menuIcon && closeIcon) {
                    const isHidden = mobileMenu.classList.contains('hidden');
                    console.log('Current state - isHidden:', isHidden);
                    
                    if (isHidden) {
                        mobileMenu.classList.remove('hidden');
                        mobileMenu.classList.add('show');
                        mobileMenu.style.display = 'block';
                        menuIcon.classList.add('hidden');
                        closeIcon.classList.remove('hidden');
                        console.log('Menu opened via test function');
                    } else {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('show');
                        mobileMenu.style.display = 'none';
                        menuIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                        console.log('Menu closed via test function');
                    }
                } else {
                    console.error('Mobile menu elements not found for testing');
                }
            };
        });
        
        // Mobile menu is now handled by mobile-menu.js
            
            // Mobile search functionality
            const searchInput = document.getElementById('search-input');
            const mobileSearchInput = document.getElementById('mobile-search-input');
            const searchSubmit = document.getElementById('search-submit');
            const mobileSearchSubmit = document.getElementById('mobile-search-submit');
            
            function performSearch(query) {
                if (query.trim()) {
                    window.location.href = `/search?q=${encodeURIComponent(query)}`;
                }
            }
            
            if (searchInput && searchSubmit) {
                searchSubmit.addEventListener('click', (e) => {
                    e.preventDefault();
                    performSearch(searchInput.value);
                });
                
                searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        performSearch(searchInput.value);
                    }
                });
            }
            
            if (mobileSearchInput && mobileSearchSubmit) {
                mobileSearchSubmit.addEventListener('click', (e) => {
                    e.preventDefault();
                    performSearch(mobileSearchInput.value);
                });
                
                mobileSearchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        performSearch(mobileSearchInput.value);
                    }
                });
            }
            
            // Touch improvements
            if ('ontouchstart' in window) {
                // Add touch class to body for CSS targeting
                document.body.classList.add('touch-device');
                
                // Improve touch targets
                const touchElements = document.querySelectorAll('a, button, input, select, textarea');
                touchElements.forEach(element => {
                    if (element.offsetHeight < 44) {
                        element.classList.add('btn-touch');
                    }
                });
            }
            
            // Prevent zoom on input focus (iOS)
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    if (window.innerWidth < 768) {
                        const viewport = document.querySelector('meta[name="viewport"]');
                        if (viewport) {
                            viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
                        }
                    }
                });
                
                input.addEventListener('blur', () => {
                    if (window.innerWidth < 768) {
                        const viewport = document.querySelector('meta[name="viewport"]');
                        if (viewport) {
                            viewport.setAttribute('content', 'width=device-width, initial-scale=1.0');
                        }
                    }
                });
            });
            
            // Smooth scrolling for mobile
            const links = document.querySelectorAll('a[href^="#"]');
            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    const targetId = link.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        e.preventDefault();
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Lazy loading for images
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.classList.remove('lazy');
                                observer.unobserve(img);
                            }
                        }
                    });
                });
                
                const lazyImages = document.querySelectorAll('img[data-src]');
                lazyImages.forEach(img => imageObserver.observe(img));
            }
        });
            
            // تحميل عدد الأدوات المحفوظة
            setTimeout(() => {
                if (typeof loadSavedCount === 'function') {
                    loadSavedCount();
                } else {
                    console.log('loadSavedCount function not found, trying again...');
                    setTimeout(() => {
                        if (typeof loadSavedCount === 'function') {
                            loadSavedCount();
                        } else {
                            console.error('loadSavedCount function still not found');
                        }
                    }, 500);
                }
            }, 100);
        });
    </script>
    @stack('scripts')
    
    <!-- CSS للعداد -->
    <style>
        #saved-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #f97316;
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            border-radius: 50%;
            height: 20px;
            width: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        #saved-count.hidden {
            display: none !important;
        }
    </style>
    
    <!-- تحميل عداد الأدوات المحفوظة -->
    <script>
        // دالة تحديث واجهة العداد - متاحة في جميع الصفحات
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
            } else {
                console.log('saved-count element not found in navbar');
            }
            
            // تحديث العداد في الهاتف المحمول (السلة - الأدوات المحفوظة)
            const mobileCartCountEl = document.getElementById('mobile-cart-count');
            if (mobileCartCountEl) {
                if (count > 0) {
                    mobileCartCountEl.textContent = count;
                    mobileCartCountEl.classList.remove('hidden');
                    
                    // Add pulse animation for attention
                    mobileCartCountEl.classList.add('animate-pulse');
                    setTimeout(() => {
                        mobileCartCountEl.classList.remove('animate-pulse');
                    }, 3000);
                    
                    console.log('Updated mobile cart counter (saved tools) to:', count);
                } else {
                    mobileCartCountEl.classList.add('hidden');
                    console.log('Hidden mobile cart counter (saved tools)');
                }
            }
            
            // تحديث العداد في الهاتف المحمول (الأدوات المحفوظة)
            const mobileSavedCountEl = document.getElementById('saved-count-mobile');
            if (mobileSavedCountEl) {
                if (count > 0) {
                    mobileSavedCountEl.textContent = count;
                    mobileSavedCountEl.classList.remove('hidden');
                    
                    // Add pulse animation for attention
                    mobileSavedCountEl.classList.add('animate-pulse');
                    setTimeout(() => {
                        mobileSavedCountEl.classList.remove('animate-pulse');
                    }, 3000);
                    
                    console.log('Updated mobile saved counter to:', count);
                } else {
                    mobileSavedCountEl.classList.add('hidden');
                    console.log('Hidden mobile saved counter');
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

        // دالة تحميل العداد - متاحة في جميع الصفحات
        function loadSavedCount() {
            console.log('Loading saved count from layout...');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch('/saved/count', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                cache: 'no-cache'
            })
            .then(response => {
                console.log('Layout response status:', response.status);
                if (response.status === 401) {
                    console.log('User not authenticated in layout');
                    updateSavedCountUI(0);
                    return;
                }
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data) {
                    console.log('Layout saved count data:', data);
                    updateSavedCountUI(data.count);
                }
            })
            .catch(error => {
                console.error('Error loading saved count from layout:', error);
                updateSavedCountUI(0);
            });
        }

        // جعل الدوال متاحة عالمياً
        window.updateSavedCountUI = updateSavedCountUI;
        window.loadSavedCount = loadSavedCount;
        window.initDesktopDropdown = initDesktopDropdown;
        window.toggleUserMenu = toggleUserMenu;

        // تحميل فوري للعداد عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, loading saved count...');
            loadSavedCount();
        });

        // تحميل عداد الأدوات المحفوظة بعد تحميل جميع الـ scripts
        window.addEventListener('load', function() {
            loadSavedCount();
        });
        
        // تحديث العداد كل 5 ثوان للتأكد من التزامن
        setInterval(function() {
            loadSavedCount();
        }, 5000);
        
        // تحديث فوري للعداد عند إضافة/حذف أدوات
        function updateCountersImmediately() {
            console.log('Updating counters immediately...');
            try {
                // تحديث مباشر للعدادات
                const mobileCartCountEl = document.getElementById('mobile-cart-count');
                const savedCountEl = document.getElementById('saved-count');
                const savedCountMobileEl = document.getElementById('saved-count-mobile');
                
                console.log('Counter elements found:', {
                    mobileCartCountEl: !!mobileCartCountEl,
                    savedCountEl: !!savedCountEl,
                    savedCountMobileEl: !!savedCountMobileEl
                });
                
                // تحديث مباشر للعدادات
                if (mobileCartCountEl || savedCountEl || savedCountMobileEl) {
                    fetch('/saved/count')
                        .then(response => response.json())
                        .then(data => {
                            const count = data.count || 0;
                            console.log('Counter count:', count);
                            
                            if (mobileCartCountEl) {
                                mobileCartCountEl.textContent = count;
                                if (count > 0) {
                                    mobileCartCountEl.classList.remove('hidden');
                                    console.log('Mobile cart counter shown:', count);
                                } else {
                                    mobileCartCountEl.classList.add('hidden');
                                    console.log('Mobile cart counter hidden');
                                }
                            }
                            
                            if (savedCountEl) {
                                savedCountEl.textContent = count;
                                if (count > 0) {
                                    savedCountEl.classList.remove('hidden');
                                    console.log('Desktop counter shown:', count);
                                } else {
                                    savedCountEl.classList.add('hidden');
                                    console.log('Desktop counter hidden');
                                }
                            }
                            
                            if (savedCountMobileEl) {
                                savedCountMobileEl.textContent = count;
                                if (count > 0) {
                                    savedCountMobileEl.classList.remove('hidden');
                                    console.log('Mobile menu counter shown:', count);
                                } else {
                                    savedCountMobileEl.classList.add('hidden');
                                    console.log('Mobile menu counter hidden');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error updating counters:', error);
                        });
                }
                
                // تحديث إضافي باستخدام الدوال الأخرى
                loadSavedCount();
                loadMobileCartCount();
                loadSavedCountMobile();
                loadSavedCountDesktop();
            } catch (error) {
                console.error('Error in updateCountersImmediately:', error);
            }
        }
        
        // دالة محددة لتحديث عداد الهاتف المحمول
        function updateMobileCounterImmediately() {
            console.log('Updating mobile counter immediately...');
            const mobileCartCountEl = document.getElementById('mobile-cart-count');
            
            if (mobileCartCountEl) {
                // تحديث فوري بدون انتظار
                fetch('/saved/count')
                    .then(response => response.json())
                    .then(data => {
                        const count = data.count || 0;
                        console.log('Mobile counter count:', count);
                        
                        // تحديث فوري للعداد
                        mobileCartCountEl.textContent = count;
                        if (count > 0) {
                            mobileCartCountEl.classList.remove('hidden');
                            console.log('Mobile cart counter shown:', count);
                            
                            // إضافة تأثير بصري للجذب
                            mobileCartCountEl.style.transform = 'scale(1.2)';
                            mobileCartCountEl.style.transition = 'transform 0.2s ease';
                            
                            setTimeout(() => {
                                mobileCartCountEl.style.transform = 'scale(1)';
                            }, 200);
                        } else {
                            mobileCartCountEl.classList.add('hidden');
                            console.log('Mobile cart counter hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating mobile counter:', error);
                    });
            }
        }
        
        // دالة تحديث فوري للعداد في الهاتف المحمول
        function updateMobileCounterInstant() {
            const mobileCartCountEl = document.getElementById('mobile-cart-count');
            if (mobileCartCountEl) {
                // تحديث فوري بدون انتظار
                fetch('/saved/count')
                    .then(response => response.json())
                    .then(data => {
                        const count = data.count || 0;
                        mobileCartCountEl.textContent = count;
                        if (count > 0) {
                            mobileCartCountEl.classList.remove('hidden');
                        } else {
                            mobileCartCountEl.classList.add('hidden');
                        }
                    })
                    .catch(() => {
                        // تحديث فوري حتى لو فشل الطلب
                        const currentCount = parseInt(mobileCartCountEl.textContent) || 0;
                        mobileCartCountEl.textContent = currentCount;
                    });
            }
        }
        
        // دالة تحديث فوري لعداد سلة التسوق في الأعلى
        function updateTopCartCounterInstant() {
            const topCartCountEl = document.getElementById('mobile-cart-count');
            if (topCartCountEl) {
                // تحديث فوري بدون انتظار
                fetch('/saved/count')
                    .then(response => response.json())
                    .then(data => {
                        const count = data.count || 0;
                        topCartCountEl.textContent = count;
                        if (count > 0) {
                            topCartCountEl.classList.remove('hidden');
                            
                            // إضافة تأثير بصري للجذب
                            topCartCountEl.style.transform = 'scale(1.3)';
                            topCartCountEl.style.transition = 'transform 0.3s ease';
                            
                            setTimeout(() => {
                                topCartCountEl.style.transform = 'scale(1)';
                            }, 300);
                        } else {
                            topCartCountEl.classList.add('hidden');
                        }
                    })
                    .catch(() => {
                        // تحديث فوري حتى لو فشل الطلب
                        const currentCount = parseInt(topCartCountEl.textContent) || 0;
                        topCartCountEl.textContent = currentCount;
                    });
            }
        }
        
        // جعل الدالة متاحة عالمياً
        window.updateCountersImmediately = updateCountersImmediately;
        window.updateMobileCounterImmediately = updateMobileCounterImmediately;
        window.updateMobileCounterInstant = updateMobileCounterInstant;
        window.updateTopCartCounterInstant = updateTopCartCounterInstant;
        
        // استماع للأحداث المخصصة لتحديث العداد
        document.addEventListener('toolSaved', function() {
            console.log('Tool saved event received, updating counters...');
            updateCountersImmediately();
        });
        
        document.addEventListener('toolRemoved', function() {
            console.log('Tool removed event received, updating counters...');
            updateCountersImmediately();
        });
        
        // استماع لتغييرات في localStorage
        window.addEventListener('storage', function(e) {
            if (e.key === 'savedToolsUpdated') {
                console.log('LocalStorage updated, refreshing counters...');
                updateCountersImmediately();
            }
        });
        
        // مراقبة تغييرات DOM للعدادات
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        // تحقق من وجود تغييرات في عناصر العداد
                        const counters = document.querySelectorAll('#saved-count, #mobile-cart-count, #saved-count-mobile');
                        counters.forEach(counter => {
                            if (mutation.target.contains(counter)) {
                                console.log('Counter element changed, updating...');
                                updateCountersImmediately();
                            }
                        });
                        
                        // تحقق من وجود تغييرات في أزرار الحفظ
                        const saveButtons = mutation.addedNodes;
                        saveButtons.forEach(node => {
                            if (node.nodeType === 1) { // Element node
                                if (node.classList && (node.classList.contains('save-tool-btn') || node.classList.contains('remove-tool-btn'))) {
                                    console.log('Save button added, updating counters...');
                                    updateCountersImmediately();
                                }
                            }
                        });
                    }
                });
            });
            
            // مراقبة تغييرات في body
            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class', 'data-tool-id']
            });
        }
        
        // مراقبة تغييرات في localStorage
        window.addEventListener('storage', function(e) {
            if (e.key === 'savedTools' || e.key === 'savedToolsUpdated') {
                console.log('LocalStorage changed, updating counters...');
                updateCountersImmediately();
            }
        });
        
        // مراقبة تغييرات في localStorage من نفس التبويب
        const originalSetItem = localStorage.setItem;
        localStorage.setItem = function(key, value) {
            originalSetItem.apply(this, arguments);
            if (key === 'savedTools' || key === 'savedToolsUpdated') {
                console.log('LocalStorage setItem detected, updating counters...');
                updateCountersImmediately();
            }
        };
        
        // مراقبة تغييرات في localStorage من نفس التبويب
        const originalRemoveItem = localStorage.removeItem;
        localStorage.removeItem = function(key) {
            originalRemoveItem.apply(this, arguments);
            if (key === 'savedTools' || key === 'savedToolsUpdated') {
                console.log('LocalStorage removeItem detected, updating counters...');
                updateCountersImmediately();
            }
        };
        
        // تحميل العداد عند تغيير الصفحة
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                console.log('Page became visible, reloading saved count...');
                loadSavedCount();
            }
        });
        
        // تحميل فوري للعداد
        setTimeout(function() {
            loadSavedCount();
        }, 1000);
        
        // تحديث فوري للعداد كل ثانية
        setInterval(function() {
            updateCountersImmediately();
        }, 1000);
        
        // مراقبة تغيير حجم الشاشة
        window.addEventListener('resize', function() {
            if (window.innerWidth <= 768) {
                console.log('Switched to mobile view, updating mobile counter...');
                updateTopCartCounterInstant();
                updateMobileCounterImmediately();
            }
        });
        
        // تحديث فوري للعداد في الهاتف المحمول عند تحميل الصفحة
        if (window.innerWidth <= 768) {
            console.log('Mobile view on page load, updating mobile counter...');
            updateTopCartCounterInstant();
            updateMobileCounterInstant();
            updateMobileCounterImmediately();
            setTimeout(() => {
                updateTopCartCounterInstant();
                updateMobileCounterInstant();
                updateMobileCounterImmediately();
            }, 100);
            setTimeout(() => {
                updateTopCartCounterInstant();
                updateMobileCounterInstant();
                updateMobileCounterImmediately();
            }, 500);
        }
        
        // تحديث فوري للعداد في الهاتف المحمول كل 500ms
        if (window.innerWidth <= 768) {
            setInterval(function() {
                updateTopCartCounterInstant();
                updateMobileCounterInstant();
            }, 500);
        }
        
        // إعادة تهيئة القائمة المنسدلة بعد تحميل الصفحة
        window.addEventListener('load', function() {
            console.log('Window loaded, re-initializing desktop dropdown...');
            initDesktopDropdown();
            
            // إعادة تهيئة القائمة المحمولة
            initializeMobileMenu();
        });
        
        // دالة منفصلة لتهيئة القائمة المحمولة
        function initializeMobileMenu() {
            console.log('Initializing mobile menu...');
            setupMobileMenu();
        }
        
        // الدوال المكررة تم حذفها - نستخدم الدوال الأساسية فقط
        
        // إضافة دالة اختبار بسيطة للقائمة الجانبية
        function testMobileMenu() {
            console.log('Testing mobile menu...');
            const mobileMenu = document.getElementById('mobileMenu');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');
            
            if (mobileMenu && menuIcon && closeIcon) {
                console.log('Elements found, toggling menu...');
                mobileMenu.classList.toggle('hidden');
                mobileMenu.classList.toggle('show');
                menuIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
                
                console.log('Menu state after toggle:', {
                    menuHidden: mobileMenu.classList.contains('hidden'),
                    menuShown: mobileMenu.classList.contains('show'),
                    menuIconHidden: menuIcon.classList.contains('hidden'),
                    closeIconHidden: closeIcon.classList.contains('hidden')
                });
            } else {
                console.error('Mobile menu elements not found for testing');
            }
        }
        
        // إضافة دالة تشخيص القائمة الجانبية
        function diagnoseMobileMenu() {
            console.log('Diagnosing mobile menu...');
            
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');
            
            console.log('Elements found:', {
                mobileMenuBtn: !!mobileMenuBtn,
                mobileMenu: !!mobileMenu,
                menuIcon: !!menuIcon,
                closeIcon: !!closeIcon
            });
            
            if (mobileMenu) {
                console.log('Mobile menu state:', {
                    classes: mobileMenu.className,
                    style: mobileMenu.style.cssText,
                    display: window.getComputedStyle(mobileMenu).display,
                    visibility: window.getComputedStyle(mobileMenu).visibility,
                    opacity: window.getComputedStyle(mobileMenu).opacity
                });
            }
            
            if (menuIcon && closeIcon) {
                console.log('Icons state:', {
                    menuIconHidden: menuIcon.classList.contains('hidden'),
                    closeIconHidden: closeIcon.classList.contains('hidden')
                });
            }
        }
        
        // جعل الدوال متاحة عالمياً
        window.initializeMobileMenu = initializeMobileMenu;
        window.setupMobileMenu = setupMobileMenu;
        window.testMobileMenu = testMobileMenu;
        window.diagnoseMobileMenu = diagnoseMobileMenu;
        
        // تهيئة إضافية للقائمة المحمولة بعد تأخير قصير
        setTimeout(function() {
            console.log('Delayed mobile menu initialization...');
            setupMobileMenu();
        }, 1000);
        
        // تهيئة إضافية عند تحميل الصفحة
        window.addEventListener('load', function() {
            console.log('Window loaded, setting up mobile menu...');
            setupMobileMenu();
        });
        
        // تهيئة إضافية للقائمة المحمولة عند تغيير حجم الشاشة
        window.addEventListener('resize', function() {
            if (window.innerWidth <= 768) {
                console.log('Screen resized to mobile, re-initializing mobile menu...');
                setTimeout(setupMobileMenu, 100);
            }
        });
        
        // تحديث فوري للعداد عند النقر على أزرار الحفظ
        document.addEventListener('click', function(e) {
            const target = e.target;
            const isSaveButton = target.closest('.save-tool-btn, .remove-tool-btn, .toggle-save-btn, .btn-save, .btn-remove, [data-tool-id]') ||
                               target.classList.contains('save-tool-btn') ||
                               target.classList.contains('remove-tool-btn') ||
                               target.classList.contains('toggle-save-btn') ||
                               target.classList.contains('btn-save') ||
                               target.classList.contains('btn-remove') ||
                               target.hasAttribute('data-tool-id') ||
                               target.textContent.includes('حفظ') ||
                               target.textContent.includes('إزالة') ||
                               target.textContent.includes('إضافة');
            
            if (isSaveButton) {
                console.log('Save/Remove button clicked, updating counters immediately...');
                
                // تحديث فوري بدون تأخير
                updateCountersImmediately();
                
                // تحديث إضافي بعد تأخير قصير للتأكد
                setTimeout(() => {
                    updateCountersImmediately();
                }, 100);
                
                setTimeout(() => {
                    updateCountersImmediately();
                }, 500);
                
                setTimeout(() => {
                    updateCountersImmediately();
                }, 1000);
            }
        });
        
        // مراقبة أكثر شمولية للنقر
        document.addEventListener('click', function(e) {
            // تحقق من أي عنصر يحتوي على كلمات الحفظ
            const target = e.target;
            const parent = target.parentElement;
            const grandParent = parent ? parent.parentElement : null;
            
            const isSaveRelated = target.textContent.includes('حفظ') ||
                                target.textContent.includes('إزالة') ||
                                target.textContent.includes('إضافة') ||
                                target.textContent.includes('Save') ||
                                target.textContent.includes('Remove') ||
                                (parent && parent.textContent.includes('حفظ')) ||
                                (parent && parent.textContent.includes('إزالة')) ||
                                (grandParent && grandParent.textContent.includes('حفظ')) ||
                                (grandParent && grandParent.textContent.includes('إزالة'));
            
            if (isSaveRelated) {
                console.log('Save-related element clicked, updating counters...');
                updateCountersImmediately();
                updateMobileCounterImmediately();
                setTimeout(() => {
                    updateCountersImmediately();
                    updateMobileCounterImmediately();
                }, 200);
                setTimeout(() => {
                    updateCountersImmediately();
                    updateMobileCounterImmediately();
                }, 500);
            }
        });
        
        // مراقبة خاصة للهاتف المحمول
        if (window.innerWidth <= 768) {
            console.log('Mobile device detected, setting up mobile-specific listeners...');
            
            // مراقبة النقر على أزرار الحفظ في الهاتف المحمول
            document.addEventListener('click', function(e) {
                const target = e.target;
                const isMobileSaveButton = target.closest('.save-tool-btn, .remove-tool-btn, .toggle-save-btn') ||
                                         target.classList.contains('save-tool-btn') ||
                                         target.classList.contains('remove-tool-btn') ||
                                         target.classList.contains('toggle-save-btn') ||
                                         target.textContent.includes('حفظ') ||
                                         target.textContent.includes('إزالة');
                
                if (isMobileSaveButton) {
                    console.log('Mobile save button clicked, updating mobile counter INSTANTLY...');
                    
                    // تحديث فوري بدون تأخير
                    updateMobileCounterImmediately();
                    
                    // تحديث فوري إضافي
                    setTimeout(() => updateMobileCounterImmediately(), 50);
                    setTimeout(() => updateMobileCounterImmediately(), 100);
                    setTimeout(() => updateMobileCounterImmediately(), 200);
                    setTimeout(() => updateMobileCounterImmediately(), 500);
                }
            });
            
            // تحديث دوري للعداد في الهاتف المحمول كل ثانية
            setInterval(function() {
                updateMobileCounterImmediately();
            }, 1000);
        }
        
        // مراقبة شاملة للهاتف المحمول - تعمل دائماً
        document.addEventListener('click', function(e) {
            // تحقق من أننا في وضع الهاتف المحمول
            if (window.innerWidth <= 768) {
                const target = e.target;
                const parent = target.parentElement;
                const grandParent = parent ? parent.parentElement : null;
                
                // فحص أكثر شمولية لأزرار الحفظ
                const isMobileSaveAction = target.closest('.save-tool-btn, .remove-tool-btn, .toggle-save-btn, .btn-save, .btn-remove') ||
                                         target.classList.contains('save-tool-btn') ||
                                         target.classList.contains('remove-tool-btn') ||
                                         target.classList.contains('toggle-save-btn') ||
                                         target.classList.contains('btn-save') ||
                                         target.classList.contains('btn-remove') ||
                                         target.textContent.includes('حفظ') ||
                                         target.textContent.includes('إزالة') ||
                                         target.textContent.includes('إضافة') ||
                                         (parent && parent.textContent.includes('حفظ')) ||
                                         (parent && parent.textContent.includes('إزالة')) ||
                                         (grandParent && grandParent.textContent.includes('حفظ')) ||
                                         (grandParent && grandParent.textContent.includes('إزالة'));
                
                if (isMobileSaveAction) {
                    console.log('Mobile save action detected, updating counter INSTANTLY...');
                    
                    // تحديث فوري متعدد
                    updateTopCartCounterInstant();
                    updateMobileCounterInstant();
                    updateMobileCounterImmediately();
                    updateCountersImmediately();
                    
                    // تحديث فوري إضافي
                    setTimeout(() => {
                        updateTopCartCounterInstant();
                        updateMobileCounterInstant();
                        updateMobileCounterImmediately();
                        updateCountersImmediately();
                    }, 10);
                    
                    setTimeout(() => {
                        updateTopCartCounterInstant();
                        updateMobileCounterInstant();
                        updateMobileCounterImmediately();
                        updateCountersImmediately();
                    }, 25);
                    
                    setTimeout(() => {
                        updateTopCartCounterInstant();
                        updateMobileCounterInstant();
                        updateMobileCounterImmediately();
                        updateCountersImmediately();
                    }, 50);
                    
                    setTimeout(() => {
                        updateTopCartCounterInstant();
                        updateMobileCounterInstant();
                        updateMobileCounterImmediately();
                        updateCountersImmediately();
                    }, 100);
                    
                    setTimeout(() => {
                        updateTopCartCounterInstant();
                        updateMobileCounterInstant();
                        updateMobileCounterImmediately();
                        updateCountersImmediately();
                    }, 200);
                }
            }
        });
        
        // مراقبة طلبات AJAX لتحديث العداد
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            return originalFetch.apply(this, args).then(response => {
                // تحقق من طلبات الحفظ/الحذف
                if (args[0] && (args[0].includes('/save-tool') || args[0].includes('/remove-tool') || args[0].includes('/saved') || args[0].includes('/api/saved'))) {
                    console.log('AJAX request detected, updating counters immediately...');
                    updateCountersImmediately();
                    setTimeout(() => {
                        updateCountersImmediately();
                    }, 200);
                }
                return response;
            }).catch(error => {
                console.error('Fetch error:', error);
                return error;
            });
        };
        
        // مراقبة XMLHttpRequest أيضاً
        const originalXHR = window.XMLHttpRequest;
        window.XMLHttpRequest = function() {
            const xhr = new originalXHR();
            const originalOpen = xhr.open;
            const originalSend = xhr.send;
            
            xhr.open = function(method, url, ...args) {
                this._url = url;
                return originalOpen.apply(this, [method, url, ...args]);
            };
            
            xhr.send = function(...args) {
                const url = this._url;
                if (url && (url.includes('/save-tool') || url.includes('/remove-tool') || url.includes('/saved'))) {
                    console.log('XHR request detected, updating counters...');
                    this.addEventListener('loadend', function() {
                        updateCountersImmediately();
                    });
                }
                return originalSend.apply(this, args);
            };
            
            return xhr;
        };
    </script>
    
    <!-- رسالة تسجيل الخروج -->
    @if(session('success') && str_contains(session('success'), 'تسجيل الخروج'))
        <div id="logout-toast" class="logout-toast">
            <i class="fas fa-check-circle icon"></i>
            <div>
                <div class="font-semibold">تم تسجيل الخروج بنجاح!</div>
                <div class="text-sm opacity-90">نراكم قريباً</div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.getElementById('logout-toast');
                if (toast) {
                    // إظهار الرسالة فوراً
                    toast.classList.add('show');
                    
                    // إخفاء الرسالة بعد 2 ثانية بدلاً من 3
                    setTimeout(() => {
                        toast.classList.remove('show');
                        setTimeout(() => {
                            toast.remove();
                        }, 300);
                    }, 2000);
                }
            });
        </script>
    @endif
    
    <!-- رسالة نجاح تسجيل الدخول/التسجيل -->
    @if(session('success') && (str_contains(session('success'), 'تسجيل الدخول') || str_contains(session('success'), 'إنشاء حساب')))
        <div id="auth-success-toast" class="auth-success-toast">
            <i class="fas fa-check-circle icon"></i>
            <div>
                <div class="font-semibold">{{ session('success') }}</div>
                <div class="text-sm opacity-90">استمتع بتجربتك في وصفة</div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.getElementById('auth-success-toast');
                if (toast) {
                    // إظهار الرسالة فوراً
                    toast.classList.add('show');
                    
                    // إخفاء الرسالة بعد 2 ثانية بدلاً من 4
                    setTimeout(() => {
                        toast.classList.remove('show');
                        setTimeout(() => {
                            toast.remove();
                        }, 300);
                    }, 2000);
                }
            });
        </script>
    @endif
    
    <!-- رسالة إعلامية -->
    @if(session('info'))
        <div id="info-toast" class="info-toast">
            <i class="fas fa-info-circle icon"></i>
            <div>
                <div class="font-semibold">{{ session('info') }}</div>
                <div class="text-sm opacity-90">تم إعادة التوجيه للصفحة الرئيسية</div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.getElementById('info-toast');
                if (toast) {
                    // إظهار الرسالة فوراً
                    toast.classList.add('show');
                    
                    // إخفاء الرسالة بعد 2 ثانية بدلاً من 4
                    setTimeout(() => {
                        toast.classList.remove('show');
                        setTimeout(() => {
                            toast.remove();
                        }, 300);
                    }, 2000);
                }
            });
        </script>
    @endif
    
    <!-- رسالة خطأ -->
    @if(session('error'))
        <div id="error-toast" class="error-toast">
            <i class="fas fa-exclamation-triangle icon"></i>
            <div>
                <div class="font-semibold">{{ session('error') }}</div>
                <div class="text-sm opacity-90">يرجى المحاولة مرة أخرى</div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.getElementById('error-toast');
                if (toast) {
                    // إظهار الرسالة فوراً
                    toast.classList.add('show');
                    
                    // إخفاء الرسالة بعد 2 ثانية بدلاً من 4
                    setTimeout(() => {
                        toast.classList.remove('show');
                        setTimeout(() => {
                            toast.remove();
                        }, 300);
                    }, 2000);
                }
            });
        </script>
    @endif
    
    <!-- دالة معالجة روابط Google Drive -->
    <script>
        function convertGoogleDriveUrl(url) {
            if (!url || !url.includes('drive.google.com')) {
                return url;
            }
            
            try {
                // تنسيق 1: https://drive.google.com/file/d/FILE_ID/view
                let match = url.match(/\/file\/d\/([a-zA-Z0-9-_]+)/);
                if (match && match[1]) {
                    return `https://lh3.googleusercontent.com/d/${match[1]}`;
                }
                
                // تنسيق 2: https://drive.google.com/open?id=FILE_ID
                if (url.includes('id=')) {
                    const urlParams = new URLSearchParams(new URL(url).search);
                    const fileId = urlParams.get('id');
                    if (fileId) {
                        return `https://lh3.googleusercontent.com/d/${fileId}`;
                    }
                }
                
                // تنسيق 3: https://drive.google.com/uc?id=FILE_ID
                if (url.includes('uc?id=')) {
                    const urlParams = new URLSearchParams(new URL(url).search);
                    const fileId = urlParams.get('id');
                    if (fileId) {
                        return `https://lh3.googleusercontent.com/d/${fileId}`;
                    }
                }
                
                // تنسيق 4: https://drive.google.com/thumbnail?id=FILE_ID
                if (url.includes('thumbnail?id=')) {
                    const urlParams = new URLSearchParams(new URL(url).search);
                    const fileId = urlParams.get('id');
                    if (fileId) {
                        return `https://lh3.googleusercontent.com/d/${fileId}`;
                    }
                }
                
                // تنسيق 5: استخراج ID من أي رابط Google Drive
                const idMatch = url.match(/[a-zA-Z0-9-_]{25,}/);
                if (idMatch) {
                    return `https://lh3.googleusercontent.com/d/${idMatch[0]}`;
                }
                
            } catch (error) {
                console.warn('Error converting Google Drive URL:', error);
            }
            
            return url;
        }
        
        // تطبيق التحويل على جميع الصور عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            // معالجة جميع الصور التي تحتوي على روابط Google Drive
            const images = document.querySelectorAll('img[src*="drive.google.com"]');
            images.forEach(function(img) {
                const originalSrc = img.src;
                const convertedSrc = convertGoogleDriveUrl(originalSrc);
                if (convertedSrc !== originalSrc) {
                    img.src = convertedSrc;
                }
            });
            
            // معالجة الصور التي يتم تحميلها ديناميكياً
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            if (node.tagName === 'IMG' && node.src && node.src.includes('drive.google.com')) {
                                const convertedSrc = convertGoogleDriveUrl(node.src);
                                if (convertedSrc !== node.src) {
                                    node.src = convertedSrc;
                                }
                            }
                            // معالجة الصور داخل العناصر المضافة
                            const images = node.querySelectorAll && node.querySelectorAll('img[src*="drive.google.com"]');
                            if (images) {
                                images.forEach(function(img) {
                                    const convertedSrc = convertGoogleDriveUrl(img.src);
                                    if (convertedSrc !== img.src) {
                                        img.src = convertedSrc;
                                    }
                                });
                            }
                        }
                    });
                });
            });
            
            // مراقبة التغييرات في DOM
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    </script>
</body>
</html>
