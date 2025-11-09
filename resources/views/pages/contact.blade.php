@extends('layouts.app')

@section('title', 'اتصل بنا - موقع وصفة')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl text-white p-12 mb-12 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">يسعدنا سماعك</h1>
            <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto leading-relaxed">
                فريق وصفة موجود لدعمك في كل ما يخص الوصفات، الورش، والشراكات. أرسل رسالتك وسنعود إليك خلال يوم عمل واحد.
            </p>
            <div class="flex flex-wrap justify-center gap-3 text-sm md:text-base mt-6">
                <span class="px-4 py-2 border border-white/40 bg-white/10 rounded-full">الورش والتدريب</span>
                <span class="px-4 py-2 border border-white/40 bg-white/10 rounded-full">طلبات التعاون</span>
                <span class="px-4 py-2 border border-white/40 bg-white/10 rounded-full">الدعم الفني</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div id="form" class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">أخبرنا كيف يمكننا مساعدتك</h2>
                <p class="text-gray-500 mb-6 leading-relaxed">املأ التفاصيل التالية لتصل رسالتك إلى الفريق المختص مباشرة. عادةً ما نرد خلال يوم عمل واحد.</p>
                
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

                <form id="contact-form" method="POST" action="{{ route('contact.send') }}" class="space-y-6">
                    @csrf
                    @include('pages.partials.contact-form-fields', [
                        'defaultSubject' => 'general',
                        'source' => 'contact-page',
                    ])
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <!-- Contact Details -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">معلومات الاتصال</h2>
                    <p class="text-gray-500 mb-6 leading-relaxed">اختر الطريقة الأنسب لك، وسيتابع فريق وصفة رسالتك بعناية لضمان حصولك على الدعم المناسب.</p>
                    
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
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-headset text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 mb-1">فريق الدعم</h3>
                                <p class="text-gray-600">نراجع الرسائل مرتين يوميًا خلال أيام العمل لضمان استجابة سريعة وواضحة.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4 rtl:space-x-reverse">
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-comments text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 mb-1">قنوات التواصل</h3>
                                <p class="text-gray-600">راسلنا عبر الرسائل المباشرة على إنستغرام أو أرسل طلبك عبر النموذج وسيتم تحويله للفريق المعني.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4 rtl:space-x-reverse">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 mb-1">مركز الرسائل</h3>
                                <p class="text-gray-600">كل الطلبات تُدار عبر نموذج التواصل لضمان متابعة مخصصة من فريق الدعم.</p>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <!-- Social Media -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-3">تابعنا</h2>
                    <p class="text-gray-500 mb-6 leading-relaxed">اكتشف أحدث الوصفات، تنبيهات الورش، ولقطات من خلف الكواليس على قنوات وصفة الاجتماعية.</p>
                    
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
                            <h3 class="font-bold text-gray-800 mb-2">متى يصلني الرد على رسالتي؟</h3>
                            <p class="text-gray-600 text-sm">نقوم بمراجعة البريد مرتين يوميًا، وستحصل على رد مبدئي خلال يوم عمل واحد كحد أقصى.</p>
                        </div>
                        
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="font-bold text-gray-800 mb-2">هل أستطيع طلب ورشة خاصة أو تعاون؟</h3>
                            <p class="text-gray-600 text-sm">بالطبع! شاركنا نوع التعاون أو الورشة التي تبحث عنها، وسننسق مع الفريق المتخصص ثم نعود إليك بالتفاصيل.</p>
                        </div>
                        
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="font-bold text-gray-800 mb-2">ماذا أفعل عند مواجهة مشكلة تقنية؟</h3>
                            <p class="text-gray-600 text-sm">أخبرنا بالصفحة التي ظهر فيها العطل والخطوات التي سبقت المشكلة، وسنرسل لك الحل أو نرتب جلسة مساعدة قصيرة إذا لزم الأمر.</p>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
