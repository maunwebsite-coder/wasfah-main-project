@extends('layouts.app')

@section('title', 'إدارة الورشات - لوحة الإدارة')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">إدارة الورشات</h1>
                <p class="text-gray-600">إدارة وإضافة وتعديل ورشات العمل</p>
                @php
                    $featuredWorkshop = \App\Models\Workshop::where('is_featured', true)->first();
                @endphp
                @if($featuredWorkshop)
                    <div class="mt-3 bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 text-white px-6 py-3 rounded-xl shadow-2xl inline-block transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div class="bg-white bg-opacity-20 p-2 rounded-full">
                                <i class="fas fa-crown text-2xl animate-pulse"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-lg">الورشة القادمة</span>
                                    <div class="flex space-x-1">
                                        <div class="w-2 h-2 bg-white rounded-full animate-bounce"></div>
                                        <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                        <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                    </div>
                                </div>
                                <span class="font-bold text-xl">{{ $featuredWorkshop->title }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-3 bg-gradient-to-r from-gray-400 to-gray-600 text-white px-6 py-3 rounded-xl shadow-lg inline-block">
                        <div class="flex items-center gap-3">
                            <div class="bg-white bg-opacity-20 p-2 rounded-full">
                                <i class="fas fa-exclamation-triangle text-xl"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-lg">لا توجد ورشة قادمة محددة</span>
                                <p class="text-sm opacity-90">اختر ورشة لتكون مميزة</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <a href="{{ route('admin.workshops.create') }}" 
               class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center gap-2 mt-4 md:mt-0">
                <i class="fas fa-plus"></i>
                إضافة ورشة جديدة
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-chalkboard-teacher text-orange-500 text-xl"></i>
                    </div>
                    <div class="mr-4">
                        <p class="text-2xl font-bold text-gray-800">{{ $workshops->total() }}</p>
                        <p class="text-gray-600">إجمالي الورشات</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="mr-4">
                        <p class="text-2xl font-bold text-gray-800">{{ $workshops->where('is_active', true)->count() }}</p>
                        <p class="text-gray-600">ورشات نشطة</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-500 text-xl"></i>
                    </div>
                    <div class="mr-4">
                        <p class="text-2xl font-bold text-gray-800">{{ $workshops->sum('bookings_count') }}</p>
                        <p class="text-gray-600">إجمالي الحجوزات</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-laptop text-purple-500 text-xl"></i>
                    </div>
                    <div class="mr-4">
                        <p class="text-2xl font-bold text-gray-800">{{ $workshops->where('is_online', true)->count() }}</p>
                        <p class="text-gray-600">ورشات أونلاين</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workshops Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-800">الورشة</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-800">المدرب</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-800">التاريخ</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-800">السعر</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-800">الحجوزات</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-800">الحالة</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-800">الورشة القادمة</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-800">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($workshops as $workshop)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="{{ $workshop->image ? asset('storage/' . $workshop->image) : 'https://placehold.co/60x60/f87171/FFFFFF?text=ورشة' }}" 
                                         alt="{{ $workshop->title }}" 
                                         class="w-12 h-12 rounded-lg object-cover ml-4">
                                    <div>
                                        <h3 class="font-semibold text-gray-800">{{ $workshop->title }}</h3>
                                        <p class="text-sm text-gray-500">{{ $workshop->is_online ? 'أونلاين' : 'حضوري' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-800">{{ $workshop->instructor }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="text-gray-800">{{ $workshop->start_date->format('d M, Y') }}</p>
                                    <p class="text-gray-500">{{ $workshop->start_date->format('H:i') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-800">{{ $workshop->price }} {{ $workshop->currency }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="text-gray-800">{{ $workshop->bookings_count }}</span>
                                    <span class="text-gray-500 mr-1">/ {{ $workshop->max_participants }}</span>
                                    @if($workshop->total_bookings > $workshop->bookings_count)
                                        <span class="text-xs text-orange-500 mr-1">({{ $workshop->total_bookings - $workshop->bookings_count }} معلق)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $workshop->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $workshop->is_active ? 'نشطة' : 'غير نشطة' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($workshop->is_featured)
                                    <div class="flex items-center gap-3">
                                        <div class="relative">
                                            <span class="px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 text-white shadow-xl transform hover:scale-105 transition-all duration-300">
                                                <i class="fas fa-crown ml-2 animate-pulse"></i>
                                                الورشة القادمة
                                            </span>
                                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-ping"></div>
                                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></div>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs text-amber-600 font-bold flex items-center gap-1">
                                                <i class="fas fa-star text-yellow-500"></i>
                                                مميزة
                                            </span>
                                            <span class="text-xs text-green-600 font-semibold">✨ نشطة</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 text-sm flex items-center gap-1">
                                            <i class="fas fa-circle text-xs"></i>
                                            عادية
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.workshops.show', $workshop->id) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors" 
                                       title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.workshops.edit', $workshop->id) }}" 
                                       class="text-orange-600 hover:text-orange-800 transition-colors" 
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$workshop->is_featured)
                                    <form action="{{ route('admin.workshops.toggle-featured', $workshop->id) }}" 
                                          method="POST" class="inline-block"
                                          onsubmit="return confirmFeaturedWorkshop('{{ $workshop->title }}')">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-3 py-2 rounded-lg shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center gap-2" 
                                                title="جعلها الورشة القادمة">
                                            <i class="fas fa-crown animate-pulse"></i>
                                            <span class="text-sm font-semibold">تمييز</span>
                                        </button>
                                    </form>
                                    @else
                                    <div class="flex items-center gap-2">
                                        <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-3 py-2 rounded-lg shadow-lg flex items-center gap-2">
                                            <i class="fas fa-crown animate-pulse"></i>
                                            <span class="text-sm font-bold">مميزة</span>
                                        </div>
                                        <form action="{{ route('admin.workshops.toggle-featured', $workshop->id) }}" 
                                              method="POST" class="inline-block"
                                              onsubmit="return confirmRemoveFeatured('{{ $workshop->title }}')">
                                            @csrf
                                            <button type="submit" 
                                                    class="bg-red-500 hover:bg-red-600 text-white px-2 py-2 rounded-lg shadow-lg transform hover:scale-105 transition-all duration-300" 
                                                    title="إلغاء التمييز">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                    <form action="{{ route('admin.workshops.toggle-status', $workshop->id) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="text-purple-600 hover:text-purple-800 transition-colors" 
                                                title="{{ $workshop->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                            <i class="fas {{ $workshop->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.workshops.destroy', $workshop->id) }}" 
                                          method="POST" class="inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الورشة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 transition-colors" 
                                                title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-chalkboard-teacher text-4xl mb-4"></i>
                                    <p class="text-lg">لا توجد ورشات حالياً</p>
                                    <p class="text-sm">ابدأ بإضافة ورشة جديدة</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($workshops->hasPages())
        <div class="mt-8">
            {{ $workshops->links() }}
        </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
// تحسين نوافذ التأكيد للورشة المميزة
function confirmFeaturedWorkshop(workshopTitle) {
    return Swal.fire({
        title: 'تأكيد التمييز',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-crown text-6xl text-amber-500 mb-4"></i>
                </div>
                <p class="text-lg mb-2">هل تريد جعل هذه الورشة هي الورشة المميزة؟</p>
                <p class="text-xl font-bold text-amber-600">"${workshopTitle}"</p>
                <div class="mt-4 p-3 bg-amber-50 rounded-lg">
                    <p class="text-sm text-amber-700">
                        <i class="fas fa-info-circle ml-1"></i>
                        سيتم إلغاء تمييز الورشة المميزة الحالية (إن وجدت)
                    </p>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-crown ml-2"></i>نعم، اجعلها مميزة',
        cancelButtonText: '<i class="fas fa-times ml-2"></i>إلغاء',
        customClass: {
            popup: 'swal2-popup-arabic',
            title: 'swal2-title-arabic',
            content: 'swal2-content-arabic'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // إظهار رسالة نجاح
            Swal.fire({
                title: 'تم التمييز بنجاح!',
                text: `تم جعل "${workshopTitle}" هي الورشة المميزة`,
                icon: 'success',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'ممتاز!'
            });
        }
        return result.isConfirmed;
    });
}

function confirmRemoveFeatured(workshopTitle) {
    return Swal.fire({
        title: 'إلغاء التمييز',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-crown text-6xl text-red-500 mb-4"></i>
                </div>
                <p class="text-lg mb-2">هل تريد إلغاء تمييز هذه الورشة؟</p>
                <p class="text-xl font-bold text-red-600">"${workshopTitle}"</p>
                <div class="mt-4 p-3 bg-red-50 rounded-lg">
                    <p class="text-sm text-red-700">
                        <i class="fas fa-exclamation-triangle ml-1"></i>
                        لن تكون هناك ورشة مميزة بعد إلغاء التمييز
                    </p>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-times ml-2"></i>نعم، ألغِ التمييز',
        cancelButtonText: '<i class="fas fa-arrow-right ml-2"></i>إلغاء',
        customClass: {
            popup: 'swal2-popup-arabic',
            title: 'swal2-title-arabic',
            content: 'swal2-content-arabic'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // إظهار رسالة نجاح
            Swal.fire({
                title: 'تم إلغاء التمييز!',
                text: `تم إلغاء تمييز "${workshopTitle}"`,
                icon: 'info',
                confirmButtonColor: '#6b7280',
                confirmButtonText: 'حسناً'
            });
        }
        return result.isConfirmed;
    });
}

// إضافة تأثيرات بصرية إضافية
document.addEventListener('DOMContentLoaded', function() {
    // تأثير hover للورشات المميزة
    const featuredRows = document.querySelectorAll('tr:has(.bg-gradient-to-r.from-amber-500)');
    featuredRows.forEach(row => {
        row.classList.add('bg-gradient-to-r', 'from-amber-50', 'to-orange-50', 'border-l-4', 'border-amber-500');
    });
    
    // تأثير النبض للشارات المميزة
    const featuredBadges = document.querySelectorAll('.animate-pulse');
    featuredBadges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.classList.add('animate-bounce');
        });
        badge.addEventListener('mouseleave', function() {
            this.classList.remove('animate-bounce');
        });
    });
});
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* تخصيص SweetAlert2 للعربية */
.swal2-popup-arabic {
    font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    direction: rtl;
}

.swal2-title-arabic {
    font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight: 700;
}

.swal2-content-arabic {
    font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* تأثيرات إضافية للورشات المميزة */
@keyframes shimmer {
    0% { background-position: -200px 0; }
    100% { background-position: calc(200px + 100%) 0; }
}

.featured-shimmer {
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    background-size: 200px 100%;
    animation: shimmer 2s infinite;
}

/* تحسين مظهر الأزرار */
.btn-featured {
    background: linear-gradient(45deg, #f59e0b, #f97316);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
    transition: all 0.3s ease;
}

.btn-featured:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.6);
}
</style>
@endsection
