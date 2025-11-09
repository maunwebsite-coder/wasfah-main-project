@extends('layouts.app')

@section('title', 'شريك وصفة - برنامج الشراكة مع الشيفات')

@section('content')
<div class="bg-slate-50">
    <!-- Hero -->
    <section class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-600 to-rose-600 text-white">
        <div class="absolute inset-0 opacity-20 mix-blend-soft-light">
            <div class="absolute -top-32 -right-20 w-96 h-96 bg-white/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -left-20 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 py-20 relative z-10">
            <div class="max-w-3xl mx-auto text-center space-y-6">
                <p class="inline-flex items-center gap-2 px-5 py-2 border border-white/40 rounded-full text-sm tracking-wider uppercase">
                    <span class="text-xl">🤝</span>
                    شريك وصفة
                </p>
                <h1 class="text-4xl md:text-5xl font-black leading-snug">
                    انضم إلى شبكة وصفة وابدأ بجني الأرباح من محتوى الطهي الحقيقي
                </h1>
                <p class="text-lg md:text-xl text-orange-50/90 leading-relaxed">
                    وصفة تجمع بين الشيفات، العلامات التجارية، والمحتوى التفاعلي في مكان واحد.
                    عبر برنامج شريك وصفة ستحصل على دخل مستمر، صفحات شخصية جذابة، ولوحة تحكم واضحة تُظهر أرباحك لحظة بلحظة.
                </p>

                <div class="flex flex-wrap justify-center gap-4 pt-4">
                    <a href="{{ route('contact') }}" class="px-8 py-3 bg-white text-orange-600 font-semibold rounded-full hover:bg-orange-50 transition-shadow shadow-lg shadow-orange-900/20">
                        انضم الآن
                    </a>
                    <a href="#partner-benefits" class="px-8 py-3 border border-white/40 rounded-full hover:bg-white/10 transition">
                        تعرف على المزايا
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-10 text-sm">
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur">
                        <p class="text-3xl font-bold mb-1">15%</p>
                        <p class="text-orange-100">حد أعلى للعمولة حسب الحملات</p>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur">
                        <p class="text-3xl font-bold mb-1">24/7</p>
                        <p class="text-orange-100">لوحة متابعة فورية للأرباح</p>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur">
                        <p class="text-3xl font-bold mb-1">+80</p>
                        <p class="text-orange-100">شيف يعتمدون Wasfa Links</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partner Overview -->
    <section class="container mx-auto px-4 py-16">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <div class="p-10 lg:p-12">
                    <p class="text-sm font-semibold text-orange-500 mb-3">لماذا وصفة؟</p>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">
                        شبكة تفاعلية تربط بين الشيفات، الجمهور، والعلامات التجارية في نظام واحد ذكي
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-8">
                        برنامج شريك وصفة يمنحك أدوات احترافية لتتبع كل نقرة، كل حجز، وكل ورشة يتم حجزها عبر روابطك. احصل على صفحات Wasfa Links المخصصة للشيفات، حملات متكاملة، ولوحة تحكم شفافة تعرض أرباحك وحالة طلباتك في الوقت الحقيقي.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100 flex items-center justify-center">
                                <i class="fas fa-chart-line text-orange-500 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">نظام تتبع ذكي للروابط</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">اعرف من أين أتت كل عملية بيع، وقارن أداء الحملات عبر لوحة تفاصيل بدقة متناهية.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center">
                                <i class="fas fa-id-badge text-emerald-500 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">صفحات مخصصة لكل شيف</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">صمّم تجربة شبيهة بالرابط في السيرة، لكن بلمسة وصفة التي تعرض الوصفات، الورش، وروابط التواصل.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-sky-100 flex items-center justify-center">
                                <i class="fas fa-gauge-high text-sky-500 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">لوحة تحكم شفافة</h3>
                                <p class="text-gray-600 text-sm leading-relaxed">راقب الأرباح، الحجوزات القادمة، وتوقعات الدخل الشهري في واجهة عربية سهلة القراءة.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-10 lg:p-12 flex flex-col justify-center">
                    <div class="space-y-6">
                        <p class="text-sm text-orange-300 mb-2">تحديثات الشركاء</p>
                        <p class="text-3xl font-bold leading-snug">
                            نجهز حالياً لإطلاق بيانات الأداء بعد تشغيل برنامج الشركاء رسمياً، لتكون كل الأرقام موثقة ودقيقة.
                        </p>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            ستظهر قصص النجاح ومؤشرات الأداء هنا فور بدء التجارب الأولى، مع تحديثات مستمرة لضمان الشفافية مع جميع الشركاء.
                        </p>
                        <div class="bg-white/5 rounded-2xl p-5 text-sm text-slate-200 leading-relaxed">
                            <p class="font-semibold text-orange-200 mb-2">ماذا سيصل إليك قريباً؟</p>
                            <ul class="list-disc list-inside space-y-1 text-slate-100/90">
                                <li>تنبيهات فورية عند تفعيل النظام وإطلاق الدعوات.</li>
                                <li>لوحة تحكم مباشرة تعرض كل عملية بيع وعمولتك المستحقة.</li>
                                <li>تقارير قابلة للمشاركة مع فريقك أو شركائك التسويقيين.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partner Benefits -->
    <section id="partner-benefits" class="container mx-auto px-4 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-orange-100">
                <div class="flex items-center gap-3 text-orange-500 font-semibold text-sm mb-6">
                    <span class="text-2xl">💰</span>
                    1. رابط الشريك والعمولات
                </div>
                <p class="text-gray-600 leading-relaxed mb-6">
                    كل شريك يحصل على رابط فريد داخل موقع وصفة يمكن مشاركته مع الشيفات أو عبر قنوات التسويق الخاصة به، وأي شيف ينشئ حساباً عبر هذا الرابط ويطلق ورشاته من خلال الموقع يُحتسب له كعميل تابع لك، وأي حجز يتم على تلك الورشات تضيف العمولة مباشرة إلى حسابك دون أي تدخل يدوي.
                </p>
                <h4 class="font-bold text-gray-900 mb-4">مميزات نظام الشركاء:</h4>
                <ul class="space-y-3 text-gray-600 text-sm">
                    <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> عمولة تبدأ من 5% وتصل إلى 15% حسب نوع الورشة أو الحملة.</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> لوحة متابعة فورية تُظهر الأرباح وعدد المشاركات القادمة.</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> إمكانية ربط الحملات الإعلانية بالرابط الخاص لتتبّع الأداء في الوقت الحقيقي.</li>
                </ul>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-slate-100">
                <div class="flex items-center gap-3 text-sky-500 font-semibold text-sm mb-6">
                    <span class="text-2xl">🔗</span>
                    2. صفحة Wasfa Links للشيف
                </div>
                <p class="text-gray-600 leading-relaxed mb-6">
                    كل شيف يمتلك صفحته الخاصة عبر نظام Wasfa Links؛ صفحة ديناميكية شبيهة بـ Link in Bio تعرض وصفاته، الورش القادمة، وروابط التواصل الخاصة به.
                </p>
                <h4 class="font-bold text-gray-900 mb-4">خصائص صفحة Wasfa Links:</h4>
                <ul class="space-y-3 text-gray-600 text-sm">
                    <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> تصميم قابل للتخصيص بالكامل (روابط، صور، ترتيب، أزرار).</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> إبراز الورشة التالية بزر واضح «احجز مكانك الآن».</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> تتبّع عدد الزيارات والنقرات لكل رابط.</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> إمكانية إنشاء أكثر من صفحة للشيف الواحد أو لفروع مختلفة.</li>
                </ul>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-emerald-100">
                <div class="flex items-center gap-3 text-emerald-500 font-semibold text-sm mb-6">
                    <span class="text-2xl">👨‍🍳</span>
                    3. ماذا يفعل الشيف داخل وصفة؟
                </div>
                <p class="text-gray-600 leading-relaxed mb-6">
                    نقدّم للشيفات لوحة احترافية لإدارة كل ما يخص محتواهم بسهولة واحترافية، لتصبح وصفة منصتهم الأساسية لتضخيم الوجود الرقمي وزيادة المبيعات.
                </p>
                <ul class="space-y-3 text-gray-600 text-sm">
                    <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> نشر وصفاتهم مع الصور والفيديوهات.</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> مشاهدة والتفاعل مع وصفات الشيفات الآخرين.</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> حفظ الوصفات المفضلة في مكتبة خاصة.</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> مشاركة الروابط بسهولة عبر إنستغرام وواتساب.</li>
                    <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> نشر ورشاتهم الخاصة ومتابعة المشاركين والحجوزات مباشرة.</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Steps -->
    <section class="container mx-auto px-4 pb-16">
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl text-white p-10 md:p-14">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-10">
                <div class="max-w-2xl space-y-4">
                    <p class="text-sm font-semibold text-orange-300">🚀 جاهز لتصبح شريك وصفة؟</p>
                    <h2 class="text-3xl font-bold leading-relaxed">ابدأ اليوم بخطوات بسيطة وواضحة</h2>
                    <p class="text-slate-200 leading-relaxed">
                        فريق الشراكات سيرافقك خطوة بخطوة. بمجرد إكمال النموذج سيصلك كل ما تحتاجه خلال ثلاثة أيام عمل كحد أقصى.
                    </p>
                </div>
                <div class="bg-white/10 rounded-2xl p-6 backdrop-blur w-full lg:w-auto">
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <span class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold">1</span>
                            <div>
                                <h3 class="font-semibold text-lg">عبّئ نموذج الانضمام</h3>
                                <p class="text-sm text-slate-200">أرسل بياناتك عبر صفحة التواصل وحدد نوع التعاون الذي تبحث عنه.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <span class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold">2</span>
                            <div>
                                <h3 class="font-semibold text-lg">استلم رابطك ولوحتك</h3>
                                <p class="text-sm text-slate-200">سيصلك رابطك الفريد، بيانات الدخول، ودليل الاستخدام خلال 3 أيام عمل.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <span class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold">3</span>
                            <div>
                                <h3 class="font-semibold text-lg">ابدأ بمشاركة الروابط</h3>
                                <p class="text-sm text-slate-200">شارك روابطك مع الشيفات والجمهور، وتتبع أرباحك مباشرة من لوحة التحكم.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ route('contact') }}" class="px-8 py-3 bg-white text-slate-900 rounded-full font-semibold shadow-lg hover:-translate-y-0.5 transition">
                    انضم الآن إلى شبكة وصفة
                </a>
            </div>
        </div>
    </section>

    <!-- Partner Contact -->
    <section id="partner-contact" class="container mx-auto px-4 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="bg-gradient-to-br from-orange-50 to-white border border-orange-100 rounded-3xl p-10 shadow-lg">
                <p class="text-sm font-semibold text-orange-600 mb-3">جاهز للانضمام؟</p>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">أخبرنا كيف يمكننا مساعدتك</h2>
                <p class="text-lg text-gray-600 leading-relaxed mb-6">
                    املأ التفاصيل التالية لتصل رسالتك إلى الفريق المختص مباشرة. عادةً ما نرد خلال يوم عمل واحد ونزوّدك بخطوات
                    تفعيل الحساب ولوحة الشريك.
                </p>
                <ul class="space-y-4 text-gray-700">
                    <li class="flex items-start gap-3">
                        <span class="text-orange-500 mt-0.5">•</span>
                        اختَر نوع التعاون أو الشراكة التي تناسبك وأخبرنا عن الجمهور الذي تستهدفه.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-orange-500 mt-0.5">•</span>
                        بعد استلام الطلب ستظهر بياناتك في لوحة الإدمن لمتابعة الحالة وخطوات الربط التالية.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-orange-500 mt-0.5">•</span>
                        يصلك إشعار عبر البريد عند مراجعة الطلب أو طلب أي مستندات إضافية من فريق الشراكات.
                    </li>
                </ul>
                <div class="mt-8 p-4 bg-white border border-dashed border-orange-200 rounded-2xl text-sm text-gray-600">
                    <p>💡 نراجع الطلبات مرتين يومياً، وتظهر حالة كل طلب مباشرة في منطقة الإدمن.</p>
                </div>
            </div>
            <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 p-8">
                <div class="mb-6">
                    <p class="text-sm font-semibold text-orange-500 mb-1">نموذج طلب الشراكة</p>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">ارسل بياناتك ليصلك رابط الإدمن ولوحة المتابعة</h3>
                    <p class="text-gray-500">سنقوم بإشعارك فور تسجيل الطلب داخل لوحة التحكم الخاصة بفريق الشراكات.</p>
                </div>

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
                    @include('pages.partials.contact-form-fields', [
                        'defaultSubject' => 'partnership',
                        'source' => 'partnership-page',
                    ])
                </form>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="container mx-auto px-4 pb-20">
        <div class="bg-white border border-dashed border-orange-200 rounded-3xl p-10 md:p-14 text-center shadow-lg shadow-orange-100/40">
            <div class="max-w-3xl mx-auto space-y-6">
                <h2 class="text-3xl font-bold text-gray-900">انضم الآن وكن جزءاً من شبكة وصفة</h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    وصفة تجمع الشيفات والمحتوى التفاعلي في عالم واحد. ابدأ اليوم، ضاعف حضورك، وتابع أرباحك بكل شفافية.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('contact') }}" class="px-10 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-full shadow-lg hover:shadow-xl transition">
                        قدّم طلب الشراكة
                    </a>
                    <a href="#partner-benefits" class="px-10 py-3 border border-orange-200 text-orange-600 font-semibold rounded-full hover:bg-orange-50 transition">
                        اطّلع على مزايا البرنامج
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
