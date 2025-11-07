@extends('layouts.app')

@section('title', 'برنامج الإحالات')

@section('content')
    <div class="bg-slate-50 py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-orange-100 p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <p class="text-sm font-semibold text-orange-500 mb-2">برنامج الشركاء</p>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-4">مرحباً {{ $partner->name }} 👋</h1>
                        <p class="text-slate-600 leading-relaxed max-w-2xl">
                            شارك رابطك الخاص لتوسيع مجتمع وصفة، واحصل على عمولة {{ number_format($partner->referral_commission_rate ?? config('referrals.default_rate'), 2) }}%
                            من كل مشارك يحجز في ورشات الشيفات الذين انضموا عن طريقك.
                        </p>
                    </div>
                    <div class="w-full lg:w-1/2">
                        <label class="text-sm font-semibold text-slate-600 mb-2 block">رابط الدعوة الشخصي</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="flex-1">
                                <input
                                    type="text"
                                    readonly
                                    id="referral-link-input"
                                    value="{{ $referralLink }}"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-mono text-slate-700 focus:border-orange-400 focus:ring-2 focus:ring-orange-200"
                                >
                            </div>
                            <button
                                id="copy-referral-link"
                                type="button"
                                class="inline-flex items-center justify-center rounded-2xl bg-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-200"
                            >
                                <i class="fas fa-copy mr-2"></i>
                                نسخ الرابط
                            </button>
                        </div>
                        <p id="copy-feedback" class="text-xs text-emerald-600 mt-2 hidden">تم نسخ الرابط إلى الحافظة ✅</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-orange-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold text-orange-500 uppercase tracking-wide">المستخدمون المسجلون</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ number_format($referredUsersCount) }}</p>
                    <p class="text-sm text-slate-500 mt-1">منهم {{ number_format($referredChefsCount) }} شيف</p>
                </div>
                <div class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold text-emerald-500 uppercase tracking-wide">عمولات جاهزة للسحب</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ number_format($readyAmount, 2) }} {{ $partner->referral_currency_symbol }}</p>
                    <p class="text-sm text-slate-500 mt-1">{{ number_format($readyCount) }} حجز مدفوع</p>
                </div>
                <div class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold text-blue-500 uppercase tracking-wide">مجموع ما تم دفعه</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ number_format($paidAmount, 2) }} {{ $partner->referral_currency_symbol }}</p>
                    <p class="text-sm text-slate-500 mt-1">إلى اليوم</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">شيفات نشطون</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ number_format($activeChefsCount) }}</p>
                    <p class="text-sm text-slate-500 mt-1">يديرون ورشات حالياً</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs font-semibold text-orange-500 uppercase tracking-wide">أبرز الشيفات</p>
                            <h2 class="text-xl font-bold text-slate-900">الشيفات الذين سجلوا عبرك</h2>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-600">
                            {{ number_format($referredChefsCount) }} شيف
                        </span>
                    </div>
                    <div class="space-y-4">
                        @forelse ($referredChefs as $chef)
                            <div class="rounded-2xl border border-slate-100 p-4 flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $chef->name }}</p>
                                    <p class="text-sm text-slate-500">{{ $chef->email }}</p>
                                    <p class="text-xs text-slate-400 mt-1">ورش نشطة: {{ $chef->workshops_count }} • حجوزات مدفوعة: {{ $chef->referral_commissions_count }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">إيرادك من ورشاته</p>
                                    <p class="text-lg font-bold text-emerald-600">{{ number_format($chef->referral_commissions_total ?? 0, 2) }} {{ $partner->referral_currency_symbol }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">لم يسجل أي شيف عبر رابطك بعد. شارك الرابط مع مجتمعك لبدء تحقيق العوائد.</p>
                        @endforelse
                    </div>
                </div>
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs font-semibold text-blue-500 uppercase tracking-wide">حالة العمولات</p>
                            <h2 class="text-xl font-bold text-slate-900">ملخص سريع</h2>
                        </div>
                    </div>
                    <dl class="space-y-4">
                        <div class="flex items-center justify-between rounded-2xl border border-emerald-100 bg-emerald-50/60 px-4 py-3">
                            <dt class="text-sm font-semibold text-emerald-700">مبالغ جاهزة</dt>
                            <dd class="text-lg font-black text-emerald-700">{{ number_format($readyAmount, 2) }} {{ $partner->referral_currency_symbol }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl border border-blue-100 bg-blue-50/60 px-4 py-3">
                            <dt class="text-sm font-semibold text-blue-700">مبالغ تم تحويلها</dt>
                            <dd class="text-lg font-black text-blue-700">{{ number_format($paidAmount, 2) }} {{ $partner->referral_currency_symbol }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <dt class="text-sm font-semibold text-slate-600">عمولات مرفوضة / ملغاة</dt>
                            <dd class="text-lg font-black text-slate-700">{{ number_format($cancelledAmount, 2) }} {{ $partner->referral_currency_symbol }}</dd>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl border border-orange-100 bg-orange-50 px-4 py-3">
                            <dt class="text-sm font-semibold text-orange-600">إجمالي الحجوزات المفوترة</dt>
                            <dd class="text-lg font-black text-orange-600">{{ number_format($totalCommissions) }}</dd>
                        </div>
                    </dl>
                    <p class="text-xs text-slate-400 mt-4">سيتم التواصل معك بواسطة فريق الإدارة لتحويل المبالغ الجاهزة بشكل دوري.</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">المسجلون مؤخراً</p>
                            <h2 class="text-xl font-bold text-slate-900">آخر المنضمين عبر رابطك</h2>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentReferrals as $ref)
                            <div class="flex items-center justify-between rounded-2xl border border-slate-100 px-4 py-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $ref->name ?? 'مستخدم بدون اسم' }}</p>
                                    <p class="text-xs text-slate-500">{{ $ref->email }}</p>
                                </div>
                                <div class="text-right text-xs text-slate-400">
                                    <p class="font-semibold text-slate-600">
                                        {{ __('roles.' . $ref->role, [], 'ar') ?? $ref->role }}
                                    </p>
                                    <p>{{ optional($ref->created_at)->locale('ar')->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">سجّل مستخدمون عبر رابطك وستظهر أسماؤهم هنا.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 px-6 py-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">سجل العمولات</p>
                        <h2 class="text-xl font-bold text-slate-900">تفاصيل الحجوزات المدفوعة</h2>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                        {{ number_format($commissions->total()) }} عملية
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">الورشة</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">الشيف</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">المشارك</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">حصة الشريك</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">حالة العمولة</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">تاريخ الإستحقاق</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($commissions as $commission)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-slate-900">{{ $commission->workshop?->title ?? '—' }}</p>
                                        <p class="text-xs text-slate-500">{{ optional($commission->workshop?->start_date)->locale('ar')->translatedFormat('d F Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $commission->referredUser?->name ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $commission->participant?->name ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-emerald-600">{{ number_format($commission->commission_amount, 2) }} {{ $commission->currency_symbol }}</p>
                                        <p class="text-xs text-slate-400">({{ number_format($commission->commission_rate, 2) }}%)</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusClasses = [
                                                \App\Models\ReferralCommission::STATUS_READY => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                                \App\Models\ReferralCommission::STATUS_PAID => 'bg-blue-50 text-blue-700 border border-blue-200',
                                                \App\Models\ReferralCommission::STATUS_CANCELLED => 'bg-rose-50 text-rose-700 border border-rose-200',
                                                \App\Models\ReferralCommission::STATUS_PENDING => 'bg-slate-50 text-slate-600 border border-slate-200',
                                            ];
                                            $statusLabels = [
                                                \App\Models\ReferralCommission::STATUS_READY => 'جاهزة للتحويل',
                                                \App\Models\ReferralCommission::STATUS_PAID => 'تم دفعها',
                                                \App\Models\ReferralCommission::STATUS_CANCELLED => 'ألغيت',
                                                \App\Models\ReferralCommission::STATUS_PENDING => 'قيد المراجعة',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$commission->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $statusLabels[$commission->status] ?? ucfirst($commission->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ optional($commission->earned_at ?? $commission->created_at)->locale('ar')->translatedFormat('d F Y - h:i a') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">
                                        لم يتم تسجيل أي عمولات بعد. عندما يحجز المشاركون عبر ورشات الشيفات الذين دعوتهم ستظهر تفاصيلهم هنا.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $commissions->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const copyButton = document.getElementById('copy-referral-link');
            const input = document.getElementById('referral-link-input');
            const feedback = document.getElementById('copy-feedback');

            if (copyButton && input) {
                copyButton.addEventListener('click', async () => {
                    try {
                        await navigator.clipboard.writeText(input.value);
                        feedback?.classList.remove('hidden');
                        setTimeout(() => feedback?.classList.add('hidden'), 2500);
                    } catch (error) {
                        alert('تعذر نسخ الرابط، يرجى النسخ اليدوي.');
                    }
                });
            }
        });
    </script>
@endsection
