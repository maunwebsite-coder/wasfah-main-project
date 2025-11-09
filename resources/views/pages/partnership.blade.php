@extends('layouts.app')

@section('title', 'شريك وصفة - برنامج الشراكات')

@section('content')
<div class="bg-gray-50">
    <div class="min-h-screen container mx-auto px-4 py-12 space-y-12">
        <!-- Hero -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-3xl p-10 md:p-16 text-center shadow-xl">
            <div class="text-5xl mb-6">🤝</div>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">شريك وصفة</h1>
            <p class="text-xl md:text-2xl mb-4">انضم إلى شبكة وصفة وابدأ بجني الأرباح من محتوى الطهي الحقيقي</p>
            <p class="text-lg max-w-4xl mx-auto opacity-90">
                وصفة تجمع بين الشيفات، العلامات التجارية، والمحتوى التفاعلي في مكان واحد. عبر برنامج شريك وصفة ستحصل على دخل مستمر، صفحات شخصية جذابة، ولوحة تحكم واضحة تُظهر أرباحك لحظة بلحظة.
            </p>
            <div class="mt-10 flex flex-wrap gap-4 justify-center">
                <a href="#partner-form" class="bg-white text-orange-600 font-bold px-8 py-3 rounded-full shadow-lg hover:shadow-2xl transition">انضم الآن</a>
                <a href="#benefits" class="border border-white text-white font-bold px-8 py-3 rounded-full hover:bg-white/10 transition">تعرف على المزايا</a>
            </div>
        </div>

        <!-- Stats -->
        <section id="benefits" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                <div class="text-5xl font-extrabold text-orange-600 mb-2">15%</div>
                <p class="text-gray-600">حد أعلى للعمولة حسب الحملات</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                <div class="text-5xl font-extrabold text-orange-600 mb-2">24/7</div>
                <p class="text-gray-600">لوحة متابعة فورية للأرباح</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                <div class="text-5xl font-extrabold text-orange-600 mb-2">+80</div>
                <p class="text-gray-600">شيف يعتمدون Wasfa Links</p>
            </div>
        </section>

        <!-- Partner Features -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-3xl shadow-lg p-8 border-t-4 border-orange-500">
                <div class="text-4xl mb-4">💰</div>
                <h2 class="text-2xl font-bold mb-4">1. رابط الشريك والعمولات</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    كل شريك يحصل على رابط فريد داخل موقع وصفة يمكن مشاركته مع الشيفات أو عبر قنوات التسويق الخاصة به، وأي شيف ينشئ حساباً عبر هذا الرابط ويطلق ورشاته من خلال الموقع يُحتسب له كعميل تابع لك، وأي حجز يتم على تلك الورشات تضيف العمولة مباشرة إلى حسابك دون أي تدخل يدوي.
                </p>
                <ul class="space-y-3 text-gray-700">
                    <li>عمولة تبدأ من 5% وتصل إلى 15% حسب نوع الورشة أو الحملة.</li>
                    <li>لوحة متابعة فورية تُظهر الأرباح وعدد المشاركات القادمة.</li>
                    <li>إمكانية ربط الحملات الإعلانية بالرابط الخاص لتتبّع الأداء في الوقت الحقيقي.</li>
                </ul>
            </div>

            <div class="bg-white rounded-3xl shadow-lg p-8 border-t-4 border-orange-500">
                <div class="text-4xl mb-4">🔗</div>
                <h2 class="text-2xl font-bold mb-4">2. صفحة Wasfa Links للشيف</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    كل شيف يمتلك صفحته الخاصة عبر نظام Wasfa Links؛ صفحة ديناميكية شبيهة بـ Link in Bio تعرض وصفاته، الورش القادمة، وروابط التواصل الخاصة به.
                </p>
                <ul class="space-y-3 text-gray-700">
                    <li>تصميم قابل للتخصيص بالكامل (روابط، صور، ترتيب، أزرار).</li>
                    <li>إبراز الورشة التالية بزر واضح «احجز مكانك الآن».</li>
                    <li>تتبّع عدد الزيارات والنقرات لكل رابط.</li>
                    <li>إمكانية إنشاء أكثر من صفحة للشيف الواحد أو لفروع مختلفة.</li>
                </ul>
            </div>

            <div class="bg-white rounded-3xl shadow-lg p-8 border-t-4 border-orange-500">
                <div class="text-4xl mb-4">👨‍🍳</div>
                <h2 class="text-2xl font-bold mb-4">3. ماذا يفعل الشيف داخل وصفة؟</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    نقدّم للشيفات لوحة احترافية لإدارة كل ما يخص محتواهم بسهولة واحترافية، لتصبح وصفة منصتهم الأساسية لتضخيم الوجود الرقمي وزيادة المبيعات.
                </p>
                <ul class="space-y-3 text-gray-700">
                    <li>نشر وصفاتهم مع الصور والفيديوهات والتفاعل مع مجتمع وصفة.</li>
                    <li>حفظ الوصفات المفضلة في مكتبة خاصة ومشاركتها عبر إنستغرام وواتساب.</li>
                    <li>نشر ورشاتهم الخاصة ومتابعة المشاركين والحجوزات مباشرة.</li>
                </ul>
            </div>
        </section>

        <!-- CTA -->
        <section class="bg-white rounded-3xl shadow-xl p-10 text-center space-y-6">
            <div class="text-4xl">🚀</div>
            <h2 class="text-3xl font-bold text-gray-900">جاهز لتصبح شريك وصفة؟</h2>
            <p class="text-gray-600 text-lg max-w-3xl mx-auto">
                ابدأ اليوم بخطوات بسيطة وواضحة. فريق الشراكات سيرافقك خطوة بخطوة. بمجرد إكمال النموذج سيصلك كل ما تحتاجه خلال ثلاثة أيام عمل كحد أقصى.
            </p>
            <div class="flex flex-col md:flex-row justify-center gap-6 text-right">
                <div class="bg-gray-50 rounded-2xl p-6 flex-1">
                    <div class="text-sm text-orange-600 font-bold mb-2">1</div>
                    <h3 class="text-xl font-bold mb-2">عبّئ نموذج الانضمام</h3>
                    <p class="text-gray-600">أرسل بياناتك عبر صفحة التواصل وحدد نوع التعاون الذي تبحث عنه.</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6 flex-1">
                    <div class="text-sm text-orange-600 font-bold mb-2">2</div>
                    <h3 class="text-xl font-bold mb-2">استلم رابطك ولوحتك</h3>
                    <p class="text-gray-600">سيصلك رابطك الفريد، بيانات الدخول، ودليل الاستخدام خلال 3 أيام عمل.</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6 flex-1">
                    <div class="text-sm text-orange-600 font-bold mb-2">3</div>
                    <h3 class="text-xl font-bold mb-2">ابدأ بمشاركة الروابط</h3>
                    <p class="text-gray-600">شارك روابطك مع الشيفات والجمهور، وتتبع أرباحك مباشرة من لوحة التحكم.</p>
                </div>
            </div>
            <a href="#partner-form" class="inline-flex items-center justify-center bg-orange-500 text-white font-bold px-10 py-3 rounded-full shadow-lg hover:bg-orange-600 transition">
                انضم الآن إلى شبكة وصفة
            </a>
        </section>

        <!-- Guidance -->
        <section class="bg-gradient-to-br from-orange-100 to-white rounded-3xl p-10 shadow-inner">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">أخبرنا كيف يمكننا مساعدتك</h2>
            <p class="text-gray-700 text-lg mb-6">
                املأ التفاصيل التالية لتصل رسالتك إلى الفريق المختص مباشرة. عادةً ما نرد خلال يوم عمل واحد ونزوّدك بخطوات تفعيل الحساب ولوحة الشريك.
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li>اختَر نوع التعاون أو الشراكة التي تناسبك وأخبرنا عن الجمهور الذي تستهدفه.</li>
                <li>بعد استلام الطلب ستظهر بياناتك في لوحة الإدمن لمتابعة الحالة وخطوات الربط التالية.</li>
                <li>يصلك إشعار عبر البريد عند مراجعة الطلب أو طلب أي مستندات إضافية من فريق الشراكات.</li>
            </ul>
            <p class="text-gray-800 font-semibold">💡 نراجع الطلبات مرتين يومياً، وتظهر حالة كل طلب مباشرة في منطقة الإدمن.</p>
        </section>

        <!-- Form -->
        <section id="partner-form" class="bg-white rounded-3xl shadow-2xl p-10 space-y-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">نموذج طلب الشراكة</h2>
                <p class="text-gray-600">ارسل بياناتك ليصلك رابط الإدمن ولوحة المتابعة. سنقوم بإشعارك فور تسجيل الطلب داخل لوحة التحكم الخاصة بفريق الشراكات.</p>
            </div>
            <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الاسم الأول</label>
                    <input type="text" placeholder="أدخل اسمك الأول" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الاسم الأخير</label>
                    <input type="text" placeholder="أدخل اسمك الأخير" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" placeholder="example@email.com" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الهاتف (اختياري)</label>
                    <input type="text" placeholder="اكتب رقم هاتفك للتواصل (اختياري)" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الموضوع</label>
                    <input type="text" value="طلب شراكة أو تعاون" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الرسالة</label>
                    <textarea rows="5" placeholder="اكتب رسالتك هنا..." class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500"></textarea>
                    <p class="text-sm text-gray-500 mt-2">عادةً ما نرد خلال يوم عمل واحد.</p>
                </div>
                <div class="md:col-span-2 flex justify-between items-center flex-wrap gap-4">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-8 py-3 rounded-full shadow-lg transition">إرسال الرسالة</button>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">انضم الآن وكن جزءاً من شبكة وصفة</p>
                        <p class="text-gray-600">وصفة تجمع الشيفات والمحتوى التفاعلي في عالم واحد. ابدأ اليوم، ضاعف حضورك، وتابع أرباحك بكل شفافية.</p>
                    </div>
                </div>
            </form>
        </section>

        <!-- Footer CTA -->
        <section class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-3xl p-10 text-center space-y-4">
            <h2 class="text-3xl font-bold">قدّم طلب الشراكة</h2>
            <p class="text-lg">اطّلع على مزايا البرنامج وابدأ بتحقيق الدخل من شبكة وصفة.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#partner-form" class="bg-white text-orange-600 font-bold px-8 py-3 rounded-full shadow-lg hover:shadow-2xl transition">قدّم طلب الشراكة</a>
                <a href="#benefits" class="border border-white px-8 py-3 rounded-full font-bold hover:bg-white/10 transition">اطّلع على مزايا البرنامج</a>
            </div>
        </section>
    </div>
</div>
@endsection
