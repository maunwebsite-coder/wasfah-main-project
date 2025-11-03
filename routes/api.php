<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\Api\UserInteractionController;

// ✅ مسارات الوصفات
Route::get('/lastrecipes', [RecipeController::class, 'apiIndex']);
Route::get('/recipes/{recipe:slug}', [RecipeController::class, 'apiShow']);

// ✅ مسارات التفاعلات (تحتاج تسجيل دخول بـ Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/interactions', [UserInteractionController::class, 'store']);
    Route::post('/interactions/remove', [UserInteractionController::class, 'removeRating']);
    Route::get('/interactions/{recipe_id}', [UserInteractionController::class, 'recipeInteractions']);
    Route::get('/interactions/me', [UserInteractionController::class, 'userInteractions']);
    
    // ✅ استرجاع بيانات المستخدم الحالي
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'is_admin' => $user->isAdmin(),
            'role' => $user->role,
            'chef_status' => $user->chef_status,
            'is_chef' => $user->isChef(),
        ]);
    });
});

// API routes للورشات
Route::get('/workshops', [App\Http\Controllers\WorkshopController::class, 'apiIndex']);

// API routes للبحث
Route::get('/search', [App\Http\Controllers\SearchController::class, 'api']);
