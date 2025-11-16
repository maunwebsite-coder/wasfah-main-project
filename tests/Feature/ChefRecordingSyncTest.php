<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workshop;
use App\Services\GoogleDriveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class ChefRecordingSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_chef_can_sync_recording_link(): void
    {
        $chef = $this->createChef();
        $workshop = $this->createWorkshop($chef);
        $meetingCode = $workshop->meeting_code ?: Workshop::extractMeetingCode($workshop->meeting_link);

        $this->mock(GoogleDriveService::class, function (MockInterface $mock) use ($meetingCode) {
            $mock->shouldReceive('isEnabled')->andReturn(true);
            $mock->shouldReceive('findRecordingUrl')
                ->once()
                ->with($meetingCode)
                ->andReturn('https://drive.google.com/file/d/abc123/preview');
        });

        $response = $this->actingAs($chef)->postJson(route('chef.workshops.recording', $workshop));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'recording_url' => 'https://drive.google.com/file/d/abc123/preview',
                'updated' => true,
            ]);

        $this->assertEquals('https://drive.google.com/file/d/abc123/preview', $workshop->fresh()->recording_url);
    }

    public function test_sync_returns_error_when_integration_disabled(): void
    {
        $chef = $this->createChef();
        $workshop = $this->createWorkshop($chef);

        $this->mock(GoogleDriveService::class, function (MockInterface $mock) {
            $mock->shouldReceive('isEnabled')->andReturn(false);
        });

        $response = $this->actingAs($chef)->postJson(route('chef.workshops.recording', $workshop));

        $response->assertStatus(503)
            ->assertJson([
                'success' => false,
            ]);
    }

    protected function createChef(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_CHEF,
            'chef_status' => User::CHEF_STATUS_APPROVED,
        ]);
    }

    protected function createWorkshop(User $chef): Workshop
    {
        return Workshop::query()->create([
            'user_id' => $chef->id,
            'title' => 'Test Workshop',
            'description' => str_repeat('Great session. ', 3),
            'content' => 'Full syllabus',
            'category' => 'Baking',
            'level' => 'beginner',
            'duration' => 90,
            'max_participants' => 20,
            'price' => 30,
            'currency' => 'USD',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'is_online' => true,
            'meeting_link' => 'https://meet.google.com/abc-defg-hij',
            'location' => 'Online',
            'address' => 'Virtual',
            'instructor' => 'Chef Test',
            'what_you_will_learn' => 'Skills',
            'requirements' => 'None',
            'materials_needed' => 'Basic tools',
            'is_active' => true,
        ]);
    }
}
