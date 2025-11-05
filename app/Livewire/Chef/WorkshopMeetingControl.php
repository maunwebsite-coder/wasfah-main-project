<?php

namespace App\Livewire\Chef;

use App\Models\Workshop;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class WorkshopMeetingControl extends Component
{
    #[Locked]
    public Workshop $workshop;

    public bool $started = false;

    public ?string $startedAtIso = null;

    public ?string $startedAtHuman = null;

    public bool $confirmHost = false;

    public ?string $errorMessage = null;

    public function mount(Workshop $workshop): void
    {
        $this->authorizeWorkshop($workshop);
        $this->syncState();
    }

    public function startMeeting(): void
    {
        $this->reset('errorMessage');

        $this->authorizeWorkshop($this->workshop);

        if (!$this->confirmHost) {
            $this->errorMessage = 'يرجى تأكيد أنك المضيف ومسجل الدخول بحساب Google قبل بدء الاجتماع.';
            return;
        }

        if (!$this->workshop->is_online || !$this->workshop->meeting_link) {
            $this->errorMessage = 'لا يمكن بدء اجتماع لورشة غير أونلاين أو بدون رابط جاهز.';
            return;
        }

        if (!$this->workshop->meeting_started_at) {
            $this->workshop->meeting_started_at = now();
            $this->workshop->meeting_started_by = Auth::id();
            $this->workshop->save();
        }

        $this->workshop->refresh();
        $this->syncState();

        $this->dispatch('meeting-started', startedAt: $this->startedAtIso);
    }

    protected function authorizeWorkshop(Workshop $workshop): void
    {
        $user = Auth::user();

        if (!$user || ($workshop->user_id !== $user->id && !$user->isAdmin())) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الورشة.');
        }
    }

    protected function syncState(): void
    {
        $this->started = (bool) $this->workshop->meeting_started_at;
        $this->startedAtIso = $this->workshop->meeting_started_at?->toIso8601String();
        $this->startedAtHuman = $this->workshop->meeting_started_at
            ? $this->workshop->meeting_started_at->locale('ar')->diffForHumans()
            : null;

        if ($this->started) {
            $this->confirmHost = true;
        }
    }

    public function render()
    {
        return view('livewire.chef.workshop-meeting-control');
    }
}
