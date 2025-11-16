/**
 * نظام مشاركة الوصفات
 * يتعامل مع مشاركة الوصفات عبر وسائل التواصل الاجتماعي ونسخ الرابط
 */

// متغيرات عامة
let shareModal = null;
let closeShareModalBtn = null;
let copyLinkBtn = null;
let copySuccessEl = null;
let currentRecipeUrl = '';
let currentRecipeTitle = '';
let currentRecipeDescription = '';

// تهيئة النظام عند تحميل الصفحة أو عند استيراد الوحدة بعد أن تصبح DOM جاهزة
let shareSystemInitialized = false;

function bootstrapShareSystem() {
    if (shareSystemInitialized) {
        return;
    }

    const hasShareElements =
        document.getElementById('share-modal') ||
        document.querySelector('[id^="share-recipe-btn"]');

    if (!hasShareElements) {
        return;
    }

    shareSystemInitialized = true;
    initializeShareSystem();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootstrapShareSystem, { once: true });
} else {
    bootstrapShareSystem();
}

/**
 * تهيئة نظام المشاركة
 */
function initializeShareSystem() {
    // الحصول على عناصر DOM
    shareModal = document.getElementById('share-modal');
    closeShareModalBtn = document.getElementById('close-share-modal');
    copyLinkBtn = document.getElementById('copy-link-btn');
    copySuccessEl = document.getElementById('copy-success');
    
    // الحصول على معلومات الوصفة
    currentRecipeUrl = window.location.href;
    currentRecipeTitle = document.querySelector('h1')?.textContent || 'وصفة لذيذة';
    currentRecipeDescription = document.querySelector('meta[name="description"]')?.content || 'وصفة شهية من موقع وصفة';
    
    // إضافة مستمعي الأحداث
    addEventListeners();
    
    // إعداد روابط وسائل التواصل الاجتماعي
    setupSocialLinks();
    
    console.log('Share system initialized');
}

/**
 * إضافة مستمعي الأحداث
 */
function addEventListeners() {
    // أزرار فتح modal المشاركة
    const shareButtons = document.querySelectorAll('[id^="share-recipe-btn"]');
    shareButtons.forEach(button => {
        button.addEventListener('click', openShareModal);
    });
    
    // زر إغلاق modal
    if (closeShareModalBtn) {
        closeShareModalBtn.addEventListener('click', closeShareModal);
    }
    
    // زر نسخ الرابط
    if (copyLinkBtn) {
        copyLinkBtn.addEventListener('click', copyRecipeLink);
    }
    
    // زر الطباعة
    const printBtn = document.getElementById('print-recipe-btn');
    if (printBtn) {
        printBtn.addEventListener('click', printRecipe);
    }
    
    // إغلاق modal عند النقر خارجه
    if (shareModal) {
        shareModal.addEventListener('click', function(e) {
            if (e.target === shareModal) {
                closeShareModal();
            }
        });
    }
    
    // إغلاق modal بمفتاح Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && shareModal && !shareModal.classList.contains('hidden')) {
            closeShareModal();
        }
    });
}

/**
 * فتح modal المشاركة
 */
function openShareModal() {
    if (!shareModal) return;
    
    shareModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // إخفاء رسالة النجاح إذا كانت ظاهرة
    if (copySuccessEl) {
        copySuccessEl.classList.add('hidden');
    }
    
    // إضافة تأثير الظهور
    setTimeout(() => {
        shareModal.classList.add('opacity-100');
    }, 10);
}

/**
 * إغلاق modal المشاركة
 */
function closeShareModal() {
    if (!shareModal) return;
    
    shareModal.classList.add('opacity-0');
    document.body.style.overflow = '';
    
    setTimeout(() => {
        shareModal.classList.add('hidden');
        shareModal.classList.remove('opacity-100', 'opacity-0');
    }, 300);
}

/**
 * إعداد روابط وسائل التواصل الاجتماعي
 */
function setupSocialLinks() {
    const encodedUrl = encodeURIComponent(currentRecipeUrl);
    const encodedTitle = encodeURIComponent(currentRecipeTitle);
    const encodedDescription = encodeURIComponent(currentRecipeDescription);
    
    // واتساب
    const whatsappLink = document.getElementById('whatsapp-share');
    if (whatsappLink) {
        whatsappLink.href = `https://wa.me/?text=${encodedTitle}%20-%20${encodedUrl}`;
    }
    
    // تليجرام
    const telegramLink = document.getElementById('telegram-share');
    if (telegramLink) {
        telegramLink.href = `https://t.me/share/url?url=${encodedUrl}&text=${encodedTitle}`;
    }
    
    // فيسبوك
    const facebookLink = document.getElementById('facebook-share');
    if (facebookLink) {
        facebookLink.href = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
    }
    
    // تويتر
    const twitterLink = document.getElementById('twitter-share');
    if (twitterLink) {
        twitterLink.href = `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`;
    }
}

/**
 * نسخ رابط الوصفة
 */
async function copyRecipeLink() {
    if (!copyLinkBtn || !copySuccessEl) return;
    
    try {
        // تعطيل الزر أثناء النسخ
        copyLinkBtn.disabled = true;
        copyLinkBtn.classList.add('opacity-70');
        
        // نسخ الرابط
        await navigator.clipboard.writeText(currentRecipeUrl);
        
        // إظهار رسالة النجاح
        copySuccessEl.classList.remove('hidden');
        
        // تحديث نص الزر مؤقتاً
        const originalText = copyLinkBtn.innerHTML;
        copyLinkBtn.innerHTML = '<i class="fas fa-check ml-2"></i>تم النسخ!';
        copyLinkBtn.classList.add('bg-green-500', 'hover:bg-green-600');
        copyLinkBtn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
        
        // إعادة الزر لحالته الأصلية بعد ثانيتين
        setTimeout(() => {
            copyLinkBtn.innerHTML = originalText;
            copyLinkBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
            copyLinkBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
            copyLinkBtn.disabled = false;
            copyLinkBtn.classList.remove('opacity-70');
        }, 2000);
        
        // إخفاء رسالة النجاح بعد 3 ثواني
        setTimeout(() => {
            copySuccessEl.classList.add('hidden');
        }, 3000);
        
    } catch (error) {
        console.error('Error copying link:', error);
        
        // في حالة فشل Clipboard API، استخدم الطريقة القديمة
        fallbackCopyTextToClipboard(currentRecipeUrl);
    }
}

/**
 * طريقة بديلة لنسخ النص (للتوافق مع المتصفحات القديمة)
 */
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showToast('تم نسخ الرابط بنجاح!', 'success');
        } else {
            showToast('فشل في نسخ الرابط', 'error');
        }
    } catch (err) {
        console.error('Fallback copy failed:', err);
        showToast('فشل في نسخ الرابط', 'error');
    }
    
    document.body.removeChild(textArea);
}

/**
 * إظهار رسالة toast
 * @param {string} message - الرسالة
 * @param {string} type - نوع الرسالة (success, error, info)
 */
function showToast(message, type = 'success') {
    // إنشاء عنصر toast
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium shadow-lg transform translate-x-full transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    toast.textContent = message;
    
    // إضافة للصفحة
    document.body.appendChild(toast);
    
    // إظهار الرسالة
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // إخفاء الرسالة بعد 3 ثواني
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

/**
 * طباعة الوصفة
 */
function printRecipe() {
    // إنشاء نافذة طباعة جديدة
    const printWindow = window.open('', '_blank');
    
    // الحصول على محتوى الوصفة
    const recipeContent = document.querySelector('main');
    if (!recipeContent) {
        showToast('خطأ في العثور على محتوى الوصفة', 'error');
        return;
    }
    
    // إنشاء HTML للطباعة
    const printHTML = `
        <!DOCTYPE html>
        <html dir="rtl" lang="ar">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>${currentRecipeTitle} - وصفة</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .recipe-header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #f97316;
                    padding-bottom: 20px;
                }
                .recipe-title {
                    font-size: 2.5em;
                    color: #f97316;
                    margin-bottom: 10px;
                }
                .recipe-meta {
                    display: flex;
                    justify-content: center;
                    gap: 30px;
                    margin: 20px 0;
                    font-size: 1.1em;
                }
                .recipe-meta span {
                    background: #f8f9fa;
                    padding: 8px 16px;
                    border-radius: 20px;
                    border: 1px solid #e9ecef;
                }
                .recipe-description {
                    font-size: 1.2em;
                    color: #666;
                    text-align: center;
                    margin: 20px 0;
                }
                .ingredients-section, .instructions-section {
                    margin: 30px 0;
                }
                .section-title {
                    font-size: 1.8em;
                    color: #f97316;
                    margin-bottom: 20px;
                    border-bottom: 1px solid #e9ecef;
                    padding-bottom: 10px;
                }
                .ingredients-list {
                    list-style: none;
                    padding: 0;
                }
                .ingredients-list li {
                    background: #f8f9fa;
                    margin: 8px 0;
                    padding: 12px 20px;
                    border-radius: 8px;
                    border-right: 4px solid #f97316;
                }
                .instructions-list {
                    counter-reset: step-counter;
                    list-style: none;
                    padding: 0;
                }
                .instructions-list li {
                    counter-increment: step-counter;
                    margin: 20px 0;
                    padding: 20px;
                    background: #f8f9fa;
                    border-radius: 8px;
                    position: relative;
                }
                .instructions-list li::before {
                    content: counter(step-counter);
                    position: absolute;
                    right: -15px;
                    top: -15px;
                    background: #f97316;
                    color: white;
                    width: 30px;
                    height: 30px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                }
                .print-footer {
                    margin-top: 40px;
                    text-align: center;
                    font-size: 0.9em;
                    color: #666;
                    border-top: 1px solid #e9ecef;
                    padding-top: 20px;
                }
                @media print {
                    body { margin: 0; padding: 15px; }
                    .recipe-meta { flex-direction: column; gap: 10px; }
                }
            </style>
        </head>
        <body>
            <div class="recipe-header">
                <h1 class="recipe-title">${currentRecipeTitle}</h1>
                <div class="recipe-meta">
                    <span>⏱️ ${document.querySelector('[data-prep-time]')?.textContent || 'غير محدد'} دقيقة</span>
                    <span>👥 ${document.querySelector('[data-servings]')?.textContent || 'غير محدد'} أشخاص</span>
                    <span>🔥 ${document.querySelector('[data-difficulty]')?.textContent || 'متوسط'} صعوبة</span>
                </div>
                <p class="recipe-description">${document.querySelector('meta[name="description"]')?.content || 'وصفة شهية من موقع وصفة'}</p>
            </div>
            
            <div class="ingredients-section">
                <h2 class="section-title">المكونات</h2>
                <ul class="ingredients-list">
                    ${Array.from(document.querySelectorAll('.ingredient-item')).map(item => 
                        `<li>${item.textContent.trim()}</li>`
                    ).join('')}
                </ul>
            </div>
            
            <div class="instructions-section">
                <h2 class="section-title">طريقة التحضير</h2>
                <ul class="instructions-list">
                    ${Array.from(document.querySelectorAll('.step-item')).map(item => 
                        `<li>${item.textContent.trim()}</li>`
                    ).join('')}
                </ul>
            </div>
            
            <div class="print-footer">
                <p>طبعت من موقع وصفة - ${new Date().toLocaleDateString('ar-SA')}</p>
                <p>${currentRecipeUrl}</p>
            </div>
        </body>
        </html>
    `;
    
    // كتابة المحتوى في النافذة الجديدة
    printWindow.document.write(printHTML);
    printWindow.document.close();
    
    // انتظار تحميل الصفحة ثم طباعتها
    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    };
    
    showToast('تم فتح نافذة الطباعة', 'success');
}

// تصدير الدوال للاستخدام العام
window.ShareRecipe = {
    initializeShareSystem,
    openShareModal,
    closeShareModal,
    copyRecipeLink,
    printRecipe,
    shareViaWebAPI
};
