<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workshop;
use App\Services\GoogleDriveService;
use Google\Service\Drive\DriveFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class ChefPublicWorkshopsTest extends TestCase
{
    use RefreshDatabase;

    public function test_drive_recording_is_attached_to_workshop_card(): void
    {
        $chef = User::factory()->create([
            'role' => User::ROLE_CHEF,
            'chef_status' => User::CHEF_STATUS_APPROVED,
        ]);

        Workshop::query()->create([
            'user_id' => $chef->id,
            'title' => 'Tiramisu',
            'description' => str_repeat('Great workshop. ', 3),
            'content' => 'Agenda',
            'category' => 'Baking',
            'level' => 'beginner',
            'duration' => 90,
            'max_participants' => 15,
            'price' => 30,
            'currency' => 'USD',
            'start_date' => now()->subDay(),
            'end_date' => now()->subHours(12),
            'is_online' => true,
            'meeting_link' => 'https://meet.google.com/abc-defg-hij',
            'location' => 'Online',
            'address' => 'Virtual',
            'instructor' => 'Chef Test',
            'what_you_will_learn' => 'Skills',
            'requirements' => 'None',
            'materials_needed' => 'Basics',
            'is_active' => true,
        ]);

        $driveFile = new DriveFile();
        $driveFile->setId('drive-file-123');
        $driveFile->setName('Tiramisu - 2025-11-19 Session');
        $driveFile->setWebViewLink('https://drive.google.com/file/d/drive-file-123/view?usp=drivesdk');
        $driveFile->setModifiedTime(now()->toAtomString());

        $this->mock(GoogleDriveService::class, function (MockInterface $mock) use ($driveFile) {
            $mock->shouldReceive('isEnabled')->andReturn(true);
            $mock->shouldReceive('listRecordings')
                ->once()
                ->with(null, 100)
                ->andReturn([$driveFile]);
        });

        $response = $this->get(route('chef.public.workshops', ['username' => $chef->id]));

        $response->assertOk();
        $response->assertSee('drive.google.com/file/d/drive-file-123/preview', false);
    }
}
