<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_view_their_bookings_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/bookings');

        $response->assertStatus(200);
    }
}
