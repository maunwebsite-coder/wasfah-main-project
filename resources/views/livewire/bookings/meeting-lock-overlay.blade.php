<div wire:poll.5s="refreshLock" class="absolute inset-0 pointer-events-none">
    @if ($meetingLocked)
        <style>
            #jitsi-container iframe {
                visibility: hidden !important;
            }
        </style>
        <div class="session-lock-overlay pointer-events-auto" aria-hidden="false">
            <span class="lock-icon" aria-hidden="true">
                <i class="fas fa-spinner fa-spin"></i>
            </span>
            <span class="sr-only">بانتظار عودة المضيف</span>
        </div>
    @else
        <style>
            #jitsi-container iframe {
                visibility: visible !important;
            }
        </style>
    @endif
</div>
