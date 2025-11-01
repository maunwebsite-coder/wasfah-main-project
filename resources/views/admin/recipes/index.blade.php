@extends('layouts.app')

@section('title', 'إدارة الوصفات - موقع وصفة')

@php
use Illuminate\Support\Facades\Storage;
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
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('admin.recipes.create') }}" class="btn-primary">
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

        <!-- Recipes Table -->
        <div class="admin-card overflow-hidden">
            @if($recipes->count())
                <div class="overflow-x-auto">
                    <table class="recipes-table">
                        <thead>
                            <tr>
                                <th class="text-right">الوصفة</th>
                                <th class="text-right">صاحب الوصفة</th>
                                <th class="text-right">المدة الإجمالية</th>
                                <th class="text-right">عدد الحصص</th>
                                <th class="text-right">التصنيف</th>
                                <th class="text-right w-48">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recipes as $recipe)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-4">
                                            <div class="relative flex-shrink-0">
                                                <img 
                                                    src="{{ $recipe->image ? Storage::disk('public')->url($recipe->image) : $recipe->image_url }}" 
                                                    alt="{{ $recipe->title }}" 
                                                    class="recipe-thumbnail"
                                                    onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                                                <span class="difficulty-badge">
                                                    {{ ucfirst($recipe->difficulty) }}
                                                </span>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="font-semibold text-gray-900">
                                                    {{ $recipe->title }}
                                                </div>
                                                <p class="text-sm text-gray-500 line-clamp-2 max-w-xs">
                                                    {{ $recipe->description }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-sm text-gray-600">
                                        {{ $recipe->author }}
                                    </td>
                                    <td class="text-sm text-gray-600">
                                        {{ (int)$recipe->prep_time + (int)$recipe->cook_time }} دقيقة
                                    </td>
                                    <td class="text-sm text-gray-600">
                                        {{ (int)$recipe->servings }} حصة
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
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الوصفة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger btn-sm inline-flex items-center gap-1">
                                                    <i class="fas fa-trash ml-1"></i>
                                                    حذف
                                                </button>
                                            </form>
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
