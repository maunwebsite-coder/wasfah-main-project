/**
 * @file search.js
 * @description نظام البحث في الموقع - يتعامل مع البحث في الوصفات والورشات
 */

document.addEventListener('DOMContentLoaded', function() {
    // عناصر البحث
    const searchInput = document.getElementById('search-input');
    const searchSubmit = document.getElementById('search-submit');
    const mobileSearchInput = document.getElementById('mobile-search-input');
    const mobileSearchSubmit = document.getElementById('mobile-search-submit');
    
    // متغيرات البحث
    let searchTimeout;
    let currentQuery = '';
    let currentRequest = null; // لإلغاء الطلبات السابقة
    let searchCache = new Map(); // cache للنتائج
    let retryCount = 0; // عداد إعادة المحاولة
    const maxRetries = 2; // الحد الأقصى لإعادة المحاولة
    
    // إزالة loading skeletons عند تحميل الصفحة
    removeLoadingSkeletons();
    
    // تهيئة البحث
    initializeSearch();
    
    function removeLoadingSkeletons() {
        // إزالة loading skeleton للبحث
        const searchLoadingSkeleton = document.getElementById('search-loading-skeleton');
        if (searchLoadingSkeleton) {
            searchLoadingSkeleton.remove();
        }
        
        // إظهار empty search إذا لم يكن هناك نتائج
        const emptySearch = document.getElementById('empty-search');
        if (emptySearch) {
            emptySearch.classList.remove('hidden');
        }
    }
    
    function initializeSearch() {
        // البحث في سطح المكتب
        if (searchInput && searchSubmit) {
            searchSubmit.addEventListener('click', handleSearch);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSearch();
                }
            });
            
            // البحث التلقائي أثناء الكتابة
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                // إزالة الاقتراحات إذا كان النص قصير جداً
                if (query.length < 2) {
                    removeExistingSuggestions();
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    performLiveSearch(query);
                }, 300); // تقليل من 500ms إلى 300ms
            });
        }
        
        // البحث في الموبايل
        if (mobileSearchInput && mobileSearchSubmit) {
            mobileSearchSubmit.addEventListener('click', handleMobileSearch);
            mobileSearchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleMobileSearch();
                }
            });
            
            // البحث التلقائي أثناء الكتابة
            mobileSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                // إزالة الاقتراحات إذا كان النص قصير جداً
                if (query.length < 2) {
                    removeExistingSuggestions();
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    performLiveSearch(query);
                }, 300); // تقليل من 500ms إلى 300ms
            });
        }
    }
    
    function handleSearch() {
        const query = searchInput.value.trim();
        if (query) {
            performSearch(query);
        }
    }
    
    function handleMobileSearch() {
        const query = mobileSearchInput.value.trim();
        if (query) {
            performSearch(query);
        }
    }
    
    function performSearch(query) {
        // إغلاق القائمة المنسدلة في الموبايل
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenu) {
            mobileMenu.classList.add('hidden');
        }
        
        // التوجه إلى صفحة نتائج البحث
        window.location.href = `/search?q=${encodeURIComponent(query)}`;
    }
    
    async function performLiveSearch(query) {
        if (query === currentQuery) return;
        
        // إلغاء الطلب السابق إذا كان موجوداً
        if (currentRequest) {
            currentRequest.abort();
        }
        
        currentQuery = query;
        
        // التحقق من cache أولاً
        if (searchCache.has(query)) {
            const cachedData = searchCache.get(query);
            showSearchSuggestions(cachedData.results, query);
            return;
        }
        
        // إظهار loading state فقط إذا لم تكن هناك نتائج محفوظة
        showLoadingState();
        
        try {
            // إنشاء AbortController لإلغاء الطلب
            const controller = new AbortController();
            currentRequest = controller;
            
            const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`, {
                signal: controller.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                // إضافة timeout للطلب
                timeout: 10000 // 10 ثواني
            });
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.success) {
                    // حفظ النتائج في cache مع timestamp
                    searchCache.set(query, {
                        ...data,
                        timestamp: Date.now()
                    });
                    
                    // تنظيف cache القديم (أكثر من 5 دقائق)
                    const now = Date.now();
                    for (const [key, value] of searchCache.entries()) {
                        if (now - value.timestamp > 300000) { // 5 دقائق
                            searchCache.delete(key);
                        }
                    }
                    
                    // تنظيف cache إذا كان كبيراً جداً
                    if (searchCache.size > 30) {
                        const firstKey = searchCache.keys().next().value;
                        searchCache.delete(firstKey);
                    }
                    
                    showSearchSuggestions(data.results, query);
                } else {
                    showErrorState('لم يتم العثور على نتائج');
                }
            } else {
                showErrorState('حدث خطأ في الخادم');
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Error performing live search:', error);
                
                // محاولة إعادة المحاولة للطلبات الفاشلة
                if (retryCount < maxRetries && (
                    error.message.includes('Failed to fetch') || 
                    error.message.includes('NetworkError') ||
                    error.message.includes('timeout')
                )) {
                    retryCount++;
                    console.log(`Retrying search request (${retryCount}/${maxRetries})`);
                    
                    // إعادة المحاولة بعد تأخير قصير
                    setTimeout(() => {
                        performLiveSearch(query);
                    }, 1000 * retryCount); // تأخير متزايد
                    
                    return;
                }
                
                // إعادة تعيين عداد المحاولات
                retryCount = 0;
                
                // تحديد نوع الخطأ وعرض رسالة مناسبة
                let errorMessage = 'حدث خطأ أثناء البحث';
                
                if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                    errorMessage = 'خطأ في الاتصال بالخادم. تحقق من اتصال الإنترنت';
                } else if (error.message.includes('500')) {
                    errorMessage = 'خطأ في الخادم. يرجى المحاولة لاحقاً';
                } else if (error.message.includes('404')) {
                    errorMessage = 'خدمة البحث غير متاحة حالياً';
                } else if (error.message.includes('timeout')) {
                    errorMessage = 'انتهت مهلة البحث. يرجى المحاولة مرة أخرى';
                }
                
                showErrorState(errorMessage);
            }
        } finally {
            currentRequest = null;
            hideLoadingState();
        }
    }
    
    function showSearchSuggestions(results, query) {
        // إزالة الاقتراحات السابقة
        removeExistingSuggestions();
        
        const totalResults = results.recipes.length + results.workshops.length;
        if (totalResults === 0) {
            showNoResultsState(query);
            return;
        }
        
        // إنشاء قائمة الاقتراحات
        const suggestionsContainer = createSuggestionsContainer();
        
        // إضافة الوصفات
        if (results.recipes.length > 0) {
            const recipesSection = createSuggestionsSection('الوصفات', results.recipes, 'recipe');
            suggestionsContainer.appendChild(recipesSection);
        }
        
        // إضافة الورشات
        if (results.workshops.length > 0) {
            const workshopsSection = createSuggestionsSection('ورشات العمل', results.workshops, 'workshop');
            suggestionsContainer.appendChild(workshopsSection);
        }
        
        // إضافة رابط "عرض جميع النتائج"
        const viewAllLink = createViewAllLink(query, totalResults);
        suggestionsContainer.appendChild(viewAllLink);
        
        // إضافة الاقتراحات تحت حقل البحث المناسب
        const searchContainer = document.getElementById('search-container');
        const mobileSearchContainer = document.getElementById('mobile-search-container');
        
        if (searchContainer) {
            searchContainer.style.position = 'relative';
            searchContainer.appendChild(suggestionsContainer);
        } else if (mobileSearchContainer) {
            mobileSearchContainer.style.position = 'relative';
            mobileSearchContainer.appendChild(suggestionsContainer);
        } else {
            // في حالة عدم وجود الحاوية، أضف إلى body كبديل
            document.body.appendChild(suggestionsContainer);
        }
        
        // إضافة مستمع للنقر خارج الاقتراحات مع تأخير
        setTimeout(() => {
            document.addEventListener('click', handleClickOutside);
        }, 100);
        
        // إضافة keyboard navigation
        addKeyboardNavigation(suggestionsContainer);
        
        // إضافة مستمع للتمرير لإغلاق القائمة
        window.addEventListener('scroll', handleScrollOutside, { passive: true });
    }
    
    function showNoResultsState(query) {
        const suggestionsContainer = createSuggestionsContainer();
        const noResultsDiv = document.createElement('div');
        noResultsDiv.className = 'p-4 text-center';
        noResultsDiv.innerHTML = `
            <div class="text-gray-500 text-sm">
                <i class="fas fa-search ml-2"></i>
                لا توجد نتائج لـ "${query}"
            </div>
        `;
        suggestionsContainer.appendChild(noResultsDiv);
        
        const searchContainer = document.getElementById('search-container');
        const mobileSearchContainer = document.getElementById('mobile-search-container');
        
        if (searchContainer) {
            searchContainer.appendChild(suggestionsContainer);
        } else if (mobileSearchContainer) {
            mobileSearchContainer.appendChild(suggestionsContainer);
        }
    }
    
    function createSuggestionsContainer() {
        const container = document.createElement('div');
        container.id = 'search-suggestions';
        container.className = 'absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto search-suggestions';
        container.style.position = 'absolute';
        container.style.zIndex = '9999';
        return container;
    }
    
    function createSuggestionsSection(title, items, type) {
        const section = document.createElement('div');
        section.className = 'p-4 border-b border-gray-100 last:border-b-0';
        
        const sectionTitle = document.createElement('h3');
        sectionTitle.className = 'text-sm font-semibold text-gray-500 mb-2';
        sectionTitle.textContent = title;
        section.appendChild(sectionTitle);
        
        const itemsList = document.createElement('div');
        itemsList.className = 'space-y-2';
        
        items.forEach(item => {
            const itemElement = createSuggestionItem(item, type);
            itemsList.appendChild(itemElement);
        });
        
        section.appendChild(itemsList);
        return section;
    }
    
    function createSuggestionItem(item, type) {
        const itemElement = document.createElement('div');
        itemElement.className = 'flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition-colors';
        itemElement.setAttribute('data-navigable', 'true');
        
        const icon = document.createElement('i');
        icon.className = type === 'recipe' ? 'fas fa-utensils text-orange-500 ml-3' : 'fas fa-graduation-cap text-orange-500 ml-3';
        
        const content = document.createElement('div');
        content.className = 'flex-1 min-w-0';
        
        const title = document.createElement('div');
        title.className = 'text-sm font-medium text-gray-900 truncate';
        title.textContent = item.title;
        
        const subtitle = document.createElement('div');
        subtitle.className = 'text-xs text-gray-500 truncate';
        if (type === 'recipe') {
            subtitle.textContent = item.author?.name || 'وصفة';
        } else {
            subtitle.textContent = item.instructor;
        }
        
        content.appendChild(title);
        content.appendChild(subtitle);
        
        itemElement.appendChild(icon);
        itemElement.appendChild(content);
        
        // إضافة مستمع للنقر مع منع إغلاق القائمة
        itemElement.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // إزالة القائمة المنسدلة قبل الانتقال
            removeExistingSuggestions();
            
            if (type === 'recipe') {
                window.location.href = `/recipe/${item.recipe_id}`;
            } else {
                window.location.href = `/workshops/${item.id}`;
            }
        });
        
        return itemElement;
    }
    
    function createViewAllLink(query, totalResults) {
        const link = document.createElement('div');
        link.className = 'p-4 text-center border-t border-gray-100';
        link.setAttribute('data-navigable', 'true');
        
        const linkElement = document.createElement('a');
        linkElement.href = `/search?q=${encodeURIComponent(query)}`;
        linkElement.className = 'text-orange-500 hover:text-orange-600 font-medium text-sm block p-2 rounded hover:bg-gray-50 transition-colors';
        linkElement.textContent = `عرض جميع النتائج (${totalResults})`;
        
        // إضافة معالج النقر
        linkElement.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // إزالة القائمة المنسدلة قبل الانتقال
            removeExistingSuggestions();
            
            // الانتقال إلى صفحة البحث
            window.location.href = `/search?q=${encodeURIComponent(query)}`;
        });
        
        link.appendChild(linkElement);
        return link;
    }
    
    function removeExistingSuggestions() {
        const existingSuggestions = document.getElementById('search-suggestions');
        if (existingSuggestions) {
            existingSuggestions.remove();
        }
    }
    
    function showLoadingState() {
        removeExistingSuggestions();
        
        const suggestionsContainer = createSuggestionsContainer();
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'p-4 text-center';
        loadingDiv.innerHTML = `
            <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-orange-500"></div>
                <span class="text-sm text-gray-500">جاري البحث...</span>
            </div>
        `;
        suggestionsContainer.appendChild(loadingDiv);
        
        const searchContainer = document.getElementById('search-container');
        const mobileSearchContainer = document.getElementById('mobile-search-container');
        
        if (searchContainer) {
            searchContainer.appendChild(suggestionsContainer);
        } else if (mobileSearchContainer) {
            mobileSearchContainer.appendChild(suggestionsContainer);
        }
    }
    
    function hideLoadingState() {
        const suggestions = document.getElementById('search-suggestions');
        if (suggestions) {
            const loadingDiv = suggestions.querySelector('.animate-spin');
            if (loadingDiv) {
                suggestions.remove();
            }
        }
    }
    
    function showErrorState(message) {
        removeExistingSuggestions();
        
        const suggestionsContainer = createSuggestionsContainer();
        const errorDiv = document.createElement('div');
        errorDiv.className = 'p-4 text-center';
        errorDiv.innerHTML = `
            <div class="text-red-500 text-sm">
                <i class="fas fa-exclamation-triangle ml-2"></i>
                ${message}
            </div>
        `;
        suggestionsContainer.appendChild(errorDiv);
        
        const searchContainer = document.getElementById('search-container');
        const mobileSearchContainer = document.getElementById('mobile-search-container');
        
        if (searchContainer) {
            searchContainer.appendChild(suggestionsContainer);
        } else if (mobileSearchContainer) {
            mobileSearchContainer.appendChild(suggestionsContainer);
        }
    }
    
    function handleClickOutside(event) {
        const suggestions = document.getElementById('search-suggestions');
        const searchInput = document.getElementById('search-input');
        const mobileSearchInput = document.getElementById('mobile-search-input');
        const searchContainer = document.getElementById('search-container');
        const mobileSearchContainer = document.getElementById('mobile-search-container');
        
        // إضافة تأخير صغير لتجنب الإغلاق السريع
        setTimeout(() => {
            if (suggestions && 
                !suggestions.contains(event.target) && 
                !searchInput?.contains(event.target) && 
                !mobileSearchInput?.contains(event.target) &&
                !searchContainer?.contains(event.target) &&
                !mobileSearchContainer?.contains(event.target)) {
                removeExistingSuggestions();
                document.removeEventListener('click', handleClickOutside);
            }
        }, 100);
    }
    
    // إضافة keyboard navigation
    function addKeyboardNavigation(container) {
        const items = container.querySelectorAll('[data-navigable]');
        let currentIndex = -1;
        
        const searchInput = document.getElementById('search-input');
        const mobileSearchInput = document.getElementById('mobile-search-input');
        const activeInput = searchInput || mobileSearchInput;
        
        if (!activeInput) return;
        
        activeInput.addEventListener('keydown', (e) => {
            if (!container || container.style.display === 'none') return;
            
            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    currentIndex = Math.min(currentIndex + 1, items.length - 1);
                    updateSelection();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    currentIndex = Math.max(currentIndex - 1, -1);
                    updateSelection();
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (currentIndex >= 0 && items[currentIndex]) {
                        items[currentIndex].click();
                    }
                    break;
                case 'Escape':
                    removeExistingSuggestions();
                    activeInput.blur();
                    break;
            }
        });
        
        function updateSelection() {
            items.forEach((item, index) => {
                item.classList.toggle('bg-orange-50', index === currentIndex);
                item.classList.toggle('bg-gray-50', index !== currentIndex);
            });
        }
    }
    
    // إخفاء الاقتراحات عند تغيير التركيز مع تأخير أطول
    if (searchInput) {
        searchInput.addEventListener('blur', () => {
            setTimeout(() => {
                // التحقق من أن المستخدم لم ينقر على عنصر في القائمة
                const suggestions = document.getElementById('search-suggestions');
                if (suggestions && !suggestions.matches(':hover')) {
                    removeExistingSuggestions();
                }
            }, 300);
        });
    }
    
    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('blur', () => {
            setTimeout(() => {
                // التحقق من أن المستخدم لم ينقر على عنصر في القائمة
                const suggestions = document.getElementById('search-suggestions');
                if (suggestions && !suggestions.matches(':hover')) {
                    removeExistingSuggestions();
                }
            }, 300);
        });
    }
});
