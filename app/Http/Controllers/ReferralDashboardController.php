<?php

namespace App\Http\Controllers;

use App\Models\ReferralCommission;
use App\Models\User;
use Illuminate\Http\Request;

class ReferralDashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $partner */
        $partner = $request->user();

        $referredUsersCount = $partner->referredUsers()->count();
        $referredChefsCount = $partner->referredUsers()
            ->where('role', User::ROLE_CHEF)
            ->count();

        $activeChefsCount = $partner->referredUsers()
            ->where('role', User::ROLE_CHEF)
            ->whereHas('workshops', function ($query) {
                $query->where('is_active', true);
            })
            ->count();

        $commissionBase = ReferralCommission::query()
            ->where('referral_partner_id', $partner->id);

        $readyAmount = (float) (clone $commissionBase)->where('status', ReferralCommission::STATUS_READY)->sum('commission_amount');
        $paidAmount = (float) (clone $commissionBase)->where('status', ReferralCommission::STATUS_PAID)->sum('commission_amount');
        $cancelledAmount = (float) (clone $commissionBase)->where('status', ReferralCommission::STATUS_CANCELLED)->sum('commission_amount');
        $readyCount = (clone $commissionBase)->where('status', ReferralCommission::STATUS_READY)->count();
        $totalCommissions = (clone $commissionBase)->count();

        $commissions = ReferralCommission::with([
                'workshop:id,title,slug,start_date',
                'referredUser:id,name',
                'participant:id,name',
            ])
            ->where('referral_partner_id', $partner->id)
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $referredChefs = $partner->referredUsers()
            ->select('id', 'name', 'email', 'role', 'chef_status', 'created_at')
            ->where('role', User::ROLE_CHEF)
            ->withCount([
                'workshops as workshops_count',
                'generatedReferralCommissions as referral_commissions_count',
            ])
            ->withSum('generatedReferralCommissions as referral_commissions_total', 'commission_amount')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentReferrals = $partner->referredUsers()
            ->select('id', 'name', 'email', 'role', 'created_at')
            ->latest()
            ->limit(6)
            ->get();

        $referralLink = $partner->referral_link ?? route('register', ['ref' => $partner->ensureReferralCode()]);

        return view('referrals.dashboard', [
            'partner' => $partner,
            'referralLink' => $referralLink,
            'referredUsersCount' => $referredUsersCount,
            'referredChefsCount' => $referredChefsCount,
            'activeChefsCount' => $activeChefsCount,
            'readyAmount' => $readyAmount,
            'paidAmount' => $paidAmount,
            'cancelledAmount' => $cancelledAmount,
            'readyCount' => $readyCount,
            'totalCommissions' => $totalCommissions,
            'commissions' => $commissions,
            'referredChefs' => $referredChefs,
            'recentReferrals' => $recentReferrals,
        ]);
    }
}
