<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\WorkshopBooking;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserMeetingController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $hostWorkshopsQuery = Workshop::query()
            ->with(['chef'])
            ->online()
            ->whereNotNull('meeting_link')
            ->where('is_active', true);

        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();

        if (!$isAdmin) {
            $hostWorkshopsQuery->where('user_id', $user->id);
        }

        $hostWorkshops = $hostWorkshopsQuery
            ->orderByRaw("CASE WHEN meeting_started_at IS NOT NULL THEN 0 ELSE 1 END")
            ->orderByDesc('meeting_started_at')
            ->orderBy('start_date')
            ->limit(40)
            ->get();

        $participantBookings = WorkshopBooking::query()
            ->with(['workshop.chef'])
            ->where('user_id', $user->id)
            ->whereHas('workshop', function ($query) {
                $query->online()->where('is_active', true);
            })
            ->orderByRaw("CASE status WHEN 'confirmed' THEN 0 WHEN 'pending' THEN 1 ELSE 2 END")
            ->orderByDesc('confirmed_at')
            ->orderByDesc('created_at')
            ->limit(80)
            ->get();

        return view('meetings.index', [
            'user' => $user,
            'isAdmin' => $isAdmin,
            'hostWorkshops' => $hostWorkshops,
            'participantBookings' => $participantBookings,
        ]);
    }
}
