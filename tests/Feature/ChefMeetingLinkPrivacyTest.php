<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChefMeetingLinkPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_chef_cannot_generate_raw_meeting_link(): void
    {
        $chef = User::factory()->create([
            'role' => User::ROLE_CHEF,
            'chef_status' => User::CHEF_STATUS_APPROVED,
            'is_admin' => false,
        ]);

        $response = $this
            ->actingAs($chef)
            ->postJson(route('chef.workshops.generate-link'), [
                'title' => 'ورشة سرية',
                'start_date' => now()->addDay()->toDateTimeString(),
            ]);

        $response->assertForbidden();
    }

    public function test_admin_can_generate_raw_meeting_link(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_admin' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->postJson(route('chef.workshops.generate-link'), [
                'title' => 'جلسة خاصة',
                'start_date' => now()->addDay()->toDateTimeString(),
            ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'meeting_link',
                'room',
                'passcode',
            ]);
    }
}
