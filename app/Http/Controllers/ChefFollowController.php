<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChefFollowController extends Controller
{
    /**
     * تابع الشيف المحدد.
     */
    public function store(Request $request, User $chef): JsonResponse
    {
        $user = $request->user();

        abort_if(! $chef->isChef(), 404);

        if ($user->id === $chef->id) {
            return response()->json([
                'message' => __('chef.follow.errors.self_follow'),
            ], 422);
        }

        $user->followingChefs()->syncWithoutDetaching([$chef->id]);
        $chef->loadCount('followers');

        return response()->json([
            'is_following' => true,
            'followers_count' => (int) $chef->followers_count,
        ]);
    }

    /**
     * ألغ متابعة الشيف المحدد.
     */
    public function destroy(Request $request, User $chef): JsonResponse
    {
        $user = $request->user();

        abort_if(! $chef->isChef(), 404);

        $user->followingChefs()->detach($chef->id);
        $chef->loadCount('followers');

        return response()->json([
            'is_following' => false,
            'followers_count' => (int) $chef->followers_count,
        ]);
    }
}
