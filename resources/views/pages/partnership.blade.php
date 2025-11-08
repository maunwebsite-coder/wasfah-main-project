@extends('layouts.app')

@section('title', 'ملف الشراكات - وصفة')

@section('content')
<div class="bg-gradient-to-b from-orange-50 via-white to-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
            <div class="space-y-6 lg:col-span-7">
                <div class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white px-4 py-2 text-sm font-medium text-orange-600 shadow-sm">
                    <i class="fas fa-handshake-angle text-base"></i>
                    <span>ملف تعريف الشركاء</span>
                </div>
                <div>
                    <h1 class="text-4xl font-black leading-tight text-slate-900 md:text-5xl">
                        شركاء وصفة: منصة ذواقة تبني تجارب طهي تلائم علامتك التجارية
                    </h1>
                    <p class="mt-4 text-lg text-slate-600 md:text-xl">
                        نمكّن العلامات التجارية الغذائية والأدوات المنزلية من الوصول لجمهور عربي متفاعل، عبر محتوى مُنتج بعناية، وورش عمل مباشرة، وتجارب تذوق رقمية تمنح منتجاتكم قصة ملهمة.
                    </p>
                </div>
                <ul class="space-y-3 text-slate-700">
                    <li class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                            <i class="fas fa-check"></i>
                        </span>
                        <span>ورش حية وتحديات تذوق تضع منتجكم في قلب التجربة.</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                            <i class="fas fa-check"></i>
                        </span>
                        <span>مجتمع مختار من محبي الحلويات الراقية، مع بيانات دقيقة عن التفاعل.</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                            <i class="fas fa-check"></i>
                        </span>
                        <span>فريق إنتاج داخلي يضمن جودة التصوير، التحرير، وتجربة الاستخدام.</span>
                    </li>
                </ul>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="mailto:wasfah99@gmail.com?subject=شراكة%20عمل%20مع%20وصفة" class="inline-flex items-center justify-center rounded-full bg-orange-500 px-8 py-3 text-base font-semibold text-white shadow-lg transition hover:bg-orange-600">
                        اطلب نسخة العرض والشروط
                        <i class="fas fa-arrow-left ml-2"></i>
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-8 py-3 text-base font-semibold text-slate-700 transition hover:border-orange-200 hover:text-orange-600">
                        جدولة مكالمة تعريفية
                        <i class="fas fa-calendar-week ml-2 text-orange-500"></i>
                    </a>
                </div>

                <div class="grid gap-5 rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm sm:grid-cols-2">
                    <div>
                        <p class="text-sm font-semibold text-orange-500">قطاعات موصى بها</p>
                        <p class="mt-1 text-lg font-bold text-slate-900">منتجات الألبان • أدوات المطبخ • المقاهي المختصة • شركات التوصيل</p>
                    </div>
                    <div class="flex flex-wrap gap-3 text-sm text-slate-600">
                        <span class="rounded-full bg-slate-50 px-3 py-1">حملات إطلاق</span>
                        <span class="rounded-full bg-slate-50 px-3 py-1">محتوى مشترك</span>
                        <span class="rounded-full bg-slate-50 px-3 py-1">ورش تدريب الموظفين</span>
                        <span class="rounded-full bg-slate-50 px-3 py-1">برامج الإحالة</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="rounded-3xl border border-orange-100 bg-white/90 p-6 shadow-xl ring-1 ring-orange-100/60">
                    <div class="rounded-2xl bg-gradient-to-br from-rose-500 to-orange-500 p-6 text-white shadow-inner">
                        <p class="text-sm uppercase tracking-[0.3em] text-white/80">أرقام مختارة</p>
                        <div class="mt-6 grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-4xl font-black">120K+</p>
                                <p class="text-sm text-white/80">متابع وترويسة بريدية</p>
                            </div>
                            <div>
                                <p class="text-4xl font-black">87</p>
                                <p class="text-sm text-white/80">ورشة مباشرة في آخر 12 شهر</p>
                            </div>
                            <div>
                                <p class="text-4xl font-black">35%</p>
                                <p class="text-sm text-white/80">متوسط معدل التحويل للحملات</p>
                            </div>
                            <div>
                                <p class="text-4xl font-black">4.9/5</p>
                                <p class="text-sm text-white/80">تقييم تجربة المشتركين</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-orange-50 text-orange-600 shadow-inner">
                                <i class="fas fa-video"></i>
                            </span>
                            <div>
                                <p class="font-semibold text-slate-900">استوديو تصوير داخلي</p>
                                <p class="text-sm text-slate-500">تصوير عالي الدقة للوصفات، المقابلات، والإعلانات القصيرة.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-orange-50 text-orange-600 shadow-inner">
                                <i class="fas fa-chart-line"></i>
                            </span>
                            <div>
                                <p class="font-semibold text-slate-900">لوحة بيانات مخصصة</p>
                                <p class="text-sm text-slate-500">مؤشرات أسبوعية حول التفاعل، المبيعات، والزيارات المحوّلة.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-orange-50 text-orange-600 shadow-inner">
                                <i class="fas fa-certificate"></i>
                            </span>
                            <div>
                                <p class="font-semibold text-slate-900">تجارب براند حصرية</p>
                                <p class="text-sm text-slate-500">نتكفّل ببناء الوصفة، ترجمتها بصرياً، وتسليم دليل التنفيذ لفريقكم.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid gap-6 md:grid-cols-4">
            @foreach ([
                ['label' => 'مشاهدات المحتوى المميز شهرياً', 'value' => '1.8M', 'trend' => '+24%'],
                ['label' => 'متوسط مدة المشاهدة', 'value' => '07:12', 'trend' => 'ثابت'],
                ['label' => 'شركاء موثوقون منذ 2021', 'value' => '32', 'trend' => '+5 هذا الربع'],
                ['label' => 'متابعة البريد والرسائل', 'value' => '48K', 'trend' => 'معدل فتح 41%'],
            ] as $stat)
                <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5 shadow-sm">
                    <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                    <p class="text-sm text-emerald-600">{{ $stat['trend'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-4 space-y-12">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-500">لماذا وصفة</p>
            <h2 class="mt-3 text-3xl font-extrabold text-slate-900 md:text-4xl">رحلة متكاملة من الفكرة إلى النتائج</h2>
            <p class="mt-3 text-lg text-slate-600">نمزج بين إنتاج المحتوى، الخبرة التقنية، وشبكة الشيف لضمان تجربة سلسة ومقاسة بالأرقام.</p>
        </div>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            @php
                $pillars = [
                    [
                        'icon' => 'fas fa-wand-magic-sparkles',
                        'title' => 'قصص براند مبتكرة',
                        'desc' => 'نحوّل خصائص منتجكم إلى قصة تلامس الجمهور عبر الوصفات، الفيديوهات القصيرة، والنشرات.'
                    ],
                    [
                        'icon' => 'fas fa-people-group',
                        'title' => 'مجتمع مختار',
                        'desc' => 'نستهدف شرائح محددة (محترفو الحلويات، قادة المطابخ المنزلية، ومتاجر المختصين) بتقارير دقيقة.'
                    ],
                    [
                        'icon' => 'fas fa-laptop-code',
                        'title' => 'أدوات قياس لحظية',
                        'desc' => 'تقارير تفصيلية لكل تفاعل، روابط تتبع مخصصة، ولوحة بيانات تشاركية مع فريقكم.'
                    ],
                    [
                        'icon' => 'fas fa-user-shield',
                        'title' => 'حماية للعلامة',
                        'desc' => 'ردّ سريع للأزمات، موافقات تحريرية مشتركة، وحفظ كامل لحقوق النشر والمواد الخام.'
                    ],
                ];
            @endphp
            @foreach ($pillars as $pillar)
                <div class="rounded-3xl border border-white bg-white p-6 shadow-lg shadow-orange-100/40">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                        <i class="{{ $pillar['icon'] }} text-xl"></i>
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-slate-900">{{ $pillar['title'] }}</h3>
                    <p class="mt-3 text-slate-600">{{ $pillar['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="bg-white py-16">
    <div class="container mx-auto px-4">
        <div class="grid gap-8 lg:grid-cols-2">
            <div class="space-y-6 rounded-3xl border border-slate-100 bg-slate-50/60 p-8 shadow-sm">
                <p class="text-sm font-semibold text-orange-500">نماذج تعاون جاهزة</p>
                <h2 class="text-3xl font-bold text-slate-900">حلول تلائم مختلف أهداف التسويق والنمو</h2>
                <div class="space-y-5">
                    <div class="rounded-2xl border border-white bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-slate-900">حملات إطلاق المنتجات</h3>
                            <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-600">4 - 6 أسابيع</span>
                        </div>
                        <ul class="mt-3 space-y-2 text-slate-600">
                            <li>سلسلة محتوى (مقال + فيديو + نشرات) مع رابط تتبع.</li>
                            <li>جلسة بث مباشر توضح الاستخدام وأجوبة الأسئلة.</li>
                            <li>حوافز شراء حصرية لمتابعي وصفة.</li>
                        </ul>
                    </div>
                    <div class="rounded-2xl border border-white bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-slate-900">تمكين نقاط البيع</h3>
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">اشتراك ربع سنوي</span>
                        </div>
                        <ul class="mt-3 space-y-2 text-slate-600">
                            <li>ورش تدريب افتراضية لفرق المبيعات أو الشركاء.</li>
                            <li>مكتبة وصفات مختصة بعلامتكم مع دليل تحضير.</li>
                            <li>تقارير زيارات فعلية ونقاط تذوق.</li>
                        </ul>
                    </div>
                    <div class="rounded-2xl border border-white bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-slate-900">برامج الإحالة المشتركة</h3>
                            <span class="rounded-full bg-slate-900/10 px-3 py-1 text-xs font-semibold text-slate-900">مدفوع بالأداء</span>
                        </div>
                        <ul class="mt-3 space-y-2 text-slate-600">
                            <li>كوبونات وسفراء من مجتمع وصفة.</li>
                            <li>دمج مباشر مع لوحة الإحالات لدينا.</li>
                            <li>دفعات شهرية وتقارير شفافة.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="space-y-6 rounded-3xl border border-orange-100 bg-white p-8 shadow-xl">
                <p class="text-sm font-semibold text-orange-500">خطة التنفيذ</p>
                <h2 class="text-3xl font-bold text-slate-900">أربع مراحل واضحة من أول اتصال وحتى القياس</h2>
                <div class="space-y-5">
                    @php
                        $steps = [
                            ['title' => 'جلسة التعريف', 'desc' => 'نراجع أهدافكم، المنتجات الأساسية، والجمهور المستهدف.'],
                            ['title' => 'تصميم التجربة', 'desc' => 'نبني فكرة الحملة، الجدول الزمني، ومؤشرات الأداء المتفق عليها.'],
                            ['title' => 'الإنتاج والتنفيذ', 'desc' => 'فريق المحتوى والشيفات يديرون التصوير، النشر، والورش الحية.'],
                            ['title' => 'التحليل والتحسين', 'desc' => 'نقدّم لوحة قياس تفاعلية وتوصيات للدورة التالية.'],
                        ];
                    @endphp
                    @foreach ($steps as $index => $step)
                        <div class="flex gap-4 rounded-2xl border border-white bg-orange-50/60 p-5 shadow-sm">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-xl font-black text-orange-500 shadow">{{ $index + 1 }}</div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $step['title'] }}</h3>
                                <p class="mt-1 text-slate-600">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="rounded-2xl border border-dashed border-orange-200 bg-orange-50/80 p-5 text-sm text-slate-600">
                    <p class="font-semibold text-orange-600">ماذا نحضر للاجتماع الأول؟</p>
                    <ul class="mt-3 list-disc space-y-1 pr-5">
                        <li>لمحة عن منتجاتكم أو الخدمات المستهدفة.</li>
                        <li>أهداف رقمية أو بيعية ترغبون بقياسها.</li>
                        <li>أي اشتراطات خاصة بالعلامة أو الهوية البصرية.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="grid gap-8 lg:grid-cols-12">
            <div class="rounded-3xl border border-slate-100 bg-white p-8 shadow-sm lg:col-span-7">
                <p class="text-sm font-semibold text-orange-500">قطاعات نتعاون معها</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    @foreach (['شركات الأغذية الفاخرة', 'متاجر الأدوات المنزلية', 'المقاهي المختصة', 'خدمات التوصيل', 'المدارس الفندقية', 'علامات التغليف الذكي', 'منصات التجارة الإلكترونية', 'شركات تنظيم الفعاليات'] as $industry)
                        <span class="rounded-full bg-slate-50 px-4 py-2 text-sm text-slate-700 shadow-sm">{{ $industry }}</span>
                    @endforeach
                </div>
                <div class="mt-8 space-y-6">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-6">
                        <p class="text-sm font-semibold text-slate-500">شهادة شريك</p>
                        <blockquote class="mt-3 text-lg text-slate-800">
                            "منصة وصفة قادت إطلاق منتجات الألبان الموسمية لدينا؛ المحتوى كان دقيقاً وحقق معدلات شراء أعلى بـ 38% من المتوقع."
                        </blockquote>
                        <p class="mt-3 text-sm font-semibold text-slate-700">مديرة التسويق - شركة ألبان محلية</p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-6">
                        <p class="text-sm font-semibold text-slate-500">نتائج ملموسة</p>
                        <ul class="mt-3 space-y-2 text-slate-600">
                            <li>+4200 طلب تذوق عبر تحدي وصفة x Cafella.</li>
                            <li>12 فيديو قصير أنتج خلال أسبوع واحد مع فريق واحد.</li>
                            <li>تقارير أسبوعية تربط المشاهدات بمخزون المتاجر.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="rounded-3xl border border-orange-100 bg-white p-8 shadow-lg lg:col-span-5">
                <p class="text-sm font-semibold text-orange-500">فلنبدأ الآن</p>
                <h3 class="mt-3 text-3xl font-bold text-slate-900">أرسل لنا موجزاً مختصراً وسنعود بخطة عمل أولية خلال 3 أيام عمل.</h3>
                <div class="mt-6 space-y-4">
                    <div class="flex items-center gap-3 text-slate-700">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                            <i class="fas fa-envelope-open-text"></i>
                        </span>
                        <div>
                            <p class="text-sm text-slate-500">البريد المباشر</p>
                            <a href="mailto:wasfah99@gmail.com" class="text-lg font-semibold text-slate-900 hover:text-orange-600">wasfah99@gmail.com</a>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-slate-700">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                            <i class="fas fa-clipboard-list"></i>
                        </span>
                        <div>
                            <p class="text-sm text-slate-500">نموذج الطلب</p>
                            <a href="{{ route('contact') }}" class="text-lg font-semibold text-slate-900 hover:text-orange-600">نموذج تواصل وصفة</a>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-slate-700">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div>
                            <p class="text-sm text-slate-500">أوقات الاستشارة</p>
                            <p class="text-lg font-semibold text-slate-900">الأحد - الخميس | 10 صباحاً - 4 مساءً (توقيت عمّان)</p>
                        </div>
                    </div>
                </div>
                <div class="mt-8 rounded-2xl bg-gradient-to-r from-orange-500 to-rose-500 p-6 text-white">
                    <p class="text-sm uppercase tracking-[0.3em] text-white/80">عرض تجريبي</p>
                    <h4 class="mt-3 text-2xl font-bold">ورشة تعريفية مجانية مدتها 30 دقيقة مع فريق الإنتاج لدينا.</h4>
                    <p class="mt-3 text-sm text-white/90">تعرّفوا على نماذج القصص والنتائج، وشاهدوا كيف نحول وصفة إلى تجربة كاملة.</p>
                    <a href="mailto:wasfah99@gmail.com?subject=حجز%20ورشة%20شراكة" class="mt-4 inline-flex items-center justify-center rounded-full bg-white/10 px-6 py-3 text-sm font-semibold text-white shadow hover:bg-white/20">
                        احجز موعداً
                        <i class="fas fa-arrow-left ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
