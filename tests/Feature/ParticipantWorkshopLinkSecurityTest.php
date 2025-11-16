<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopBooking;
use App\Services\GoogleMeetService;
use App\Services\WorkshopLinkSecurityService;
use App\Services\WorkshopMeetingAttendeeSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class ParticipantWorkshopLinkSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

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

    public function test_launch_request_requires_signed_url(): void
    {
        $user = User::factory()->create();
        $booking = $this->createConfirmedOnlineBooking($user);

        $response = $this
            ->actingAs($user)
            ->get(route('bookings.launch', ['booking' => $booking->public_code]));

        $response->assertRedirect();

        $redirectLocation = $response->headers->get('Location');

        $this->assertNotNull($redirectLocation);
        $this->assertStringContainsString('signature=', $redirectLocation);
    }

    public function test_launch_request_with_invalid_signature_is_rejected(): void
    {
        $user = User::factory()->create();
        $booking = $this->createConfirmedOnlineBooking($user);
        $signedLaunchUrl = app(WorkshopLinkSecurityService::class)->makeParticipantLaunchUrl($booking);
        $tamperedUrl = $signedLaunchUrl . 'extra';

        $this->actingAs($user)
            ->get($tamperedUrl)
            ->assertForbidden();
    }

    public function test_join_page_does_not_render_raw_meeting_link(): void
    {
        $user = User::factory()->create();
        $booking = $this->createConfirmedOnlineBooking($user);
        $signedUrl = app(WorkshopLinkSecurityService::class)->makeParticipantJoinUrl($booking);

        $response = $this->actingAs($user)->get($signedUrl);

        $response->assertOk();
        $response->assertDontSee('https://meet.example.com/secure-room');
    }

    public function test_launch_redirects_through_account_chooser_when_attendee_is_whitelisted(): void
    {
        $user = User::factory()->create([
            'google_email' => 'participant@wasfah.com',
        ]);

        $googleService = Mockery::mock(GoogleMeetService::class);
        $googleService->shouldReceive('isEnabled')->andReturnTrue();
        $googleService->shouldReceive('syncEventAttendees')->zeroOrMoreTimes();
        $googleService->shouldReceive('eventHasAttendee')
            ->once()
            ->with('event-123', 'participant@wasfah.com', 'calendar@wasfah.com')
            ->andReturnTrue();

        app()->instance(GoogleMeetService::class, $googleService);
        $attendeeSync = $this->fakeAttendeeSyncService();
        app()->instance(WorkshopMeetingAttendeeSyncService::class, $attendeeSync);

        $booking = $this->createConfirmedOnlineBooking($user, [
            'meeting_link' => 'https://meet.google.com/abc-defg-hij',
            'meeting_provider' => 'google_meet',
            'meeting_event_id' => 'event-123',
            'meeting_calendar_id' => 'calendar@wasfah.com',
            'meeting_started_at' => Carbon::now(),
        ]);

        $initialSyncCount = $attendeeSync->syncCount;

        $signedLaunchUrl = app(WorkshopLinkSecurityService::class)->makeParticipantLaunchUrl($booking);

        $response = $this->actingAs($user)->get($signedLaunchUrl);

        $response->assertRedirect();
        $location = $response->headers->get('Location');

        $this->assertNotNull($location);
        $this->assertStringContainsString('accounts.google.com/AccountChooser', $location);
        $this->assertStringContainsString('Email=participant%40wasfah.com', $location);
        $this->assertStringContainsString('continue=https%3A%2F%2Fmeet.google.com%2Fabc-defg-hij', $location);
        $this->assertSame($initialSyncCount, $attendeeSync->syncCount);
    }

    public function test_launch_rejects_unlisted_attendees_even_after_resync(): void
    {
        $user = User::factory()->create([
            'google_email' => 'member@wasfah.com',
        ]);

        $googleService = Mockery::mock(GoogleMeetService::class);
        $googleService->shouldReceive('isEnabled')->andReturnTrue();
        $googleService->shouldReceive('syncEventAttendees')->zeroOrMoreTimes();
        $googleService->shouldReceive('eventHasAttendee')
            ->twice()
            ->with('event-456', 'member@wasfah.com', 'calendar@wasfah.com')
            ->andReturn(false, false);
        $googleService->shouldReceive('ensureAttendeePresent')
            ->once()
            ->with(
                'event-456',
                Mockery::on(function ($payload) {
                    return isset($payload['email']) && $payload['email'] === 'member@wasfah.com';
                }),
                'calendar@wasfah.com'
            )
            ->andReturnFalse();

        app()->instance(GoogleMeetService::class, $googleService);
        $attendeeSync = $this->fakeAttendeeSyncService();
        app()->instance(WorkshopMeetingAttendeeSyncService::class, $attendeeSync);

        $booking = $this->createConfirmedOnlineBooking($user, [
            'meeting_link' => 'https://meet.google.com/xyz-abcd-efg',
            'meeting_provider' => 'google_meet',
            'meeting_event_id' => 'event-456',
            'meeting_calendar_id' => 'calendar@wasfah.com',
            'meeting_started_at' => Carbon::now(),
        ]);

        $initialSyncCount = $attendeeSync->syncCount;

        $signedLaunchUrl = app(WorkshopLinkSecurityService::class)->makeParticipantLaunchUrl($booking);

        $response = $this->actingAs($user)->get($signedLaunchUrl);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $location = $response->headers->get('Location');
        $this->assertNotNull($location);
        $this->assertStringContainsString('/bookings/' . $booking->public_code . '/join', $location);
        $this->assertStringContainsString('signature=', $location);
        $this->assertSame($initialSyncCount + 1, $attendeeSync->syncCount);
    }

    protected function createConfirmedOnlineBooking(User $user, array $workshopOverrides = []): WorkshopBooking
    {
        $workshop = Workshop::create(array_merge(
            $this->baseWorkshopAttributes($user),
            [
                'is_online' => true,
                'meeting_link' => 'https://meet.example.com/secure-room',
                'meeting_provider' => 'google_meet',
                'start_date' => Carbon::now()->addDay(),
                'end_date' => Carbon::now()->addDay()->addHours(2),
                'registration_deadline' => Carbon::now()->addHours(12),
            ],
            $workshopOverrides,
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
            'currency' => 'USD',
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

    protected function fakeAttendeeSyncService(): WorkshopMeetingAttendeeSyncService
    {
        return new class extends WorkshopMeetingAttendeeSyncService {
            public int $syncCount = 0;

            public function __construct()
            {
            }

            public function sync(Workshop $workshop): void
            {
                $this->syncCount++;
            }
        };
    }
}
