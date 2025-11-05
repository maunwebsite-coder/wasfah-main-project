@extends('layouts.app')

@section('title', 'تفاصيل الحجز للورشة #' . $booking->id)

@section('content')
@php
    $workshop = $booking->workshop ?? $booking->loadMissing('workshop')->workshop;
    $statusLabels = [
        'pending' => 'قيد المراجعة',
        'confirmed' => 'مؤكد',
        'cancelled' => 'ملغى',
    ];
@endphp

<div class="min-h-screen bg-gray-50 py-10">
    <div class="container mx-auto px-4">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm uppercase tracking-widest text-orange-500 font-semibold">حجوزاتي</p>
                <h1 class="text-3xl font-bold text-slate-900">تفاصيل الحجز رقم #{{ $booking->id }}</h1>
                <p class="text-slate-500 mt-1">راجع تفاصيل الورشة وحالة الحجز وروابط الانضمام المتاحة.</p>
            </div>
            <a href="{{ route('bookings.index') }}"
               class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-slate-600 font-semibold shadow-sm hover:border-slate-300 hover:text-slate-900">
                <i class="fas fa-arrow-right"></i>
                العودة لقائمة الحجوزات
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-[2fr,1fr]">
            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="mb-6">
                    <p class="text-xs uppercase tracking-wider text-orange-500 font-semibold">الورشة</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">{{ $workshop->title ?? 'ورشة غير معروفة' }}</h2>
                    <p class="text-sm text-slate-500">
                        {{ optional($workshop->start_date)->locale('ar')->translatedFormat('d F Y • h:i a') }}
                        @if($workshop?->duration)
                            • المدة {{ $workshop->duration }} دقيقة
                        @endif
                    </p>
                </div>

                <dl class="space-y-4 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <dt class="font-semibold text-slate-800">حالة الحجز</dt>
                        <dd class="rounded-full px-3 py-1 text-xs font-semibold {{ $booking->status === 'confirmed' ? 'bg-emerald-50 text-emerald-600' : ($booking->status === 'pending' ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-600') }}">
                            {{ $statusLabels[$booking->status] ?? $booking->status }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="font-semibold text-slate-800">تاريخ الحجز</dt>
                        <dd>{{ $booking->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="font-semibold text-slate-800">طريقة الدفع</dt>
                        <dd>{{ $booking->payment_method ? ucfirst($booking->payment_method) : 'سيتم التحديد لاحقاً' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="font-semibold text-slate-800">المبلغ</dt>
                        <dd>{{ number_format($booking->payment_amount ?? $workshop->price ?? 0, 2) }} {{ $workshop->currency ?? 'SAR' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-800 mb-1">الملاحظات</dt>
                        <dd class="rounded-2xl bg-slate-50 px-4 py-3 text-slate-600">
                            {{ $booking->notes ?: 'لا توجد ملاحظات مضافة' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">الموقع/طريقة الانضمام</p>
                    <h3 class="mt-2 text-lg font-semibold text-slate-900">
                        {{ $workshop->is_online ? 'ورشة أونلاين' : 'ورشة حضورية' }}
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $workshop->is_online ? 'سيتم عقد هذه الورشة عبر منصة Jitsi ضمن موقع وصفة.' : ($workshop->location ?? 'سيتم مشاركة التفاصيل لاحقاً.') }}
                    </p>

                    @if ($workshop->is_online && $workshop->meeting_link)
                        @if ($booking->status === 'confirmed')
                            <a href="{{ route('bookings.join', ['booking' => $booking->public_code]) }}"
                               class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow hover:from-indigo-600 hover:to-indigo-700">
                                <i class="fas fa-video"></i>
                                دخول غرفة الورشة
                            </a>
                            <p class="mt-2 text-xs text-slate-500 text-center">
                                سيتم فتح الغرفة قبل موعد الورشة بقليل بعد موافقة الشيف.
                            </p>
                        @else
                            <div class="mt-4 rounded-2xl border border-dashed border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                                سيتم تفعيل زر الانضمام بعد تأكيد الحجز من قبل فريق وصفة.
                            </div>
                        @endif
                    @endif
                </div>

                @if ($booking->status === 'pending')
                    <div class="rounded-3xl border border-amber-100 bg-amber-50 p-5 text-sm text-amber-700">
                        <p class="font-semibold mb-1">الحجز قيد المراجعة</p>
                        <p>سنتواصل معك فور تأكيد الحجز. يمكنك متابعة حالة الطلب من صفحة ملفك الشخصي.</p>
                    </div>
                @elseif ($booking->status === 'cancelled')
                    <div class="rounded-3xl border border-red-100 bg-red-50 p-5 text-sm text-red-700">
                        <p class="font-semibold mb-1">تم إلغاء هذا الحجز</p>
                        <p>{{ $booking->cancellation_reason ?: 'تم الإلغاء بناءً على طلبك أو لعدم إكمال الإجراءات.' }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
