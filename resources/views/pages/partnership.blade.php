@extends('layouts.app')

@section('title', 'شريك وصفة - برنامج الشراكات')
@section('content')
<div class="relative bg-gradient-to-b from-orange-50 via-white to-white overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -left-32 top-6 w-72 h-72 bg-orange-200 rounded-full blur-3xl opacity-30"></div>
        <div class="absolute right-0 bottom-0 w-96 h-96 bg-rose-200 rounded-full blur-3xl opacity-40 translate-x-1/2 translate-y-1/3"></div>
        <div class="absolute left-1/2 top-1/3 -translate-x-1/2 w-96 h-96 bg-white/40 border border-orange-100 rounded-full blur-2xl"></div>
    </div>
    <div class="relative min-h-screen container mx-auto px-4 py-12 space-y-12 z-10">
        <!-- Hero -->
        <div class="relative bg-gradient-to-r from-orange-500 via-orange-600 to-rose-500 text-white rounded-[2.5rem] p-10 md:p-16 text-center shadow-[0_40px_80px_-40px_rgba(249,115,22,0.9)] overflow-hidden">
            <div class="absolute inset-x-16 inset-y-10 bg-white/10 blur-3xl rounded-[3rem]"></div>
            <div class="relative space-y-6">
                <div class="inline-flex items-center gap-2 bg-white/15 border border-white/30 text-white px-6 py-2 rounded-full text-sm tracking-wide backdrop-blur">
                    <i class="fas fa-fire text-lg"></i>
                    <span>شبكة الشركاء الأولى لمحتوى الطهي</span>
                </div>
                <div class="text-5xl">
                    <i class="fas fa-handshake text-white"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-black leading-snug">
                    شريك وصفة
                    <span class="block text-2xl md:text-3xl font-semibold text-orange-100 mt-2">مصدر دخل مستمر من محتوى الطهي الحقيقي</span>
                </h1>
                <p class="text-lg md:text-xl max-w-4xl mx-auto text-white/90">
                    وصفة تجمع بين الشيفات، العلامات التجارية، والمحتوى التفاعلي في مكان واحد. عبر برنامج شريك وصفة ستحصل على صفحات شخصية جذابة، لوحات بيانات لحظية، وأدوات تسويق ذكية تعكس أرباحك فوراً.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-3xl mx-auto mt-10 text-sm font-semibold">
                    <div class="bg-white/15 rounded-2xl py-4 px-6 border border-white/20">مدفوعات تلقائية</div>
                    <div class="bg-white/15 rounded-2xl py-4 px-6 border border-white/20">روابط ذكية قابلة للتتبع</div>
                    <div class="bg-white/15 rounded-2xl py-4 px-6 border border-white/20">دعم مخصص للشركاء</div>
                </div>
                <div class="mt-10 flex flex-wrap gap-4 justify-center">
                    <a href="#partner-form" class="bg-white text-orange-600 font-bold px-10 py-3 rounded-full shadow-2xl hover:-translate-y-0.5 hover:shadow-[0_20px_40px_rgba(255,255,255,0.35)] transition">انضم الآن</a>
                    <a href="#benefits" class="border border-white/60 text-white font-bold px-10 py-3 rounded-full hover:bg-white/10 transition">تعرف على المزايا</a>
                </div>
            </div>
            <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-white/20 rounded-full blur-2xl opacity-70"></div>
        </div>

        <!-- Stats -->
        <section id="benefits" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="group bg-white/80 backdrop-blur rounded-3xl shadow-xl p-8 text-center border border-orange-100 hover:-translate-y-1 transition">
                <div class="text-4xl mb-3 text-orange-500">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-rose-500 mb-2">15%</div>
                <p class="text-gray-600">حد أعلى للعمولة حسب الحملات</p>
            </div>
            <div class="group bg-white/80 backdrop-blur rounded-3xl shadow-xl p-8 text-center border border-orange-100 hover:-translate-y-1 transition">
                <div class="text-4xl mb-3 text-orange-500">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-rose-500 mb-2">24/7</div>
                <p class="text-gray-600">لوحة متابعة فورية للأرباح</p>
            </div>
            <div class="group bg-white/80 backdrop-blur rounded-3xl shadow-xl p-8 text-center border border-orange-100 hover:-translate-y-1 transition">
                <div class="text-4xl mb-3 text-orange-500">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-rose-500 mb-2">+80</div>
                <p class="text-gray-600">شيف يعتمدون Wasfa Links</p>
            </div>
        </section>

        <div class="text-center space-y-3">
            <span class="inline-flex items-center px-4 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold text-sm">لماذا الشركاء يختارون وصفة؟</span>
            <h2 class="text-3xl md:text-4xl font-black text-gray-900">ثلاث ركائز تضمن نمو عائداتك</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">منصة تقنية متكاملة، محتوى جذاب، وفريق دعم يرافقك خطوة بخطوة لزيادة الدخل من كل ورشة أو وصفة.</p>
        </div>

        <!-- Partner Features -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="group relative bg-white rounded-3xl shadow-2xl p-8 border border-orange-100 overflow-hidden hover:-translate-y-2 transition">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-white opacity-0 group-hover:opacity-100 transition"></div>
                <div class="relative space-y-6">
                    <div class="text-5xl text-orange-500">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold mb-2">1. رابط الشريك والعمولات</h2>
                        <p class="text-gray-600 leading-relaxed">
                            كل شريك يحصل على رابط فريد داخل موقع وصفة يمكن مشاركته مع الشيفات أو عبر قنوات التسويق الخاصة به، وأي شيف ينشئ حساباً عبر هذا الرابط يُحتسب لك تلقائياً.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2 text-sm">
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full">تتبّع لحظي</span>
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full">دفعات تلقائية</span>
                    </div>
                    <ul class="space-y-3 text-gray-700">
                        <li>عمولة تبدأ من 5% وتصل إلى 15% حسب نوع الورشة أو الحملة.</li>
                        <li>لوحة متابعة فورية تُظهر الأرباح وعدد المشاركات القادمة.</li>
                        <li>إمكانية ربط الحملات الإعلانية بالرابط الخاص لتتبّع الأداء في الوقت الحقيقي.</li>
                    </ul>
                </div>
            </div>

            <div class="group relative bg-white rounded-3xl shadow-2xl p-8 border border-orange-100 overflow-hidden hover:-translate-y-2 transition">
                <div class="absolute inset-0 bg-gradient-to-br from-rose-50 to-white opacity-0 group-hover:opacity-100 transition"></div>
                <div class="relative space-y-6">
                    <div class="text-5xl text-orange-500">
                        <i class="fas fa-link"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold mb-2">2. صفحة Wasfa Links للشيف</h2>
                        <p class="text-gray-600 leading-relaxed">
                            كل شيف يمتلك صفحته الخاصة عبر نظام Wasfa Links؛ صفحة ديناميكية شبيهة بـ Link in Bio تعرض وصفاته، الورش القادمة، وروابط التواصل الخاصة به.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2 text-sm">
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full">تصميم مرن</span>
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full">CTA بارز</span>
                    </div>
                    <ul class="space-y-3 text-gray-700">
                        <li>تصميم قابل للتخصيص بالكامل (روابط، صور، ترتيب، أزرار).</li>
                        <li>إبراز الورشة التالية بزر واضح «احجز مكانك الآن».</li>
                        <li>تتبّع عدد الزيارات والنقرات لكل رابط.</li>
                        <li>إمكانية إنشاء أكثر من صفحة للشيف الواحد أو لفروع مختلفة.</li>
                    </ul>
                </div>
            </div>

            <div class="group relative bg-white rounded-3xl shadow-2xl p-8 border border-orange-100 overflow-hidden hover:-translate-y-2 transition">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-rose-50 opacity-0 group-hover:opacity-100 transition"></div>
                <div class="relative space-y-6">
                    <div class="text-5xl text-orange-500">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold mb-2">3. ماذا يفعل الشيف داخل وصفة؟</h2>
                        <p class="text-gray-600 leading-relaxed">
                            نقدّم للشيفات لوحة احترافية لإدارة كل ما يخص محتواهم بسهولة واحترافية، لتصبح وصفة منصتهم الأساسية لتضخيم الوجود الرقمي وزيادة المبيعات.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2 text-sm">
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full">مجتمع نشط</span>
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full">ورش مباشرة</span>
                    </div>
                    <ul class="space-y-3 text-gray-700">
                        <li>نشر وصفاتهم مع الصور والفيديوهات والتفاعل مع مجتمع وصفة.</li>
                        <li>حفظ الوصفات المفضلة في مكتبة خاصة ومشاركتها عبر إنستغرام وواتساب.</li>
                        <li>نشر ورشاتهم الخاصة ومتابعة المشاركين والحجوزات مباشرة.</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="relative bg-white rounded-3xl shadow-[0_25px_70px_-30px_rgba(249,115,22,0.7)] p-10 space-y-8 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-white opacity-60"></div>
            <div class="relative space-y-4 text-center">
                <div class="text-4xl text-orange-500">
                    <i class="fas fa-rocket"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">جاهز لتصبح شريك وصفة؟</h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">
                    ابدأ اليوم بخطوات بسيطة وواضحة. فريق الشراكات سيرافقك خطوة بخطوة. بمجرد إكمال النموذج سيصلك كل ما تحتاجه خلال ثلاثة أيام عمل كحد أقصى.
                </p>
            </div>
            <div class="relative grid grid-cols-1 md:grid-cols-3 gap-6 text-right">
                <div class="relative bg-white rounded-2xl border border-orange-100 p-6 shadow-lg">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-rose-500 text-white font-black mb-4">1</div>
                    <h3 class="text-xl font-bold mb-2">عبّئ نموذج الانضمام</h3>
                    <p class="text-gray-600">أرسل بياناتك عبر صفحة التواصل وحدد نوع التعاون الذي تبحث عنه.</p>
                </div>
                <div class="relative bg-white rounded-2xl border border-orange-100 p-6 shadow-lg">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-rose-500 text-white font-black mb-4">2</div>
                    <h3 class="text-xl font-bold mb-2">استلم رابطك ولوحتك</h3>
                    <p class="text-gray-600">سيصلك رابطك الفريد، بيانات الدخول، ودليل الاستخدام خلال 3 أيام عمل.</p>
                </div>
                <div class="relative bg-white rounded-2xl border border-orange-100 p-6 shadow-lg">
                    <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-rose-500 text-white font-black mb-4">3</div>
                    <h3 class="text-xl font-bold mb-2">ابدأ بمشاركة الروابط</h3>
                    <p class="text-gray-600">شارك روابطك مع الشيفات والجمهور، وتتبع أرباحك مباشرة من لوحة التحكم.</p>
                </div>
            </div>
            <div class="relative text-center">
                <a href="#partner-form" class="inline-flex items-center justify-center bg-orange-500 text-white font-bold px-12 py-3 rounded-full shadow-lg hover:bg-orange-600 transition">
                    انضم الآن إلى شبكة وصفة
                </a>
            </div>
        </section>

        <!-- Guidance -->
        <section class="bg-gradient-to-br from-orange-100 via-white to-white rounded-3xl p-10 shadow-inner border border-orange-100">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">أخبرنا كيف يمكننا مساعدتك</h2>
            <p class="text-gray-700 text-lg mb-6">
                املأ التفاصيل التالية لتصل رسالتك إلى الفريق المختص مباشرة. عادةً ما نرد خلال يوم عمل واحد ونزوّدك بخطوات تفعيل الحساب ولوحة الشريك.
            </p>
            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li>اختَر نوع التعاون أو الشراكة التي تناسبك وأخبرنا عن الجمهور الذي تستهدفه.</li>
                <li>بعد استلام الطلب ستظهر بياناتك في لوحة الإدمن لمتابعة الحالة وخطوات الربط التالية.</li>
                <li>يصلك إشعار عبر البريد عند مراجعة الطلب أو طلب أي مستندات إضافية من فريق الشراكات.</li>
            </ul>
            <p class="text-gray-800 font-semibold inline-flex items-center gap-2">
                <i class="fas fa-lightbulb text-orange-500"></i>
                <span>نراجع الطلبات مرتين يومياً، وتظهر حالة كل طلب مباشرة في منطقة الإدمن.</span>
            </p>
        </section>

        <!-- Form -->
        <section id="partner-form" class="bg-white/95 backdrop-blur rounded-3xl shadow-[0_25px_60px_-35px_rgba(249,115,22,0.9)] p-10 space-y-6 border border-orange-100">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">نموذج طلب الشراكة</h2>
                <p class="text-gray-600">ارسل بياناتك ليصلك رابط الإدمن ولوحة المتابعة. سنقوم بإشعارك فور تسجيل الطلب داخل لوحة التحكم الخاصة بفريق الشراكات.</p>
            </div>
            <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الاسم الأول</label>
                    <input type="text" placeholder="أدخل اسمك الأول" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500 focus:bg-white shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الاسم الأخير</label>
                    <input type="text" placeholder="أدخل اسمك الأخير" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500 focus:bg-white shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" placeholder="example@email.com" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500 focus:bg-white shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">رقم الهاتف (اختياري)</label>
                    <input type="text" placeholder="اكتب رقم هاتفك للتواصل (اختياري)" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500 focus:bg-white shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الموضوع</label>
                    <input type="text" value="طلب شراكة أو تعاون" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500 focus:bg-white shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الرسالة</label>
                    <textarea rows="5" placeholder="اكتب رسالتك هنا..." class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500 focus:bg-white shadow-sm"></textarea>
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
        <section class="relative bg-gradient-to-r from-orange-500 via-orange-600 to-rose-500 text-white rounded-3xl p-10 text-center space-y-5 overflow-hidden">
            <div class="absolute inset-0 opacity-40" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.6), transparent 45%), radial-gradient(circle at 80% 0%, rgba(255,255,255,0.4), transparent 40%);"></div>
            <div class="relative space-y-3">
                <h2 class="text-3xl font-bold">قدّم طلب الشراكة</h2>
                <p class="text-lg text-white/90">اطّلع على مزايا البرنامج وابدأ بتحقيق الدخل من شبكة وصفة.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#partner-form" class="bg-white text-orange-600 font-bold px-8 py-3 rounded-full shadow-lg hover:shadow-2xl transition">قدّم طلب الشراكة</a>
                    <a href="#benefits" class="border border-white/60 px-8 py-3 rounded-full font-bold hover:bg-white/10 transition">اطّلع على مزايا البرنامج</a>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
