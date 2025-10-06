@extends('layouts.app')

@section('title', 'الإشعارات')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <section class="bg-white py-8 shadow-sm">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                            الإشعارات
                            <span id="unread-count" class="unread-count bg-red-500 text-white text-sm font-bold rounded-full px-2 py-1 ml-2 {{ $unreadCount > 0 ? '' : 'hidden' }}">{{ $unreadCount }}</span>
                        </h1>
                        <p class="text-gray-600">
                            جميع إشعاراتك في مكان واحد
                        </p>
                    </div>
                    
                    <div class="flex space-x-2 rtl:space-x-reverse">
                        <button id="mark-all-read" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-check-double ml-2"></i>
                            تحديد الكل كمقروء
                        </button>
                        <button id="clear-read" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-trash ml-2"></i>
                            حذف المقروء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                @if($notifications->count() > 0)
                    <!-- Notifications List -->
                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow notification-item {{ $notification->is_read ? 'opacity-75' : 'bg-orange-50 border-orange-200' }}" 
                                 data-id="{{ $notification->id }}">
                                <div class="p-6">
                                    <div class="flex items-start space-x-4 rtl:space-x-reverse">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 {{ $notification->type === 'workshop_booking' ? 'bg-blue-100' : ($notification->type === 'workshop_confirmed' ? 'bg-green-100' : ($notification->type === 'workshop_cancelled' ? 'bg-red-100' : 'bg-orange-100')) }} rounded-full flex items-center justify-center">
                                                <i class="fas fa-{{ $notification->type === 'workshop_booking' ? 'calendar-plus' : ($notification->type === 'workshop_confirmed' ? 'check-circle' : ($notification->type === 'workshop_cancelled' ? 'times-circle' : 'info-circle')) }} {{ $notification->type === 'workshop_booking' ? 'text-blue-600' : ($notification->type === 'workshop_confirmed' ? 'text-green-600' : ($notification->type === 'workshop_cancelled' ? 'text-red-600' : 'text-orange-600')) }}"></i>
                                            </div>
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h3 class="text-lg font-semibold text-gray-900 {{ $notification->is_read ? '' : 'font-bold' }}">
                                                        {{ $notification->title }}
                                                    </h3>
                                                    <p class="text-gray-600 mt-1">{{ $notification->message }}</p>
                                                    <p class="text-sm text-gray-400 mt-2">
                                                        <i class="fas fa-clock ml-1"></i>
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                                
                                                <!-- Actions -->
                                                <div class="flex items-center space-x-2 rtl:space-x-reverse ml-4">
                                                    @if(!$notification->is_read)
                                                        <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                                                    @endif
                                                    
                                                    <div class="flex space-x-1 rtl:space-x-reverse">
                                                        @if(!$notification->is_read)
                                                            <button class="mark-read-btn p-2 text-orange-600 hover:bg-orange-100 rounded-lg transition-colors" 
                                                                    data-id="{{ $notification->id }}"
                                                                    title="تحديد كمقروء">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @endif
                                                        
                                                        <button class="delete-notification-btn p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" 
                                                                data-id="{{ $notification->id }}"
                                                                title="حذف الإشعار">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-bell-slash text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">لا توجد إشعارات</h3>
                        <p class="text-gray-600">ستظهر إشعاراتك هنا عند توفرها</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .notification-item {
        transition: all 0.3s ease;
    }
    
    .notification-item:hover {
        transform: translateY(-1px);
    }
    
    .notification-item.unread {
        border-left: 4px solid #f97316;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديث دوري للإشعارات كل 5 ثوان
    setInterval(function() {
        // تحديث الإشعارات فقط بدون إعادة تحميل الصفحة - استخدام NotificationManager المركزي
        if (window.NotificationManager) {
            window.NotificationManager.getNotifications((data, error) => {
                if (error) {
                    console.error('Error updating notifications:', error);
                    return;
                }
                
                const unreadCount = data?.unreadCount || 0;
                const countElement = document.getElementById('unread-count');
                if (countElement) {
                    const oldCount = parseInt(countElement.textContent) || 0;
                    countElement.textContent = unreadCount;
                    
                    if (unreadCount > 0) {
                        countElement.classList.remove('hidden');
                        
                        // إضافة تأثير بصري عند ظهور إشعارات جديدة
                        if (unreadCount > oldCount) {
                            countElement.classList.add('animate-bounce');
                            setTimeout(() => {
                                countElement.classList.remove('animate-bounce');
                            }, 2000);
                        }
                    } else {
                        countElement.classList.add('hidden');
                    }
                }
            });
        } else {
            console.warn('NotificationManager not available, falling back to direct fetch');
            // Fallback to direct fetch
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch('/notifications/api', {
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    const unreadCount = data.unreadCount || 0;
                    const countElement = document.getElementById('unread-count');
                    if (countElement) {
                        const oldCount = parseInt(countElement.textContent) || 0;
                        countElement.textContent = unreadCount;
                        
                        if (unreadCount > 0) {
                            countElement.classList.remove('hidden');
                            
                            if (unreadCount > oldCount) {
                                countElement.classList.add('animate-bounce');
                                setTimeout(() => {
                                    countElement.classList.remove('animate-bounce');
                                }, 2000);
                            }
                        } else {
                            countElement.classList.add('hidden');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating notifications:', error);
                });
        }
    }, 5000);
    
    // تحديث فوري عند التركيز على النافذة - استخدام NotificationManager المركزي
    window.addEventListener('focus', function() {
        if (window.NotificationManager) {
            window.NotificationManager.getNotifications((data, error) => {
                if (error) {
                    console.error('Error updating notifications on focus:', error);
                    return;
                }
                
                const unreadCount = data?.unreadCount || 0;
                const countElement = document.getElementById('unread-count');
                if (countElement) {
                    const oldCount = parseInt(countElement.textContent) || 0;
                    countElement.textContent = unreadCount;
                    
                    if (unreadCount > 0) {
                        countElement.classList.remove('hidden');
                        
                        // إضافة تأثير بصري عند ظهور إشعارات جديدة
                        if (unreadCount > oldCount) {
                            countElement.classList.add('animate-bounce');
                            setTimeout(() => {
                                countElement.classList.remove('animate-bounce');
                            }, 2000);
                        }
                    } else {
                        countElement.classList.add('hidden');
                    }
                }
            });
        } else {
            console.warn('NotificationManager not available, falling back to direct fetch');
            // Fallback to direct fetch
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch('/notifications/api', {
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    const unreadCount = data.unreadCount || 0;
                    const countElement = document.getElementById('unread-count');
                    if (countElement) {
                        const oldCount = parseInt(countElement.textContent) || 0;
                        countElement.textContent = unreadCount;
                        
                        if (unreadCount > 0) {
                            countElement.classList.remove('hidden');
                            
                            if (unreadCount > oldCount) {
                                countElement.classList.add('animate-bounce');
                                setTimeout(() => {
                                    countElement.classList.remove('animate-bounce');
                                }, 2000);
                            }
                        } else {
                            countElement.classList.add('hidden');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating notifications on focus:', error);
                });
        }
    });
    
    // تحديث فوري عند العودة للصفحة
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // تحديث الإشعارات فقط
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch('/notifications/api', {
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    const unreadCount = data.unreadCount || 0;
                    const countElement = document.getElementById('unread-count');
                    if (countElement) {
                        const oldCount = parseInt(countElement.textContent) || 0;
                        countElement.textContent = unreadCount;
                        
                        if (unreadCount > 0) {
                            countElement.classList.remove('hidden');
                            
                            // إضافة تأثير بصري عند ظهور إشعارات جديدة
                            if (unreadCount > oldCount) {
                                countElement.classList.add('animate-bounce');
                                setTimeout(() => {
                                    countElement.classList.remove('animate-bounce');
                                }, 2000);
                            }
                        } else {
                            countElement.classList.add('hidden');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating notifications:', error);
                });
        }
    });
    
    // تحديد إشعار كمقروء
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            markAsRead(notificationId);
        });
    });
    
    // حذف إشعار
    document.querySelectorAll('.delete-notification-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            deleteNotification(notificationId);
        });
    });
    
    // تحديد الكل كمقروء
    document.getElementById('mark-all-read').addEventListener('click', function() {
        markAllAsRead();
    });
    
    // حذف المقروء
    document.getElementById('clear-read').addEventListener('click', function() {
        clearReadNotifications();
    });
    
    // النقر على إشعار لتحديده كمقروء
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // تجنب التنفيذ عند النقر على الأزرار
            if (e.target.closest('button')) return;
            
            const notificationId = this.dataset.id;
            const isRead = this.classList.contains('opacity-75');
            
            if (!isRead) {
                markAsRead(notificationId);
            }
        });
    });
});

function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.add('opacity-75');
                notificationItem.classList.remove('bg-orange-50', 'border-orange-200');
                notificationItem.classList.add('bg-white', 'border-gray-200');
                
                // إزالة النقطة البرتقالية
                const dot = notificationItem.querySelector('.w-3.h-3.bg-orange-500');
                if (dot) dot.remove();
                
                // إزالة زر "تحديد كمقروء"
                const markReadBtn = notificationItem.querySelector('.mark-read-btn');
                if (markReadBtn) markReadBtn.remove();
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function deleteNotification(notificationId) {
    // إنشاء modal جميل للتأكيد
    const modalHTML = `
        <div id="delete-notification-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-100">
                <div class="text-center">
                    <!-- الأيقونة -->
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-trash-alt text-red-600 text-3xl"></i>
                    </div>
                    
                    <!-- العنوان -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">حذف الإشعار</h3>
                    
                    <!-- الرسالة -->
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        هل أنت متأكد من حذف هذا الإشعار؟<br>
                        <span class="text-sm text-gray-500 mt-2 block">
                            لا يمكن التراجع عن هذا الإجراء.
                        </span>
                    </p>
                    
                    <!-- الأزرار -->
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <button id="cancel-delete-notification" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-6 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </button>
                        <button id="confirm-delete-notification" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                            <i class="fas fa-trash ml-2"></i>
                            حذف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // إضافة الـ modal للصفحة
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // إضافة event listeners
    document.getElementById('cancel-delete-notification').addEventListener('click', function() {
        document.getElementById('delete-notification-modal').remove();
    });
    
    document.getElementById('confirm-delete-notification').addEventListener('click', function() {
        // إظهار حالة التحميل
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري الحذف...';
        
        fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // إظهار رسالة نجاح
                showSuccessMessage('تم حذف الإشعار بنجاح!');
                
                // حذف العنصر من الصفحة
                const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.style.transition = 'all 0.3s ease';
                    notificationItem.style.transform = 'translateX(100%)';
                    notificationItem.style.opacity = '0';
                    setTimeout(() => {
                        notificationItem.remove();
                    }, 300);
                }
                
                // إغلاق الـ modal
                document.getElementById('delete-notification-modal').remove();
            } else {
                showErrorMessage('حدث خطأ أثناء حذف الإشعار');
                document.getElementById('delete-notification-modal').remove();
            }
        })
        .catch(error => {
            console.error('Error deleting notification:', error);
            showErrorMessage('حدث خطأ أثناء حذف الإشعار');
            document.getElementById('delete-notification-modal').remove();
        });
    });
    
    // إغلاق الـ modal عند النقر خارجها
    document.getElementById('delete-notification-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.remove();
        }
    });
}

function markAllAsRead() {
    // إنشاء modal جميل للتأكيد
    const modalHTML = `
        <div id="mark-all-read-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-100">
                <div class="text-center">
                    <!-- الأيقونة -->
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-check-double text-orange-600 text-3xl"></i>
                    </div>
                    
                    <!-- العنوان -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">تحديد الكل كمقروء</h3>
                    
                    <!-- الرسالة -->
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        هل تريد تحديد جميع الإشعارات كمقروءة؟<br>
                        <span class="text-sm text-gray-500 mt-2 block">
                            سيتم تحديد جميع الإشعارات غير المقروءة كمقروءة.
                        </span>
                    </p>
                    
                    <!-- الأزرار -->
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <button id="cancel-mark-all-read" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-6 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </button>
                        <button id="confirm-mark-all-read" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                            <i class="fas fa-check-double ml-2"></i>
                            تحديد الكل
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // إضافة الـ modal للصفحة
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // إضافة event listeners
    document.getElementById('cancel-mark-all-read').addEventListener('click', function() {
        document.getElementById('mark-all-read-modal').remove();
    });
    
    document.getElementById('confirm-mark-all-read').addEventListener('click', function() {
        // إظهار حالة التحميل
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري التحديث...';
        
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // إظهار رسالة نجاح
                showSuccessMessage('تم تحديد جميع الإشعارات كمقروءة!');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showErrorMessage('حدث خطأ أثناء تحديث الإشعارات');
                document.getElementById('mark-all-read-modal').remove();
            }
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
            showErrorMessage('حدث خطأ أثناء تحديث الإشعارات');
            document.getElementById('mark-all-read-modal').remove();
        });
    });
    
    // إغلاق الـ modal عند النقر خارجها
    document.getElementById('mark-all-read-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.remove();
        }
    });
}

function clearReadNotifications() {
    // إنشاء modal جميل للتأكيد
    const modalHTML = `
        <div id="clear-read-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-100">
                <div class="text-center">
                    <!-- الأيقونة -->
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-trash-alt text-red-600 text-3xl"></i>
                    </div>
                    
                    <!-- العنوان -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">حذف الإشعارات المقروءة</h3>
                    
                    <!-- الرسالة -->
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        هل أنت متأكد من حذف جميع الإشعارات المقروءة؟<br>
                        <span class="text-sm text-gray-500 mt-2 block">
                            سيتم حذف جميع الإشعارات التي تم قراءتها مسبقاً ولا يمكن التراجع عن هذا الإجراء.
                        </span>
                    </p>
                    
                    <!-- الأزرار -->
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <button id="cancel-clear-read" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-6 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </button>
                        <button id="confirm-clear-read" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-3 px-6 rounded-lg font-medium transition-colors">
                            <i class="fas fa-trash ml-2"></i>
                            حذف المقروء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // إضافة الـ modal للصفحة
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // إضافة event listeners
    document.getElementById('cancel-clear-read').addEventListener('click', function() {
        document.getElementById('clear-read-modal').remove();
    });
    
    document.getElementById('confirm-clear-read').addEventListener('click', function() {
        // إظهار حالة التحميل
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري الحذف...';
        
        fetch('/notifications/clear-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // إظهار رسالة نجاح
                showSuccessMessage('تم حذف الإشعارات المقروءة بنجاح!');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showErrorMessage('حدث خطأ أثناء حذف الإشعارات');
                document.getElementById('clear-read-modal').remove();
            }
        })
        .catch(error => {
            console.error('Error clearing read notifications:', error);
            showErrorMessage('حدث خطأ أثناء حذف الإشعارات');
            document.getElementById('clear-read-modal').remove();
        });
    });
    
    // إغلاق الـ modal عند النقر خارجها
    document.getElementById('clear-read-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.remove();
        }
    });
}

// دالة إظهار رسالة النجاح
function showSuccessMessage(message) {
    const toastHTML = `
        <div id="success-toast" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-xl ml-3"></i>
                <span class="font-medium">${message}</span>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', toastHTML);
    
    // إظهار الرسالة
    setTimeout(() => {
        const toast = document.getElementById('success-toast');
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // إخفاء الرسالة بعد 3 ثوان
    setTimeout(() => {
        const toast = document.getElementById('success-toast');
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// دالة إظهار رسالة الخطأ
function showErrorMessage(message) {
    const toastHTML = `
        <div id="error-toast" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-xl ml-3"></i>
                <span class="font-medium">${message}</span>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', toastHTML);
    
    // إظهار الرسالة
    setTimeout(() => {
        const toast = document.getElementById('error-toast');
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // إخفاء الرسالة بعد 4 ثوان
    setTimeout(() => {
        const toast = document.getElementById('error-toast');
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 4000);
}
</script>
@endpush
