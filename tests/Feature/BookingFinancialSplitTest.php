<?php

namespace Tests\Feature;

use App\Models\BookingRevenueShare;
use App\Models\ReferralCommission;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopBooking;
use App\Models\FinanceInvoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Telescope\Telescope;
use Tests\TestCase;

class BookingFinancialSplitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (class_exists(Telescope::class)) {
            Telescope::stopRecording();
        }
    }

    public function test_paid_booking_generates_chef_and_admin_shares(): void
    {
        config([
            'finance.chef_share_percent' => 70,
            'finance.admin_share_percent' => 30,
        ]);

        $chef = User::factory()->create([
            'role' => User::ROLE_CHEF,
            'chef_status' => User::CHEF_STATUS_APPROVED,
        ]);

        $customer = User::factory()->create();

        $workshop = $this->createWorkshop($chef, ['price' => 100]);

        $booking = WorkshopBooking::create([
            'workshop_id' => $workshop->id,
            'user_id' => $customer->id,
            'status' => 'confirmed',
            'booking_date' => now(),
            'payment_status' => 'pending',
            'payment_amount' => 100,
            'payment_method' => 'manual',
        ]);

        $booking->update(['payment_status' => 'paid']);

        $booking->refresh();

        $this->assertSame(WorkshopBooking::FINANCIAL_STATUS_DISTRIBUTED, $booking->financial_status);
        $this->assertNotNull($booking->financial_split_at);

        $chefShare = $booking->revenueShares()
            ->where('recipient_type', BookingRevenueShare::TYPE_CHEF)
            ->first();

        $adminShare = $booking->revenueShares()
            ->where('recipient_type', BookingRevenueShare::TYPE_ADMIN)
            ->first();

        $this->assertNotNull($chefShare);
        $this->assertNotNull($adminShare);

        $this->assertEquals(70.0, (float) $chefShare->amount);
        $this->assertEquals(30.0, (float) $adminShare->amount);

        $this->assertDatabaseHas('finance_invoices', [
            'workshop_booking_id' => $booking->id,
            'status' => FinanceInvoice::STATUS_PAID,
        ]);
    }

    public function test_paid_booking_with_referral_partner_distributes_three_shares(): void
    {
        config([
            'finance.chef_share_percent' => 70,
            'finance.admin_share_percent' => 30,
            'referrals.default_rate' => 5,
        ]);

        $partner = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_referral_partner' => true,
            'referral_commission_rate' => 5,
            'referral_commission_currency' => 'USD',
        ]);

        $chef = User::factory()->create([
            'role' => User::ROLE_CHEF,
            'chef_status' => User::CHEF_STATUS_APPROVED,
            'referrer_id' => $partner->id,
        ]);

        $customer = User::factory()->create();
        $workshop = $this->createWorkshop($chef, ['price' => 200]);

        $booking = WorkshopBooking::create([
            'workshop_id' => $workshop->id,
            'user_id' => $customer->id,
            'status' => 'confirmed',
            'booking_date' => now(),
            'payment_status' => 'pending',
            'payment_amount' => 200,
            'payment_method' => 'manual',
        ]);

        $booking->update(['payment_status' => 'paid']);
        $booking->refresh();

        $this->assertSame(WorkshopBooking::FINANCIAL_STATUS_DISTRIBUTED, $booking->financial_status);

        $shares = $booking->revenueShares()
            ->where('status', BookingRevenueShare::STATUS_DISTRIBUTED)
            ->get()
            ->keyBy('recipient_type');

        $this->assertCount(3, $shares);

        $this->assertEquals(140.0, (float) $shares[BookingRevenueShare::TYPE_CHEF]->amount);
        $this->assertEquals(10.0, (float) $shares[BookingRevenueShare::TYPE_PARTNER]->amount);
        $this->assertEquals(50.0, (float) $shares[BookingRevenueShare::TYPE_ADMIN]->amount);

        $this->assertDatabaseHas('referral_commissions', [
            'workshop_booking_id' => $booking->id,
            'status' => ReferralCommission::STATUS_READY,
            'commission_amount' => 10.00,
        ]);

        $this->assertDatabaseHas('finance_invoices', [
            'workshop_booking_id' => $booking->id,
            'status' => FinanceInvoice::STATUS_PAID,
        ]);
    }

    public function test_refunded_booking_voids_existing_shares(): void
    {
        $chef = User::factory()->create([
            'role' => User::ROLE_CHEF,
            'chef_status' => User::CHEF_STATUS_APPROVED,
        ]);

        $customer = User::factory()->create();
        $workshop = $this->createWorkshop($chef, ['price' => 150]);

        $booking = WorkshopBooking::create([
            'workshop_id' => $workshop->id,
            'user_id' => $customer->id,
            'status' => 'confirmed',
            'booking_date' => now(),
            'payment_status' => 'pending',
            'payment_amount' => 150,
            'payment_method' => 'card',
        ]);

        $booking->update(['payment_status' => 'paid']);
        $booking->refresh();

        $this->assertSame(WorkshopBooking::FINANCIAL_STATUS_DISTRIBUTED, $booking->financial_status);
        $this->assertSame(FinanceInvoice::STATUS_PAID, $booking->invoice->status);

        $booking->update(['payment_status' => 'refunded']);
        $booking->load('invoice');

        $this->assertSame(WorkshopBooking::FINANCIAL_STATUS_VOID, $booking->financial_status);
        $this->assertNull($booking->financial_split_at);
        $this->assertSame(FinanceInvoice::STATUS_VOID, $booking->invoice->status);

        $this->assertDatabaseHas('booking_revenue_shares', [
            'workshop_booking_id' => $booking->id,
            'recipient_type' => BookingRevenueShare::TYPE_CHEF,
            'status' => BookingRevenueShare::STATUS_CANCELLED,
        ]);

        $this->assertDatabaseHas('booking_revenue_shares', [
            'workshop_booking_id' => $booking->id,
            'recipient_type' => BookingRevenueShare::TYPE_ADMIN,
            'status' => BookingRevenueShare::STATUS_CANCELLED,
        ]);
    }

    public function test_booking_without_assigned_chef_is_marked_void(): void
    {
        $placeholderChef = User::factory()->create([
            'role' => User::ROLE_CHEF,
            'chef_status' => User::CHEF_STATUS_APPROVED,
        ]);

        $customer = User::factory()->create();

        $workshop = $this->createWorkshop($placeholderChef, [
            'user_id' => null,
            'instructor' => 'Guest Chef',
        ]);

        $booking = WorkshopBooking::create([
            'workshop_id' => $workshop->id,
            'user_id' => $customer->id,
            'status' => 'confirmed',
            'booking_date' => now(),
            'payment_status' => 'pending',
            'payment_amount' => 180,
            'payment_method' => 'card',
        ]);

        $booking->update(['payment_status' => 'paid']);
        $booking->refresh();

        $this->assertSame(WorkshopBooking::FINANCIAL_STATUS_VOID, $booking->financial_status);
        $this->assertNull($booking->financial_split_at);
        $this->assertSame(0, $booking->revenueShares()->count());
        $this->assertSame(FinanceInvoice::STATUS_PAID, $booking->invoice->status);
    }

    private function createWorkshop(User $chef, array $overrides = []): Workshop
    {
        $defaults = [
            'user_id' => $chef->id,
            'title' => 'ورشة اختبار',
            'slug' => Str::slug('workshop-' . Str::uuid()),
            'description' => 'تفاصيل الورشة للاختبار.',
            'content' => 'محتوى تفصيلي',
            'instructor' => $chef->name,
            'category' => 'Cooking',
            'level' => 'Beginner',
            'duration' => 90,
            'max_participants' => 20,
            'price' => 100,
            'currency' => 'USD',
            'location' => 'Amman',
            'address' => 'Amman',
            'start_date' => now()->addWeek(),
            'end_date' => now()->addWeek()->addHours(2),
            'registration_deadline' => now()->addDays(3),
            'is_online' => true,
            'meeting_link' => 'https://example.com',
            'meeting_provider' => 'manual',
            'requirements' => 'Laptop',
            'what_you_will_learn' => 'Testing skills',
            'materials_needed' => 'Notebook',
            'is_active' => true,
            'images' => [],
        ];

        return Workshop::create(array_merge($defaults, $overrides));
    }
}
