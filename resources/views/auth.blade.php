@extends('layouts.auth')

@section('title', 'تسجيل الدخول - وصفة')

@push('styles')
<style>
    .auth-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    .auth-card {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        backdrop-filter: blur(10px);
    }
    .google-btn {
        background: #fff;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .google-btn:hover {
        border-color: #f97316;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .google-btn:active {
        transform: translateY(0);
    }
    
    .welcome-text {
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    /* Mobile Responsive Improvements */
    @media (max-width: 768px) {
        .auth-container {
            padding: 1rem;
        }
        
        .auth-card {
            flex-direction: column;
        }
        
        .auth-image-section {
            height: 200px;
            order: 1;
        }
        
        .auth-form-section {
            order: 2;
            padding: 2rem 1.5rem;
        }
        
        .auth-logo {
            height: 3rem;
            margin-bottom: 1rem;
        }
        
        .auth-title {
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
        }
        
        .auth-subtitle {
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .google-btn {
            padding: 0.875rem 1rem;
            font-size: 1rem;
        }
        
        .auth-terms {
            font-size: 0.75rem;
            margin-top: 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .auth-container {
            padding: 0.5rem;
        }
        
        .auth-form-section {
            padding: 1.5rem 1rem;
        }
        
        .auth-logo {
            height: 2.5rem;
        }
        
        .auth-title {
            font-size: 1.5rem;
        }
        
        .auth-subtitle {
            font-size: 0.9rem;
        }
        
        .google-btn {
            padding: 0.75rem 0.875rem;
            font-size: 0.9rem;
        }
        
        .auth-terms {
            font-size: 0.7rem;
        }
    }
</style>
@endpush

@section('content')
<div class="auth-container flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-5xl">
        <div class="flex flex-col lg:flex-row bg-white rounded-2xl shadow-2xl overflow-hidden auth-card">
            <!-- Left side - Image with overlay -->
            <div class="lg:w-1/2 relative auth-image-section">
                <div class="h-80 lg:h-full bg-cover bg-center relative" style="background-image: url('{{ asset('image/Brownies.png') }}');">
                    <div class="image-overlay absolute inset-0"></div>
                    <div class="absolute inset-0 flex items-center justify-center p-8">
                        <div class="text-center text-white">
                            <div class="mt-6 flex justify-center">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Auth form -->
            <div class="lg:w-1/2 p-8 lg:p-12 flex flex-col justify-center auth-form-section">
                <div class="text-center mb-8">
                    <a href="{{ route('home') }}" class="inline-block group">
                        <img src="{{ asset('image/logo.png') }}" alt="Logo" class="h-20 w-auto mb-6 group-hover:scale-105 transition-transform duration-300 auth-logo">
                    </a>
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4 auth-title">مرحباً بك في وصفة</h1>
                    <p class="text-gray-600 text-lg auth-subtitle">انضم لمجتمعنا – سجّل دخولك أو أنشئ حسابًا جديدًا</p>
                </div>
                
                <div class="w-full max-w-sm mx-auto space-y-6">
                    <!-- Traditional Login Form -->
                    <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                        @csrf
                        <!-- حقل مخفي لتخزين معرف الورشة -->
                        <input type="hidden" name="pending_workshop_booking" id="pending_workshop_booking" value="">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                            <input type="email" id="email" name="email" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300"
                                   placeholder="أدخل بريدك الإلكتروني">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                            <input type="password" id="password" name="password" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300"
                                   placeholder="أدخل كلمة المرور">
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-orange-500 text-white py-3 px-4 rounded-xl font-semibold hover:bg-orange-600 transition-colors duration-300">
                            تسجيل الدخول
                        </button>
                    </form>
                    
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">أو</span>
                        </div>
                    </div>
                    
                    <!-- Google Auth Button -->
                    <button onclick="redirectToGoogleAuth()" class="google-btn w-full flex items-center justify-center p-4 rounded-xl font-semibold text-gray-700 group">
                        <img src="https://img.icons8.com/color/28/google-logo.png" class="ml-3 group-hover:scale-110 transition-transform duration-300"/>
                        <span class="text-lg">المتابعة مع جوجل</span>
                    </button>
                </div>
                
                <div class="mt-8 text-center auth-terms">
                    <p class="text-xs text-gray-400">
                        بالدخول إلى الموقع، فإنك توافق على 
                        <a href="#" class="text-orange-500 hover:underline">شروط الخدمة</a>
                        و
                        <a href="#" class="text-orange-500 hover:underline">سياسة الخصوصية</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // قراءة معرف الورشة من URL
    const urlParams = new URLSearchParams(window.location.search);
    const workshopId = urlParams.get('pending_workshop_booking');
    
    if (workshopId) {
        // تعبئة الحقل المخفي
        document.getElementById('pending_workshop_booking').value = workshopId;
    }
});

// دالة التوجيه لـ Google OAuth مع معرف الورشة
function redirectToGoogleAuth() {
    const workshopId = document.getElementById('pending_workshop_booking').value;
    if (workshopId) {
        window.location.href = `/auth/google/redirect?pending_workshop_booking=${workshopId}`;
    } else {
        window.location.href = '/auth/google/redirect';
    }
}
</script>
@endpush
