@extends('layouts.app')

@section('title', 'اتصل بنا - موقع وصفة')

@section('content')
<div class="bg-gradient-to-b from-orange-50 via-white to-white">
    <div class="container mx-auto px-4 py-10 lg:py-16 space-y-10">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 text-white shadow-2xl">
            <div class="absolute inset-y-0 -left-20 w-52 bg-white/10 blur-3xl rounded-full hidden lg:block"></div>
            <div class="grid lg:grid-cols-2 gap-10 p-8 md:p-12">
                <div>
                    <div class="inline-flex items-center gap-2 text-xs uppercase tracking-[0.4em] text-white/70 mb-6">
                        <span class="w-8 h-px bg-white/60"></span>
                        <span>شراكات الشركات</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">يسعدنا سماعك</h1>
                    <p class="text-lg md:text-xl text-white/90 leading-relaxed max-w-2xl">
                        فريق وصفة موجود لدعمك في كل ما يخص الوصفات، الورش، والشراكات. أرسل رسالتك وسنعود إليك خلال يوم عمل واحد.
                    </p>
                    <div class="flex flex-wrap gap-3 text-sm mt-8">
                        <span class="px-4 py-2 rounded-full border border-white/50 bg-white/10">الورش والتدريب</span>
                        <span class="px-4 py-2 rounded-full border border-white/50 bg-white/10">طلبات التعاون</span>
                        <span class="px-4 py-2 rounded-full border border-white/50 bg-white/10">الدعم الفني</span>
                    </div>
                    <dl class="mt-10 grid sm:grid-cols-3 gap-4 text-sm">
                        <div class="bg-white/10 rounded-2xl p-4">
                            <dt class="text-white/70 mb-1">متوسط وقت الاستجابة</dt>
                            <dd class="text-2xl font-bold">ساعات 24</dd>
                        </div>
                        <div class="bg-white/10 rounded-2xl p-4">
                            <dt class="text-white/70 mb-1">ورش خاصة قيد المتابعة</dt>
                            <dd class="text-2xl font-bold">18+</dd>
                        </div>
                        <div class="bg-white/10 rounded-2xl p-4">
                            <dt class="text-white/70 mb-1">شراكات الشركات</dt>
                            <dd class="text-2xl font-bold">30</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div class="grid xl:grid-cols-[minmax(0,1.9fr)_minmax(0,1.1fr)] gap-10">
            <div class="space-y-8">
                <div id="form" class="bg-white rounded-3xl shadow-xl border border-orange-100 p-6 md:p-10">
                    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">أخبرنا كيف يمكننا مساعدتك</h2>
                            <p class="text-gray-500">املأ التفاصيل التالية لتصل رسالتك إلى الفريق المختص مباشرة. عادةً ما نرد خلال يوم عمل واحد.</p>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-orange-600 font-semibold">
                            <i class="far fa-envelope-open-text"></i>
                            <span>نراجع البريد مرتين يومياً</span>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl mb-6 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-6 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <form id="contact-form" method="POST" action="{{ route('contact.send') }}" class="space-y-6">
                        @csrf
                        @include('pages.partials.contact-form-fields', [
                            'defaultSubject' => 'general',
                            'source' => 'contact-page',
                        ])
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 pt-2 border-t border-gray-100">
                            <i class="far fa-clock text-orange-500"></i>
                            <p>عادةً ما نرد خلال يوم عمل واحد.</p>
                        </div>
                    </form>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div class="rounded-2xl bg-gradient-to-br from-orange-100 to-orange-50 border border-orange-100 p-5">
                        <p class="text-xs font-semibold text-orange-600 mb-2">الورش والتدريب</p>
                        <p class="text-gray-700 text-sm">رسائل الورش الخاصة تصل مباشرة إلى منسق التدريب.</p>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-purple-100 to-purple-50 border border-purple-100 p-5">
                        <p class="text-xs font-semibold text-purple-600 mb-2">طلبات التعاون</p>
                        <p class="text-gray-700 text-sm">نجهز لك عرضًا تفصيليًا يراعي هوية علامتك.</p>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-50 border border-emerald-100 p-5">
                        <p class="text-xs font-semibold text-emerald-600 mb-2">الدعم الفني</p>
                        <p class="text-gray-700 text-sm">أخبرنا بالصفحة التي ظهر فيها العطل لنرسل لك الحل.</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-gray-100 shadow-lg p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">الأسئلة الشائعة</h2>
                    <div class="space-y-5">
                        <div class="group">
                            <div class="flex items-center justify-between cursor-pointer">
                                <h3 class="font-semibold text-gray-800">متى يصلني الرد على رسالتي؟</h3>
                                <i class="fas fa-angle-down text-orange-500 group-hover:translate-y-1 transition"></i>
                            </div>
                            <p class="text-gray-600 text-sm mt-2">نقوم بمراجعة البريد مرتين يوميًا، وستحصل على رد مبدئي خلال يوم عمل واحد كحد أقصى.</p>
                        </div>
                        <div class="group pt-5 border-t border-dashed border-gray-200">
                            <div class="flex items-center justify-between cursor-pointer">
                                <h3 class="font-semibold text-gray-800">هل أستطيع طلب ورشة خاصة أو تعاون؟</h3>
                                <i class="fas fa-angle-down text-orange-500 group-hover:translate-y-1 transition"></i>
                            </div>
                            <p class="text-gray-600 text-sm mt-2">بالطبع! شاركنا نوع التعاون أو الورشة التي تبحث عنها، وسننسق مع الفريق المتخصص ثم نعود إليك بالتفاصيل.</p>
                        </div>
                        <div class="group pt-5 border-t border-dashed border-gray-200">
                            <div class="flex items-center justify-between cursor-pointer">
                                <h3 class="font-semibold text-gray-800">ماذا أفعل عند مواجهة مشكلة تقنية؟</h3>
                                <i class="fas fa-angle-down text-orange-500 group-hover:translate-y-1 transition"></i>
                            </div>
                            <p class="text-gray-600 text-sm mt-2">أخبرنا بالصفحة التي ظهر فيها العطل والخطوات التي سبقت المشكلة، وسنرسل لك الحل أو نرتب جلسة مساعدة قصيرة إذا لزم الأمر.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-slate-900 text-white rounded-3xl p-6 md:p-8 shadow-xl">
                    <h2 class="text-2xl font-bold mb-4">معلومات الاتصال</h2>
                    <p class="text-white/80 mb-6">اختر الطريقة الأنسب لك، وسيتابع فريق وصفة رسالتك بعناية لضمان حصولك على الدعم المناسب.</p>
                    <div class="space-y-5 text-sm">
                        <div class="flex items-start gap-4">
                            <div class="bg-white/15 p-3 rounded-2xl">
                                <i class="fas fa-map-marker-alt text-orange-300"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white mb-1">العنوان</h3>
                                <p class="text-white/80">عمان، الأردن</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="bg-white/15 p-3 rounded-2xl">
                                <i class="fas fa-headset text-purple-200"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white mb-1">فريق الدعم</h3>
                                <p class="text-white/80">نراجع الرسائل مرتين يوميًا خلال أيام العمل لضمان استجابة سريعة وواضحة.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="bg-white/15 p-3 rounded-2xl">
                                <i class="fas fa-comments text-emerald-200"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white mb-1">قنوات التواصل</h3>
                                <p class="text-white/80">راسلنا عبر الرسائل المباشرة على إنستغرام أو أرسل طلبك عبر النموذج وسيتم تحويله للفريق المعني.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="bg-white/15 p-3 rounded-2xl">
                                <i class="fas fa-envelope text-sky-200"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white mb-1">مركز الرسائل</h3>
                                <p class="text-white/80">كل الطلبات تُدار عبر نموذج التواصل لضمان متابعة مخصصة من فريق الدعم.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-gray-100 shadow-lg p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">تابعنا</h2>
                    <p class="text-gray-600 mb-6 text-sm">اكتشف أحدث الوصفات، تنبيهات الورش، ولقطات من خلف الكواليس على قنوات وصفة الاجتماعية.</p>
                    <div class="space-y-4">
                        <a
                            href="https://www.instagram.com/wasfah.jo/"
                            target="_blank"
                            class="flex items-center justify-between rounded-2xl p-4 bg-gradient-to-r from-pink-500 to-purple-600 text-white shadow-lg hover:-translate-y-1 transition"
                        >
                            <div class="flex items-center gap-3">
                                <i class="fab fa-instagram text-2xl"></i>
                                <div>
                                    <h3 class="font-semibold text-lg">إنستغرام</h3>
                                    <p class="text-sm text-white/80">@wasfah.jo</p>
                                </div>
                            </div>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <a
                            href="https://www.youtube.com/@wasfah.jordan"
                            target="_blank"
                            class="flex items-center justify-between rounded-2xl p-4 bg-red-600 text-white shadow-lg hover:-translate-y-1 transition"
                        >
                            <div class="flex items-center gap-3">
                                <i class="fab fa-youtube text-2xl"></i>
                                <div>
                                    <h3 class="font-semibold text-lg">يوتيوب</h3>
                                    <p class="text-sm text-white/80">@wasfah.jordan</p>
                                </div>
                            </div>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-gray-100 shadow-lg p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-ribbon text-orange-500 text-2xl"></i>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">شعار وصفة</h3>
                            <p class="text-gray-600 text-sm">منصّة وصفة للحلويات الفاخرة والراقية، نرافقك في كل خطوة لتقديم أطيب الحلويات.</p>
                        </div>
                    </div>
                    <div class="border-t border-dashed border-gray-200 pt-4">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-[0.3em] mb-3">اكتشف</h4>
                        <div class="grid grid-cols-2 gap-3 text-sm text-gray-700">
                            <span>جميع الوصفات</span>
                            <span>ورشات العمل</span>
                            <span>نصائح الحلويات</span>
                            <span>الدليل السريع</span>
                            <span>أدوات الشيف</span>
                            <span>شراكات الشركات</span>
                            <span>البحث عن وصفة</span>
                            <span>تواصل معنا</span>
                        </div>
                        <div class="mt-4 text-xs text-gray-500 space-y-2">
                            <p>فريق الدعم يرد خلال يوم عمل عند إرسال الطلب عبر نموذج التواصل.</p>
                            <p>© 2025 وصفة. جميع الحقوق محفوظة. موقع وصفة هو جزء من شركة وصفة الأردن.</p>
                            <p>نهتم بجودة تفاصيل كل وصفة.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
