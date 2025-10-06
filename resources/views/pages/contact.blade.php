@extends('layouts.app')

@section('title', 'اتصل بنا - موقع وصفة')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl text-white p-12 mb-12 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">اتصل بنا</h1>
            <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto">
                نحن هنا لمساعدتك! تواصل معنا لأي استفسار أو اقتراح
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">أرسل لنا رسالة</h2>
                
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-check-circle ml-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-exclamation-triangle ml-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.send') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الأول</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('first_name') border-red-500 @enderror" placeholder="أدخل اسمك الأول" required>
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الأخير</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('last_name') border-red-500 @enderror" placeholder="أدخل اسمك الأخير" required>
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('email') border-red-500 @enderror" placeholder="example@email.com" required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('phone') border-red-500 @enderror" placeholder="+962 7X XXX XXXX">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الموضوع</label>
                        <select name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('subject') border-red-500 @enderror" required>
                            <option value="">اختر الموضوع</option>
                            <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>استفسار عام</option>
                            <option value="recipe" {{ old('subject') == 'recipe' ? 'selected' : '' }}>مشكلة في وصفة</option>
                            <option value="workshop" {{ old('subject') == 'workshop' ? 'selected' : '' }}>استفسار عن ورشة عمل</option>
                            <option value="technical" {{ old('subject') == 'technical' ? 'selected' : '' }}>مشكلة تقنية</option>
                            <option value="suggestion" {{ old('subject') == 'suggestion' ? 'selected' : '' }}>اقتراح</option>
                            <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                        @error('subject')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الرسالة</label>
                        <textarea name="message" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('message') border-red-500 @enderror" placeholder="اكتب رسالتك هنا..." required>{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-4 rounded-lg font-bold text-lg transition-colors">
                        إرسال الرسالة
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <!-- Contact Details -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">معلومات الاتصال</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4 rtl:space-x-reverse">
                            <div class="bg-orange-100 p-3 rounded-full">
                                <i class="fas fa-map-marker-alt text-orange-600"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 mb-1">العنوان</h3>
                                <p class="text-gray-600">عمان، الأردن</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4 rtl:space-x-reverse">
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-phone text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 mb-1">الهاتف</h3>
                                <p class="text-gray-600">+962 6 123 4567</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4 rtl:space-x-reverse">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 mb-1">البريد الإلكتروني</h3>
                                <p class="text-gray-600">wasfah99@gmail.com</p>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <!-- Social Media -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">تابعنا</h2>
                    
                    <div class="space-y-4">
                        <a href="https://www.instagram.com/wasfah.jo/" target="_blank" class="flex items-center space-x-4 rtl:space-x-reverse p-4 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-shadow">
                            <i class="fab fa-instagram text-2xl"></i>
                            <div>
                                <h3 class="font-bold">إنستغرام</h3>
                                <p class="text-sm opacity-90">@wasfah.jo</p>
                            </div>
                        </a>
                        
                        
                        <a href="https://www.youtube.com/@wasfah.jordan" target="_blank" class="flex items-center space-x-4 rtl:space-x-reverse p-4 bg-red-600 text-white rounded-lg hover:shadow-lg transition-shadow">
                            <i class="fab fa-youtube text-2xl"></i>
                            <div>
                                <h3 class="font-bold">يوتيوب</h3>
                                <p class="text-sm opacity-90">@wasfah.jordan</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- FAQ -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">الأسئلة الشائعة</h2>
                    
                    <div class="space-y-4">
                        
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="font-bold text-gray-800 mb-2">كيف أحجز ورشة عمل؟</h3>
                            <p class="text-gray-600 text-sm">اذهب إلى صفحة "ورشات العمل" واختر الورشة التي تريدها ثم اضغط "احجز الآن" والذي سينقلك مباشرة إلى الواتساب لملء البيانات المطلوبة.</p>
                        </div>
                        
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="font-bold text-gray-800 mb-2">هل يمكنني تعديل معلومات حسابي؟</h3>
                            <p class="text-gray-600 text-sm">نعم، يمكنك تعديل جميع معلوماتك من صفحة الملف الشخصي</p>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

