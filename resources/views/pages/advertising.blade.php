@extends('layouts.app')

@section('title', 'الإعلان - موقع وصفة')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl text-white p-12 mb-12 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">الإعلان معنا</h1>
            <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto">
                اعلن منتجك أو خدمتك على منصة وصفة ووصل إلى آلاف محبي الطبخ والحلويات
            </p>
        </div>

        <!-- Why Advertise Section -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">لماذا تعلن معنا؟</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-orange-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-orange-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">جمهور مستهدف</h3>
                    <p class="text-gray-600">
                        أكثر من 10,000 مستخدم نشط من محبي الطبخ والحلويات في المنطقة العربية
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-chart-line text-green-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">معدلات تفاعل عالية</h3>
                    <p class="text-gray-600">
                        معدل تفاعل يصل إلى 15% مع جمهور متحمس ومهتم بالمنتجات الغذائية
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-mobile-alt text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">تغطية شاملة</h3>
                    <p class="text-gray-600">
                        إعلانات تظهر على جميع الأجهزة: الهواتف، الأجهزة اللوحية، وأجهزة الكمبيوتر
                    </p>
                </div>
            </div>
        </div>

        <!-- Advertising Packages -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">باقات الإعلان</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Basic Package -->
                <div class="border-2 border-gray-200 rounded-xl p-8 hover:border-orange-500 transition-colors">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">الباقة الأساسية</h3>
                        <div class="text-4xl font-bold text-orange-600 mb-2">$99</div>
                        <div class="text-gray-500">شهرياً</div>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>إعلان بانر في الصفحة الرئيسية</span>
                        </li>
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>100,000 عرض شهرياً</span>
                        </li>
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>رابط مباشر لموقعك</span>
                        </li>
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>تقارير أداء شهرية</span>
                        </li>
                    </ul>
                    <button class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-medium transition-colors">
                        اختر الباقة
                    </button>
                </div>

                <!-- Premium Package -->
                <div class="border-2 border-orange-500 rounded-xl p-8 relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-orange-500 text-white px-4 py-2 rounded-full text-sm font-medium">الأكثر شعبية</span>
                    </div>
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">الباقة المميزة</h3>
                        <div class="text-4xl font-bold text-orange-600 mb-2">$199</div>
                        <div class="text-gray-500">شهرياً</div>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>إعلان بانر في جميع الصفحات</span>
                        </li>
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>300,000 عرض شهرياً</span>
                        </li>
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>إعلان في النشرة الإخبارية</span>
                        </li>
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>تخصيص تصميم الإعلان</span>
                        </li>
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>تقارير أداء أسبوعية</span>
                        </li>
                    </ul>
                    <button class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-medium transition-colors">
                        اختر الباقة
                    </button>
                </div>

                <!-- Enterprise Package -->
                <div class="border-2 border-gray-200 rounded-xl p-8 hover:border-orange-500 transition-colors">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">باقة المؤسسات</h3>
                        <div class="text-4xl font-bold text-orange-600 mb-2">$399</div>
                        <div class="text-gray-500">شهرياً</div>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>إعلانات مخصصة في جميع الأماكن</span>
                        </li>
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>1,000,000 عرض شهرياً</span>
                        </li>
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>مقالات مدفوعة</span>
                        </li>
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>دعم فني مخصص</span>
                        </li>
                        <li class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fas fa-check text-green-500"></i>
                            <span>تقارير أداء يومية</span>
                        </li>
                    </ul>
                    <button class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-medium transition-colors">
                        اختر الباقة
                    </button>
                </div>
            </div>
        </div>

        <!-- Ad Formats -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">أشكال الإعلانات</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">إعلانات البانر</h3>
                        <div class="bg-gray-100 h-32 rounded-lg flex items-center justify-center mb-4">
                            <span class="text-gray-500">عرض الإعلان هنا</span>
                        </div>
                        <p class="text-gray-600">
                            إعلانات بانر بأحجام مختلفة تظهر في أعلى وأسفل الصفحات
                        </p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">إعلانات الجانب</h3>
                        <div class="bg-gray-100 h-48 rounded-lg flex items-center justify-center mb-4">
                            <span class="text-gray-500">إعلان جانبي</span>
                        </div>
                        <p class="text-gray-600">
                            إعلانات تظهر في الشريط الجانبي للموقع
                        </p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">إعلانات المحتوى</h3>
                        <div class="bg-gray-100 h-32 rounded-lg flex items-center justify-center mb-4">
                            <span class="text-gray-500">إعلان محتوى</span>
                        </div>
                        <p class="text-gray-600">
                            إعلانات تظهر بين محتوى الوصفات والمقالات
                        </p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">إعلانات الفيديو</h3>
                        <div class="bg-gray-100 h-32 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-play-circle text-4xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-600">
                            إعلانات فيديو قصيرة تظهر قبل أو أثناء المحتوى
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl text-white p-12 text-center">
            <h2 class="text-3xl font-bold mb-6">ابدأ إعلانك اليوم</h2>
            <p class="text-xl opacity-90 mb-8 max-w-2xl mx-auto">
                تواصل معنا لمعرفة المزيد عن خيارات الإعلان المتاحة والحصول على عرض سعر مخصص
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div class="text-center">
                    <i class="fas fa-envelope text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-2">البريد الإلكتروني</h3>
                    <p class="opacity-90">wasfah99@gmail.com</p>
                </div>
                
                <div class="text-center">
                    <i class="fas fa-phone text-4xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-2">الهاتف</h3>
                    <p class="opacity-90">+962 6 123 4567</p>
                </div>
                
            </div>
            
            <button class="bg-white text-orange-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition-colors">
                تواصل معنا الآن
            </button>
        </div>
    </div>
</div>
@endsection

