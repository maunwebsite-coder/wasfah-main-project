/**
 * تحسينات الأداء للجافا سكريبت
 * تقليل الطلبات وتحسين الاستجابة
 */

// إعدادات الأداء
const PERFORMANCE_CONFIG = {
    // تقليل فترة تحديث الإشعارات من 5 ثوان إلى 30 ثانية
    NOTIFICATION_POLLING_INTERVAL: 30000, // 30 ثانية
    
    // تفعيل التخزين المؤقت المحلي
    ENABLE_LOCAL_CACHE: true,
    
    // مدة التخزين المؤقت المحلي (بالثواني)
    CACHE_TTL: 300, // 5 دقائق
    
    // عدد الإشعارات المحفوظة محلياً
    MAX_CACHED_NOTIFICATIONS: 50,
    
    // تفعيل الضغط
    ENABLE_COMPRESSION: true,
    
    // تفعيل التحميل البطيء
    ENABLE_LAZY_LOADING: true
};

// تخزين مؤقت محلي
class LocalCache {
    constructor() {
        this.cache = new Map();
        this.ttl = PERFORMANCE_CONFIG.CACHE_TTL * 1000; // تحويل إلى ملي ثانية
    }

    set(key, value) {
        this.cache.set(key, {
            value: value,
            timestamp: Date.now()
        });
    }

    get(key) {
        const item = this.cache.get(key);
        if (!item) return null;

        // فحص انتهاء الصلاحية
        if (Date.now() - item.timestamp > this.ttl) {
            this.cache.delete(key);
            return null;
        }

        return item.value;
    }

    clear() {
        this.cache.clear();
    }

    // تنظيف التخزين المؤقت التلقائي
    cleanup() {
        const now = Date.now();
        for (const [key, item] of this.cache.entries()) {
            if (now - item.timestamp > this.ttl) {
                this.cache.delete(key);
            }
        }
    }
}

// إنشاء مثيل التخزين المؤقت
const localCache = new LocalCache();

// تنظيف التخزين المؤقت كل 5 دقائق
setInterval(() => {
    localCache.cleanup();
}, 5 * 60 * 1000);

// تحسين دالة تحديث الإشعارات - استخدام NotificationManager المركزي
function optimizedLoadNotifications() {
    if (window.NotificationManager) {
        window.NotificationManager.getNotifications((data, error) => {
            if (error) {
                console.error('Error loading notifications:', error);
                showNotificationError();
                return;
            }
            
            updateNotificationsUI(data?.notifications || [], data?.unreadCount || 0);
        });
    } else {
        console.warn('NotificationManager not available, falling back to direct fetch');
        // Fallback to direct fetch if NotificationManager is not available
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch('/notifications/api', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            updateNotificationsUI(data.notifications, data.unreadCount);
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            showNotificationError();
        });
    }
}

// تحسين دالة تحديث العداد - استخدام NotificationManager المركزي
function optimizedLoadNotificationsCount() {
    if (window.NotificationManager) {
        window.NotificationManager.getNotifications((data, error) => {
            if (error) {
                console.error('Error loading notifications count:', error);
                return;
            }
            
            updateNotificationsCountUI(data?.unreadCount || 0);
        });
    } else {
        console.warn('NotificationManager not available, falling back to direct fetch');
        // Fallback to direct fetch if NotificationManager is not available
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
                const count = data.unreadCount || 0;
                updateNotificationsCountUI(count);
            })
            .catch(error => {
                console.error('Error loading notifications count:', error);
            });
    }
}

// دالة تحديث واجهة العداد
function updateNotificationsCountUI(count) {
    const countElement = document.getElementById('notifications-count-nav');
    const mobileCountElement = document.getElementById('mobile-notifications-count');
    const mobileMenuCountElement = document.getElementById('notifications-count-mobile');
    
    [countElement, mobileCountElement, mobileMenuCountElement].forEach(element => {
        if (element) {
            const oldCount = parseInt(element.textContent) || 0;
            element.textContent = count;
            
            if (count > 0) {
                element.classList.remove('hidden');
                
                // إضافة تأثير بصري عند ظهور إشعارات جديدة
                if (count > oldCount) {
                    element.classList.add('animate-bounce');
                    setTimeout(() => {
                        element.classList.remove('animate-bounce');
                    }, 2000);
                }
            } else {
                element.classList.add('hidden');
            }
        }
    });
}

// دالة إظهار خطأ الإشعارات
function showNotificationError() {
    const errorElement = document.getElementById('notifications-error');
    if (errorElement) {
        errorElement.style.display = 'block';
        setTimeout(() => {
            errorElement.style.display = 'none';
        }, 5000);
    }
}

// تحسين التحديث الدوري
let notificationInterval;

function startOptimizedNotificationPolling() {
    // إيقاف التحديث السابق إذا كان موجوداً
    if (notificationInterval) {
        clearInterval(notificationInterval);
    }
    
    // تحديث فوري
    optimizedLoadNotifications();
    
    // تحديث دوري محسن
    notificationInterval = setInterval(() => {
        optimizedLoadNotifications();
    }, PERFORMANCE_CONFIG.NOTIFICATION_POLLING_INTERVAL);
}

// تحسين التحديث عند التركيز على النافذة
function optimizedWindowFocusHandler() {
    // تنظيف التخزين المؤقت عند التركيز
    if (PERFORMANCE_CONFIG.ENABLE_LOCAL_CACHE) {
        localCache.clear();
    }
    
    // تحديث فوري
    optimizedLoadNotifications();
}

// تحسين التحديث عند تغيير الصفحة
function optimizedPageChangeHandler() {
    // تنظيف التخزين المؤقت
    if (PERFORMANCE_CONFIG.ENABLE_LOCAL_CACHE) {
        localCache.clear();
    }
}

// تطبيق التحسينات
document.addEventListener('DOMContentLoaded', function() {
    // بدء التحديث المحسن
    startOptimizedNotificationPolling();
    
    // إضافة مستمعي الأحداث
    window.addEventListener('focus', optimizedWindowFocusHandler);
    window.addEventListener('beforeunload', optimizedPageChangeHandler);
    
    // تحديث عند تغيير الصفحة (للمواقع أحادية الصفحة)
    window.addEventListener('popstate', optimizedPageChangeHandler);
});

// تصدير الدوال للاستخدام العام
window.PerformanceOptimization = {
    loadNotifications: optimizedLoadNotifications,
    loadNotificationsCount: optimizedLoadNotificationsCount,
    clearCache: () => localCache.clear(),
    startPolling: startOptimizedNotificationPolling,
    stopPolling: () => {
        if (notificationInterval) {
            clearInterval(notificationInterval);
            notificationInterval = null;
        }
    }
};

