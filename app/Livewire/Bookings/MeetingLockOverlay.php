<?php

namespace App\Livewire\Bookings;

use App\Models\Workshop;
use App\Models\WorkshopBooking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public ?string $lockedAtHuman = null;

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
        $this->lockedAtHuman = $this->formatLockedAtHuman($this->lockedAtIso);
    }

    public function refreshLock(): void
    {
        $workshop = Workshop::query()
            ->select(['id', 'meeting_started_at', 'meeting_locked_at'])
            ->find($this->workshopId);

        if (!$workshop) {
            return;
        }

        $this->startedAtIso = optional($workshop->meeting_started_at)->toIso8601String();
        $this->lockedAtIso = optional($workshop->meeting_locked_at)->toIso8601String();
        $this->meetingStarted = $this->startedAtIso !== null;
        $this->meetingLocked = $this->lockedAtIso !== null;
        $this->lockedAtHuman = $this->formatLockedAtHuman($this->lockedAtIso);
    }

    public function render()
    {
        return view('livewire.bookings.meeting-lock-overlay');
    }

    protected function formatLockedAtHuman(?string $isoTimestamp): ?string
    {
        if (!$isoTimestamp) {
            return null;
        }

        return Carbon::parse($isoTimestamp)->locale('ar')->diffForHumans();
    }
}

