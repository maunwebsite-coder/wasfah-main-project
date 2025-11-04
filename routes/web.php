<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Chef\LinkItemController as ChefLinkItemController;
use App\Http\Controllers\Chef\LinkPageController as ChefLinkPageController;
use App\Http\Controllers\Chef\RecipeController as ChefRecipeController;
use App\Http\Controllers\Chef\WorkshopController as ChefWorkshopController;
use App\Http\Controllers\ChefLinkPublicController;
use App\Http\Controllers\ChefPublicProfileController;
use App\Models\Recipe;
use App\Models\Workshop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// مسارات المصادقة عبر Google
Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])->name('google.callback');

// المسار الرئيسي لعرض الصفحة الرئيسية
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// صفحة روابط Wasfah للمنصات الاجتماعية
Route::get('/wasfah-links', function () {
    $fallbackSelections = collect([
        [
            'title' => 'صندوق وصفة الموسمي',
            'image' => asset('image/tnl.png'),
            'alt' => 'صندوق وصفة الموسمي',
            'url' => url('/recipes'),
        ],
        [
            'title' => 'ورش عمل مباشرة عبر Google Meet',
            'image' => asset('image/wterm.png'),
            'alt' => 'ورش عمل مباشرة عبر Google Meet',
            'url' => url('/workshops'),
        ],
        [
            'title' => 'أدوات المطبخ المختارة',
            'image' => asset('image/term.png'),
            'alt' => 'أدوات المطبخ المختارة',
            'url' => url('/tools'),
        ],
    ]);

    $monthlySelections = Recipe::approved()
        ->public()
        ->inRandomOrder()
        ->take(3)
        ->get()
        ->map(function (Recipe $recipe) {
            return [
                'title' => $recipe->title,
                'image' => $recipe->image_url ?? asset('image/Brownies.png'),
                'alt' => $recipe->title,
                'url' => route('recipe.show', ['recipe' => $recipe->slug]),
            ];
        });

    if ($monthlySelections->isEmpty()) {
        $monthlySelections = $fallbackSelections->values();
    } elseif ($monthlySelections->count() < 3) {
        $monthlySelections = $monthlySelections->concat(
            $fallbackSelections->take(3 - $monthlySelections->count())
        )->values();
    } else {
        $monthlySelections = $monthlySelections->values();
    }

    $nextWorkshop = Workshop::query()
        ->active()
        ->upcoming()
        ->orderBy('start_date')
        ->first();

    $upcomingWorkshop = null;

    if ($nextWorkshop) {
        $upcomingWorkshop = [
            'title' => $nextWorkshop->title,
            'slug' => $nextWorkshop->slug,
            'instructor' => $nextWorkshop->instructor,
            'start_date' => $nextWorkshop->start_date?->locale('ar')->translatedFormat('d F Y • h:i a'),
            'mode' => $nextWorkshop->is_online
                ? 'أونلاين عبر Google Meet'
                : ($nextWorkshop->location ?? 'حضوري'),
            'image' => $nextWorkshop->image
                ? \Storage::disk('public')->url($nextWorkshop->image)
                : asset('image/wterm.png'),
        ];
    }

    return view('links', [
        'monthlySelections' => $monthlySelections,
        'upcomingWorkshop' => $upcomingWorkshop,
    ]);
})->name('links');

Route::get('/wasfah-links/{chefLinkPage:slug}', [ChefLinkPublicController::class, 'show'])
    ->name('links.chef');

// صفحة عامة لعرض بروفايلات الشيف مع وصفاتهم
Route::get('/chefs/{chef}', [ChefPublicProfileController::class, 'show'])
    ->whereNumber('chef')
    ->name('chefs.show');

// مسار صفحة المصادقة الموحدة (تسجيل الدخول + إنشاء حساب)
Route::get('/login', function () {
    // إذا كان المستخدم مسجل دخول، أعد توجيهه للصفحة الرئيسية
    if (Auth::check()) {
        return redirect('/')->with('info', 'أنت مسجل دخول بالفعل');
    }
    request()->session()->regenerateToken();

    $response = response()
        ->view('auth')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');

    $response->withCookie(
        cookie(
            'XSRF-TOKEN',
            csrf_token(),
            config('session.lifetime'),
            '/',
            config('session.domain'),
            (bool) config('session.secure', false),
            false,
            false,
            config('session.same_site', 'lax')
        )
    );

    return $response;
})->name('login');
Route::post('/login', [LoginController::class, 'store'])
    ->middleware('guest')
    ->name('login.password');

// مسارات المحفوظات
Route::prefix('saved')->middleware(['web'])->group(function () {
    // عداد المحفوظات متاح للضيوف ويعيد 0 عند عدم تسجيل الدخول
    Route::get('/count', [App\Http\Controllers\SavedController::class, 'count'])->name('saved.count');
    Route::post('/count', [App\Http\Controllers\SavedController::class, 'count'])->name('saved.count.post');

    Route::middleware('auth')->group(function () {
        Route::get('/', [App\Http\Controllers\SavedController::class, 'index'])->name('saved.index');
        Route::post('/add', [App\Http\Controllers\SavedController::class, 'add'])->name('saved.add');
        Route::post('/remove', [App\Http\Controllers\SavedController::class, 'remove'])->name('saved.remove');
        Route::get('/status', [App\Http\Controllers\SavedController::class, 'status'])->name('saved.status');
    });
});


// مسار صفحة إنشاء الحساب (يوجه لنفس الصفحة)
Route::get('/register', function () {
    // إذا كان المستخدم مسجل دخول، أعد توجيهه للصفحة الرئيسية
    if (Auth::check()) {
        return redirect('/')->with('info', 'أنت مسجل دخول بالفعل');
    }
    request()->session()->regenerateToken();

    $response = response()
        ->view('auth')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');

    $response->withCookie(
        cookie(
            'XSRF-TOKEN',
            csrf_token(),
            config('session.lifetime'),
            '/',
            config('session.domain'),
            (bool) config('session.secure', false),
            false,
            false,
            config('session.same_site', 'lax')
        )
    );

    return $response;
})->name('register');
Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('guest')
    ->name('register.password');
Route::get('/register/verify', [RegisterController::class, 'showVerificationForm'])
    ->middleware('guest')
    ->name('register.verify.show');
Route::post('/register/verify', [RegisterController::class, 'verifyCode'])
    ->middleware('guest')
    ->name('register.verify');
Route::post('/register/verify/resend', [RegisterController::class, 'resendCode'])
    ->middleware('guest')
    ->name('register.verify.resend');

// إكمال بيانات الشيف بعد تسجيل الدخول عبر Google
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

// منطقة الشيف - إدارة الوصفات الخاصة
Route::middleware(['auth', 'chef'])->prefix('chef')->name('chef.')->group(function () {
    Route::get('/', [ChefRecipeController::class, 'index'])->name('dashboard');
    Route::get('links', [ChefLinkPageController::class, 'edit'])->name('links.edit');
    Route::put('links', [ChefLinkPageController::class, 'update'])->name('links.update');
    Route::post('links/items', [ChefLinkItemController::class, 'store'])->name('links.items.store');
    Route::put('links/items/{item}', [ChefLinkItemController::class, 'update'])->name('links.items.update');
    Route::delete('links/items/{item}', [ChefLinkItemController::class, 'destroy'])->name('links.items.destroy');
    Route::post('recipes/{recipe}/submit', [ChefRecipeController::class, 'submit'])->name('recipes.submit');
    Route::resource('recipes', ChefRecipeController::class)->except(['show']);
    Route::post('workshops/generate-meeting-link', [ChefWorkshopController::class, 'generateMeetingLink'])->name('workshops.generate-link');
    Route::resource('workshops', ChefWorkshopController::class)->except(['show']);
});

Route::get('/recipe/{recipe:slug}', [App\Http\Controllers\RecipeController::class, 'show'])->name('recipe.show');

// مسار ورشات العمل
Route::get('/workshops', [App\Http\Controllers\WorkshopController::class, 'index'])->name('workshops');
Route::get('/workshops/{workshop:slug}', [App\Http\Controllers\WorkshopController::class, 'show'])->name('workshop.show');
Route::get('/workshops/search', [App\Http\Controllers\WorkshopController::class, 'search'])->name('workshops.search');

// مسار البحث
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');

// مسار الوصفات
Route::get('/recipes', [App\Http\Controllers\RecipeController::class, 'index'])->name('recipes');

// مسار الأدوات
Route::get('/tools', [App\Http\Controllers\ToolsController::class, 'index'])->name('tools');
Route::get('/tools/{tool}', [App\Http\Controllers\ToolsController::class, 'show'])->name('tools.show');

// مسارات الحجوزات - محمية بـ middleware المصادقة
Route::middleware('auth')->group(function () {
    Route::post('/bookings', [App\Http\Controllers\WorkshopBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [App\Http\Controllers\WorkshopBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [App\Http\Controllers\WorkshopBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [App\Http\Controllers\WorkshopBookingController::class, 'cancel'])->name('bookings.cancel');
});

// مسارات الإشعارات - محمية بـ middleware المصادقة
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::get('/api', [App\Http\Controllers\NotificationController::class, 'api'])->name('api');
    Route::post('/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    Route::post('/clear-read', [App\Http\Controllers\NotificationController::class, 'clearRead'])->name('clear-read');
});

// مسارات إدارة الأدوات (للإدمن فقط)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('tools', App\Http\Controllers\Admin\AdminToolsController::class);
    Route::post('tools/{tool}/toggle', [App\Http\Controllers\Admin\AdminToolsController::class, 'toggle'])->name('tools.toggle');
    Route::post('tools/extract-amazon-data', [App\Http\Controllers\Admin\AdminToolsController::class, 'extractAmazonData'])->name('tools.extract-amazon-data');
});

// مسارات الملف الشخصي - محمية بـ middleware المصادقة
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

// مسارات الإدارة - محمية بـ middleware المصادقة والادمن
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // لوحة التحكم
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // صفحة منطقة الإدمن الرئيسية
    Route::get('/admin-area', [App\Http\Controllers\Admin\AdminAreaController::class, 'index'])->name('admin-area');
    
    // إدارة الورشات
    Route::resource('workshops', App\Http\Controllers\Admin\WorkshopController::class);
    Route::post('workshops/{workshop}/toggle-status', [App\Http\Controllers\Admin\WorkshopController::class, 'toggleStatus'])->name('workshops.toggle-status');
    Route::post('workshops/{workshop}/toggle-featured', [App\Http\Controllers\Admin\WorkshopController::class, 'toggleFeatured'])->name('workshops.toggle-featured');
    
    // إدارة الحجوزات
    Route::get('bookings', [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show');
    Route::post('bookings/{booking}/confirm', [App\Http\Controllers\Admin\BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('bookings/{booking}/cancel', [App\Http\Controllers\Admin\BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('bookings/{booking}/update-payment', [App\Http\Controllers\Admin\BookingController::class, 'updatePayment'])->name('bookings.update-payment');
    
    // إضافة الحجوزات يدوياً
    Route::get('bookings/manual/add', [App\Http\Controllers\Admin\ManualBookingController::class, 'index'])->name('bookings.manual');
    Route::post('bookings/manual/add', [App\Http\Controllers\Admin\ManualBookingController::class, 'store'])->name('bookings.manual.store');
    Route::post('bookings/quick-add', [App\Http\Controllers\Admin\ManualBookingController::class, 'quickAdd'])->name('bookings.quick-add');

    // إدارة طلبات الشيفات الجدد
    Route::get('chefs/requests', [App\Http\Controllers\Admin\ChefApprovalController::class, 'index'])->name('chefs.requests');
    Route::post('chefs/{user}/approve', [App\Http\Controllers\Admin\ChefApprovalController::class, 'approve'])->name('chefs.approve');
    Route::post('chefs/{user}/reject', [App\Http\Controllers\Admin\ChefApprovalController::class, 'reject'])->name('chefs.reject');

    // إدارة إعدادات الرؤية
    Route::get('visibility', [App\Http\Controllers\Admin\VisibilityController::class, 'index'])->name('visibility.index');
    Route::put('visibility/{section}', [App\Http\Controllers\Admin\VisibilityController::class, 'update'])->name('visibility.update');
    Route::post('visibility/{section}/toggle', [App\Http\Controllers\Admin\VisibilityController::class, 'toggle'])->name('visibility.toggle');
    Route::get('visibility/config', [App\Http\Controllers\Admin\VisibilityController::class, 'getConfig'])->name('visibility.config');
    Route::post('visibility/clear-cache', [App\Http\Controllers\Admin\VisibilityController::class, 'clearCache'])->name('visibility.clear-cache');
    Route::post('visibility/initialize-defaults', [App\Http\Controllers\Admin\VisibilityController::class, 'initializeDefaults'])->name('visibility.initialize-defaults');
    Route::post('visibility/bulk-update', [App\Http\Controllers\Admin\VisibilityController::class, 'bulkUpdate'])->name('visibility.bulk-update');
});

// مسارات الصفحات العامة
Route::get('/about', [App\Http\Controllers\PageController::class, 'about'])->name('about');
Route::get('/baking-tips', [App\Http\Controllers\PageController::class, 'bakingTips'])->name('baking-tips');
Route::get('/advertising', [App\Http\Controllers\PageController::class, 'advertising'])->name('advertising');
Route::get('/contact', [App\Http\Controllers\PageController::class, 'contact'])->name('contact');

// Admin Routes - محمية بـ middleware الإدارة
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/recipes', [App\Http\Controllers\Admin\RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/create', [App\Http\Controllers\Admin\RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [App\Http\Controllers\Admin\RecipeController::class, 'store'])->name('recipes.store');
    Route::post('/recipes/{recipe}/approve', [App\Http\Controllers\Admin\RecipeController::class, 'approve'])->name('recipes.approve');
    Route::post('/recipes/{recipe}/reject', [App\Http\Controllers\Admin\RecipeController::class, 'reject'])->name('recipes.reject');
    Route::get('/recipes/{recipe}', [App\Http\Controllers\Admin\RecipeController::class, 'show'])->name('recipes.show');
    Route::get('/recipes/{recipe}/edit', [App\Http\Controllers\Admin\RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{recipe}', [App\Http\Controllers\Admin\RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe}', [App\Http\Controllers\Admin\RecipeController::class, 'destroy'])->name('recipes.destroy');
});

// مسار تسجيل الخروج
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح');
})->name('logout');

// مسارات التطوير والاختبار
Route::get('/debug', [App\Http\Controllers\DebugController::class, 'index'])->name('debug.index');

Route::get('/test-stats', [App\Http\Controllers\TestStatsController::class, 'index'])->name('test.stats');
Route::get('/debug-recipe', [App\Http\Controllers\DebugController::class, 'debugRecipe'])->name('debug.recipe');
Route::get('/test-recipe-js', [App\Http\Controllers\DebugController::class, 'testRecipeJs'])->name('test.recipe.js');
Route::get('/test-api', [App\Http\Controllers\DebugController::class, 'testApi'])->name('test.api');
Route::get('/test-recipe-debug', [App\Http\Controllers\DebugController::class, 'testRecipeDebug'])->name('test.recipe.debug');
Route::get('/test-recipe-page', [App\Http\Controllers\DebugController::class, 'testRecipePage'])->name('test.recipe.page');
Route::get('/check-script-loading', [App\Http\Controllers\DebugController::class, 'checkScriptLoading'])->name('check.script.loading');
Route::get('/check-dom-elements', [App\Http\Controllers\DebugController::class, 'checkDomElements'])->name('check.dom.elements');
Route::get('/check-tools', [App\Http\Controllers\DebugController::class, 'checkTools'])->name('check.tools');
Route::get('/test-save', function() { return view('test-save'); })->name('test.save');
Route::get('/test-flip-cards', function() { return view('test-flip-cards'); })->name('test.flip.cards');
Route::get('/test-workshop-simple', [App\Http\Controllers\WorkshopController::class, 'testSimple'])->name('test.workshop.simple');

// Test Amazon extraction without CSRF
Route::post('/test-amazon-extraction', [App\Http\Controllers\Admin\AdminToolsController::class, 'extractAmazonData'])->name('test.amazon.extraction');

// مسارات الاتصال
Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact');
Route::post('/contact/send', [App\Http\Controllers\ContactController::class, 'sendMessage'])->name('contact.send');

