<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Workshop;
use App\Models\UserInteraction;

class ProfileController extends Controller
{
    /**
     * عرض صفحة الملف الشخصي للمستخدم
     */
    public function index()
    {
        $user = Auth::user();
        
        // الحصول على الوصفات المحفوظة
        $savedRecipes = UserInteraction::where('user_id', $user->id)
            ->where('is_saved', true)
            ->with('recipe')
            ->get()
            ->pluck('recipe')
            ->filter(); // إزالة القيم الفارغة
        
        // الحصول على الوصفات المصنوعة
        $madeRecipes = UserInteraction::where('user_id', $user->id)
            ->where('is_made', true)
            ->with('recipe')
            ->get()
            ->pluck('recipe')
            ->filter();
        
        // الحصول على جميع الورشات المحجوزة (مؤكدة ومعلقة وملغية)
        $bookedWorkshops = $user->workshopBookings()
            ->with('workshop')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // الحصول على تقييمات الورشات
        $workshopReviews = $user->workshopReviews()
            ->with('workshop')
            ->get();
        
        // إحصائيات المستخدم
        $stats = [
            'saved_recipes_count' => $savedRecipes->count(),
            'made_recipes_count' => $madeRecipes->count(),
            'booked_workshops_count' => $bookedWorkshops->count(),
            'confirmed_workshops_count' => $bookedWorkshops->where('status', 'confirmed')->count(),
            'pending_workshops_count' => $bookedWorkshops->where('status', 'pending')->count(),
            'cancelled_workshops_count' => $bookedWorkshops->where('status', 'cancelled')->count(),
            'reviews_count' => $workshopReviews->count(),
        ];
        
        return view('profile', compact(
            'user',
            'savedRecipes',
            'madeRecipes', 
            'bookedWorkshops',
            'workshopReviews',
            'stats'
        ));
    }
    
    /**
     * تحديث معلومات الملف الشخصي
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        
        return redirect()->route('profile')->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }
}
