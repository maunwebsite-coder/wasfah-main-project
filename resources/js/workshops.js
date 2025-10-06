/**
 * تحويل رابط Google Drive إلى رابط مباشر للصورة
 * @param {string} url - رابط الصورة
 * @returns {string} - رابط محول أو الرابط الأصلي
 */
function convertGoogleDriveUrl(url) {
    if (!url || !url.includes('drive.google.com')) {
        return url;
    }
    
    try {
        // تنسيق 1: https://drive.google.com/file/d/FILE_ID/view
        let match = url.match(/\/file\/d\/([a-zA-Z0-9-_]+)/);
        if (match && match[1]) {
            return `https://lh3.googleusercontent.com/d/${match[1]}`;
        }
        
        // تنسيق 2: https://drive.google.com/open?id=FILE_ID
        if (url.includes('id=')) {
            const urlParams = new URLSearchParams(new URL(url).search);
            const fileId = urlParams.get('id');
            if (fileId) {
                return `https://lh3.googleusercontent.com/d/${fileId}`;
            }
        }
        
        // تنسيق 3: https://drive.google.com/uc?id=FILE_ID
        if (url.includes('uc?id=')) {
            const urlParams = new URLSearchParams(new URL(url).search);
            const fileId = urlParams.get('id');
            if (fileId) {
                return `https://lh3.googleusercontent.com/d/${fileId}`;
            }
        }
        
        // تنسيق 4: https://drive.google.com/thumbnail?id=FILE_ID
        if (url.includes('thumbnail?id=')) {
            const urlParams = new URLSearchParams(new URL(url).search);
            const fileId = urlParams.get('id');
            if (fileId) {
                return `https://lh3.googleusercontent.com/d/${fileId}`;
            }
        }
        
        // تنسيق 5: استخراج ID من أي رابط Google Drive
        const idMatch = url.match(/[a-zA-Z0-9-_]{25,}/);
        if (idMatch) {
            return `https://lh3.googleusercontent.com/d/${idMatch[0]}`;
        }
        
    } catch (error) {
        console.warn('Error converting Google Drive URL:', error);
    }
    
    return url;
}

// متغيرات عامة
let workshops = [];
let filteredWorkshops = [];
let currentFilter = 'all';

// بيانات الورشات (يمكن استبدالها ببيانات من API في المستقبل)
const staticWorkshops = [
    {
        id: 1,
        title: 'أساسيات الكيك الإسفنجي',
        instructor: 'الشيف سارة أحمد',
        price: '50 دينار',
        location: 'عمان - استوديو وصفة',
        date: '20 أكتوبر 2025',
        type: 'offline',
        level: 'beginner',
        category: 'baking',
        image: 'https://placehold.co/600x400/f87171/FFFFFF?text=ورشة+الكيك',
        spots: 3,
    },
    {
        id: 2,
        title: 'فن صناعة المعجنات الفرنسية',
        instructor: 'الشيف عمر خالد',
        price: '75 دينار',
        location: 'اونلاين (مباشر)',
        date: '25 أكتوبر 2025',
        type: 'online',
        level: 'advanced',
        category: 'baking',
        image: 'https://placehold.co/600x400/c084fc/FFFFFF?text=ورشة+المعجنات',
        spots: 15,
    },
    {
        id: 3,
        title: 'مقدمة في الطبخ الإيطالي',
        instructor: 'الشيف ماركو',
        price: '60 دينار',
        location: 'إربد - فندق إيوان',
        date: '5 نوفمبر 2025',
        type: 'offline',
        level: 'beginner',
        category: 'cooking',
        image: 'https://placehold.co/600x400/4ade80/FFFFFF?text=الطبخ+الإيطالي',
        spots: 0, // مكتملة
    },
    {
        id: 4,
        title: 'أسرار تحضير الستيك المثالي',
        instructor: 'الشيف عمر خالد',
        price: '80 دينار',
        location: 'اونلاين (مباشر)',
        date: '12 نوفمبر 2025',
        type: 'online',
        level: 'advanced',
        category: 'cooking',
        image: 'https://placehold.co/600x400/facc15/FFFFFF?text=ورشة+الستيك',
        spots: 8,
    },
    {
        id: 5,
        title: 'تزيين الكب كيك للأطفال',
        instructor: 'الشيف ليلى حسن',
        price: '30 دينار',
        location: 'عمان - استوديو وصفة',
        date: '18 نوفمبر 2025',
        type: 'offline',
        level: 'beginner',
        category: 'baking',
        image: 'https://placehold.co/600x400/fb7185/FFFFFF?text=كب+كيك+للأطفال',
        spots: 5,
    },
    {
        id: 6,
        title: 'صناعة الشوكولاتة من الصفر',
        instructor: 'الشيف ليلى حسن',
        price: '90 دينار',
        location: 'اونلاين (مباشر)',
        date: '22 نوفمبر 2025',
        type: 'online',
        level: 'advanced',
        category: 'baking',
        image: 'https://placehold.co/600x400/a16207/FFFFFF?text=ورشة+الشوكولاتة',
        spots: 12,
    }
];

// بيانات الأسئلة الشائعة
const faqData = [
    {
        question: "كيف يمكنني التسجيل في ورشة عمل؟",
        answer: "يمكنك التسجيل بسهولة عبر النقر على زر 'احجز الآن' في بطاقة الورشة والذي سينقلك مباشرة إلى الواتساب لملء البيانات المطلوبة."
    },
    {
        question: "ماذا يشمل سعر الورشة؟",
        answer: "سعر الورشة يشمل جميع المكونات والأدوات اللازمة خلال الورشة (للورشات الأوفلاين)، الوصول إلى المواد التعليمية الرقمية، ووصفات الدورة، بالإضافة إلى شهادة إتمام."
    },
    {
        question: "هل أحتاج إلى خبرة سابقة للمشاركة؟",
        answer: "لا تحتاج لخبرة سابقة في معظم ورشات المبتدئين. كل ورشة يوضح في وصفها المستوى المطلوب (مبتدئ، متوسط، متقدم) لمساعدتك في اختيار الأنسب لك."
    },
    {
        question: "ماذا لو فاتتني حصة من الورشة الأونلاين؟",
        answer: "لا تقلق! جميع الورشات الأونلاين يتم تسجيلها وإتاحتها للمشاركين لمدة شهر بعد انتهاء الورشة، بحيث يمكنك مشاهدتها في أي وقت يناسبك."
    },
    {
        question: "هل يتم توفير المكونات في الورشات الأوفلاين؟",
        answer: "بالتأكيد. في جميع ورشاتنا التي تقام في مواقعنا، نقوم بتوفير كافة المكونات الطازجة وعالية الجودة والأدوات اللازمة لكل مشارك."
    }
];


document.addEventListener('DOMContentLoaded', () => {

    const workshopsContainer = document.getElementById('workshops-container');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const faqContainer = document.querySelector('.faq-item');
    const loadingEl = document.getElementById('loading');
    const noResultsEl = document.getElementById('no-results');

    // ----- 1. عرض الورشات -----
    function renderWorkshops(cardsToShow) {
        console.log('Rendering workshops:', cardsToShow.length);
        
        if (!workshopsContainer) {
            console.log('Workshops container not found');
            return;
        }
        
        // إخفاء loading
        if (loadingEl) loadingEl.style.display = 'none';
        
        // إخفاء جميع الورشات الموجودة
        const existingCards = workshopsContainer.querySelectorAll('.workshop-card');
        existingCards.forEach(card => {
            card.style.display = 'none';
        });

        if(cardsToShow.length === 0) {
            console.log('No workshops to render, showing no results message');
            if (noResultsEl) {
                noResultsEl.classList.remove('hidden');
            }
            return;
        }
        
        // إخفاء no results
        if (noResultsEl) {
            noResultsEl.classList.add('hidden');
        }

        // إظهار الورشات المفلترة
        cardsToShow.forEach(card => {
            card.style.display = 'block';
        });
    }

    // ----- 2. فلترة الورشات -----
    function setupFiltering() {
        if (!filterButtons || filterButtons.length === 0) return;

        // Quick filter buttons
        filterButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                console.log('Filter button clicked:', e.target.textContent, 'Filter:', e.target.dataset.filter);
                
                // تحديث شكل الأزرار
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                e.target.classList.add('active');

                const filter = e.target.dataset.filter;
                applyFilters(filter);
            });
        });

        // Advanced filters
        const categoryFilter = document.getElementById('category-filter');
        const priceFilter = document.getElementById('price-filter');
        const sortFilter = document.getElementById('sort-filter');
        const clearFiltersBtn = document.getElementById('clear-filters');

        if (categoryFilter) {
            categoryFilter.addEventListener('change', () => applyAdvancedFilters());
        }

        if (priceFilter) {
            priceFilter.addEventListener('change', () => applyAdvancedFilters());
        }

        if (sortFilter) {
            sortFilter.addEventListener('change', () => applyAdvancedFilters());
        }

        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => {
                // Reset all filters
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                document.querySelector('.filter-btn[data-filter="all"]').classList.add('active');
                
                if (categoryFilter) categoryFilter.value = '';
                if (priceFilter) priceFilter.value = '';
                if (sortFilter) sortFilter.value = 'date';
                
                applyFilters('all');
            });
        }
    }

    function applyFilters(quickFilter) {
        console.log('Applying filter:', quickFilter);
        
        // الحصول على جميع بطاقات الورشات الموجودة في الصفحة
        const allCards = workshopsContainer.querySelectorAll('.workshop-card');
        let visibleCards = [];

        allCards.forEach(card => {
            const type = card.getAttribute('data-type');
            const level = card.getAttribute('data-level');
            const category = card.getAttribute('data-category');
            
            let shouldShow = true;
            
            if (quickFilter !== 'all') {
                if (quickFilter === 'online') {
                    shouldShow = type === 'online';
                } else if (quickFilter === 'offline') {
                    shouldShow = type === 'offline';
                } else if (quickFilter === 'beginner') {
                    shouldShow = level === 'beginner';
                } else if (quickFilter === 'advanced') {
                    shouldShow = level === 'advanced';
                } else {
                    shouldShow = category === quickFilter;
                }
            }
            
            if (shouldShow) {
                visibleCards.push(card);
            }
        });

        console.log('Filtered workshops count:', visibleCards.length);
        renderWorkshops(visibleCards);
    }

    function applyAdvancedFilters(workshopsToFilter = workshops) {
        let filtered = [...workshopsToFilter];

        // Category filter
        const categoryFilter = document.getElementById('category-filter');
        if (categoryFilter && categoryFilter.value) {
            filtered = filtered.filter(w => w.category === categoryFilter.value);
        }

        // Sort filter
        const sortFilter = document.getElementById('sort-filter');
        if (sortFilter && sortFilter.value) {
            const sortBy = sortFilter.value;
            filtered.sort((a, b) => {
                switch (sortBy) {
                    case 'price-low':
                        return parseFloat(a.price) - parseFloat(b.price);
                    case 'price-high':
                        return parseFloat(b.price) - parseFloat(a.price);
                    case 'rating':
                        return parseFloat(b.rating) - parseFloat(a.rating);
                    case 'popularity':
                        return (b.bookings_count || 0) - (a.bookings_count || 0);
                    case 'date':
                    default:
                        return new Date(a.start_date) - new Date(b.start_date);
                }
            });
        }

        return filtered;
    }

    // ----- 3. عرض الأسئلة الشائعة -----
    function renderFAQs() {
        // الأسئلة الشائعة موجودة بالفعل في HTML
        // هذه الدالة للتوافق مع الكود القديم
        console.log('FAQs are already rendered in HTML');
    }

    // ----- 4. تفعيل الأكورديون للأسئلة -----
    function setupFAQAccordion() {
        // إضافة مستمع للأكورديون
        document.addEventListener('click', (e) => {
            const questionButton = e.target.closest('.faq-question');
            if (questionButton) {
                const faqItem = questionButton.parentElement;
                
                // إغلاق جميع الأسئلة الأخرى
                document.querySelectorAll('.faq-item.open').forEach(item => {
                    if (item !== faqItem) {
                        item.classList.remove('open');
                    }
                });
                
                // فتح أو إغلاق السؤال الحالي
                faqItem.classList.toggle('open');
            }
        });
    }

    // ----- 5. تحميل الورشات من API -----
    async function loadWorkshops() {
        try {
            // الحصول على جميع بطاقات الورشات الموجودة في الصفحة
            const allCards = workshopsContainer.querySelectorAll('.workshop-card');
            console.log('Found workshop cards in page:', allCards.length);
            
            // إظهار جميع الورشات في البداية
            allCards.forEach(card => {
                card.style.display = 'block';
            });
            
            // إخفاء loading إذا كان موجود
            if (loadingEl) loadingEl.style.display = 'none';
            
            // إخفاء no results
            if (noResultsEl) {
                noResultsEl.classList.add('hidden');
            }
            
        } catch (error) {
            console.error('Error loading workshops:', error);
        }
    }

    // ----- معالجة روابط Google Drive -----
    function processGoogleDriveImages() {
        // معالجة جميع الصور التي تحتوي على روابط Google Drive
        const images = document.querySelectorAll('img[src*="drive.google.com"]');
        console.log('Found Google Drive images:', images.length);
        
        images.forEach(function(img) {
            const originalSrc = img.src;
            const convertedSrc = convertGoogleDriveUrl(originalSrc);
            
            console.log('Converting:', originalSrc, 'to:', convertedSrc);
            
            if (convertedSrc !== originalSrc) {
                // إضافة معالج للأخطاء
                img.onerror = function() {
                    console.warn('Failed to load converted image:', convertedSrc);
                    // محاولة استخدام رابط بديل
                    const alternativeUrl = originalSrc.replace('/view', '').replace('/edit', '');
                    this.src = alternativeUrl;
                };
                
                img.src = convertedSrc;
            }
        });
        
        // معالجة الصور الجديدة التي قد تظهر لاحقاً
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.tagName === 'IMG' && node.src && node.src.includes('drive.google.com')) {
                        const convertedSrc = convertGoogleDriveUrl(node.src);
                        if (convertedSrc !== node.src) {
                            node.src = convertedSrc;
                        }
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // ----- نقطة البداية -----
    loadWorkshops();
    setupFiltering();
    renderFAQs();
    setupFAQAccordion();
    processGoogleDriveImages();
});

