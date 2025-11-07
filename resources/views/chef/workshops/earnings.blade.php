@extends('layouts.app')

@section('title', 'عوائد ورش العمل')

@section('content')
@php
    $formatCurrency = function ($amount) {
        $value = (float) ($amount ?? 0);
        return number_format($value, 2) . ' د.أ';
    };

    $formatNumber = fn ($number) => number_format((int) ($number ?? 0));
@endphp
<div class="min-h-screen bg-gradient-to-b from-orange-50/70 to-white py-10">
    <div class="container mx-auto px-4 space-y-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-orange-500">منطقة الشيف</p>
                <h1 class="mt-1 text-3xl font-bold text-slate-900">عوائد ورش العمل</h1>
                <p class="mt-2 text-sm text-slate-600">
                    راجع الأداء المالي لورشاتك الأونلاين وتابع صافي الدخل المتوقع بعد خصم حصة المنصّة.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('chef.workshops.create') }}"
                   class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-white shadow hover:from-orange-600 hover:to-orange-700">
                    <i class="fas fa-plus"></i>
                    إطلاق ورشة جديدة
                </a>
                <a href="{{ route('chef.workshops.index') }}"
                   class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-slate-600 shadow-sm hover:border-slate-300 hover:text-slate-800">
                    <i class="fas fa-arrow-right"></i>
                    العودة لقائمة الورش
                </a>
            </div>
        </div>

        <div class="rounded-3xl border border-amber-200 bg-amber-50/80 p-5 text-amber-900 shadow">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white text-amber-500 shadow-inner">
                    <i class="fas fa-balance-scale text-2xl"></i>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-semibold uppercase tracking-wider text-amber-500">معلومة مهمة</p>
                    <h2 class="text-lg font-bold text-amber-900">سيتم خصم 25% – 30% لصالح منصّة وصفة</h2>
                    <p class="text-sm leading-relaxed text-amber-800">
                        عند إنشاء أي ورشة جديدة يتم اقتطاع نسبة 25% إلى 30% لتغطية عمليات الدفع، الدعم التقني والتسويق.
                        يتم تحويل باقي المبلغ لك خلال أسبوع من انتهاء الورشة بعد تسوية المدفوعات.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-4">
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-sm font-semibold text-slate-500">إجمالي المبالغ المحصّلة</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $formatCurrency($lifetimeGross) }}</p>
                <p class="mt-1 text-xs text-slate-500">من جميع الحجوزات المدفوعة</p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-sm font-semibold text-slate-500">صافي متوقع بعد خصم المنصّة</p>
                <p class="mt-3 text-2xl font-bold text-slate-900">
                    {{ $formatCurrency($netRange['low']) }}
                    <span class="text-sm font-semibold text-slate-500">إلى</span>
                    {{ $formatCurrency($netRange['high']) }}
                </p>
                <p class="mt-1 text-xs text-slate-500">يعتمد على نسبة الخصم (25% – 30%)</p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-sm font-semibold text-slate-500">عدد المقاعد المدفوعة</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $formatNumber($paidSeats) }}</p>
                <p class="mt-1 text-xs text-slate-500">يشمل جميع الورش المكتملة</p>
            </div>
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-sm font-semibold text-slate-500">متوسط العائد للمشارك</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $formatCurrency($averageSeat) }}</p>
                <p class="mt-1 text-xs text-slate-500">يساعدك في تسعير الورش القادمة</p>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <p class="text-sm font-semibold text-slate-500">عوائد هذا الشهر</p>
                <div class="mt-3 flex items-baseline gap-3">
                    <p class="text-3xl font-bold text-slate-900">{{ $formatCurrency($currentMonthGross) }}</p>
                    @php
                        $delta = $currentMonthGross - $previousMonthGross;
                    @endphp
                    <span class="text-sm font-semibold {{ $delta >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $delta >= 0 ? '+' : '' }}{{ $formatCurrency($delta) }} مقارنـةً بالشهر الماضي
                    </span>
                </div>
                <p class="mt-2 text-xs text-slate-500">يتم التحديث بمجرد تأكيد دفع المشاركين</p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <p class="text-sm font-semibold text-slate-500">عوائد الشهر الماضي</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $formatCurrency($previousMonthGross) }}</p>
                <p class="mt-2 text-xs text-slate-500">للمقارنة التاريخية فقط</p>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-orange-500">أفضل الورش أداءً</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">تفصيل حسب كل ورشة</h2>
                    <p class="mt-1 text-sm text-slate-500">أرقام تقريبية للصافي بعد خصم حصة المنصّة.</p>
                </div>
                <a href="{{ route('chef.workshops.index') }}"
                   class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:border-slate-300 hover:text-slate-800">
                    إدارة الورش
                </a>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-4 py-3 text-right font-semibold text-slate-600">الورشة</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-600">تاريخ البداية</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-600">المشاركون المدفوعون</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-600">إجمالي المدفوع</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-600">صافي متوقع</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($workshopBreakdown as $workshop)
                            @php
                                $paidTotal = (float) ($workshop->paid_total ?? 0);
                                $netLow = $paidTotal * 0.70;
                                $netHigh = $paidTotal * 0.75;
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-slate-900">{{ $workshop->title }}</p>
                                    <p class="text-xs text-slate-500">سعة {{ $workshop->max_participants ?? 'غير محددة' }}</p>
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    @if ($workshop->start_date)
                                        {{ $workshop->start_date->locale('ar')->translatedFormat('d F Y • h:i a') }}
                                    @else
                                        <span class="text-xs text-slate-400">لم يحدد بعد</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-slate-900 font-semibold">
                                    {{ $formatNumber($workshop->paid_seats) }}
                                </td>
                                <td class="px-4 py-4 text-slate-900 font-semibold">
                                    {{ $formatCurrency($paidTotal) }}
                                </td>
                                <td class="px-4 py-4 text-slate-900 font-semibold">
                                    {{ $formatCurrency($netLow) }}
                                    <span class="text-xs text-slate-500">-</span>
                                    {{ $formatCurrency($netHigh) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-500">
                                    لا توجد بيانات مالية بعد. ابدأ بإنشاء ورشة وتأكيد الحجوزات المدفوعة.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
