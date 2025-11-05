@extends('layouts.app')

@section('title', 'منطقة الإدمن')

@push('styles')
<style>
    .admin-section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .admin-section-title i {
        color: #f97316;
    }
    .attention-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.5rem;
        border-radius: 16px;
        background: linear-gradient(135deg, #fff7ed, #fffbeb);
        border: 1px solid #fed7aa;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .attention-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(249, 115, 22, 0.18);
    }
    .attention-card.is-empty {
        background: #f9fafb;
        border-color: #e5e7eb;
    }
    .attention-card__icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f97316;
        color: #fff;
        box-shadow: 0 10px 25px rgba(249, 115, 22, 0.35);
        flex-shrink: 0;
    }
    .attention-card.is-empty .attention-card__icon {
        background: #9ca3af;
        box-shadow: none;
    }
    .attention-card__label {
        font-weight: 600;
        color: #1f2937;
    }
    .attention-card__value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #f97316;
        display: block;
    }
    .attention-card.is-empty .attention-card__value {
        color: #6b7280;
    }
    .attention-card__status {
        font-size: 0.9rem;
        color: #6b7280;
        display: block;
        margin-top: 0.35rem;
    }
    .attention-card__cta {
        position: absolute;
        inset-inline-end: 1.25rem;
        top: 1.25rem;
        color: #f97316;
        transition: transform 0.2s ease;
    }
    .attention-card:hover .attention-card__cta {
        transform: translateX(-4px);
    }
    .attention-card.is-empty .attention-card__cta {
        color: #9ca3af;
    }
    .metric-card {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.5rem;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #f3f4f6;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        border-color: #fed7aa;
    }
    .metric-card__icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f97316, #fb923c);
        color: #fff;
        box-shadow: 0 12px 22px rgba(249, 115, 22, 0.35);
    }
    .metric-card__value {
        font-size: 2rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1;
        display: block;
    }
    .metric-card__label {
        font-weight: 600;
        color: #1f2937;
    }
    .metric-card__hint {
        font-size: 0.85rem;
        color: #6b7280;
        display: block;
        margin-top: 0.35rem;
    }
    .panel {
        background: #ffffff;
        border-radius: 18px;
        padding: 1.5rem;
        border: 1px solid #f3f4f6;
        box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
    }
    .panel__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    .panel__title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .panel__subtitle {
        font-size: 0.85rem;
        color: #6b7280;
    }
    .panel__list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .panel__item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .panel__item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .panel__item-title {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.25rem;
        display: block;
    }
    .panel__item-meta {
        font-size: 0.85rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .panel__badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
    }
    .panel__badge--default {
        background: #f3f4f6;
        color: #374151;
    }
    .panel__badge--pending {
        background: rgba(234, 179, 8, 0.18);
        color: #92400e;
    }
    .panel__badge--approved {
        background: rgba(34, 197, 94, 0.15);
        color: #166534;
    }
    .panel__badge--rejected {
        background: rgba(239, 68, 68, 0.17);
        color: #991b1b;
    }
    .panel__badge--draft {
        background: rgba(148, 163, 184, 0.2);
        color: #334155;
    }
    .panel__empty {
        text-align: center;
        color: #6b7280;
        padding: 2rem 1rem;
        border-radius: 12px;
        background: #f9fafb;
        border: 1px dashed #d1d5db;
    }
    .management-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 1.75rem;
        border: 1px solid #f3f4f6;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .management-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 35px rgba(15, 23, 42, 0.12);
        border-color: #fed7aa;
    }
    .management-card__icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: linear-gradient(135deg, #f97316, #f59e0b);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 16px 32px rgba(249, 115, 22, 0.3);
    }
    .management-card__title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
    }
    .management-card__description {
        color: #6b7280;
        line-height: 1.6;
        font-size: 0.95rem;
    }
    .management-card__links {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .management-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1.15rem;
        border-radius: 999px;
        border: 1px solid #e5e7eb;
        color: #1f2937;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.2s ease;
        background: #f9fafb;
    }
    .management-link:hover {
        border-color: #f97316;
        color: #f97316;
        background: #fff7ed;
    }
    .quick-actions {
        background: linear-gradient(135deg, #fff7ed, #ffe4e6);
        border-radius: 24px;
        padding: 2.5rem 2rem;
        border: 1px solid rgba(249, 115, 22, 0.15);
        box-shadow: 0 16px 45px rgba(249, 115, 22, 0.15);
    }
    .quick-actions__grid {
        display: grid;
        gap: 1.25rem;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        margin-top: 1.5rem;
    }
    .quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.65rem;
        padding: 1.25rem;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(249, 115, 22, 0.2);
        color: #f97316;
        font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        text-align: center;
    }
    .quick-action i {
        font-size: 1.75rem;
    }
    .quick-action:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 30px rgba(249, 115, 22, 0.2);
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.9rem 1.8rem;
        border-radius: 14px;
        background: #374151;
        color: #ffffff;
        font-weight: 600;
        transition: background 0.2s ease, transform 0.2s ease;
    }
    .back-link:hover {
        background: #111827;
        transform: translateY(-2px);
    }
    @media (max-width: 768px) {
        .attention-card {
            flex-direction: column;
            align-items: flex-start;
            padding-inline-end: 3.25rem;
        }
        .attention-card__cta {
            inset-inline-end: 1rem;
            top: 1rem;
        }
        .metric-card {
            flex-direction: column;
            align-items: flex-start;
        }
        .panel__item {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12">
    <header class="text-center space-y-3">
        <h1 class="text-4xl font-bold text-gray-900 flex items-center justify-center gap-3">
            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-orange-600 text-white shadow-lg">
                <i class="fas fa-crown"></i>
            </span>
            منطقة الإدمن
        </h1>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto">
            مرجعك السريع لكل ما يتعلق بإدارة Wasfah. راجع المهام العاجلة، واحصل على نظرة عامة، وابدأ العمل فوراً.
        </p>
    </header>

    <section class="space-y-4">
        <div class="admin-section-title">
            <i class="fas fa-bell"></i>
            ما يحتاج انتباهك الآن
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($attentionItems as $item)
                @php
                    $isEmpty = (int) $item['value'] === 0;
                    $destination = $item['route'] ?? null;
                    $params = $item['route_params'] ?? [];
                    $url = $item['url'] ?? ($destination ? route($destination, $params) : '#');
                @endphp
                <a href="{{ $url }}" class="attention-card {{ $isEmpty ? 'is-empty' : '' }}">
                    <div class="attention-card__icon">
                        <i class="fas {{ $item['icon'] }}"></i>
                    </div>
                    <div>
                        <span class="attention-card__label">{{ $item['label'] }}</span>
                        <span class="attention-card__value">{{ number_format($item['value']) }}</span>
                        <span class="attention-card__status">
                            {{ $isEmpty ? $item['empty_state'] : $item['cta'] }}
                        </span>
                    </div>
                    <span class="attention-card__cta">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="space-y-4">
        <div class="admin-section-title">
            <i class="fas fa-chart-line"></i>
            نظرة سريعة
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach($metrics as $metric)
                @php
                    $metricUrl = $metric['url'] ?? route($metric['route'], $metric['route_params'] ?? []);
                @endphp
                <a href="{{ $metricUrl }}" class="metric-card">
                    <div class="metric-card__icon">
                        <i class="fas {{ $metric['icon'] }}"></i>
                    </div>
                    <div>
                        <span class="metric-card__value">{{ number_format($metric['value']) }}</span>
                        <span class="metric-card__label">{{ $metric['label'] }}</span>
                        @if(!empty($metric['hint']))
                            <span class="metric-card__hint">{{ $metric['hint'] }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="panel">
            <div class="panel__header">
                <div>
                    <div class="panel__title">
                        <i class="fas fa-fire text-orange-500 ml-2"></i>
                        أحدث الوصفات
                    </div>
                    <div class="panel__subtitle">تابع ما تم إضافته مؤخراً وتأكد من جاهزيته للنشر</div>
                </div>
                <a href="{{ route('admin.recipes.index') }}" class="text-sm font-semibold text-orange-500 hover:text-orange-600">
                    عرض الكل
                </a>
            </div>
            <div class="panel__list">
                @forelse($recentRecipes as $recipe)
                    <div class="panel__item">
                        <div>
                            <a href="{{ route('admin.recipes.edit', $recipe) }}" class="panel__item-title">
                                {{ \Illuminate\Support\Str::limit($recipe->title, 80) }}
                            </a>
                            <div class="panel__item-meta">
                                <i class="fas fa-clock"></i>
                                {{ $recipe->created_at?->diffForHumans() }}
                            </div>
                        </div>
                        @php
                            $statusKey = $recipe->status ?? 'default';
                            $badgeMap = [
                                \App\Models\Recipe::STATUS_PENDING => 'panel__badge panel__badge--pending',
                                \App\Models\Recipe::STATUS_APPROVED => 'panel__badge panel__badge--approved',
                                \App\Models\Recipe::STATUS_REJECTED => 'panel__badge panel__badge--rejected',
                                \App\Models\Recipe::STATUS_DRAFT => 'panel__badge panel__badge--draft',
                            ];
                            $badgeClass = $badgeMap[$statusKey] ?? 'panel__badge panel__badge--default';
                        @endphp
                        <span class="{{ $badgeClass }}">
                            {{ $recipeStatusLabels[$statusKey] ?? 'غير محدد' }}
                        </span>
                    </div>
                @empty
                    <div class="panel__empty">
                        لا توجد وصفات حديثة حالياً. ابدأ بإضافة محتوى جديد!
                    </div>
                @endforelse
            </div>
        </div>
        <div class="panel">
            <div class="panel__header">
                <div>
                    <div class="panel__title">
                        <i class="fas fa-calendar-alt text-orange-500 ml-2"></i>
                        الورشات القادمة
                    </div>
                    <div class="panel__subtitle">تحقق من الجدول وتأكد من جاهزية الفرق للحجوزات</div>
                </div>
                <a href="{{ route('admin.workshops.index') }}" class="text-sm font-semibold text-orange-500 hover:text-orange-600">
                    كل الورشات
                </a>
            </div>
            <div class="panel__list">
                @forelse($upcomingWorkshops as $workshop)
                    <div class="panel__item">
                        <div>
                            <a href="{{ route('admin.workshops.edit', $workshop) }}" class="panel__item-title">
                                {{ \Illuminate\Support\Str::limit($workshop->title, 80) }}
                            </a>
                            <div class="panel__item-meta">
                                <i class="fas fa-clock"></i>
                                {{ optional($workshop->start_date)->translatedFormat('d F Y - h:i A') ?? '—' }}
                                <span class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full {{ $workshop->is_online ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-700' }}">
                                    <i class="fas {{ $workshop->is_online ? 'fa-video ml-1' : 'fa-map-marker-alt ml-1' }}"></i>
                                    {{ $workshop->is_online ? 'أونلاين' : 'حضوري' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="panel__empty">
                        لا توجد ورشات قادمة خلال الأيام القادمة. خطط لورشة جديدة الآن.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="space-y-6">
        <div class="admin-section-title">
            <i class="fas fa-layer-group"></i>
            إدارة أقسام المنصة
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($managementSections as $section)
                <div class="management-card">
                    <div class="management-card__icon">
                        <i class="fas {{ $section['icon'] }}"></i>
                    </div>
                    <div>
                        <h3 class="management-card__title">{{ $section['title'] }}</h3>
                        <p class="management-card__description">{{ $section['description'] }}</p>
                    </div>
                    <div class="management-card__links">
                        @foreach($section['items'] as $item)
                            @php
                                $itemUrl = $item['url'] ?? route($item['route'], $item['params'] ?? []);
                            @endphp
                            <a href="{{ $itemUrl }}" class="management-link">
                                <i class="fas fa-arrow-left"></i>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="quick-actions">
        <div class="admin-section-title justify-center">
            <i class="fas fa-bolt"></i>
            إجراءات سريعة
        </div>
        <div class="quick-actions__grid">
            @foreach($quickActions as $action)
                <a href="{{ route($action['route'], $action['params'] ?? []) }}" class="quick-action">
                    <i class="fas {{ $action['icon'] }}"></i>
                    <span>{{ $action['label'] }}</span>
                </a>
            @endforeach
        </div>
        <div class="mt-8 text-center">
            <a href="{{ route('admin.dashboard') }}" class="back-link">
                <i class="fas fa-arrow-right"></i>
                العودة إلى لوحة التحكم
            </a>
        </div>
    </section>
</div>
@endsection
