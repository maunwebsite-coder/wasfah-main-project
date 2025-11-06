<?php

namespace App\Livewire\Bookings;

use App\Models\Workshop;
use App\Models\WorkshopBooking;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Locked;
use Livewire\Component;

class MeetingLockOverlay extends Component
{
    #[Locked]
    public int $workshopId;

    #[Locked]
    public int $bookingId;

    public bool $meetingStarted = false;

    public bool $meetingLocked = false;

    public ?string $startedAtIso = null;

    public ?string $lockedAtIso = null;

    public function mount(string $bookingCode, int $workshopId, ?string $initialStartedAt = null, ?string $initialLockedAt = null, bool $initialLocked = false): void
    {
        $booking = WorkshopBooking::query()
            ->where('public_code', $bookingCode)
            ->where('workshop_id', $workshopId)
            ->first();

        if (!$booking) {
            throw new ModelNotFoundException('Unable to locate the booking associated with this الاجتماع.');
        }

        $this->bookingId = $booking->id;
        $this->workshopId = $workshopId;

        $this->startedAtIso = $initialStartedAt;
        $this->lockedAtIso = $initialLockedAt;
        $this->meetingStarted = $initialStartedAt !== null;
        $this->meetingLocked = $initialLocked;
    }

    public function refreshLock(): void
    {
        $columns = ['id', 'meeting_started_at'];

        if ($this->meetingLockSupported()) {
            $columns[] = 'meeting_locked_at';
        }

        $workshop = Workshop::query()
            ->select($columns)
            ->find($this->workshopId);

        if (!$workshop) {
            return;
        }

        $this->startedAtIso = optional($workshop->meeting_started_at)->toIso8601String();
        $this->meetingStarted = $this->startedAtIso !== null;

        if ($this->meetingLockSupported()) {
            $this->lockedAtIso = optional($workshop->meeting_locked_at)->toIso8601String();
            $this->meetingLocked = $this->lockedAtIso !== null;
        } else {
            $this->lockedAtIso = null;
            $this->meetingLocked = false;
        }
    }

    public function render()
    {
        return view('livewire.bookings.meeting-lock-overlay');
    }

    protected function meetingLockSupported(): bool
    {
        static $supported;

        if ($supported === null) {
            $supported = Schema::hasColumns('workshops', [
                'meeting_started_at',
                'meeting_locked_at',
            ]);
        }

        return $supported;
    }
}
