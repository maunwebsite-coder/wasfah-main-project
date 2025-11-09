@extends('layouts.app')

@section('title', 'طلبات التواصل - الإدارة')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="flex flex-col gap-4 md:gap-0 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm text-orange-500 font-semibold uppercase tracking-widest">مركز الرسائل</p>
                <h1 class="text-3xl font-bold text-slate-900 mt-2">طلبات التواصل والشراكات</h1>
                <p class="text-sm text-slate-500 mt-1">كل الرسائل المرسلة عبر صفحات «اتصل بنا» و«شريك وصفة». يمكن تصفيتها ومتابعة حالتها من هنا.</p>
            </div>
            <a href="{{ route('admin.admin-area') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-white text-sm font-semibold text-slate-600 hover:text-orange-600 hover:border-orange-200 transition">
                <i class="fas fa-arrow-right"></i>
                الرجوع لمنطقة الإدمن
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white border border-orange-100 rounded-2xl p-5 shadow-sm">
                <p class="text-sm text-orange-500 font-semibold">طلبات جديدة</p>
                <p class="text-3xl font-black text-slate-900 mt-2">{{ number_format($stats['pending'] ?? 0) }}</p>
                <p class="text-xs text-slate-500 mt-1">بانتظار إشعار الفريق</p>
            </div>
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
                <p class="text-sm text-slate-500 font-semibold">بانتظار المراجعة</p>
                <p class="text-3xl font-black text-slate-900 mt-2">{{ number_format($stats['unreviewed'] ?? 0) }}</p>
                <p class="text-xs text-slate-500 mt-1">بما فيها طلبات الشراكة</p>
            </div>
            <div class="bg-white border border-emerald-100 rounded-2xl p-5 shadow-sm">
                <p class="text-sm text-emerald-600 font-semibold">طلبات شراكة</p>
                <p class="text-3xl font-black text-slate-900 mt-2">{{ number_format($stats['partnership'] ?? 0) }}</p>
                <p class="text-xs text-slate-500 mt-1">بحاجة إلى متابعة من فريق الشراكات</p>
            </div>
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
                <p class="text-sm text-slate-500 font-semibold">إجمالي الرسائل</p>
                <p class="text-3xl font-black text-slate-900 mt-2">{{ number_format($stats['total'] ?? 0) }}</p>
                <p class="text-xs text-slate-500 mt-1">كل الطلبات المسجلة</p>
            </div>
        </div>

        <form method="GET" class="bg-white border border-slate-100 rounded-3xl shadow p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">بحث عام</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم، بريد، هاتف، أو كلمة من الرسالة" class="w-full rounded-2xl border-slate-200 focus:border-orange-400 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">الموضوع</label>
                    <select name="subject" class="w-full rounded-2xl border-slate-200 focus:border-orange-400 focus:ring-orange-400">
                        <option value="">كل المواضيع</option>
                        @foreach ($subjectLabels as $key => $label)
                            <option value="{{ $key }}" @selected(request('subject') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">الحالة</label>
                    <select name="status" class="w-full rounded-2xl border-slate-200 focus:border-orange-400 focus:ring-orange-400">
                        <option value="">كل الحالات</option>
                        @foreach ($statusLabels as $key => $label)
                            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="flex-1 inline-flex justify-center items-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold py-3">
                        <i class="fas fa-filter"></i>
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex justify-center items-center rounded-2xl border border-slate-200 text-slate-600 font-semibold px-4 py-3 hover:text-orange-600 hover:border-orange-300 transition">
                        إعادة ضبط
                    </a>
                </div>
            </div>
        </form>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-slate-100 rounded-3xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">المرسل</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الموضوع</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الرسالة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($messages as $message)
                            <tr class="{{ $highlightId === $message->id ? 'bg-orange-50/60' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-slate-900">{{ $message->full_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $message->email }}</div>
                                    <div class="text-xs text-slate-400">{{ $message->phone ?? '—' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-1">تم الإرسال: {{ $message->created_at?->format('Y-m-d H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-semibold text-slate-900">{{ $message->subject_label }}</span>
                                        @if ($message->subject === 'partnership')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 w-max">شريك وصفة</span>
                                        @endif
                                        @if ($source = data_get($message->meta, 'source'))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 w-max">
                                                من: {{ $source }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700">
                                    <div class="prose prose-slate max-w-none prose-sm">
                                        {!! nl2br(e(\Illuminate\Support\Str::limit($message->message, 280))) !!}
                                    </div>
                                    <div class="mt-2 text-[11px] text-slate-400 flex flex-wrap gap-2">
                                        @if($ip = data_get($message->meta, 'ip'))
                                            <span class="inline-flex items-center gap-1">
                                                <i class="fas fa-network-wired"></i>
                                                {{ $ip }}
                                            </span>
                                        @endif
                                        @if($ua = data_get($message->meta, 'user_agent'))
                                            <span class="inline-flex items-center gap-1">
                                                <i class="fas fa-window-maximize"></i>
                                                {{ \Illuminate\Support\Str::limit($ua, 40) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="{{ $message->status_badge_class }}">{{ $message->status_label }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-left">
                                    <div class="flex flex-wrap gap-2">
                                        @if ($message->status !== \App\Models\ContactMessage::STATUS_REVIEWED)
                                            <form method="POST" action="{{ route('admin.contact-messages.update-status', $message) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ \App\Models\ContactMessage::STATUS_REVIEWED }}">
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-emerald-500 text-white text-xs font-semibold hover:bg-emerald-600 transition">
                                                    <i class="fas fa-check"></i>
                                                    تمت المراجعة
                                                </button>
                                            </form>
                                        @endif
                                        @if ($message->status !== \App\Models\ContactMessage::STATUS_NOTIFIED)
                                            <form method="POST" action="{{ route('admin.contact-messages.update-status', $message) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ \App\Models\ContactMessage::STATUS_NOTIFIED }}">
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full border border-slate-200 text-slate-600 text-xs font-semibold hover:text-orange-600 hover:border-orange-200 transition">
                                                    <i class="fas fa-bell"></i>
                                                    تم إشعار الفريق
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                                    لا توجد رسائل لعرضها.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $messages->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
