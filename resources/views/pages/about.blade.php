@extends('layouts.app')

@section('title', 'عن وصفة - موقع وصفة')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl text-white p-12 mb-12 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">عن وصفة</h1>
            <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto">
                منصة متخصصة في عالم الحلويات والطبخ، نقدم لك أفضل الوصفات وورشات العمل
            </p>
        </div>

        <!-- Mission Section -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">رؤيتنا</h2>
                    <p class="text-gray-600 text-lg leading-relaxed mb-6">
                        نهدف إلى أن نكون المنصة الرائدة في عالم الحلويات والطبخ في المنطقة العربية، 
                        من خلال تقديم وصفات عالية الجودة وورشات عمل متخصصة تساعد محبي الطبخ 
                        على تطوير مهاراتهم واكتشاف عالم جديد من النكهات.
                    </p>
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <div class="bg-orange-100 p-3 rounded-full">
                            <i class="fas fa-heart text-orange-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">شغفنا بالطبخ</h3>
                            <p class="text-gray-600">نؤمن أن الطبخ فن وحب</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl p-8 text-center">
                    <i class="fas fa-utensils text-6xl text-orange-600 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">أكثر من 25 وصفة</h3>
                    <p class="text-gray-600">وصفات متنوعة من جميع أنحاء العالم</p>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-book text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">وصفات متنوعة</h3>
                <p class="text-gray-600">
                    مجموعة شاملة من الوصفات من الحلويات التقليدية إلى الأطباق الحديثة
                </p>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow">
                <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-graduation-cap text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">ورشات عمل</h3>
                <p class="text-gray-600">
                    ورشات عمل تفاعلية مع أفضل الشيفات لتعلم تقنيات الطبخ المتقدمة
                </p>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow">
                <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-users text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">مجتمع متحمس</h3>
                <p class="text-gray-600">
                    انضم إلى مجتمع من محبي الطبخ وشارك تجاربك ووصفاتك
                </p>
            </div>
        </div>

        <!-- Story Section -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">قصتنا</h2>
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">البداية</h3>
                        <p class="text-gray-600 leading-relaxed">
                            بدأت وصفة كفكرة بسيطة من مجموعة من محبي الطبخ الذين أرادوا مشاركة 
                            شغفهم مع الآخرين. من خلال سنوات من الخبرة والتطوير، نعمل على 
                            تقديم أفضل تجربة في عالم الحلويات والطبخ.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">التطور</h3>
                        <p class="text-gray-600 leading-relaxed">
                            تطورنا من مجرد موقع وصفات إلى منصة شاملة تشمل ورشات العمل، 
                            التقييمات، والمجتمع التفاعلي. نستمر في إضافة ميزات جديدة 
                            لتحسين تجربة المستخدمين.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl text-white p-12 mb-12">
            <h2 class="text-3xl font-bold text-center mb-12">إحصائياتنا</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold mb-2">25+</div>
                    <div class="text-orange-200">وصفة</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">5+</div>
                    <div class="text-orange-200">ورشة عمل</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">100+</div>
                    <div class="text-orange-200">مستخدم</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">8+</div>
                    <div class="text-orange-200">شيف</div>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">فريقنا</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-gradient-to-br from-orange-400 to-orange-600 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">فريق التطوير</h3>
                    <p class="text-gray-600">مطورون مبدعون يعملون على تحسين المنصة</p>
                </div>
                <div class="text-center">
                    <div class="bg-gradient-to-br from-green-400 to-green-600 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chef-hat text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">الشيفات</h3>
                    <p class="text-gray-600">شيفات محترفون يشاركون خبراتهم</p>
                </div>
                <div class="text-center">
                    <div class="bg-gradient-to-br from-blue-400 to-blue-600 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">فريق الدعم</h3>
                    <p class="text-gray-600">فريق دعم متاح لمساعدتك دائماً</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

