<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopBooking;
use App\Services\WorkshopLinkSecurityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ParticipantWorkshopLinkSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_unsigned_join_request_redirects_to_signed_url(): void
    {
        $user = User::factory()->create();
        $booking = $this->createConfirmedOnlineBooking($user);

        $response = $this
            ->actingAs($user)
            ->get(route('bookings.join', ['booking' => $booking->public_code]));

        $response->assertRedirect();

        $redirectLocation = $response->headers->get('Location');

        $this->assertNotNull($redirectLocation);
        $this->assertStringContainsString('signature=', $redirectLocation);
    }

    public function test_join_request_with_invalid_signature_is_rejected(): void
    {
        $user = User::factory()->create();
        $booking = $this->createConfirmedOnlineBooking($user);
        $signedUrl = app(WorkshopLinkSecurityService::class)->makeParticipantJoinUrl($booking);

        $tamperedUrl = $signedUrl . 'tampered';

        $this->actingAs($user)
            ->get($tamperedUrl)
            ->assertForbidden();
    }

    public function test_status_endpoint_requires_signed_url(): void
    {
        $user = User::factory()->create();
        $booking = $this->createConfirmedOnlineBooking($user);

        $this->actingAs($user)
            ->get(route('bookings.status', ['booking' => $booking->public_code]))
            ->assertForbidden();

        $signedStatusUrl = app(WorkshopLinkSecurityService::class)->makeParticipantStatusUrl($booking);

        $this->actingAs($user)
            ->getJson($signedStatusUrl)
            ->assertOk();
    }

    protected function createConfirmedOnlineBooking(User $user): WorkshopBooking
    {
        $workshop = Workshop::create(array_merge(
            $this->baseWorkshopAttributes($user),
            [
                'is_online' => true,
                'meeting_link' => 'https://meet.example.com/secure-room',
                'start_date' => Carbon::now()->addDay(),
                'end_date' => Carbon::now()->addDay()->addHours(2),
                'registration_deadline' => Carbon::now()->addHours(12),
            ],
        ));

        return WorkshopBooking::create([
            'workshop_id' => $workshop->id,
            'user_id' => $user->id,
            'status' => 'confirmed',
            'booking_date' => Carbon::now(),
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'payment_amount' => 150.00,
            'confirmed_at' => Carbon::now(),
        ]);
    }

    protected function baseWorkshopAttributes(User $user): array
    {
        return [
            'user_id' => $user->id,
            'title' => 'Secure Cooking Workshop',
            'description' => 'Learn safe workshop practices.',
            'content' => 'Extensive content goes here.',
            'instructor' => 'Chef Secure',
            'instructor_bio' => 'Experienced security-conscious chef.',
            'category' => 'Cooking',
            'level' => 'beginner',
            'duration' => 120,
            'max_participants' => 30,
            'price' => 150.00,
            'currency' => 'SAR',
            'image' => null,
            'images' => null,
            'location' => 'Online',
            'address' => 'Remote',
            'latitude' => null,
            'longitude' => null,
            'requirements' => 'Internet connection',
            'what_you_will_learn' => 'Kitchen security tips',
            'materials_needed' => 'Laptop',
            'is_active' => true,
            'is_featured' => false,
        ];
    }
}
