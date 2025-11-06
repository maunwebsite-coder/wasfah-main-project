<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class WorkshopMeetingLinkEncryptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_meeting_link_is_encrypted_and_decrypted_transparently(): void
    {
        $user = User::factory()->create();

        $workshop = Workshop::create(array_merge(
            $this->baseWorkshopAttributes($user),
            [
                'meeting_link' => 'https://meet.example.com/secure-room',
                'start_date' => Carbon::now()->addDay(),
                'end_date' => Carbon::now()->addDay()->addHours(2),
            ],
        ));

        $this->assertSame('https://meet.example.com/secure-room', $workshop->meeting_link);
        $this->assertNotSame(
            'https://meet.example.com/secure-room',
            $workshop->getRawOriginal('meeting_link')
        );
        $this->assertNotEmpty($workshop->getRawOriginal('meeting_link_cipher'));
    }

    public function test_clearing_meeting_link_resets_cipher_storage(): void
    {
        $user = User::factory()->create();

        $workshop = Workshop::create(array_merge(
            $this->baseWorkshopAttributes($user),
            [
                'meeting_link' => 'https://meet.example.com/clear-room',
                'start_date' => Carbon::now()->addDay(),
                'end_date' => Carbon::now()->addDay()->addHours(3),
            ],
        ));

        $workshop->update(['meeting_link' => null]);
        $workshop->refresh();

        $this->assertNull($workshop->getRawOriginal('meeting_link'));
        $this->assertNull($workshop->getRawOriginal('meeting_link_cipher'));
        $this->assertNull($workshop->meeting_link);
    }

    protected function baseWorkshopAttributes(User $user): array
    {
        return [
            'user_id' => $user->id,
            'title' => 'Encryption Workshop',
            'description' => 'Focuses on security measures.',
            'content' => 'Detailed curriculum.',
            'instructor' => 'Chef Encryptor',
            'instructor_bio' => 'Encryption expert in culinary arts.',
            'category' => 'Security',
            'level' => 'advanced',
            'duration' => 90,
            'max_participants' => 25,
            'price' => 200.00,
            'currency' => 'SAR',
            'image' => null,
            'images' => null,
            'location' => 'Online',
            'address' => 'Virtual Kitchen',
            'latitude' => null,
            'longitude' => null,
            'requirements' => 'Interest in security',
            'what_you_will_learn' => 'Protect workshop links',
            'materials_needed' => 'Notebook',
            'is_online' => true,
            'registration_deadline' => Carbon::now()->addHours(6),
            'is_active' => true,
            'is_featured' => false,
        ];
    }
}
