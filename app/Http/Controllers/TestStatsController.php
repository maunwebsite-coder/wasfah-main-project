<?php

namespace App\Http\Controllers;

use App\Models\WorkshopBooking;
use App\Models\Workshop;
use App\Models\User;
use Illuminate\Http\Request;

class TestStatsController extends Controller
{
    public function index()
    {
        // إحصائيات الحجوزات
        $totalBookings = WorkshopBooking::count();
        $confirmedBookings = WorkshopBooking::where('status', 'confirmed')->count();
        $pendingBookings = WorkshopBooking::where('status', 'pending')->count();
        $cancelledBookings = WorkshopBooking::where('status', 'cancelled')->count();
        $paidBookings = WorkshopBooking::where('payment_status', 'paid')->count();

        // إحصائيات الإيرادات
        $totalRevenue = WorkshopBooking::where('status', 'confirmed')
            ->join('workshops', 'workshop_bookings.workshop_id', '=', 'workshops.id')
            ->sum('workshops.price');

        $monthlyRevenue = WorkshopBooking::where('status', 'confirmed')
            ->whereMonth('workshop_bookings.created_at', now()->month)
            ->whereYear('workshop_bookings.created_at', now()->year)
            ->join('workshops', 'workshop_bookings.workshop_id', '=', 'workshops.id')
            ->sum('workshops.price');

        // آخر الحجوزات
        $recentBookings = WorkshopBooking::with(['workshop', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('test-stats', compact(
            'totalBookings',
            'confirmedBookings', 
            'pendingBookings',
            'cancelledBookings',
            'paidBookings',
            'totalRevenue',
            'monthlyRevenue',
            'recentBookings'
        ));
    }
}
