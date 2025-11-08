@extends('layouts.app')

@section('title', 'شراكات وصفة')

@section('content')
<div class="bg-gradient-to-b from-orange-50 via-white to-white py-12">
    <div class="container mx-auto px-4">
        <div class="space-y-6 text-center md:text-right">
            <span class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white px-4 py-1.5 text-sm font-semibold text-orange-600 shadow-sm">
                <i class="fas fa-handshake-angle"></i>
                <span>برنامج الشراكة في نص وصفة</span>
            </span>
            <h1 class="text-4xl font-black leading-tight text-slate-900 md:text-5xl">
                ركّز شراكتك على ورش Live، وخذ عمولتك عن كل مشارك يأتي من طرفك
            </h1>
            <p class="text-lg text-slate-600 md:text-xl">
                نص وصفة تبث ورش Live أسبوعية بشيفات مختصين، مع نظام تتبع يربط كل حجز بالرابط الخاص بك. بمجرد انضمام العميل للبث المدفوع، تُضاف قيمته إلى لوحة أرباحك تلقائياً.
            </p>
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-full bg-orange-500 px-8 py-3 text-base font-semibold text-white shadow-lg transition hover:bg-orange-600">
                    اطلب مكالمة تعريفية
                    <i class="fas fa-arrow-left ml-2"></i>
                </a>
                <a href="#partner-steps" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-8 py-3 text-base font-semibold text-slate-700 transition hover:border-orange-200 hover:text-orange-600">
                    تعرّف على طريقة الانضمام
                    <i class="fas fa-circle-info ml-2 text-orange-500"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="bg-white py-16">
    <div class="container mx-auto px-4">
        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-3xl border border-orange-100 bg-orange-50/60 p-6 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-orange-500">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-slate-900">عمولة ورش Live</h3>
                <p class="mt-2 text-slate-600">عمولة تبدأ من 15% على كل مشارك يحجز ورشة Live عبر رابطك، مع لوحة مباشرة لمتابعة البث وعدد المقاعد المحجوزة.</p>
            </div>
            <div class="rounded-3xl border border-orange-100 bg-orange-50/60 p-6 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-orange-500">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-slate-900">ورش Live مشتركة</h3>
                <p class="mt-2 text-slate-600">ننتج ورش Live تحمل هوية الشريك، ويمكن استضافة شيف من فريقه أو من شبكة وصفة مع توفير غرفة بث وتجربة تفاعلية للأسئلة المباشرة.</p>
            </div>
            <div class="rounded-3xl border border-orange-100 bg-orange-50/60 p-6 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-orange-500">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-slate-900">ترويج البث المباشر</h3>
                <p class="mt-2 text-slate-600">نعلن عن ورش Live على صفحات الوصفات، النشرات البريدية، وروابط الشيف، لضمان امتلاء الغرف وتحقيق ظهور حقيقي لمنتجك.</p>
            </div>
        </div>
    </div>
</div>

<div id="partner-steps" class="bg-gray-50 py-16">
    <div class="container mx-auto px-4 space-y-10">
        <div class="text-center md:text-right">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-500">كيف تصبح شريكاً</p>
            <h2 class="mt-3 text-3xl font-extrabold text-slate-900 md:text-4xl">ثلاث خطوات واضحة للانضمام</h2>
            <p class="mt-2 text-slate-600">نركز على السرعة والوضوح، لذلك جميع المتطلبات موحدة ويمكن متابعتها من خلال لوحة خاصة.</p>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            @php
                $steps = [
                    ['title' => 'طلب الانضمام', 'desc' => 'تعبئة نموذج مختصر حول نوع النشاط والجمهور المستهدف للورش Live، وسيصلك رابط لوحة المتابعة.'],
                    ['title' => 'تحديد النسبة والبث', 'desc' => 'نحدد نسبة العمولة، وننشئ رابط تتبع يربط حملاتك بصفحة تسجيل ورشة Live محددة.'],
                    ['title' => 'تشغيل ورشتك Live', 'desc' => 'إطلاق البث، متابعة الأسئلة المباشرة، واستلام تقارير المقاعد والعائد أسبوعياً.'],
                ];
            @endphp
            @foreach ($steps as $index => $step)
                <div class="rounded-3xl border border-white bg-white p-6 shadow-lg shadow-orange-100/40">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-500 text-xl font-bold text-white">{{ $index + 1 }}</span>
                    <h3 class="mt-4 text-xl font-semibold text-slate-900">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-slate-600">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="bg-white py-16">
    <div class="container mx-auto px-4 grid gap-10 lg:grid-cols-2">
        <div class="space-y-6">
            <p class="text-sm font-semibold text-orange-500">نظام الشيف وروابطه</p>
            <h2 class="text-3xl font-bold text-slate-900">كل شيف يستطيع نشر وصفاته وصناعة صفحة روابط خاصة</h2>
            <p class="text-slate-600">نمنح الشيفات والشركاء لوحة إعدادات تتيح نشر الوصفات مع صور وفيديوهات، ثم ربطها في صفحة روابط ديناميكية مشابهة لصفحات "link in bio". يمكن مشاركة الصفحة في إنستغرام أو واتساب، وتعرض مواعيد ورش Live القادمة وأدوات الشيف الداعمة لها.</p>
            <ul class="space-y-3 text-slate-700">
                <li class="flex items-start gap-3">
                    <span class="mt-1 flex h-7 w-7 items-center justify-center rounded-full bg-orange-100 text-orange-600"><i class="fas fa-check"></i></span>
                    <span>إمكانية إنشاء صفحة روابط متعددة لكل شيف أو فرع مع تحكم كامل بالترتيب وإبراز ورشة Live التالية.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 flex h-7 w-7 items-center justify-center rounded-full bg-orange-100 text-orange-600"><i class="fas fa-check"></i></span>
                    <span>نشر الوصفات مباشرة على المنصة وربطها بزر "احجز مكانك Live" لجمهور الشريك.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 flex h-7 w-7 items-center justify-center rounded-full bg-orange-100 text-orange-600"><i class="fas fa-check"></i></span>
                    <span>تتبع زيارات كل رابط لمعرفة عدد المهتمين بالبث، والورشة التي جذبتهم قبل بدء الجلسة.</span>
                </li>
            </ul>
        </div>
        <div class="rounded-3xl border border-orange-100 bg-orange-50/60 p-8 shadow-xl">
            <p class="text-sm font-semibold text-orange-500">نماذج تعاون سريعة</p>
            <div class="mt-6 space-y-5">
                <div class="rounded-2xl border border-white bg-white p-5 shadow">
                    <h3 class="text-xl font-semibold text-slate-900">برنامج الإحالة</h3>
                    <p class="mt-2 text-slate-600">اربط متجرك أو منصتك برابط حجز ورشة Live محددة، وأضف العمولات فورياً لكل تسجيل يتم قبل بدء البث.</p>
                </div>
                <div class="rounded-2xl border border-white bg-white p-5 shadow">
                    <h3 class="text-xl font-semibold text-slate-900">ورش Live مشتركة</h3>
                    <p class="mt-2 text-slate-600">نقدّم بثاً مباشراً باسم الشريك، مع سيناريو تفاعلي، وإدراج الورشة تلقائياً في صفحات الروابط الخاصة بالشيفات للترويج السريع.</p>
                </div>
                <div class="rounded-2xl border border-white bg-white p-5 shadow">
                    <h3 class="text-xl font-semibold text-slate-900">تحديات Live للوصفات</h3>
                    <p class="mt-2 text-slate-600">إطلاق سلسلة بث مباشر لتجربة وصفات تستخدم منتجات الشريك مع تفاعل حي، ما يزيد الطلب ويمنح الجمهور دليلاً عملياً لحظة بلحظة.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="rounded-3xl border border-orange-100 bg-white p-8 text-center shadow-lg">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-500">جاهز لبث Live مع وصفة؟</p>
            <h2 class="mt-3 text-3xl font-extrabold text-slate-900">نرسل لك خطة ورش Live وعمولاتها خلال 3 أيام عمل</h2>
            <p class="mt-3 text-slate-600">شاركنا تفاصيل نشاطك عبر نموذج التواصل، وسنعود إليك بجدول ورش Live مقترح، وسعر المقعد، ونسبة العمولة المناسبة.</p>
            <a href="{{ route('contact') }}" class="mt-6 inline-flex items-center justify-center rounded-full bg-orange-500 px-8 py-3 text-base font-semibold text-white shadow-lg transition hover:bg-orange-600">
                انتقل إلى نموذج التواصل
                <i class="fas fa-arrow-left ml-2"></i>
            </a>
        </div>
    </div>
</div>
@endsection
