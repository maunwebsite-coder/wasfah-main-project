@extends('layouts.app')

@section('title', 'نصائح الحلويات الفاخرة - منصّة وصفة')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl text-white p-12 mb-12 text-center shadow-2xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">نصائح الحلويات الفاخرة</h1>
            <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto">
                أسرار صنع أرقى الحلويات العالمية من التيراميسو الإيطالي إلى الشوكولاتة البلجيكية الفاخرة
            </p>
        </div>

        <!-- Tips Categories -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <!-- Basic Tips -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-cookie-bite text-orange-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">نصائح أساسية للحلويات</h2>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex items-start space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>اقرأ الوصفة كاملة قبل البدء</span>
                    </li>
                    <li class="flex items-start space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>جهز جميع المكونات مسبقاً</span>
                    </li>
                    <li class="flex items-start space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>استخدم ميزان دقيق للمقادير</span>
                    </li>
                    <li class="flex items-start space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>سخن الفرن قبل وضع الحلويات</span>
                    </li>
                </ul>
            </div>

            <!-- Temperature Tips -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-thermometer-half text-red-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">درجة الحرارة للحلويات</h2>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex items-start space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>دع المكونات تصل لدرجة حرارة الغرفة</span>
                    </li>
                    <li class="flex items-start space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>استخدم ميزان حرارة للفرن</span>
                    </li>
                    <li class="flex items-start space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>لا تفتح الفرن في أول 10 دقائق</span>
                    </li>
                    <li class="flex items-start space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>اترك الحلويات تبرد قبل التقطيع</span>
                    </li>
                </ul>
            </div>

            <!-- Ingredient Tips -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-candy-cane text-green-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">مكونات الحلويات</h2>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex items-start space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>استخدم شوكولاتة عالية الجودة</span>
                    </li>
                    <li class="flex items-start space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>تأكد من صلاحية البيض</span>
                    </li>
                    <li class="flex items-start space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>انخل الدقيق والسكر قبل الاستخدام</span>
                    </li>
                    <li class="flex items-start space-x-3 rtl:space-x-reverse">
                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                        <span>استخدم الزبدة الطرية للكريمات</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Detailed Tips Section -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">نصائح مفصلة للحلويات</h2>
            
            <div class="space-y-8">
                <!-- Tip 1 -->
                <div class="border-l-4 border-orange-500 pl-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">1. تحضير الكيك</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        عند تحضير الكيك، تأكد من خلط المكونات الجافة والرطبة بشكل منفصل أولاً. 
                        اخلط الزبدة والسكر حتى يصبح الخليط فاتح اللون ومتجانس، ثم أضف البيض تدريجياً.
                    </p>
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <p class="text-orange-800 font-medium">
                            <i class="fas fa-lightbulb ml-2"></i>
                            نصيحة: لا تخلط العجين أكثر من اللازم لتجنب جفاف الكيك
                        </p>
                    </div>
                </div>

                <!-- Tip 2 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">2. تحضير الكريمة</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        لتحضير كريمة ناعمة ومتجانس، تأكد من أن الزبدة في درجة حرارة الغرفة. 
                        ابدأ بخلط الزبدة وحدها، ثم أضف السكر تدريجياً، وأخيراً أضف السائل.
                    </p>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-green-800 font-medium">
                            <i class="fas fa-utensils ml-2"></i>
                            نصيحة: استخدم خلاط كهربائي للحصول على كريمة ناعمة ومتجانس
                        </p>
                    </div>
                </div>

                <!-- Tip 3 -->
                <div class="border-l-4 border-blue-500 pl-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">3. تذويب الشوكولاتة</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        لتذويب الشوكولاتة بشكل صحيح، استخدم حمام مائي (بين ماري). 
                        ضع الشوكولاتة في وعاء فوق ماء ساخن وليس مغلي، وقلبها باستمرار.
                    </p>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-blue-800 font-medium">
                            <i class="fas fa-fire ml-2"></i>
                            نصيحة: لا تدع الماء يصل للشوكولاتة مباشرة، وإلا ستتكتل
                        </p>
                    </div>
                </div>

                <!-- Tip 4 -->
                <div class="border-l-4 border-purple-500 pl-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">4. تزيين الحلويات</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        عند تزيين الحلويات، ابدأ بالتزيين البسيط ثم انتقل للمعقد. 
                        استخدم أكياس التزيين وأدوات مناسبة، وتأكد من أن الكريمة باردة قبل التزيين.
                    </p>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <p class="text-purple-800 font-medium">
                            <i class="fas fa-palette ml-2"></i>
                            نصيحة: استخدم ألوان طعام طبيعية للحصول على ألوان جميلة وآمنة
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Common Mistakes Section -->
        <div class="bg-red-50 rounded-2xl p-8 mb-12">
            <h2 class="text-3xl font-bold text-red-800 mb-8 text-center">أخطاء شائعة في عمل الحلويات</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-times-circle text-red-500 ml-3"></i>
                        خلط العجين أكثر من اللازم
                    </h3>
                    <p class="text-gray-600">
                        خلط العجين لفترة طويلة يسبب تكون الغلوتين الزائد مما يجعل الكيك مطاطي وجاف. 
                        اخلط فقط حتى تختلط المكونات.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-times-circle text-red-500 ml-3"></i>
                        عدم تبريد المكونات
                    </h3>
                    <p class="text-gray-600">
                        استخدام مكونات باردة يمنع الخلط الجيد. دع الزبدة والبيض يصلان لدرجة حرارة الغرفة 
                        قبل الاستخدام.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-times-circle text-red-500 ml-3"></i>
                        فتح الفرن كثيراً
                    </h3>
                    <p class="text-gray-600">
                        فتح باب الفرن أثناء الخبز يسبب انخفاض درجة الحرارة ويمكن أن يفسد الحلويات. 
                        انتظر حتى آخر 5 دقائق للتحقق من النضج.
                    </p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-times-circle text-red-500 ml-3"></i>
                        تقطيع الحلويات ساخنة
                    </h3>
                    <p class="text-gray-600">
                        تقطيع الحلويات وهي ساخنة يسبب انهيارها. اتركها تبرد تماماً في الرف 
                        قبل التقطيع أو التزيين.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tools Section -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">أدوات عمل الحلويات الأساسية</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="bg-orange-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-balance-scale text-orange-600 text-3xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">ميزان رقمي</h3>
                    <p class="text-gray-600 text-sm">لقياس المكونات بدقة</p>
                </div>

                <div class="text-center">
                    <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-thermometer-half text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">ميزان حرارة</h3>
                    <p class="text-gray-600 text-sm">لقياس درجة حرارة الفرن</p>
                </div>

                <div class="text-center">
                    <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-cookie-bite text-green-600 text-3xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">أقواس الكيك</h3>
                    <p class="text-gray-600 text-sm">أقواس مختلفة الأحجام</p>
                </div>

                <div class="text-center">
                    <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-utensils text-purple-600 text-3xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">خلاط كهربائي</h3>
                    <p class="text-gray-600 text-sm">لخلط المكونات بسهولة</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
