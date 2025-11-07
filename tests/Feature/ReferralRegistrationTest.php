<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReferralRegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_assigns_referrer_when_registering_via_referral_link(): void
    {
        config(['services.registration.require_email_verification' => false]);

        $partner = User::factory()->create([
            'is_referral_partner' => true,
            'referral_commission_rate' => 5,
        ]);

        $this->get('/register?ref=' . $partner->ensureReferralCode())
            ->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'Test Chef',
            'email' => 'chef@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => User::ROLE_CHEF,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'chef@example.com',
            'referrer_id' => $partner->id,
        ]);
    }
}
