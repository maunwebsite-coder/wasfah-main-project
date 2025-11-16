<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralCommission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ReferralController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $partnersQuery = User::query()
            ->where('is_referral_partner', true)
            ->withCount([
                'referredUsers as referred_users_count',
                'referralCommissions as commissions_count',
            ])
            ->withSum(['referralCommissions as pending_commission_total' => function ($query) {
                $query->where('status', ReferralCommission::STATUS_READY);
            }], 'commission_amount')
            ->withSum(['referralCommissions as paid_commission_total' => function ($query) {
                $query->where('status', ReferralCommission::STATUS_PAID);
            }], 'commission_amount')
            ->latest('referral_partner_since_at');

        if ($search !== '') {
            $partnersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        $partners = $partnersQuery->paginate(15)->withQueryString();

        $stats = [
            'partners_count' => User::where('is_referral_partner', true)->count(),
            'new_partners_this_month' => User::where('is_referral_partner', true)
                ->where('referral_partner_since_at', '>=', now()->startOfMonth())
                ->count(),
            'ready_amount' => ReferralCommission::where('status', ReferralCommission::STATUS_READY)->sum('commission_amount'),
            'paid_amount' => ReferralCommission::where('status', ReferralCommission::STATUS_PAID)->sum('commission_amount'),
        ];

        $topPartners = User::where('is_referral_partner', true)
            ->withSum('referralCommissions as lifetime_commission_total', 'commission_amount')
            ->orderByDesc('lifetime_commission_total')
            ->limit(5)
            ->get(['id', 'name', 'email', 'referral_code', 'referral_commission_currency']);

        $currencyOptions = config('referrals.currencies', []);

        return view('admin.referrals.index', compact(
            'partners',
            'stats',
            'search',
            'topPartners',
            'currencyOptions',
        ));
    }

    public function activate(Request $request): RedirectResponse
    {
        $supportedCurrencies = array_keys(config('referrals.currencies', []));
        if (empty($supportedCurrencies)) {
            $supportedCurrencies = [config('referrals.default_currency', 'USD')];
        }

        $data = $request->validate([
            'user_lookup' => ['required', 'string'],
            'referral_commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'referral_commission_currency' => ['required', Rule::in($supportedCurrencies)],
        ]);

        $lookup = trim($data['user_lookup']);

        $userQuery = User::query()->where('email', $lookup);

        if (is_numeric($lookup)) {
            $userQuery->orWhere('id', (int) $lookup);
        }

        $user = $userQuery->first();

        if (!$user) {
            return back()->withErrors([
                'user_lookup' => 'لم يتم العثور على مستخدم يطابق البيانات المدخلة.',
            ])->withInput();
        }

        $user->forceFill([
            'is_referral_partner' => true,
            'referral_commission_rate' => $data['referral_commission_rate']
                ?? ($user->referral_commission_rate ?? config('referrals.default_rate')),
            'referral_commission_currency' => $data['referral_commission_currency'] ?? config('referrals.default_currency', 'USD'),
        ]);

        $user->ensureReferralCode();
        $user->save();

        return redirect()
            ->route('admin.referrals.show', $user)
            ->with('success', 'تم تفعيل المستخدم كشريك إحالات بنجاح.');
    }

    public function show(User $user, Request $request): View
    {
        $commissionBase = ReferralCommission::where('referral_partner_id', $user->id);

        $commissionTotals = [
            'ready' => (clone $commissionBase)->where('status', ReferralCommission::STATUS_READY)->sum('commission_amount'),
            'paid' => (clone $commissionBase)->where('status', ReferralCommission::STATUS_PAID)->sum('commission_amount'),
            'cancelled' => (clone $commissionBase)->where('status', ReferralCommission::STATUS_CANCELLED)->sum('commission_amount'),
            'count' => (clone $commissionBase)->count(),
        ];

        $recentReferrals = $user->referredUsers()
            ->latest()
            ->limit(8)
            ->get(['id', 'name', 'email', 'role', 'created_at']);

        $commissions = ReferralCommission::with([
                'workshop:id,title,slug,start_date',
                'referredUser:id,name',
                'participant:id,name,email',
            ])
            ->where('referral_partner_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $referredChefs = $user->referredUsers()
            ->where('role', User::ROLE_CHEF)
            ->withCount([
                'workshops as workshops_count',
                'generatedReferralCommissions as generated_commissions_count',
            ])
            ->withSum('generatedReferralCommissions as generated_commissions_total', 'commission_amount')
            ->orderByDesc('generated_commissions_total')
            ->limit(6)
            ->get(['id', 'name', 'email', 'chef_status']);

        $currencyOptions = config('referrals.currencies', []);

        return view('admin.referrals.show', compact(
            'user',
            'commissionTotals',
            'recentReferrals',
            'commissions',
            'referredChefs',
            'currencyOptions',
        ));
    }

    public function updateCommissionStatus(User $user, ReferralCommission $commission, Request $request): RedirectResponse
    {
        if ((int) $commission->referral_partner_id !== (int) $user->id) {
            abort(404);
        }

        if ($commission->status === ReferralCommission::STATUS_CANCELLED) {
            return back()->with('error', 'لا يمكن تحديث حالة عمولة تم إلغاؤها.');
        }

        $data = $request->validate([
            'status' => ['required', Rule::in([ReferralCommission::STATUS_READY, ReferralCommission::STATUS_PAID])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $targetStatus = $data['status'];
        $notes = array_key_exists('notes', $data) ? trim((string) $data['notes']) : null;
        $notes = $notes === '' ? null : $notes;

        if ($commission->status === $targetStatus) {
            if ($commission->notes !== $notes) {
                $commission->notes = $notes;
                $commission->save();

                return back()->with('success', 'تم تحديث ملاحظات العمولة.');
            }

            return back()->with('success', 'الحالة مختارة مسبقاً لهذا السجل.');
        }

        if ($targetStatus === ReferralCommission::STATUS_PAID) {
            $commission->markPaid($notes);
            $message = 'تم تعليم العمولة كمحوّلة/مدفوعة.';
        } else {
            $commission->status = ReferralCommission::STATUS_READY;
            $commission->paid_at = null;
            $commission->notes = $notes;
            $commission->save();
            $message = 'تمت إعادة العمولة إلى حالة جاهزة للتحويل.';
        }

        return back()->with('success', $message);
    }

    public function update(User $user, Request $request): RedirectResponse
    {
        $supportedCurrencies = array_keys(config('referrals.currencies', []));
        if (empty($supportedCurrencies)) {
            $supportedCurrencies = [config('referrals.default_currency', 'USD')];
        }

        $data = $request->validate([
            'is_referral_partner' => ['required', 'boolean'],
            'referral_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'referral_admin_notes' => ['nullable', 'string', 'max:2000'],
            'referral_commission_currency' => ['required', Rule::in($supportedCurrencies)],
        ]);

        $user->fill($data);

        if ($data['is_referral_partner']) {
            $user->ensureReferralCode();
        }

        $user->save();

        return back()->with('success', 'تم تحديث بيانات برنامج الإحالات لهذا المستخدم.');
    }
}
