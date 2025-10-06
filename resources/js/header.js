/**
 * @file header.js
 * @description هذا الملف يعالج حالة المصادقة للمستخدم ويقوم بتحديث شريط التنقل ديناميكياً بالاعتماد على جلسات Laravel.
 */

document.addEventListener('DOMContentLoaded', async () => {
	console.log('Header.js loaded and DOM ready');
	
	// Mobile Search Modal functionality
	setupMobileSearch();
	
	// التحقق من وجود مستخدم مسجل في الصفحة
	const userMenuContainer = document.getElementById('user-menu-container');
	if (!userMenuContainer) {
		console.log('No logged in user found, skipping header update');
		return;
	}
	
	// استهداف عناصر القائمة
	const desktopNav = document.querySelector('.hidden.md\\:flex nav');
	const mobileNav = document.querySelector('#mobileMenu nav');
	console.log('Desktop nav found:', desktopNav);
	console.log('Mobile nav found:', mobileNav);

	try {
		// تهيئة كوكي CSRF الخاص بـ Sanctum لضمان عمل الجلسة عبر /api
		await fetch('/sanctum/csrf-cookie', { credentials: 'include' });

		//  محاولة جلب بيانات المستخدم المسجل.
		// المتصفح سيرسل "كوكيز" الجلسة مع الطلب بفضل credentials: 'include'
		const response = await fetch('/api/user', {
			headers: {
				'Accept': 'application/json',
			},
			credentials: 'include'
		});

		if (response.ok) {
			// إذا كان المستخدم مسجلاً، قم بتحديث شريط التنقل
			const user = await response.json();
			console.log('User data:', user);
			updateNavForLoggedInUser(user, desktopNav, mobileNav);
		} else {
			// إذا لم يكن المستخدم مسجلاً، أضف رابط ورشات العمل
			console.log('User not logged in');
			updateNavForGuestUser(desktopNav, mobileNav);
		}
	} catch (error) {
		console.error('Error fetching user status:', error);
	}
});

/**
 * يقوم بتحديث شريط التنقل (العلوي والجوال) لعرض قائمة المستخدم المسجل.
 * @param {object} user - كائن يحتوي على بيانات المستخدم (name, avatar).
 * @param {HTMLElement} desktopNav - عنصر شريط التنقل لسطح المكتب.
 * @param {HTMLElement} mobileNav - عنصر شريط التنقل للجوال.
 */
function updateNavForLoggedInUser(user, desktopNav, mobileNav) {
	console.log('Updating nav for logged in user:', user);
	
	// التحقق من صلاحيات المدير
	console.log('User is_admin:', user.is_admin);
	if (user.is_admin) {
		console.log('Adding admin menu items');
		
	// تم إزالة إضافة روابط الإدارة هنا لأنها موجودة بالفعل في app.blade.php
	// لتجنب التكرار
		
		// تم إزالة إضافة روابط الإدارة للقائمة المحمولة هنا أيضاً
		// لأنها موجودة بالفعل في app.blade.php لتجنب التكرار
	} else {
		console.log('User is not admin, skipping admin menu items');
	}
}

/**
 * يقوم بتحديث شريط التنقل للمستخدم غير المسجل
 * @param {HTMLElement} desktopNav - عنصر شريط التنقل لسطح المكتب.
 * @param {HTMLElement} mobileNav - عنصر شريط التنقل للجوال.
 */
function updateNavForGuestUser(desktopNav, mobileNav) {
	const guestMenuHTML = `
		<a href="/workshops" class="hover:text-orange-500 transition-colors font-medium">
			<i class="fas fa-graduation-cap ml-1"></i>
			ورشات العمل
		</a>
		<a href="/login" class="hover:text-orange-500 transition-colors">تسجيل الدخول</a>
	`;

	const mobileGuestMenuHTML = `
		<a href="/workshops" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors">
			<i class="fas fa-graduation-cap ml-3 text-orange-500"></i>
			<span class="font-medium">ورشات العمل</span>
		</a>
		<a href="/login" class="hover:text-orange-500 transition-colors">تسجيل الدخول</a>
	`;

	if (desktopNav) {
		desktopNav.innerHTML = guestMenuHTML;
	}
	
	if (mobileNav) {
		mobileNav.innerHTML = mobileGuestMenuHTML;
	}
}

/**
 * يتعامل مع عملية تسجيل الخروج
 */
function handleLogout() {
	// إنشاء form مخفي لتسجيل الخروج
	const form = document.createElement('form');
	form.method = 'POST';
	form.action = '/logout';
	
	// إضافة CSRF token
	const csrfToken = document.querySelector('meta[name="csrf-token"]');
	if (csrfToken) {
		const csrfInput = document.createElement('input');
		csrfInput.type = 'hidden';
		csrfInput.name = '_token';
		csrfInput.value = csrfToken.getAttribute('content');
		form.appendChild(csrfInput);
	}
	
	// إضافة form إلى الصفحة وتنفيذه
	document.body.appendChild(form);
	form.submit();
}

/**
 * إعداد وظائف البحث المحمول
 */
function setupMobileSearch() {
	const mobileSearchBtn = document.getElementById('mobileSearchBtn');
	const mobileSearchModal = document.getElementById('mobileSearchModal');
	const closeMobileSearchModal = document.getElementById('closeMobileSearchModal');
	const mobileSearchInput = document.getElementById('mobile-search-input');
	const mobileSearchSubmit = document.getElementById('mobile-search-submit');
	
	if (!mobileSearchBtn || !mobileSearchModal || !closeMobileSearchModal || !mobileSearchInput || !mobileSearchSubmit) {
		console.log('Mobile search elements not found');
		return;
	}
	
	// فتح نافذة البحث المحمول
	mobileSearchBtn.addEventListener('click', () => {
		mobileSearchModal.classList.remove('hidden');
		document.body.style.overflow = 'hidden';
		// التركيز على حقل البحث
		setTimeout(() => {
			mobileSearchInput.focus();
		}, 100);
	});
	
	// إغلاق نافذة البحث المحمول
	closeMobileSearchModal.addEventListener('click', closeMobileSearch);
	
	// إغلاق عند النقر خارج النافذة
	mobileSearchModal.addEventListener('click', (e) => {
		if (e.target === mobileSearchModal) {
			closeMobileSearch();
		}
	});
	
	// البحث عند الضغط على Enter
	mobileSearchInput.addEventListener('keypress', (e) => {
		if (e.key === 'Enter') {
			e.preventDefault();
			performMobileSearch();
		}
	});
	
	// البحث عند النقر على زر البحث
	mobileSearchSubmit.addEventListener('click', (e) => {
		e.preventDefault();
		performMobileSearch();
	});
	
	function closeMobileSearch() {
		mobileSearchModal.classList.add('hidden');
		document.body.style.overflow = 'auto';
		mobileSearchInput.value = '';
	}
	
	function performMobileSearch() {
		const query = mobileSearchInput.value.trim();
		if (query) {
			window.location.href = `/search?q=${encodeURIComponent(query)}`;
		} else {
			window.location.href = '/';
		}
	}
}