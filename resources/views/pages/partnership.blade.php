@extends('layouts.app')

@section('title', 'غرفة الانتظار - وصفة')

@section('content')
<section class="relative min-h-screen bg-slate-950 text-white overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 via-rose-500/5 to-slate-900"></div>
    <div class="absolute top-10 -right-20 w-96 h-96 blur-[160px] bg-orange-500/30"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 blur-[120px] bg-rose-500/20"></div>

    <div class="relative z-10 container mx-auto px-4 py-16 grid lg:grid-cols-[1.1fr_0.9fr] gap-10">
        <!-- لوحة الحالة -->
        <div class="space-y-6">
            <div class="flex items-center gap-2 text-sm text-orange-200/80">
                <span class="opacity-80">الرئيسية</span>
                <span class="text-white/40">/</span>
                <span class="opacity-80">Join</span>
            </div>

            <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/10 text-sm tracking-widest uppercase">
                <span class="text-xl">🎥</span>
                بانتظار عودة المضيف
            </div>

            <div class="bg-white/5 backdrop-blur rounded-3xl p-8 shadow-2xl shadow-orange-900/20 flex flex-col gap-8">
                <div class="flex flex-col gap-2">
                    <p class="text-sm text-orange-100/70">جلسة مباشرة</p>
                    <h1 class="text-3xl md:text-4xl font-black leading-tight">براونيز</h1>
                    <p class="text-slate-100/80 leading-relaxed">
                        تأكد من اتصالك بالإنترنت، ثم اسمح للمتصفح بالوصول إلى الميكروفون والكاميرا عند فتح الغرفة. ستظهر عناصر التحكم عند الضغط على زر الانضمام.
                    </p>
                </div>

                <div class="flex flex-col items-center gap-3 py-8">
                    <span class="relative w-16 h-16 rounded-full bg-orange-100/60 flex items-center justify-center">
                        <span class="absolute inset-0 rounded-full bg-orange-200/40 animate-ping"></span>
                        <span class="w-6 h-6 border-4 border-transparent border-t-orange-500 border-l-orange-500 rounded-full animate-spin"></span>
                    </span>
                    <p class="text-sm text-orange-100 font-medium">انتظر الشيف ليدخل الاجتماع</p>
                </div>

                <div class="grid sm:grid-cols-2 gap-4 text-sm text-slate-200">
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400">المضيف</p>
                        <p class="font-semibold">ma'un web</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400">المدة</p>
                        <p class="font-semibold">90 دقيقة تقريباً</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400">عدد المشاركين</p>
                        <p class="font-semibold">1 مشارك مؤكد</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400">دخول</p>
                        <p class="font-semibold">دخول آمن عبر وصفة</p>
                    </div>
                </div>

                <div class="rounded-3xl bg-slate-900/60 border border-white/10 p-5 flex flex-col gap-2">
                    <p class="text-sm text-slate-400">موعد الورشة</p>
                    <div class="flex flex-wrap gap-4 items-center">
                        <p class="text-2xl font-bold">10 نوفمبر 2025 • 05:34 م</p>
                        <span class="px-3 py-1 rounded-full text-xs bg-emerald-500/10 text-emerald-200">تبدأ غدًا</span>
                    </div>
                    <p class="text-slate-300 text-sm">
                        تم فتح الغرفة منذ يوم، ويمكنك الانضمام متى شئت. إذا انقطع الاتصال أعد تحديث الصفحة وسيستمر البث تلقائيًا.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button class="px-6 py-3 rounded-2xl bg-white text-slate-900 font-semibold shadow-lg hover:bg-orange-50 transition">انضم للبث الآن</button>
                    <button class="px-6 py-3 rounded-2xl border border-white/30 hover:bg-white/10 transition">طريقة الشبكة</button>
                </div>
            </div>
        </div>

        <!-- قسم الملاحظات والتحضير -->
        <div class="space-y-6">
            <div class="bg-white/5 backdrop-blur rounded-3xl p-6 border border-white/10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500/20 text-orange-200 grid place-items-center text-xl">🔔</div>
                    <div>
                        <p class="text-sm text-slate-400">جاهزون تقريبًا</p>
                        <p class="font-semibold text-lg">الغرفة مفتوحة الآن</p>
                    </div>
                </div>
                <p class="text-slate-200 text-sm leading-relaxed">
                    تذكير سريع بالضبط الصوتي:
                    <br>• استخدم سماعات أو كتم الميكروفون عند عدم التحدث.
                    <br>• يمكنك التبديل لطريقة الشبكة من زر عرض المربعات داخل البث.
                    <br>• إذا لم تسمع الصوت، افتح إعدادات Jitsi واختر الجهاز الصحيح.
                </p>
            </div>

            <div class="bg-white/5 backdrop-blur rounded-3xl p-6 border border-white/10">
                <p class="text-sm text-slate-400 mb-2">بيانات الدخول</p>
                <div class="space-y-3">
                    <div class="rounded-2xl bg-slate-900/40 px-4 py-3">
                        <p class="text-xs text-slate-400">اسمك في الغرفة</p>
                        <p class="font-semibold">abdullah daoud</p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/40 px-4 py-3">
                        <p class="text-xs text-slate-400">عنوان العرض</p>
                        <p class="font-semibold">براونيز</p>
                    </div>
                    <input type="text" placeholder="أدخل عنوان العرض" class="w-full px-4 py-3 rounded-2xl bg-white/10 border border-white/10 focus:outline-none focus:border-orange-300 placeholder-slate-400 text-white">
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500/30 to-rose-500/20 rounded-3xl p-6 border border-white/10">
                <p class="text-sm text-white/70 mb-3">هل تحتاج تذكيراً؟</p>
                <p class="text-white text-sm leading-relaxed">
                    ستجد رابط الورشة دائمًا داخل صفحة حجوزاتك في وصفة. يمكنك نسخ الرابط أو مشاركته مع الحضور مباشرة.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
