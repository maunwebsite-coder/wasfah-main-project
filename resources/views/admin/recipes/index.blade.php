@extends('layouts.app')

@section('title', 'إدارة الوصفات - موقع وصفة')

@php
    use Illuminate\Support\Facades\Storage;
    use App\Models\Recipe;

    $currentStatus = $status ?? 'all';
    $statusCounts = $statusCounts ?? [];

    $statusFilters = [
        'all' => [
            'label' => 'كل الوصفات',
            'hint' => 'عرض جميع الحالات',
            'value' => 'all',
            'badge' => 'bg-gray-200 text-gray-700',
        ],
        'pending' => [
            'label' => 'وصفات جديدة',
            'hint' => 'تنتظر المراجعة والموافقة',
            'value' => Recipe::STATUS_PENDING,
            'badge' => 'bg-orange-100 text-orange-700',
        ],
        'approved' => [
            'label' => 'وصفات معتمدة',
            'hint' => 'منشورة في الموقع',
            'value' => Recipe::STATUS_APPROVED,
            'badge' => 'bg-emerald-100 text-emerald-700',
        ],
        'draft' => [
            'label' => 'مسودات',
            'hint' => 'لم يتم إرسالها بعد',
            'value' => Recipe::STATUS_DRAFT,
            'badge' => 'bg-slate-100 text-slate-600',
        ],
        'rejected' => [
            'label' => 'مرفوضة',
            'hint' => 'تحتاج تعديلات قبل الاعتماد',
            'value' => Recipe::STATUS_REJECTED,
            'badge' => 'bg-red-100 text-red-600',
        ],
    ];

    $statusMeta = [
        Recipe::STATUS_DRAFT => ['label' => 'مسودة', 'classes' => 'bg-gray-100 text-gray-700'],
        Recipe::STATUS_PENDING => ['label' => 'قيد المراجعة', 'classes' => 'bg-orange-100 text-orange-700'],
        Recipe::STATUS_APPROVED => ['label' => 'معتمدة', 'classes' => 'bg-emerald-100 text-emerald-700'],
        Recipe::STATUS_REJECTED => ['label' => 'مرفوضة', 'classes' => 'bg-red-100 text-red-700'],
    ];

    $difficultyLabels = [
        'easy' => 'سهل',
        'medium' => 'متوسط',
        'hard' => 'صعب',
    ];
@endphp

@push('styles')
<style>
    .admin-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #f3f4f6;
    }
    .recipes-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .recipes-table thead th {
        background: #f9fafb;
        color: #4b5563;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .recipes-table tbody td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        vertical-align: middle;
    }
    .recipes-table tbody tr:hover {
        background: #f9fafb;
    }
    .recipe-thumbnail {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 0.75rem;
        object-fit: cover;
        box-shadow: 0 4px 10px rgba(15, 118, 110, 0.08);
    }
    .difficulty-badge {
        position: absolute;
        top: -0.35rem;
        right: -0.35rem;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        font-size: 0.6875rem;
        font-weight: 600;
        padding: 0.3rem 0.65rem;
        border-radius: 9999px;
        box-shadow: 0 2px 6px rgba(249, 115, 22, 0.35);
    }
    .btn-sm {
        padding: 0.45rem 0.9rem;
        font-size: 0.875rem;
    }
    .btn-sm i {
        font-size: 0.8125rem;
    }
    .btn-primary {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
    }
    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }
    .btn-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
    }
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-success:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 9999px;
        padding: 0.35rem 0.875rem;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .status-badge .dot {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 9999px;
        background-color: currentColor;
    }
    .pending-row {
        background: #fff7ed;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="admin-card p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">إدارة الوصفات</h1>
                    <p class="text-gray-600">إدارة وإضافة وتعديل الوصفات</p>
                </div>
                <div class="mt-4 md:mt-0 flex flex-col gap-3 sm:flex-row sm:items-center">
                    @php $pendingRecipesCount = $approvalPendingCount ?? 0; @endphp
                    <form method="POST" action="{{ route('admin.recipes.approve-all') }}" class="w-full sm:w-auto"
                          onsubmit="return confirm('هل أنت متأكد من اعتماد جميع الوصفات قيد المراجعة دفعة واحدة؟');">
                        @csrf
                        <button type="submit" class="btn-success w-full inline-flex items-center justify-center gap-2 font-semibold {{ $pendingRecipesCount === 0 ? 'opacity-70' : '' }}">
                            <i class="fas fa-check-double ml-2"></i>
                            اعتماد جميع الوصفات مرة واحدة
                            <span class="text-xs bg-white/20 text-white rounded-full px-2 py-0.5">({{ number_format($pendingRecipesCount) }})</span>
                        </button>
                        @if($pendingRecipesCount === 0)
                            <p class="text-xs text-gray-500 text-center mt-1">لا توجد وصفات قيد المراجعة حالياً</p>
                        @endif
                    </form>
                    <a href="{{ route('admin.recipes.create') }}" class="btn-primary w-full sm:w-auto inline-flex items-center justify-center gap-2 text-center">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة وصفة جديدة
                    </a>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle ml-2"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-triangle ml-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Recipes Table -->
        <div class="admin-card overflow-hidden">
            <div class="px-6 pt-6 pb-4 border-b border-gray-100 bg-gray-50">
                <div class="flex flex-wrap gap-3 items-center">
                    @foreach($statusFilters as $key => $filter)
                        @php
                            $isActive = $currentStatus === $filter['value'];
                            $count = $statusCounts[$key] ?? 0;
                            $routeParams = $filter['value'] === 'all' ? [] : ['status' => $filter['value']];
                            $url = empty($routeParams)
                                ? route('admin.recipes.index')
                                : route('admin.recipes.index', $routeParams);
                        @endphp
                        <a href="{{ $url }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ $isActive ? 'border-orange-300 bg-orange-50 shadow-sm' : 'border-gray-200 hover:border-orange-200 hover:bg-orange-50/50' }}">
                            <div class="flex flex-col">
                                <span class="font-semibold text-sm {{ $isActive ? 'text-orange-700' : 'text-gray-700' }}">
                                    {{ $filter['label'] }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $filter['hint'] }}
                                </span>
                            </div>
                            <span class="ml-2 inline-flex items-center justify-center min-w-[2.5rem] px-2 py-1 text-xs font-semibold rounded-full {{ $filter['badge'] }}">
                                {{ $count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
            @if($recipes->count())
                <div class="overflow-x-auto">
                    <table class="recipes-table">
                        <thead>
                            <tr>
                                <th class="text-right">الوصفة</th>
                                <th class="text-right">صاحب الوصفة</th>
                                <th class="text-right">الحالة</th>
                                <th class="text-right">المدة الإجمالية</th>
                                <th class="text-right">عدد الحصص</th>
                                <th class="text-right">التصنيف</th>
                                <th class="text-right w-64">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recipes as $recipe)
                                @php
                                    $statusInfo = $statusMeta[$recipe->status] ?? $statusMeta[Recipe::STATUS_DRAFT];
                                    $difficultyLabel = $recipe->difficulty ? ($difficultyLabels[$recipe->difficulty] ?? ucfirst($recipe->difficulty)) : null;
                                    $totalDuration = (int) ($recipe->prep_time ?? 0) + (int) ($recipe->cook_time ?? 0);
                                    $servings = (int) ($recipe->servings ?? 0);
                                    $imageSrc = $recipe->image
                                        ? Storage::disk('public')->url($recipe->image)
                                        : ($recipe->image_url ?: asset('image/logo.webp'));
                                    $ownerName = $recipe->chef?->name ?? ($recipe->author ?: 'فريق وصفة');
                                    $ownerSubtitle = $recipe->chef ? 'شيف مسجل' : 'فريق وصفة';
                                @endphp
                                <tr class="{{ $recipe->status === Recipe::STATUS_PENDING ? 'pending-row' : '' }}">
                                    <td>
                                        <div class="flex items-center gap-4">
                                            <div class="relative flex-shrink-0">
                                                <img 
                                                    src="{{ $imageSrc }}" 
                                                    alt="{{ $recipe->title }}" 
                                                    class="recipe-thumbnail"
                                                    onerror="this.src='{{ asset('image/logo.webp') }}'; this.alt='صورة افتراضية';" loading="lazy">
                                                @if($difficultyLabel)
                                                    <span class="difficulty-badge">
                                                        {{ $difficultyLabel }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="space-y-1">
                                                <div class="font-semibold text-gray-900">
                                                    {{ $recipe->title }}
                                                </div>
                                                <p class="text-sm text-gray-500 line-clamp-2 max-w-xs">
                                                    {{ \Illuminate\Support\Str::limit($recipe->description, 110) }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-sm text-gray-600">
                                        <div class="font-semibold text-gray-900">{{ $ownerName }}</div>
                                        <p class="text-xs text-gray-500">{{ $ownerSubtitle }}</p>
                                        @if($recipe->chef)
                                            <p class="text-xs text-gray-400">{{ $recipe->chef->email }}</p>
                                        @endif
                                    </td>
                                    <td class="text-sm text-gray-600">
                                        <span class="status-badge {{ $statusInfo['classes'] }}">
                                            <span class="dot"></span>
                                            {{ $statusInfo['label'] }}
                                        </span>
                                        @if($recipe->status === Recipe::STATUS_PENDING)
                                            <p class="mt-1 text-xs text-orange-600">بانتظار مراجعة الإدارة</p>
                                        @elseif($recipe->status === Recipe::STATUS_REJECTED)
                                            <p class="mt-1 text-xs text-red-600">تمت إعادتها للشيف للتعديل</p>
                                        @elseif($recipe->status === Recipe::STATUS_APPROVED && $recipe->approved_at)
                                            <p class="mt-1 text-xs text-emerald-600">اعتمدت بتاريخ {{ $recipe->approved_at->locale('ar')->translatedFormat('d F Y') }}</p>
                                        @endif
                                    </td>
                                    <td class="text-sm text-gray-600">
                                        {{ $totalDuration > 0 ? $totalDuration . ' دقيقة' : 'غير محدد' }}
                                    </td>
                                    <td class="text-sm text-gray-600">
                                        {{ $servings > 0 ? $servings . ' حصة' : 'غير محدد' }}
                                    </td>
                                    <td class="text-sm text-gray-600">
                                        {{ $recipe->category->name ?? 'غير محدد' }}
                                    </td>
                                    <td class="text-sm">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <a href="{{ route('admin.recipes.show', $recipe) }}" 
                                               class="btn-secondary btn-sm inline-flex items-center gap-1">
                                                <i class="fas fa-eye ml-1"></i>
                                                عرض
                                            </a>
                                            <a href="{{ route('admin.recipes.edit', $recipe) }}" 
                                               class="btn-primary btn-sm inline-flex items-center gap-1">
                                                <i class="fas fa-edit ml-1"></i>
                                                تعديل
                                            </a>
                                            <form action="{{ route('admin.recipes.destroy', $recipe) }}" 
                                                  method="POST" 
                                                  class="inline-flex"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الوصفة؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger btn-sm inline-flex items-center gap-1">
                                                    <i class="fas fa-trash ml-1"></i>
                                                    حذف
                                                </button>
                                            </form>
                                            @if($recipe->status !== Recipe::STATUS_APPROVED)
                                                <form method="POST" 
                                                      action="{{ route('admin.recipes.approve', $recipe) }}" 
                                                      class="inline-flex"
                                                      onsubmit="return confirm('هل تريد اعتماد هذه الوصفة ونشرها الآن؟');">
                                                    @csrf
                                                    <button type="submit" class="btn-success btn-sm inline-flex items-center gap-1">
                                                        <i class="fas fa-check ml-1"></i>
                                                        اعتماد
                                                    </button>
                                                </form>
                                            @endif
                                            @if($recipe->status === Recipe::STATUS_PENDING)
                                                <form method="POST" 
                                                      action="{{ route('admin.recipes.reject', $recipe) }}" 
                                                      class="inline-flex"
                                                      onsubmit="return confirm('هل تريد رفض هذه الوصفة وإعادتها للشيف؟');">
                                                    @csrf
                                                    <button type="submit" class="btn-danger btn-sm inline-flex items-center gap-1">
                                                        <i class="fas fa-times ml-1"></i>
                                                        رفض
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-16 text-center">
                    <i class="fas fa-utensils text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">لا توجد وصفات</h3>
                    <p class="text-gray-500 mb-6">ابدأ بإضافة وصفة جديدة</p>
                    <a href="{{ route('admin.recipes.create') }}" class="btn-primary btn-sm inline-flex items-center gap-1">
                        <i class="fas fa-plus ml-1"></i>
                        إضافة وصفة جديدة
                    </a>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($recipes->hasPages())
            <div class="mt-8">
                {{ $recipes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection


