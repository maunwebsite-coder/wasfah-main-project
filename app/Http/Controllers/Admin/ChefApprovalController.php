<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChefApprovalController extends Controller
{
    /**
     * Display chef onboarding requests with optional status filtering.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', User::CHEF_STATUS_PENDING);
        $allowedStatuses = [
            'all',
            User::CHEF_STATUS_PENDING,
            User::CHEF_STATUS_NEEDS_PROFILE,
            User::CHEF_STATUS_APPROVED,
            User::CHEF_STATUS_REJECTED,
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = User::CHEF_STATUS_PENDING;
        }

        $query = User::query()->where('role', User::ROLE_CHEF);

        if ($status !== 'all') {
            $query->where('chef_status', $status);
        }

        $requests = $query
            ->orderByRaw(
                "FIELD(chef_status, ?, ?, ?, ?) ASC",
                [
                    User::CHEF_STATUS_PENDING,
                    User::CHEF_STATUS_NEEDS_PROFILE,
                    User::CHEF_STATUS_REJECTED,
                    User::CHEF_STATUS_APPROVED,
                ]
            )
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $statusCounts = [
            'pending' => User::query()
                ->where('role', User::ROLE_CHEF)
                ->where('chef_status', User::CHEF_STATUS_PENDING)
                ->count(),
            'needs_profile' => User::query()
                ->where('role', User::ROLE_CHEF)
                ->where('chef_status', User::CHEF_STATUS_NEEDS_PROFILE)
                ->count(),
            'approved' => User::query()
                ->where('role', User::ROLE_CHEF)
                ->where('chef_status', User::CHEF_STATUS_APPROVED)
                ->count(),
            'rejected' => User::query()
                ->where('role', User::ROLE_CHEF)
                ->where('chef_status', User::CHEF_STATUS_REJECTED)
                ->count(),
        ];

        $statusCounts['all'] = array_sum($statusCounts);

        return view('admin.chefs.requests', [
            'requests' => $requests,
            'status' => $status,
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * Approve a chef request.
     */
    public function approve(User $user): RedirectResponse
    {
        $this->ensureChefRole($user);

        $user->update([
            'chef_status' => User::CHEF_STATUS_APPROVED,
            'chef_approved_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'تم اعتماد الشيف بنجاح وإتاحة صلاحياته في المنصة.');
    }

    /**
     * Reject a chef request.
     */
    public function reject(Request $request, User $user): RedirectResponse
    {
        $this->ensureChefRole($user);

        $user->update([
            'chef_status' => User::CHEF_STATUS_REJECTED,
            'chef_approved_at' => null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'تم رفض طلب الشيف. يمكنك التواصل معه لإبلاغه بالملاحظات.');
    }

    /**
     * Ensure the provided user belongs to the chef role.
     */
    private function ensureChefRole(User $user): void
    {
        if ($user->role !== User::ROLE_CHEF) {
            abort(404);
        }
    }
}

