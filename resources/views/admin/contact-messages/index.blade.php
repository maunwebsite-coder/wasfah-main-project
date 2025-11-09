@extends('layouts.app')

@section('title', 'إدارة رسائل التواصل - وصفة')

@php
    $statusLabels = [
        'all' => 'كل الرسائل',
        \App\Models\ContactMessage::STATUS_PENDING => 'بانتظار المراجعة',
        \App\Models\ContactMessage::STATUS_NOTIFIED => 'تم إشعار الفريق',
    ];

    $statusBadgeClasses = [
        \App\Models\ContactMessage::STATUS_PENDING => 'bg-amber-50 text-amber-700 border border-amber-200',
        \App\Models\ContactMessage::STATUS_NOTIFIED => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    ];

    $baseQuery = array_filter([
        'status' => $status !== 'all' ? $status : null,
        'subject' => $subjectFilter !== 'all' ? $subjectFilter : null,
        'search' => $searchTerm ?: null,
        'per_page' => $perPage !== 25 ? $perPage : null,
    ], function ($value) {
        return !is_null($value) && $value !== '';
    });

    $pageAwareQuery = $baseQuery;
    if ($messages->currentPage() > 1) {
        $pageAwareQuery['page'] = $messages->currentPage();
    }
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10 space-y-8">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">صندوق رسائل Wasfah</h1>
            <p class="text-sm text-slate-500">تابع طلبات الشراكات والاستفسارات القادمة من نموذج الاتصال العام.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900">
                <span class="hidden sm:inline">لوحة التحكم</span>
                <span class="sm:hidden">لوحة التحكم</span>
            </a>
            <a href="{{ route('admin.admin-area') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-black">
                منطقة الإدمن
            </a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-100 bg-white px-5 py-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">إجمالي الرسائل</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($statusTotals['all'] ?? 0) }}</p>
            <p class="text-xs text-slate-500">يشمل كل المواضيع والحالات</p>
        </div>
        <div class="rounded-2xl border border-amber-100 bg-amber-50/60 px-5 py-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">بانتظار المتابعة</p>
            <p class="mt-2 text-3xl font-bold text-amber-700">{{ number_format($statusTotals[\App\Models\ContactMessage::STATUS_PENDING] ?? 0) }}</p>
            <p class="text-xs text-amber-600">رسائل تحتاج تدخل إداري</p>
        </div>
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 px-5 py-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">تم إشعار الفريق</p>
            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ number_format($statusTotals[\App\Models\ContactMessage::STATUS_NOTIFIED] ?? 0) }}</p>
            <p class="text-xs text-emerald-600">تم تسليمها عبر البريد</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white px-6 py-6 shadow-sm space-y-6">
        <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">فلاتر البحث</h2>
                <p class="text-sm text-slate-500">حدد الحالة أو الموضوع أو استخدم البحث للوصول السريع لرسالة معينة.</p>
            </div>
            @if(!empty($baseQuery))
                <a href="{{ route('admin.contact-messages.index') }}" class="text-sm font-semibold text-amber-600 hover:text-amber-700">إعادة تعيين الفلاتر</a>
            @endif
        </div>

        @if(request()->filled('message') && !$selectedMessage)
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                تعذر العثور على الرسالة المطلوبة. ربما تمت إزالتها أو تغيير معرّفها.
                <a href="{{ route('admin.contact-messages.index', $baseQuery) }}" class="font-semibold underline-offset-4 hover:underline">إخفاء التنبيه</a>
            </div>
        @endif

        <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="grid gap-4 md:grid-cols-4">
            <div class="space-y-2">
                <label for="status" class="text-sm font-semibold text-slate-700">حالة الرسائل</label>
                <select id="status" name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label for="subject" class="text-sm font-semibold text-slate-700">موضوع الرسالة</label>
                <select id="subject" name="subject" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                    <option value="all" @selected($subjectFilter === 'all')>كل المواضيع</option>
                    @foreach($subjectOptions as $key => $label)
                        <option value="{{ $key }}" @selected($subjectFilter === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label for="search" class="text-sm font-semibold text-slate-700">كلمة البحث</label>
                <input id="search" name="search" value="{{ $searchTerm }}" placeholder="ابحث بالاسم أو البريد أو الهاتف" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200" />
            </div>
            <div class="space-y-2">
                <label for="per_page" class="text-sm font-semibold text-slate-700">عدد النتائج في الصفحة</label>
                <select id="per_page" name="per_page" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-200">
                    @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }} / صفحة</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 flex gap-3 pt-2">
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-amber-500 px-6 py-2 text-sm font-semibold text-white shadow hover:bg-amber-600">
                    تطبيق الفلاتر
                </button>
                @if(!empty($baseQuery))
                    <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-6 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900">
                        مسح
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl border border-slate-100 bg-white px-6 py-6 shadow-sm space-y-4">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">قائمة الرسائل</h2>
                    <p class="text-sm text-slate-500">
                        {{ number_format($messages->total()) }} رسالة • الصفحة {{ $messages->currentPage() }} من {{ max(1, $messages->lastPage()) }}
                    </p>
                </div>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                    {{ $statusLabels[$status] ?? 'الرسائل' }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-right">المرسل</th>
                            <th class="px-4 py-3 text-right">الموضوع</th>
                            <th class="px-4 py-3 text-right">الحالة</th>
                            <th class="px-4 py-3 text-right">تاريخ الإرسال</th>
                            <th class="px-4 py-3 text-right">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($messages as $message)
                            @php
                                $isSelected = $selectedMessage && $selectedMessage->id === $message->id;
                                $messageQuery = array_merge($pageAwareQuery, ['message' => $message->id]);
                            @endphp
                            <tr class="{{ $isSelected ? 'bg-amber-50/60' : 'hover:bg-slate-50' }}">
                                <td class="px-4 py-4 align-top">
                                    <div class="font-semibold text-slate-900">{{ $message->full_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $message->email }}</div>
                                    @if($message->phone)
                                        <div class="text-xs text-slate-400">{{ $message->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="text-sm font-semibold text-slate-900">{{ $message->subject_label }}</div>
                                    <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($message->message, 90) }}</p>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadgeClasses[$message->status] ?? 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                        {{ $statusLabels[$message->status] ?? $message->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 align-top text-sm text-slate-600">
                                    {{ optional($message->created_at)->locale('ar')->translatedFormat('d F Y • h:i a') }}
                                    <div class="text-xs text-slate-400">{{ optional($message->created_at)->diffForHumans() }}</div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <a href="{{ route('admin.contact-messages.index', $messageQuery) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:text-amber-600">
                                        عرض التفاصيل
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                    لا توجد رسائل تطابق معايير البحث الحالية.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 pt-4">
                {{ $messages->links() }}
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white px-6 py-6 shadow-sm space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">تفاصيل الرسالة</h2>
                    <p class="text-sm text-slate-500">اختر رسالة من الجدول لعرض بياناتها كاملة.</p>
                </div>
                @if($selectedMessage)
                    <a href="{{ route('admin.contact-messages.index', $baseQuery) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900">
                        إغلاق
                    </a>
                @endif
            </div>

            @if($selectedMessage)
                @php
                    $meta = $selectedMessage->meta ?? [];
                @endphp
                <div class="space-y-5">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-base font-semibold text-slate-900">{{ $selectedMessage->full_name }}</div>
                                <div class="text-xs text-slate-500">{{ $selectedMessage->email }}</div>
                            </div>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadgeClasses[$selectedMessage->status] ?? 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                {{ $statusLabels[$selectedMessage->status] ?? $selectedMessage->status }}
                            </span>
                        </div>
                        <div class="mt-3 text-xs text-slate-500">
                            {{ optional($selectedMessage->created_at)->locale('ar')->translatedFormat('d F Y • h:i a') }}
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">موضوع الرسالة</p>
                        <p class="mt-1 text-base font-semibold text-slate-900">{{ $selectedMessage->subject_label }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">محتوى الرسالة</p>
                        <div class="mt-2 rounded-2xl border border-slate-100 bg-white px-4 py-4 text-sm leading-relaxed text-slate-800 whitespace-pre-line">
                            {!! nl2br(e($selectedMessage->message)) !!}
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">معلومات التواصل</p>
                        <ul class="mt-2 space-y-2 text-sm text-slate-600">
                            <li>
                                البريد:
                                <a href="mailto:{{ $selectedMessage->email }}" class="font-semibold text-amber-600 hover:text-amber-700">
                                    {{ $selectedMessage->email }}
                                </a>
                            </li>
                            @if($selectedMessage->phone)
                                <li>
                                    الهاتف:
                                    <a href="tel:{{ $selectedMessage->phone }}" class="font-semibold text-amber-600 hover:text-amber-700">
                                        {{ $selectedMessage->phone }}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">بيانات إضافية</p>
                        @if(!empty($meta))
                            <dl class="mt-3 space-y-2 text-sm text-slate-600">
                                @if(!empty($meta['source']))
                                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                                        <dt class="font-semibold text-slate-500">المصدر</dt>
                                        <dd class="text-slate-800">{{ $meta['source'] }}</dd>
                                    </div>
                                @endif
                                @if(!empty($meta['ip']))
                                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                                        <dt class="font-semibold text-slate-500">عنوان IP</dt>
                                        <dd class="text-slate-800">{{ $meta['ip'] }}</dd>
                                    </div>
                                @endif
                                @if(!empty($meta['user_agent']))
                                    <div class="rounded-xl bg-slate-50 px-3 py-2">
                                        <dt class="font-semibold text-slate-500">المتصفح</dt>
                                        <dd class="text-slate-800 text-xs leading-5">{{ $meta['user_agent'] }}</dd>
                                    </div>
                                @endif
                                @if(!empty($meta['mail_error']))
                                    <div class="rounded-xl bg-red-50 px-3 py-2">
                                        <dt class="font-semibold text-red-600">خطأ البريد</dt>
                                        <dd class="text-red-700 text-xs leading-5">{{ $meta['mail_error'] }}</dd>
                                    </div>
                                @endif
                            </dl>
                        @else
                            <p class="mt-2 text-xs text-slate-400">لا توجد بيانات إضافية لهذه الرسالة.</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-200 px-6 py-12 text-center text-sm text-slate-500">
                    اختر أي رسالة من القائمة لعرض تفاصيلها هنا.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
