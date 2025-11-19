<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReferralControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_enable_fee_waiver_for_pending_chef_partner(): void
    {
        $admin = $this->makeAdminUser();
        $this->actingAs($admin);

        $chef = User::factory()->create([
            'role' => User::ROLE_CHEF,
            'chef_status' => User::CHEF_STATUS_PENDING,
            'is_referral_partner' => false,
            'referral_skip_platform_fee' => false,
        ]);

        $payload = [
            'is_referral_partner' => 1,
            'referral_commission_rate' => 12.5,
            'referral_admin_notes' => 'allow fee waiver',
            'referral_commission_currency' => config('referrals.default_currency', 'USD'),
            'referral_skip_platform_fee' => 1,
        ];

        $response = $this->from(route('admin.referrals.show', $chef))
            ->patch(route('admin.referrals.update', $chef), $payload);

        $response->assertRedirect(route('admin.referrals.show', $chef));

        $chef->refresh();

        $this->assertTrue($chef->is_referral_partner);
        $this->assertTrue((bool) $chef->referral_skip_platform_fee);
    }

    public function test_fee_waiver_is_forced_off_when_partner_flag_is_disabled(): void
    {
        $admin = $this->makeAdminUser();
        $this->actingAs($admin);

        $chef = User::factory()->create([
            'role' => User::ROLE_CHEF,
            'chef_status' => User::CHEF_STATUS_PENDING,
            'is_referral_partner' => true,
            'referral_skip_platform_fee' => true,
        ]);

        $payload = [
            'is_referral_partner' => 0,
            'referral_commission_rate' => 10,
            'referral_admin_notes' => null,
            'referral_commission_currency' => config('referrals.default_currency', 'USD'),
            'referral_skip_platform_fee' => 1,
        ];

        $response = $this->patch(route('admin.referrals.update', $chef), $payload);

        $response->assertRedirect();

        $chef->refresh();

        $this->assertFalse($chef->is_referral_partner);
        $this->assertFalse((bool) $chef->referral_skip_platform_fee);
    }

    protected function makeAdminUser(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_admin' => true,
        ]);
    }
}

