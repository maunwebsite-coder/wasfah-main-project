<div wire:poll.7s="refreshLock" class="absolute inset-0 pointer-events-none">
    @if ($meetingLocked)
        <div class="session-lock-overlay pointer-events-auto" aria-hidden="false">
            <span class="lock-icon">
                <i class="fas fa-user-clock"></i>
            </span>
            <h2 class="text-white">بانتظار عودة المضيف</h2>
            <p>غادر المضيف الاجتماع للحظات. سيتم فتح الغرفة تلقائياً فور عودته.</p>
            @if ($lockedAtHuman)
                <p class="lock-since">آخر حضور للمضيف {{ $lockedAtHuman }}.</p>
            @endif
        </div>
    @endif
</div>
