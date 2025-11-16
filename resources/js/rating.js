/**
 * @file rating.js
 * @description نظام التقييم للوصفات - يتضمن إرسال التقييمات وعرضها
 */

class RecipeRating {
    constructor() {
        this.ratingForm = null;
        this.submitBtn = null;
        this.ratingText = null;
        this.starInputs = null;
        this.recipeId = null;
        this.userId = null;
        this.currentRating = null;
        
        this.init();
    }

    /**
     * تهيئة نظام التقييم
     */
    init() {
        // التحقق من وجود عناصر التقييم
        this.ratingForm = document.querySelector('.star-rating');
        this.submitBtn = document.getElementById('submit-rating-btn');
        this.ratingText = document.getElementById('user-rating-text');
        this.starInputs = document.querySelectorAll('input[name="rating"]');
        
        if (!this.ratingForm || !this.submitBtn || !this.ratingText) {
            console.warn('عناصر التقييم غير موجودة في الصفحة');
            return;
        }

        // استخراج معرفات الوصفة والمستخدم
        this.recipeId = this.getRecipeIdFromUrl();
        this.userId = document.body.dataset.userId;
        
        if (!this.recipeId) {
            console.error('لا يمكن العثور على معرف الوصفة');
            return;
        }

        if (!this.userId) {
            console.warn('المستخدم غير مسجل دخول - سيتم إخفاء نظام التقييم');
            this.hideRatingSystem();
            return;
        }

        // تهيئة الحالة الأولية
        this.updateSubmitButton();
        
        this.setupEventListeners();
        this.loadCurrentRating();
    }

    /**
     * استخراج معرف الوصفة من الرابط
     */
    getRecipeIdFromUrl() {
        const pathParts = window.location.pathname.split('/');
        const recipeId = pathParts[pathParts.length - 1];
        return recipeId && !isNaN(recipeId) ? recipeId : null;
    }

    /**
     * إخفاء نظام التقييم للمستخدمين غير المسجلين
     */
    hideRatingSystem() {
        const ratingSection = document.querySelector('section.py-8');
        if (ratingSection) {
            ratingSection.style.display = 'none';
        }
    }

    /**
     * إعداد مستمعي الأحداث
     */
    setupEventListeners() {
        // مستمع تغيير التقييم
        this.starInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleRatingChange(e.target.value);
            });
        });

        // مستمع إرسال التقييم
        this.submitBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.submitRating();
        });

        // مستمعات hover للنجوم
        this.starInputs.forEach(input => {
            const label = input.nextElementSibling;
            if (label) {
                label.addEventListener('mouseenter', () => {
                    this.highlightStars(parseInt(input.value));
                });
            }
        });

        this.ratingForm.addEventListener('mouseleave', () => {
            this.resetStarHighlight();
        });
    }

    /**
     * معالجة تغيير التقييم
     */
    handleRatingChange(rating) {
        this.currentRating = parseInt(rating);
        this.updateRatingText(this.currentRating);
        this.updateSubmitButton();
    }

    /**
     * تحديث نص التقييم
     */
    updateRatingText(rating) {
        const ratingTexts = {
            1: 'مقبول',
            2: 'جيد',
            3: 'جيد جداً',
            4: 'ممتاز',
            5: 'رائع جداً'
        };
        
        this.ratingText.textContent = ratingTexts[rating] || 'الرجاء تقييم الوصفة';
    }

    /**
     * تحديث حالة زر الإرسال
     */
    updateSubmitButton() {
        if (this.currentRating) {
            this.submitBtn.disabled = false;
            this.submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            this.submitBtn.classList.add('hover:bg-orange-600');
            
            // تغيير نص الزر إذا كان هناك تقييم موجود
            const originalText = this.submitBtn.innerHTML;
            if (!originalText.includes('تحديث')) {
                this.submitBtn.innerHTML = '<i class="fas fa-edit ml-2"></i>تحديث التقييم';
            }
        } else {
            this.submitBtn.disabled = true;
            this.submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            this.submitBtn.classList.remove('hover:bg-orange-600');
            this.submitBtn.innerHTML = '<i class="fas fa-paper-plane ml-2"></i>أرسل التقييم';
        }
    }

    /**
     * إرسال التقييم إلى الخادم
     */
    async submitRating() {
        if (!this.currentRating) {
            this.showMessage('الرجاء اختيار تقييم قبل الإرسال', 'error');
            return;
        }

        // تعطيل الزر أثناء الإرسال
        this.setSubmitButtonLoading(true);

        try {
            const response = await fetch('/api/interactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({
                    recipe_id: this.recipeId,
                    rating: this.currentRating
                })
            });

            const result = await response.json();

            if (response.ok) {
                const message = this.currentRating ? 'تم تحديث التقييم بنجاح!' : 'تم إرسال التقييم بنجاح!';
                this.showMessage(message, 'success');
                this.loadCurrentRating(); // إعادة تحميل التقييم الحالي
                this.updateRecipeRatingSummary(); // تحديث ملخص التقييم
            } else {
                const errorMessage = result.message || 'حدث خطأ أثناء إرسال التقييم';
                throw new Error(errorMessage);
            }

        } catch (error) {
            console.error('خطأ في إرسال التقييم:', error);
            
            // رسائل خطأ مخصصة حسب نوع الخطأ
            let errorMessage = 'حدث خطأ أثناء إرسال التقييم. يرجى المحاولة مرة أخرى.';
            
            if (error.message.includes('Unauthenticated') || error.message.includes('401')) {
                errorMessage = 'يجب تسجيل الدخول لتقييم الوصفة';
            } else if (error.message.includes('422') || error.message.includes('validation')) {
                errorMessage = 'تقييم غير صالح. يرجى اختيار تقييم من 1 إلى 5 نجوم';
            } else if (error.message.includes('404')) {
                errorMessage = 'الوصفة غير موجودة';
            } else if (error.message.includes('500')) {
                errorMessage = 'خطأ في الخادم. يرجى المحاولة لاحقاً';
            }
            
            this.showMessage(errorMessage, 'error');
        } finally {
            this.setSubmitButtonLoading(false);
        }
    }

    /**
     * تحميل التقييم الحالي للمستخدم
     */
    async loadCurrentRating() {
        try {
            const response = await fetch(`/api/recipes/${this.recipeId}`, {
                credentials: 'include'
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data && result.data.user_rating) {
                    this.currentRating = result.data.user_rating;
                    this.selectRating(this.currentRating);
                    this.updateRatingText(this.currentRating);
                    this.updateSubmitButton();
                }
            }
        } catch (error) {
            console.error('خطأ في تحميل التقييم الحالي:', error);
        }
    }

    /**
     * تحديد التقييم المحدد
     */
    selectRating(rating) {
        this.starInputs.forEach(input => {
            input.checked = parseInt(input.value) === rating;
        });
    }

    /**
     * تحديث ملخص التقييم في الصفحة
     */
    async updateRecipeRatingSummary() {
        try {
            const response = await fetch(`/api/recipes/${this.recipeId}`, {
                credentials: 'include'
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data) {
                    const recipe = result.data;
                    const avgRating = parseFloat(recipe.interactions_avg_rating || 0).toFixed(1);
                    const ratingSummaryEl = document.getElementById('recipe-rating-summary');
                    
                    if (ratingSummaryEl) {
                        ratingSummaryEl.innerHTML = `<i class="fas fa-star text-yellow-400 ml-1"></i><span class="ml-2">${avgRating} تقييم</span>`;
                    }
                }
            }
        } catch (error) {
            console.error('خطأ في تحديث ملخص التقييم:', error);
        }
    }

    /**
     * إظهار رسالة للمستخدم
     */
    showMessage(message, type = 'info') {
        // إزالة أي رسائل سابقة
        const existingMessages = document.querySelectorAll('.rating-message');
        existingMessages.forEach(msg => msg.remove());

        // إنشاء عنصر الرسالة
        const messageEl = document.createElement('div');
        messageEl.className = `rating-message fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        
        // إضافة أيقونة حسب نوع الرسالة
        const icon = type === 'success' ? 'fas fa-check-circle' :
                    type === 'error' ? 'fas fa-exclamation-triangle' :
                    'fas fa-info-circle';
        
        messageEl.innerHTML = `
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <i class="${icon}"></i>
                <span>${message}</span>
            </div>
        `;

        // إضافة الرسالة للصفحة
        document.body.appendChild(messageEl);

        // إظهار الرسالة مع تأثير
        setTimeout(() => {
            messageEl.classList.remove('translate-x-full');
        }, 100);

        // إزالة الرسالة بعد 4 ثوانٍ
        setTimeout(() => {
            messageEl.classList.add('translate-x-full');
            setTimeout(() => {
                if (messageEl.parentNode) {
                    messageEl.parentNode.removeChild(messageEl);
                }
            }, 300);
        }, 4000);
    }

    /**
     * تعيين حالة تحميل لزر الإرسال
     */
    setSubmitButtonLoading(loading) {
        if (loading) {
            this.submitBtn.disabled = true;
            this.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري الإرسال...';
            this.submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            this.submitBtn.disabled = false;
            // استعادة النص الصحيح حسب وجود تقييم
            if (this.currentRating) {
                this.submitBtn.innerHTML = '<i class="fas fa-edit ml-2"></i>تحديث التقييم';
            } else {
                this.submitBtn.innerHTML = '<i class="fas fa-paper-plane ml-2"></i>أرسل التقييم';
            }
            this.submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    /**
     * تمييز النجوم عند hover
     */
    highlightStars(rating) {
        this.starInputs.forEach(input => {
            const label = input.nextElementSibling;
            const star = label?.querySelector('.star');
            if (star) {
                if (parseInt(input.value) <= rating) {
                    star.style.color = '#eab308';
                    star.style.transform = 'scale(1.1)';
                } else {
                    star.style.color = '#e5e7eb';
                    star.style.transform = 'scale(1)';
                }
            }
        });
    }

    /**
     * إعادة تعيين تمييز النجوم
     */
    resetStarHighlight() {
        this.starInputs.forEach(input => {
            const label = input.nextElementSibling;
            const star = label?.querySelector('.star');
            if (star) {
                if (input.checked) {
                    star.style.color = '#eab308';
                    star.style.transform = 'scale(1.1)';
                } else {
                    star.style.color = '#e5e7eb';
                    star.style.transform = 'scale(1)';
                }
            }
        });
    }

    /**
     * الحصول على CSRF token
     */
    getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
}

function bootstrapRecipeRating() {
    const ratingForm = document.querySelector('.star-rating');
    if (!ratingForm) {
        return;
    }

    if (window.__recipeRatingInitialized) {
        return;
    }

    window.recipeRatingInstance = new RecipeRating();
    window.__recipeRatingInitialized = true;
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootstrapRecipeRating, { once: true });
} else {
    bootstrapRecipeRating();
}
