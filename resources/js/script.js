/**
 * @file Script to fetch and display recipes, and handle UI interactions.
 * @description This script fetches recipe data from an API, populates the main section with interactive recipe cards in a Swiper slider,
 * fills a sidebar with the latest recipes, and manages UI events like mobile menu toggling and save/flip actions on cards.
 */

// =================================================================================
//  وظائف إنشاء HTML (HTML Rendering Functions)
// =================================================================================

/**
 * إنشاء كود HTML لبطاقة وصفة عصرية قابلة للقلب.
 * @param {object} recipe - كائن يحتوي على بيانات الوصفة (مثل id, title, image_url).
 * @returns {string} - سلسلة نصية تحتوي على كود HTML الخاص بالبطاقة.
 */
function createModernCardHTML(recipe) {
	// إعداد متغيرات العرض مع قيم احتياطية في حال عدم توفر البيانات
	const desc = `وصفة ${recipe.title} هي طبق شهي ومغذي، مثالي لجميع المناسبات.`;
	const rating = recipe.interactions_avg_rating ? parseFloat(recipe.interactions_avg_rating).toFixed(1) : "لا تقييمات";
	const category = recipe.category?.name || "بدون فئة";
	// معالجة أفضل لروابط الصور
	let imgUrl = 'https://placehold.co/500x300/f97316/ffffff?text=لا+توجد+صورة';
	if (recipe.image_url) {
		// تحقق إذا كان الرابط خارجي
		if (recipe.image_url.startsWith('http')) {
			imgUrl = recipe.image_url;
		} else {
			// إذا كان محلي، أضف storage path
			imgUrl = `/storage/${recipe.image_url}`;
		}
	}
	const userId = document.body.dataset.userId; // قراءة معرّف المستخدم من الـ body
	const isSaved = recipe.is_saved === 1 || recipe.is_saved === true; // التحقق من حالة الحفظ (قد تكون 1 أو true)
	const isRegistrationClosed = recipe.is_registration_closed === 1 || recipe.is_registration_closed === true; // التحقق من انتهاء مهلة الحجز
	const saveButtonClass = isSaved ? 'bg-green-500 hover:bg-green-600 text-white' : 'bg-orange-500 hover:bg-orange-600 text-white';
	const saveButtonText = isSaved ? 'محفوظة' : 'حفظ';
	const saveButtonBackClass = isSaved ? 'bg-green-500 hover:bg-green-600 text-white' : 'bg-orange-500 hover:bg-orange-600 text-white';
	

	// استخدام الـ Template Literals لسهولة قراءة وكتابة الـ HTML
	return `
	<div class="swiper-slide">
		<div class="card-container" data-recipe-id="${recipe.recipe_id}" data-user-id="${userId || ''}">
			<div class="card-inner">
				
				<div class="card-front bg-white">
					<div class="relative h-2/3">
						<img src="${imgUrl}" alt="${recipe.title}" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/500x300/f97316/ffffff?text=لا+توجد+صورة'; this.alt='صورة افتراضية';">
						<div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
						<div class="absolute bottom-4 right-4"><span class="bg-orange-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">${category}</span></div>
					</div>
					<div class="p-6 flex flex-col flex-grow">
						<div class="flex justify-between items-start mb-2">
							<h3 class="text-xl font-bold text-gray-800 truncate" style="max-width: 70%;">${recipe.title}</h3>
							<div class="flex items-center space-x-1 space-x-reverse text-sm text-gray-500">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
								<span class="font-semibold">${rating}</span>
							</div>
						</div>
						${isRegistrationClosed ? 
							`<button class="w-full mt-auto bg-yellow-400 text-yellow-800 font-semibold py-2 px-4 rounded-lg cursor-not-allowed flex items-center justify-center space-x-2 space-x-reverse">
								<i class="fas fa-clock"></i>
								<span>انتهت مهلة الحجز</span>
							</button>` :
							`<button class="save-btn w-full mt-auto ${saveButtonClass} font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center space-x-2 space-x-reverse" data-saved="${isSaved}" data-recipe-id="${recipe.recipe_id}">
								<i class="fas fa-bookmark"></i>
								<span>${saveButtonText}</span>
							</button>`
						}
					</div>
				</div>

				<div class="card-back p-6 flex flex-col justify-between">
					<div>
						<h3 class="text-2xl font-bold text-gray-800 mb-3">الوصف</h3>
						<p class="text-gray-600 text-base leading-relaxed">${desc}</p>
					</div>
					<div class="space-y-3">
						${isRegistrationClosed ? 
							`<button class="w-full bg-yellow-400 text-yellow-800 font-bold py-3 px-4 rounded-xl cursor-not-allowed flex items-center justify-center space-x-2 space-x-reverse">
								<i class="fas fa-clock"></i>
								<span>انتهت مهلة الحجز</span>
							</button>` :
							`<a href="/recipe/${recipe.recipe_id}" class="view-recipe-btn-link w-full block">
								<button class="view-recipe-btn w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-xl transition-colors duration-300 flex items-center justify-center space-x-2 space-x-reverse">
									<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
									<span>عرض الوصفة</span>
								</button>
							</a>`
						}
						<button class="save-btn w-full ${saveButtonBackClass} font-semibold py-3 px-4 rounded-xl transition-colors duration-300 flex items-center justify-center space-x-2 space-x-reverse" data-saved="${isSaved}" data-recipe-id="${recipe.recipe_id}">
							<i class="fas fa-bookmark"></i>
							<span>${saveButtonText}</span>
						</button>
					</div>
				</div>

			</div>
		</div>
	</div>`;
}

/**
 * إنشاء كود HTML لقائمة "أحدث الوصفات" في الشريط الجانبي.
 * @param {object[]} recipes - مصفوفة من كائنات الوصفات.
 * @returns {string} - سلسلة نصية تحتوي على كود HTML لعناصر القائمة.
 */
function createLatestRecipesHTML(recipes) {
	// نستخدم slice(0, 7) لأخذ أول 7 وصفات فقط
	return recipes.slice(0, 7).map(rec => {
		// معالجة أفضل لروابط الصور
		let imgUrl = "https://placehold.co/80x80?text=No+Image";
		if (rec.image_url) {
			if (rec.image_url.startsWith('http')) {
				imgUrl = rec.image_url;
			} else {
				imgUrl = `/storage/${rec.image_url}`;
			}
		}
		
		return `
		<li class="flex items-start gap-4 p-2 hover:bg-gray-50">
			<a href="/recipe/${rec.recipe_id}" class="view-recipe-btn-link w-full block flex items-start gap-4">
				<img src="${imgUrl}" alt="${rec.title}" class="w-20 h-20 object-cover rounded-lg" onerror="this.src='https://placehold.co/80x80?text=No+Image'; this.alt='صورة افتراضية';">
				<div>
					<span class="text-xs text-gray-500">${rec.category?.name || "حلويات"}</span>
					<p class="font-semibold text-gray-800">${rec.title}</p>
				</div>
			</a>
		</li>`;
	}).join('');
}


// =================================================================================
// وظائف جلب البيانات والعرض (Data Fetching & Display Functions)
// =================================================================================

/**
 * تجلب بيانات الوصفات من API ثم تعرضها في القسم الرئيسي والشريط الجانبي.
 */
async function fetchAndDisplayRecipes() {
	const recipeCardsContainer = document.getElementById("recipeCards");
	const latestRecipesList = document.getElementById("latest-recipes-list");

	if (!recipeCardsContainer && !latestRecipesList) return;

	try {
		const response = await fetch("/api/lastrecipes", { credentials: 'include' });
		
		// التحقق من أن الاستجابة تحتوي على بيانات صالحة
		if (!response.ok) {
			throw new Error(`فشل الاتصال بالـ API. الحالة: ${response.status}`);
		}
		
		let recipes = await response.json();
		
		if (!Array.isArray(recipes)) {
			throw new Error("استجابة غير متوقعة من الخادم.");
		}
		
		if (recipeCardsContainer) {
			// إزالة loading skeleton
			const loadingElement = document.getElementById('recipes-loading');
			if (loadingElement) {
				loadingElement.remove();
			}
			
			recipeCardsContainer.innerHTML = recipes.map(createModernCardHTML).join('');
			
			new Swiper('.swiper', {
				slidesPerView: 'auto',
				spaceBetween: 16,
				grabCursor: true,
				navigation: { 
					nextEl: '#nextBtn', 
					prevEl: '#prevBtn' 
				},
			});
		}
		
		if (latestRecipesList) {
			// إزالة loading skeleton
			const latestLoadingElement = document.getElementById('latest-recipes-loading');
			if (latestLoadingElement) {
				latestLoadingElement.remove();
			}
			
			latestRecipesList.innerHTML = createLatestRecipesHTML(recipes);
		}

		// تهيئة أزرار الحفظ مباشرة بعد تحميل الوصفات
		if (typeof SaveRecipe !== 'undefined') {
			SaveRecipe.initializeSaveButtons();
		} else {
			setTimeout(() => {
				if (typeof SaveRecipe !== 'undefined') {
					SaveRecipe.initializeSaveButtons();
				}
			}, 500);
		}
		
		// event delegation يتم التعامل معه من خلال save-recipe.js

	} catch (error) {
		console.error("Error fetching recipes:", error);
		
		// إزالة loading skeletons
		const loadingElement = document.getElementById('recipes-loading');
		if (loadingElement) {
			loadingElement.remove();
		}
		const latestLoadingElement = document.getElementById('latest-recipes-loading');
		if (latestLoadingElement) {
			latestLoadingElement.remove();
		}
		
		if (recipeCardsContainer) {
			recipeCardsContainer.innerHTML = `<p class="text-red-500 w-full text-center">فشل تحميل الوصفات.</p>`;
		}
		if (latestRecipesList) {
			latestRecipesList.innerHTML = `<p class="text-red-500">فشل تحميل الوصفات.</p>`;
		}
	}
}


// =================================================================================
// وظائف معالجة الأحداث وتهيئة الواجهة (UI & Event Handlers)
// =================================================================================

/**
 * إعداد event delegation لأزرار الحفظ
 * تم إزالة هذا المستمع لأن save-recipe.js يتولى هذا الأمر
 */
function setupSaveButtonDelegation() {
	// تم إزالة المستمع المكرر - save-recipe.js يتولى هذا الأمر
	console.log('Save button delegation handled by save-recipe.js');
}

/**
 * تنفيذ منطق الحفظ كبديل
 * تم إزالة هذه الدالة لأن save-recipe.js يتولى هذا الأمر
 */
async function handleSaveRecipeFallback(button, recipeId) {
	// تم إزالة هذه الدالة - save-recipe.js يتولى هذا الأمر
	console.log('Save recipe handling delegated to save-recipe.js');
}

/**
 * تحديث حالة الزر كبديل
 * تم إزالة هذه الدالة لأن save-recipe.js يتولى هذا الأمر
 */
function updateSaveButtonStateFallback(button, isSaved) {
	// تم إزالة هذه الدالة - save-recipe.js يتولى هذا الأمر
	console.log('Save button state update delegated to save-recipe.js');
}

/**
 * إظهار رسالة نجاح كبديل
 * تم إزالة هذه الدالة لأن save-recipe.js يتولى هذا الأمر
 */
function showToastFallback(message) {
	// تم إزالة هذه الدالة - save-recipe.js يتولى هذا الأمر
	console.log('Toast messages delegated to save-recipe.js');
}

/**
 * يحدث عداد الحفظ فورياً في واجهة المستخدم (للصفحة الرئيسية)
 * @param {boolean} isSaved - true إذا تم الحفظ، false إذا تم إلغاء الحفظ
 */
function updateSaveCountUI(isSaved) {
	const saveItCountEl = document.getElementById('save-it-count');
	if (!saveItCountEl) return;
	
	const currentCount = parseInt(saveItCountEl.querySelector('span').textContent) || 0;
	const newCount = isSaved ? currentCount + 1 : Math.max(0, currentCount - 1);
	
	saveItCountEl.innerHTML = `تم حفظها من قبل <span class="font-bold text-orange-500 text-lg">${newCount}</span> شخص!`;
	
	// إضافة تأثير بصري
	saveItCountEl.style.transform = 'scale(1.05)';
	saveItCountEl.style.transition = 'transform 0.2s ease-in-out';
	
	setTimeout(() => {
		saveItCountEl.style.transform = 'scale(1)';
	}, 200);
}

/**
 * تحميل عدد المنتجات في السلة
 */
function loadCartCount() {
	fetch('/cart/count')
		.then(response => response.json())
		.then(data => {
			// تحديث العداد في الهيدر
			const cartCountEl = document.getElementById('cart-count');
			if (cartCountEl) {
				cartCountEl.textContent = data.count;
			}
			
			// تحديث العداد في صفحة الأدوات
			const cartCountBadge = document.getElementById('cart-count-badge');
			if (cartCountBadge) {
				if (data.count > 0) {
					cartCountBadge.textContent = data.count;
					cartCountBadge.classList.remove('hidden');
				} else {
					cartCountBadge.classList.add('hidden');
				}
			}
		})
		.catch(error => {
			console.error('Error loading cart count:', error);
		});
}

/**
 * تحميل عدد الأدوات المحفوظة
 */
function loadSavedCount() {
	console.log('Loading saved count...'); // Debug log
	
	// إضافة CSRF token
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
	
	fetch('/saved/count', {
		method: 'GET',
		headers: {
			'X-CSRF-TOKEN': csrfToken || '',
			'Content-Type': 'application/json',
			'Accept': 'application/json',
			'X-Requested-With': 'XMLHttpRequest'
		},
		credentials: 'same-origin',
		cache: 'no-cache'
	})
		.then(response => {
			console.log('Response status:', response.status);
			if (response.status === 401) {
				// المستخدم غير مسجل دخول
				console.log('User not authenticated, showing 0');
				updateSavedCountUI(0);
				return;
			}
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return response.json();
		})
		.then(data => {
			if (data) {
				console.log('Saved count data:', data); // Debug log
				updateSavedCountUI(data.count);
			}
		})
		.catch(error => {
			console.error('Error loading saved count:', error);
			// في حالة الخطأ، نعرض 0
			updateSavedCountUI(0);
		});
}

// دالة محسنة لتحميل العداد مع معالجة أفضل للأخطاء
function loadSavedCountImproved() {
	console.log('Loading saved count improved...'); // Debug log
	
	// إضافة CSRF token
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
	
	fetch('/saved/count', {
		method: 'GET',
		headers: {
			'X-CSRF-TOKEN': csrfToken || '',
			'Content-Type': 'application/json',
			'Accept': 'application/json',
			'X-Requested-With': 'XMLHttpRequest'
		},
		credentials: 'same-origin',
		cache: 'no-cache'
	})
		.then(response => {
			console.log('Improved response status:', response.status);
			if (response.status === 401) {
				// المستخدم غير مسجل دخول
				console.log('User not authenticated (improved), showing 0');
				updateSavedCountUI(0);
				return;
			}
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return response.json();
		})
		.then(data => {
			if (data) {
				console.log('Improved saved count data:', data); // Debug log
				updateSavedCountUI(data.count);
			}
		})
		.catch(error => {
			console.error('Error loading saved count improved:', error);
			// في حالة الخطأ، نعرض 0
			updateSavedCountUI(0);
		});
}

// دالة بديلة لتحميل العداد مع session مختلف
function loadSavedCountAlternative() {
	console.log('Loading saved count alternative...'); // Debug log
	
	// إضافة CSRF token
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
	
	fetch('/saved/count', {
		method: 'POST',
		headers: {
			'X-CSRF-TOKEN': csrfToken || '',
			'Content-Type': 'application/json',
			'Accept': 'application/json',
			'X-Requested-With': 'XMLHttpRequest'
		},
		credentials: 'same-origin',
		cache: 'no-cache',
		body: JSON.stringify({})
	})
		.then(response => {
			console.log('Alternative response status:', response.status);
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return response.json();
		})
		.then(data => {
			console.log('Alternative saved count data:', data); // Debug log
			updateSavedCountUI(data.count);
		})
		.catch(error => {
			console.error('Error loading saved count alternative:', error);
			// في حالة الخطأ، نعرض 0
			updateSavedCountUI(0);
		});
}

// دالة مساعدة لتحميل العداد مع إعادة المحاولة
function loadSavedCountWithRetry(retries = 3) {
	loadSavedCount();
	
	// إعادة المحاولة مرة واحدة فقط إذا لزم الأمر
	if (retries > 0) {
		setTimeout(() => {
			const savedCountEl = document.getElementById('saved-count');
			if (savedCountEl && savedCountEl.classList.contains('hidden') && retries > 0) {
				console.log('Retrying loadSavedCount, final attempt');
				loadSavedCount(); // محاولة أخيرة بدون إعادة الاستدعاء
			}
		}, 1000); // تقليل الوقت من 2000 إلى 1000
	}
}

// دالة لاختبار العداد مباشرة
function testCounterDisplay() {
	const savedCountEl = document.getElementById('saved-count');
	if (savedCountEl) {
		console.log('Testing counter display...');
		savedCountEl.textContent = '5';
		savedCountEl.classList.remove('hidden');
		console.log('Counter should now be visible with number 5');
	} else {
		console.error('saved-count element not found');
	}
}

/**
 * تحديث واجهة عداد الأدوات المحفوظة
 */
function updateSavedCountUI(count) {
	// تحديث العداد في الهيدر
	const savedCountEl = document.getElementById('saved-count');
	if (savedCountEl) {
		if (count > 0) {
			savedCountEl.textContent = count;
			savedCountEl.classList.remove('hidden');
			console.log('Saved count updated in navbar:', count); // Debug log
		} else {
			savedCountEl.classList.add('hidden');
			console.log('Saved count hidden in navbar'); // Debug log
		}
	} else {
		console.log('saved-count element not found in navbar'); // Debug log
	}
	
	// تحديث العداد في صفحة الأدوات
	const savedCountBadge = document.getElementById('saved-count-badge');
	if (savedCountBadge) {
		if (count > 0) {
			savedCountBadge.textContent = count;
			savedCountBadge.classList.remove('hidden');
		} else {
			savedCountBadge.classList.add('hidden');
		}
	}
}

/**
 * تهيئة مستمعي الأحداث لعناصر الواجهة مثل القائمة الجانبية في الجوال.
 */
function initializeUIEventListeners() {
	// Mobile menu functionality is now handled in app.blade.php
	// This function is kept for other UI event listeners if needed
}

/**
 * تهيئة مستمعي الأحداث لبطاقات الوصفات (القلب والحفظ).
 * يتم استخدام تفويض الأحداث (Event Delegation) لتحسين الأداء.
 */
function initializeCardEventListeners() {
	const recipeContainer = document.querySelector('.swiper');
	if (!recipeContainer) return;

	recipeContainer.addEventListener('click', async (event) => {
		const cardContainer = event.target.closest('.card-container');
		if (!cardContainer) return;

		// --- معالجة النقر على زر الحفظ ---
		const clickedSaveBtn = event.target.closest('.save-btn, .save-recipe-btn');
		if (clickedSaveBtn) {
			// النظام الجديد يتولى إدارة أزرار الحفظ
			// لا حاجة لمعالجة إضافية هنا
			return;
		}
		
		// --- معالجة قلب البطاقة ---
		// منع القلب عند النقر على زر الحفظ أو زر عرض الوصفة
		if (event.target.closest('.save-btn, .save-recipe-btn') || event.target.closest('.view-recipe-btn-link')) return;
		cardContainer.classList.toggle('is-flipped');
	});
}


// =================================================================================
// نقطة الدخول الرئيسية (Main Entry Point)
// =================================================================================

/**
 * يتم تنفيذ هذا الكود بعد تحميل محتوى الـ DOM بالكامل.
 */
document.addEventListener("DOMContentLoaded", () => {
	initializeUIEventListeners();
	fetchAndDisplayRecipes();
	initializeCardEventListeners();
	loadCartCount(); // تحميل عدد المنتجات في السلة
	loadSavedCount(); // تحميل عدد الأدوات المحفوظة
});

// جعل الدالة متاحة عالمياً للتحديث الفوري
window.loadSavedCount = loadSavedCount;
window.loadSavedCountImproved = loadSavedCountImproved;
window.loadSavedCountAlternative = loadSavedCountAlternative;
window.loadSavedCountWithRetry = loadSavedCountWithRetry;
window.testCounterDisplay = testCounterDisplay;
window.updateSavedCountUI = updateSavedCountUI;
// =================================================================================   
// نهاية الملف (End of File)        
