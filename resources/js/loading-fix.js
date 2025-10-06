/**
 * @file loading-fix.js
 * @description ملف لإصلاح مشاكل التحميل المستمر
 */

// التأكد من انتهاء تحميل جميع الملفات
document.addEventListener('DOMContentLoaded', function() {
    console.log('Loading fix: DOM is ready');
    
    // إزالة أي loading indicators متبقية
    setTimeout(() => {
        removeAnyRemainingLoadingIndicators();
    }, 1000);
    
    // التأكد من إنهاء تحميل الصفحة
    setTimeout(() => {
        markPageAsFullyLoaded();
    }, 2000);
});

/**
 * إزالة أي مؤشرات تحميل متبقية
 */
function removeAnyRemainingLoadingIndicators() {
    // البحث عن أي عناصر تحميل متبقية
    const loadingElements = document.querySelectorAll('[id*="loading"], [class*="loading"], [class*="spinner"]');
    
    loadingElements.forEach(element => {
        if (element.style.display !== 'none' && !element.classList.contains('hidden')) {
            console.log('Found persistent loading element:', element);
            element.style.display = 'none';
            element.classList.add('hidden');
        }
    });
    
    // إزالة أي CSS animations مستمرة
    const animatedElements = document.querySelectorAll('[style*="animation"]');
    animatedElements.forEach(element => {
        if (element.style.animation && element.style.animation.includes('infinite')) {
            console.log('Stopping infinite animation on:', element);
            element.style.animation = 'none';
        }
    });
}

/**
 * تأكيد انتهاء تحميل الصفحة
 */
function markPageAsFullyLoaded() {
    console.log('Page loading complete - all operations finished');
    
    // إضافة class للإشارة إلى انتهاء التحميل
    document.body.classList.add('page-fully-loaded');
    
    // إرسال event مخصص لتأكيد انتهاء التحميل
    const loadCompleteEvent = new CustomEvent('pageFullyLoaded', {
        detail: { timestamp: Date.now() }
    });
    document.dispatchEvent(loadCompleteEvent);
}

console.log('Loading fix script loaded');
