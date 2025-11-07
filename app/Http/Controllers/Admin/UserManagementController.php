<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $role = $request->query('role');
        $chefStatus = $request->query('chef_status');
        $perPage = (int) $request->query('per_page', 20);

        $query = User::query()
            ->withCount([
                'workshops' => function ($builder) {
                    $builder->where('is_active', true);
                },
                'workshopBookings',
            ]);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role && in_array($role, [User::ROLE_ADMIN, User::ROLE_CHEF, User::ROLE_CUSTOMER], true)) {
            $query->where('role', $role);
        }

        if ($chefStatus && in_array($chefStatus, [
            User::CHEF_STATUS_NEEDS_PROFILE,
            User::CHEF_STATUS_PENDING,
            User::CHEF_STATUS_APPROVED,
            User::CHEF_STATUS_REJECTED,
        ], true)) {
            $query->where('chef_status', $chefStatus);
        }

        $users = $query
            ->latest('created_at')
            ->paginate(max(10, min($perPage, 100)))
            ->withQueryString();

        $stats = [
            'total' => User::count(),
            'chefs' => User::where('role', User::ROLE_CHEF)->count(),
            'admins' => User::where('role', User::ROLE_ADMIN)->count(),
            'referrals' => User::where('is_referral_partner', true)->count(),
            'pending_chefs' => User::where('role', User::ROLE_CHEF)
                ->where('chef_status', User::CHEF_STATUS_PENDING)
                ->count(),
        ];

        $recentUsers = User::latest()
            ->limit(6)
            ->get(['id', 'name', 'email', 'role', 'created_at']);

        return view('admin.users.index', [
            'users' => $users,
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'filters' => [
                'search' => $search,
                'role' => $role,
                'chef_status' => $chefStatus,
                'per_page' => $perPage,
            ],
        ]);
    }
}
